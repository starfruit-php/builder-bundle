<?php

namespace Starfruit\BuilderBundle\Seo;

use Starfruit\BuilderBundle\Model\Seo;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\SystemTool;
use Starfruit\BuilderBundle\Tool\ApiTool;

class SeoScore
{
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const DANGER = 'danger';

    protected $title;
    protected $description;
    protected $content;
    protected $keyword;
    protected $slug;

    protected $url;
    protected $scoring;

    // times for keyword appears in content
    private int $times;
    // total words in content
    private int $countWord;
    // total error
    private int $errorTotal;

    public function __construct($title, $description, $content, $keyword, $slug)
    {
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->keyword = $keyword;
        $this->slug = $slug;
        $this->url = SystemTool::getUrl($this->slug);

        $this->scoring = [
            'data' => [
                'title' => $this->title,
                'description' => $this->description,
                'content' => $this->content,
                'keyword'=> $this->keyword,
                'slug' => $this->slug,
                'url' => $this->url,
            ],
        ];
        $this->times = SeoHelper::countAppearTimes($this->content, $this->keyword);
        $this->countWord = SeoHelper::countWord($this->content);
        $this->errorTotal = 0;

        $this->testBasic();
        $this->testAdditional();
        $this->testReadability();
        $this->testLink();
    }

    public function scoring()
    {
        return $this->scoring;
    }

    private function testBasic()
    {
        $this->errorTotal = 0;
        $countWord = $this->countWord;
        $keyword = [];

        $titleScore = $this->scoreTimes(SeoHelper::countAppearTimes($this->title, $this->keyword));
        $keyword['title'] = $titleScore;

        $descriptionScore = $this->scoreTimes(SeoHelper::countAppearTimes($this->description, $this->keyword));
        $keyword['description'] = $descriptionScore;

        $urlScore = $this->scoreTimes(SeoHelper::countAppearTimes($this->url, TextTool::getPretty($this->keyword)));
        $keyword['url'] = $urlScore;

        $firstContentScore = $this->scoreTimes(SeoHelper::countAppearTimes($this->content, $this->keyword, 10));
        $keyword['firstContent'] = $firstContentScore;

        $contentScore = $this->scoreTimes($this->times);
        $keyword['content'] = $contentScore;

        $errorTotal = $this->errorTotal;
        $this->scoring['basic'] = compact('keyword', 'countWord', 'errorTotal');
    }

    private function testAdditional()
    {
        $this->errorTotal = 0;
        $urlLength = $this->scoreUrlLength();
        $link = $this->scoreLink();
        $keyword = [];

        $subheadingScore = $this->scoreTimes(SeoHelper::countAppearTimes(SeoHelper::getAllValues("#<h(2|3|4).*?>(.*?)</h(2|3|4)>#i", $this->content, 2), $this->keyword));
        $keyword['subheading'] = $subheadingScore;

        $imgAltScore = $this->scoreTimes(SeoHelper::countAppearTimes(SeoHelper::getAllValues('/<img(.*?)alt=\"(.*?)\"(.*?)>/si', $this->content, 2), $this->keyword));
        $keyword['imgAlt'] = $imgAltScore;

        $keyword['density'] = $this->scoreKeywordDensity();
        $keyword['unique'] = $this->scoreKeywordUnique();

        $errorTotal = $this->errorTotal;
        $this->scoring['additional'] = compact('keyword', 'urlLength', 'link', 'errorTotal');
    }

    private function testReadability()
    {
        $this->errorTotal = 0;
        $title = [];
        $content = [
            // 'TOC' => false,
        ];

        $beginWithKeyword = $this->scoreNeedTrue(str_starts_with(strtolower($this->title), strtolower($this->keyword)));
        $title['beginWithKeyword'] = $beginWithKeyword;

        $usingNumber = $this->scoreNeedTrue(SeoHelper::containNumber($this->title));
        $title['usingNumber'] = $usingNumber;

        $content['shortParagraphs'] = $this->scoreParagraph();

        // >= 4 items is best
        $mediaScore = $this->scoreTimes(SeoHelper::countMediaTag($this->content, "img|video"));
        $content['media'] = $mediaScore;

        $errorTotal = $this->errorTotal;
        $this->scoring['readability'] = compact('title', 'content', 'errorTotal');
    }

    private function testLink()
    {
        $this->errorTotal = 0;
        $detail = [];
        $statusCodeList = [];

        $pattern = '/<a[^>]*href="(.*?)"[^>]*>/i';
        $urls = SeoHelper::getContentArray($pattern, $this->content, 1);

        foreach ($urls as $url) {
            $checkUrl = $this->checkUrl($url);
            $detail[] = $checkUrl;
            $statusCode = $checkUrl['statusCode'];
            if (array_key_exists($statusCode, $statusCodeList)) {
                $statusCodeList[$statusCode]['count'] += 1;
            } else {
                $statusCodeList[$statusCode] = [
                    'count' => 1,
                    'status' => $checkUrl['status'],
                ];
            }
        }

        $errorTotal = $this->errorTotal;
        $this->scoring['link'] = compact('detail', 'statusCodeList', 'errorTotal');
    }

    private function scoreTimes($times)
    {
        $error = $times == 0;
        $status = $error ? self::DANGER : self::SUCCESS;
        $this->errorTotal += (int) $error;

        return compact('times', 'status', 'error');
    }

    private function scoreNeedTrue($bool, $mergeFields = [])
    {
        $error = !$bool;
        $status = $error ? self::DANGER : self::SUCCESS;
        $this->errorTotal += (int) $error;

        return array_merge(compact('status', 'error'), $mergeFields);
    }

    private function scoreNeedFalse($bool, $mergeFields = [])
    {
        $error = $bool;
        $status = $error ? self::DANGER : self::SUCCESS;
        $this->errorTotal += (int) $error;

        return array_merge(compact('status', 'error'), $mergeFields);
    }

    // good between 1-1.5%, warning if <= 2.5%
    private function scoreKeywordDensity()
    {
        $times = $this->times;
        $density = ($this->countWord == 0 ? 0 : round(100 * SeoHelper::countWord($this->keyword) * $this->times / $this->countWord));
        $error = false;
        if ($density >= 1 && $density <= 1.5) {
            $status = self::SUCCESS;
        } elseif ($density > 1.5 && $density <= 2.5) {
            $status = self::WARNING;
        } else {
            $error = true;
            $status = self::DANGER;
        }

        return compact('status', 'error', 'density', 'times');
    }

    // good if keyword is unique
    private function scoreKeywordUnique()
    {
        $record = Seo::getByKeyword($this->keyword);
        $total = count($record);

        return $this->scoreNeedTrue($total == 1, compact('total'));
    }

    // good with <= 75
    private function scoreUrlLength()
    {
        $length = strlen($this->url);
        return $this->scoreNeedTrue($length <= 75, compact('length'));
    }

    // good with short paragraphs <= 120 words
    private function scoreParagraph()
    {
        $paragraphs = [
            'total' => 0,
            'short' => 0,
            'long' => 0,
            'empty' => 0,
            'detail' => []
        ];

        $pattern = '/<p>(.+?)<\/p>/i';
        $paragraphContents = SeoHelper::getContentArray($pattern, $this->content);

        if (!empty($paragraphContents)) {
            $paragraphs['total'] = count($paragraphContents);

            foreach ($paragraphContents as $paragraph) {
                $wordCounts = SeoHelper::countWord($paragraph);
                $isEmpty = $wordCounts == 0;
                $isShort = !$isEmpty && $wordCounts <= 120;

                if ($isEmpty) {
                    $paragraphs['empty'] += 1;
                } else {
                    if ($isShort) {
                        $paragraphs['short'] += 1;
                    } else {
                        $paragraphs['long'] += 1;
                    }
                }

                $paragraphs['detail'][] = compact('wordCounts', 'isShort');
            }
        }

        $error = false;
        $shortDensity = $paragraphs['total'] > 0 ? round(100 * $paragraphs['short'] / $paragraphs['total']) : 0;
        if ($shortDensity >= 50) {
            $status = self::SUCCESS;
        } elseif ($shortDensity >= 31) {
            $status = self::WARNING;
        } else {
            $error = true;
            $status = self::DANGER;
        }

        $this->errorTotal += (int) $error;
        return compact('status', 'error', 'paragraphs');
    }

    private function scoreLink()
    {
        $pattern = '/<a\s+[^>]*href="(http|https):\/\/([^"]+)"[^>]*>/i';
        $external = SeoHelper::getCount($pattern, $this->content);

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*>/i';
        $total = SeoHelper::getCount($pattern, $this->content);

        $internal = $total - $external;

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*rel="[^>]*nofollow[^>]*"[^>]*>/i';
        $nofollow = SeoHelper::getCount($pattern, $this->content);

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*rel="[^>]*dofollow[^>]*"[^>]*>/i';
        $dofollow = SeoHelper::getCount($pattern, $this->content);

        return compact('total', 'internal', 'external', 'nofollow', 'dofollow');
    }

    private function checkUrl($url)
    {
        $url = SystemTool::getUrl($url);
        $response = ApiTool::call('GET', $url);
        $statusCode = $response['isRedirect'] ? 301 :  $response['status'];
        $error = false;

        if ($statusCode == 200) {
            $status = self::SUCCESS;
        } elseif ($statusCode == 404) {
            $error = true;
            $status = self::DANGER;
        } else {
            $status = self::WARNING;
        }

        $this->errorTotal += (int) $error;

        return compact('status', 'error', 'statusCode', 'url');
    }
}
