<?php

namespace Starfruit\BuilderBundle\Seo;

use Starfruit\BuilderBundle\Tool\TextTool;

class SeoHelper
{
    public static function countAppearTimes($string, $word, $percent = 100): ?int
    {
        if (!$string || !$word) {
            return 0;
        }

        // remove break line
        $string = TextTool::getStringAsOneLine($string);
        // remove html tag
        $string = TextTool::removeHtmlTag($string);

        $percent = (int) $percent;
        $percent = $percent < 100 && $percent > 0 ? $percent : 100; 
        if ($percent < 100) {
            $length = round(self::countWord($string) * $percent / 100);

            $string = implode(' ', array_slice(explode(' ', $string), 0, $length));
        }

        return substr_count(strtolower($string), strtolower($word));
    }

    public static function countCharacter($string): ?int
    {
        // remove break line
        $string = TextTool::getStringAsOneLine($string);
        // remove html tag
        $string = TextTool::removeHtmlTag($string);
        // trim string,
        // replace multiple spaces ('    ') -> a single space (' ')
        $string = preg_replace('/\s+/', ' ', trim($string));

        return strlen($string);
    }

    public static function countWord($string): ?int
    {
        // remove break line
        $string = TextTool::getStringAsOneLine($string);
        // remove html tag
        $string = TextTool::removeHtmlTag($string);
        // remove special character, include Vietnamese
        $string = TextTool::getPretty($string);
        $string = str_replace("-", " ", $string);

        // count word, adding option '0123456789' to make number as a word
        return str_word_count($string, 0, '0123456789');
    }

    public static function countTag($string, $tag): ?int
    {
        $pattern = '/<' . $tag . '[^>]*>/i';
        return self::getCount($pattern, $string);
    }

    // img | video | iframe
    public static function countMediaTag($string, $tag): ?int
    {
        $pattern = '/<' . $tag . '\s+[^>]*src="([^"]+)"[^>]*>/i';
        return self::getCount($pattern, $string);
    }

    public static function countLink($string): ?array
    {
        $pattern = '/<a\s+[^>]*href="\/([^"]+)"[^>]*>/i';
        $internal = self::getCount($pattern, $string);

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*>/i';
        $total = self::getCount($pattern, $string);

        $external = $total - $internal;

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*rel="[^>]*nofollow[^>]*"[^>]*>/i';
        $nofollow = self::getCount($pattern, $string);

        $pattern = '/<a\s+[^>]*href="([^"]+)"[^>]*rel="[^>]*dofollow[^>]*"[^>]*>/i';
        $dofollow = self::getCount($pattern, $string);

        return compact('total', 'internal', 'external', 'nofollow', 'dofollow');
    }

    // concat content of multiple tags 
    public static function getAllValues($pattern, $string, $position = 0): ?string
    {
        return implode('. ', self::getContentArray($pattern, $string, $position));
    }

    // get content by position of element (key)
    public static function getContentArray($pattern, $string, $position = 0): ?array
    {
        // remove break line
        $string = TextTool::getStringAsOneLine($string);
        preg_match_all($pattern, $string, $result);

        return isset($result[$position]) && is_array($result[$position]) ? $result[$position] : [];
    }

    public static function getCount($pattern, $string): ?int
    {
        return count(self::getContentArray($pattern, $string));
    }

    public static function containNumber($string): ?bool
    {
        $pattern = '~[0-9]+~';
        return self::validateString($pattern, $string);
    }

    public static function validateString($pattern, $string): ?bool
    {
        return (bool) preg_match($pattern, $string);
    }
}
