<?php

namespace Starfruit\BuilderBundle\Seo;

use Symfony\Component\HttpFoundation\Request;
use Starfruit\BuilderBundle\Tool\SystemTool;

class SeoSchema
{
    public static function validate($schemaBlock): ?bool
    {
        $pattern = '/<script type="application\/ld\+json">[^>]*<\/script>/i';
        return SeoHelper::validateString($pattern, $schemaBlock);
    }

    public static function getSchemaData($schemaBlock, $params = []): ?array
    {
        $schemaData = [];
        if (!empty($schemaBlock) && self::validate($schemaBlock)) {
            $pattern = '/<script type="application\/ld\+json">(.*?)<\/script>/i';
            $schemaContents = SeoHelper::getContentArray($pattern, $schemaBlock, 1);
            $schemaBlocks = SeoHelper::getContentArray($pattern, $schemaBlock, 0);

            if (!empty($schemaContents)) {
                $url = isset($params['url']) ? $params['url'] : SystemTool::getCurrentUrl();

                foreach ($schemaContents as $key => $schemaContent) {
                    $schemaField = json_decode($schemaContent, true);

                    if ($schemaField) {
                        $schemaField = self::replaceUrl($schemaField, $url);
                        $schemaData[] = str_replace($schemaContent, json_encode($schemaField), $schemaBlocks[$key]);
                    }
                }     
            }
        }

        return $schemaData;
    }

    // replace "url" + "urlTemplate"
    private static function replaceUrl($schemaField, $url)
    {
        $replaceFields = ["url", "urlTemplate"];

        foreach ($schemaField as $field => $value) {
            if (is_array($value)) {
                $schemaField[$field] = self::replaceUrl($value, $url);
            }

            if (in_array($field, $replaceFields)) {
                if (empty($value)) {
                    $schemaField[$field] = $url;
                }
            }
        }

        return $schemaField;
    }
}
