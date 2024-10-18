<?php

namespace Starfruit\BuilderBundle\Extension;

use Pimcore\Model\Document;
use Pimcore\Navigation\Container;
use Pimcore\Navigation\Page\Document as NavDocument;
use Pimcore\Twig\Extension\Templating\Navigation;
use Pimcore\Twig\Extension\Templating\Placeholder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NavigationExtension extends AbstractExtension
{
    const NAVIGATION_EXTENSION_POINT_PROPERTY = 'navigation_extension_point';

    /**
     * @var CategoryLinkGenerator
     */
    protected $categoryLinkGenerator;

    /**
     * @var Navigation
     */
    protected $navigationHelper;

    /**
     * @var Placeholder
     */
    protected $placeholderHelper;

    /**
     * @param Navigation $navigationHelper
     */
    public function __construct(Navigation $navigationHelper, Placeholder $placeholderHelper)
    {
        $this->navigationHelper = $navigationHelper;
        $this->placeholderHelper = $placeholderHelper;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_navigation_data_links', [$this, 'getDataLinks']),
        ];
    }

    /**
     * @param Document $document
     * @param Document $startNode
     *
     * @return \Pimcore\Navigation\Container
     */
    public function getDataLinks(Document $document, Document $startNode)
    {
        $navigation = $this->navigationHelper->build([
            'active' => $document,
            'root' => $startNode,
        ]);

        return $navigation;
    }
}
