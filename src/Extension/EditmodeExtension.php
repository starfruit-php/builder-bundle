<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Starfruit\BuilderBundle\Config\TemplateConfig;

class EditmodeExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_editmode_config', [$this, 'getEditmodeConfig']),
        ];
    }

    /**
     * @param object $image
     * @param string $class
     * @param string $thumbnailName
     */
    public function getEditmodeConfig()
    {
        $config = new TemplateConfig();
        return $config->getEditmodeConfig();
    }
}
