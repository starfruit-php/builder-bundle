<?php

namespace Starfruit\BuilderBundle\Seo;

use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\SystemTool;

class SeoScore
{
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

        $this->testBasic();
        $this->testAdditional();
        $this->testReadability();
    }

    public function scoring()
    {
        return $this->scoring;
    }

    private function testBasic()
    {
        $test = [
            'keyword' => [
                'title' => SeoHelper::countAppearTimes($this->title, $this->keyword),
                'description' => SeoHelper::countAppearTimes($this->description, $this->keyword),
                'url' => SeoHelper::countAppearTimes($this->url, TextTool::getPretty($this->keyword)),
                'firstContent' => SeoHelper::countAppearTimes($this->content, $this->keyword, 10),
                'content' => $this->times,
            ],
            'countWord' => $this->countWord,
        ];

        $this->scoring['basic'] = $test;
    }

    private function testAdditional()
    {
        $test = [
            'keyword' => [
                'subheading' => SeoHelper::countAppearTimes(SeoHelper::getAllValues("#<h(2|3|4).*?>(.*?)</h(2|3|4)>#i", $this->content, 2), $this->keyword),
                'imgAlt' => SeoHelper::countAppearTimes(SeoHelper::getAllValues('/<img(.*?)alt=\"(.*?)\"(.*?)>/si', $this->content, 2), $this->keyword),
                'density' => [
                    'times' => $this->times,
                    'density' => ($this->countWord == 0 ? 0 : round(100 * SeoHelper::countWord($this->keyword) * $this->times / $this->countWord)) . '%',
                ],
                'unique' => true,
            ],
            'urlLength' => strlen($this->url),
            'link' => SeoHelper::countLink($this->content),
        ];

        $this->scoring['additional'] = $test;
    }

    private function testReadability()
    {
        $test = [
            'title' => [
                'beginWithKeyword' => str_starts_with(strtolower($this->title), strtolower($this->keyword)),
                'usingNumber' => SeoHelper::containNumber($this->title),
            ],
            'content' => [
                'TOC' => false,
                'shortParagraphs' => false,
                'media' => SeoHelper::countMediaTag($this->content, "img|video"),
            ]
        ];

        $this->scoring['readability'] = $test;
    }
}
