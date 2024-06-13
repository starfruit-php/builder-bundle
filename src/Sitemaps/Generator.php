<?php

namespace Starfruit\BuilderBundle\Sitemaps;

use Pimcore\Bundle\SeoBundle\Sitemap\Element\AbstractElementGenerator;
use Pimcore\Bundle\SeoBundle\Sitemap\Element\GeneratorContext;
use Pimcore\Localization\LocaleServiceInterface;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Tool\SystemTool;

class Generator extends AbstractElementGenerator
{
    public function __construct(
        protected LocaleServiceInterface $localeService,
        protected AbstractLinkGenerator $abstractLinkGenerator,
        array $filters = [],
        array $processors = []
    )
    {
        parent::__construct($filters, $processors);
    }

    public function populate(UrlContainerInterface $urlContainer, ?string $section = null): void
    {
        $section = "default";
        $list = new Document\Listing();
        $list->setCondition("`type` = 'page'");
        $list->setOrderKey('modificationDate');
        $list->setOrder('DESC');

        // the context contains metadata for filters/processors
        // it contains at least the url container, but you can add additional data
        // with the params parameter
        $context = new GeneratorContext($urlContainer, $section, ['foo' => 'bar']);

        $hostname = SystemTool::getHost();

        // dd($_SERVER);

        foreach ($list as $item) {
            // $link = $item->getUrl("builder.localhost");

            if ($item->getPrettyUrl()) {
                $link = $item->getPrettyUrl();
            } else {
                $link = $item->getFullPath();
            }

            if (empty($link)) {
                continue;
            }

            // create an entry for the sitemap
            $url = new UrlConcrete($link);

            // run url through processors
            $url = $this->process($url, $item, $context);

            // processors can return null to exclude the url
            if (null === $url) {
                continue;
            }

            // add the url to the container
            $urlContainer->addUrl($url, $section);
        }

        $sitemapKeys = Setting::getSitemapKeys();
        if (empty($sitemapKeys)) {
            return;
        }

        $classConfigs = ObjectConfig::getListClass();
        $languages = LanguageTool::getList();

        foreach ($classConfigs as $key => $classConfig) {
            $class = $classConfig['class_name'];
            $section = strtolower($class);

            if (in_array($section, $sitemapKeys)) {
                continue;
            }

            $list = call_user_func_array('\\Pimcore\\Model\\DataObject\\'. $class .'::getList', []);
            $list->setOrderKey('modificationDate');
            $list->setOrder('DESC');

            // the context contains metadata for filters/processors
            // it contains at least the url container, but you can add additional data
            // with the params parameter
            $context = new GeneratorContext($urlContainer, $section, ['foo' => 'bar']);

            foreach ($languages as $language) {
                //change locale as per multilingual setup
                $this->localeService->setLocale($language);

                foreach ($list as $object) {
                    // only add element if it is not filtered
                    if (!$this->canBeAdded($object, $context)) {
                        continue;
                    }

                    // use a link generator to generate an URL
                    // you need to make sure the link generator generates an absolute url
                    $link = $this->abstractLinkGenerator->generate($object);

                    if (empty($link)) {
                        continue;
                    }

                    // create an entry for the sitemap
                    $url = new UrlConcrete($link);

                    // run url through processors
                    $url = $this->process($url, $object, $context);

                    // processors can return null to exclude the url
                    if (null === $url) {
                        continue;
                    }

                    // add the url to the container
                    $urlContainer->addUrl($url, $section);
                }
            } 
        }
    }
}
