<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;

use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Model\Seo;

class SeoExtension extends \Twig\Extension\AbstractExtension
{
    public function __construct(
        protected HeadMeta $headMeta,
        protected HeadTitle $headTitle
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_seo', [$this, 'setupSeoData']),
        ];
    }

    public function setupSeoData($object, $locale = null)
    {
        if (!$locale) {
            $locale = LanguageTool::getLocale();
        }

        $seo = Seo::getOrCreate($object, $locale);
        $seoData = $seo->getSeoData();

        if (!empty($seoData)) {
            $defaultMetas = [
                "og:locale" => $locale,
                "og:type" => "website",
            ];

            $metas = [
                "og:title" => "title",
                "og:description" => "description",
                "og:image" => "image",
                "og:image:alt" => "title",
                "og:url" => "slug",
                "twitter:title" => "title",
                "twitter:description" => "description",
            ];

            foreach ($defaultMetas as $key => $value) {
                $this->headMeta->appendProperty($key, $value);
            }

            foreach ($metas as $key => $field) {
                $this->headMeta->appendProperty($key, $seoData[$field]);
            }

            $this->headTitle->set($seoData['title']);
            $this->headMeta->setDescription($seoData['description']);
        }
    }
}
