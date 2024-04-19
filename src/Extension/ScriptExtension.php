<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Model\DataObject\AbstractObject;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;

class ScriptExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_script_head', [$this, 'addScriptToHead']),
            new TwigFunction('builder_script_body', [$this, 'addScriptToBody']),
        ];
    }

    private function getScriptConfig()
    {
        $snippet = \Pimcore\Config::getWebsiteConfigValue('builder_script_config');

        return $snippet;
    }

    public function addScriptToHead()
    {
        $snippet = $this->getScriptConfig();
        if ($snippet instanceof \Pimcore\Model\Document\Snippet) {
            $key = 'headCode';
            $code = $snippet->getEditable($key)?->getData();

            return $code;
        }

        return '';
    }

    public function addScriptToBody()
    {
        $snippet = $this->getScriptConfig();
        if ($snippet instanceof \Pimcore\Model\Document\Snippet) {
            $key = 'bodyCode';
            $code = $snippet->getEditable($key)->getData();

            return $code;
        }

        return '';
    }
}
