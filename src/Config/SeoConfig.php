<?php

namespace Starfruit\BuilderBundle\Config;

class SeoConfig
{
    const CONFIG_NAME = 'starfruit_builder.seo';

    private $config;
    private $imageThumbnail;

    public function __construct()
    {
        $this->imageThumbnail = null;
        $this->setup();
    }

    public function getImageThumbnail()
    {
        return $this->imageThumbnail;
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
            $this->imageThumbnail = isset($this->config['image_thumbnail']) ? $this->config['image_thumbnail'] : $this->imageThumbnail;
        }
    }
}