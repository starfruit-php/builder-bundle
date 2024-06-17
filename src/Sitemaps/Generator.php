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
use Starfruit\BuilderBundle\Service\SitemapService;

class Generator extends AbstractElementGenerator
{
    public function __construct(
        protected LocaleServiceInterface $localeService,
        protected AbstractLinkGenerator $abstractLinkGenerator,
        array $filters = [],
        array $processors = [],
        array $languages = []
    )
    {
        parent::__construct($filters, $processors);
    }

    public function populate(UrlContainerInterface $urlContainer, ?string $section = null): void
    {
        if ($section) {
            if ($section == SitemapService::PAGE_SECTION_NAME) {
                $this->populateWithPage($urlContainer);
            } else {
                $this->populateWithObjects($urlContainer, $section);
            }
        } else {
            $this->populateWithPage($urlContainer);
            $this->populateWithObjects($urlContainer);
        }
    }

    private function populateWithPage($urlContainer)
    {
        $section = SitemapService::PAGE_SECTION_NAME;
        $list = new Document\Listing();
        $list->setCondition("`type` = 'page'");
        $list->setOrderKey('modificationDate');
        $list->setOrder('DESC');

        $hostname = SystemTool::getHost();
        if ($hostname) {
            foreach ($list as $item) {
                $link = $item->getUrl($hostname);
                $link = explode($hostname, $link)[1];
                $this->addUrl($urlContainer, $link, $item, $section);
            }
        }
    }

    private function populateWithObjects($urlContainer, $section = null)
    {
        $sitemapKeys = Setting::getSitemapKeys();
        if (empty($sitemapKeys)) {
            return;
        }

        $sitemapKeys = $section ? [strtolower($section)] : $sitemapKeys;

        $classConfigs = ObjectConfig::getListClass();
        $this->languages = LanguageTool::getList();

        foreach ($classConfigs as $key => $classConfig) {
            $class = $classConfig['class_name'];
            $section = strtolower($class);

            if (!in_array($section, $sitemapKeys)) {
                continue;
            }

            $this->populateWithObject($urlContainer, $section, $class);
        }
    }

    private function populateWithObject($urlContainer, $section, $class)
    {
        $list = call_user_func_array('\\Pimcore\\Model\\DataObject\\'. $class .'::getList', []);
        $list->setOrderKey('modificationDate');
        $list->setOrder('DESC');

        // the context contains metadata for filters/processors
        // it contains at least the url container, but you can add additional data
        // with the params parameter
        $context = new GeneratorContext($urlContainer, $section, ['foo' => 'bar']);

        foreach ($this->languages as $language) {
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
                $this->addUrl($urlContainer, $link, $object, $section);
            }
        }
    }

    private function addUrl($urlContainer, $link, $item, $section)
    {
        if (empty($link)) {
            return;
        }

        // create an entry for the sitemap
        $url = new UrlConcrete($link);

        // the context contains metadata for filters/processors
        // it contains at least the url container, but you can add additional data
        // with the params parameter
        $context = new GeneratorContext($urlContainer, $section, ['foo' => 'bar']);

        // run url through processors
        $url = $this->process($url, $item, $context);

        // processors can return null to exclude the url
        if (null === $url) {
            return;
        }

        // add the url to the container
        $urlContainer->addUrl($url, $section);
    }
}
