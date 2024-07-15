<?php

namespace Starfruit\BuilderBundle\Sitemap\Type;

use Starfruit\BuilderBundle\Sitemap\File;
use Starfruit\BuilderBundle\Tool\SystemTool;

abstract class BaseType
{
    const REPLACE = 'REPLACE_CONTENT';

    public function replaceThenPushContent(array $slugs, $section = null): void
    {
        $replaceContent = $this->getReplaceContent($slugs);
        $this->pushContent($replaceContent, $section);
    }

    public function pushContent($replaceContent, $section = null): void
    {
        $struct = $this->getStruct();
        $content = str_replace(self::REPLACE, $replaceContent, $struct);
        File::generateXMLFile($content, $section);
    }

    public function getReplaceContent(array $slugs): string
    {
        $replaceContent = '';
        foreach ($slugs as $slug) {
            $replaceContent .= $this->getItem($slug);
        }

        return $replaceContent;
    }

    public function getItem(array $slug, string $id = null): string
    {
        $tag = $this->getTag();
        if ($id) {
            $item = '<' . $tag . '><loc>' . SystemTool::getUrl($slug['loc']) . '</loc><lastmod>' . $slug['lastmod'] . '</lastmod><id>' . $slug['id'] . '</id></' . $tag . '>';
        } else {
            $item = '<' . $tag . '><loc>' . SystemTool::getUrl($slug['loc']) . '</loc><lastmod>' . $slug['lastmod'] . '</lastmod></' . $tag . '>';
        }

        return $item;
    }
}
