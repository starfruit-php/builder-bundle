<?php

declare(strict_types=1);

namespace Starfruit\BuilderBundle\Sitemaps\Processors;

use Pimcore\Bundle\SeoBundle\Sitemap\Element\GeneratorContextInterface;
use Pimcore\Bundle\SeoBundle\Sitemap\Element\ProcessorInterface;
use Pimcore\Bundle\SeoBundle\Sitemap\UrlGeneratorInterface;
use Pimcore\Model\Element\ElementInterface;
use Presta\SitemapBundle\Sitemap\Url\Url;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class AbsoluteURLProcessor implements ProcessorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function process(Url $url, ElementInterface $element, GeneratorContextInterface $context): ?Url
    {
        $path = $this->urlGenerator->generateUrl($url->getLoc());

        return new UrlConcrete($path);
    }
}
