<?php

namespace Starfruit\BuilderBundle\Config;

class SitemapConfig
{
    const CONFIG_NAME = 'starfruit_builder.sitemap';

    private $config;

    public function __construct()
    {
        $this->documentAutoGenerate = false;
        $this->setup();
    }

    public function getDocumentAutoGenerate()
    {
        return $this->documentAutoGenerate;
    }

    private function getConfig()
    {
        $config = \Pimcore::getContainer()->getParameter(self::CONFIG_NAME);
        return $config;
    }

    private function setup()
    {
        $this->config = $this->getConfig();

        if (!empty($this->config)) {
            $this->documentAutoGenerate = isset($this->config['document']['auto_regenerate']) ? $this->config['document']['auto_regenerate'] : $this->documentAutoGenerate;
        }
    }
}