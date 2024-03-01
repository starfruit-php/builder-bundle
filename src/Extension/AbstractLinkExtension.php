<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Model\DataObject\AbstractObject;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;

class AbstractLinkExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var AbstractLinkGenerator
     */
    protected $abstractLinkGenerator;

    /**
     * AbstractExtension constructor.
     *
     * @param AbstractLinkGenerator $abstractLinkGenerator
     */
    public function __construct(AbstractLinkGenerator $abstractLinkGenerator)
    {
        $this->abstractLinkGenerator = $abstractLinkGenerator;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_get_link', [$this, 'generateLink']),
        ];
    }

    /**
     * @param $abstract
     */
    public function generateLink(AbstractObject $abstract, $params = [])
    {
        return $this->abstractLinkGenerator->generate($abstract, $params);
    }
}
