<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;

use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;

class SeoExtension extends \Twig\Extension\AbstractExtension
{
    public function __construct(
        protected HeadMeta $headMeta,
        protected HeadTitle $headTitle,
        protected AbstractLinkGenerator $abstractLinkGenerator
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

        $config = new ObjectConfig($object);
        $seoData = $config->getSeoData();

        if (!empty($seoData)) {
            $defaultMetas = [
                "og:locale" => $locale,
                "og:type" => "website",
                "og:url" => $this->abstractLinkGenerator->generate($object),
            ];

            $metas = [
                "og:title" => "title",
                "og:description" => "description",
                "og:image" => "image",
                "og:image:alt" => "title",
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
