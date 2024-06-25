<?php

namespace Starfruit\BuilderBundle\Config;

class SeoConfig
{
    const CONFIG_NAME = 'starfruit_builder.seo';

    private $config;
    private $imageThumbnail;
    private $autoFillMeta;

    public function __construct()
    {
        $this->imageThumbnail = null;
        $this->autoFillMeta = true;
        $this->setup();
    }

    public function getImageThumbnail()
    {
        return $this->imageThumbnail;
    }

    public function getAutoFillMeta()
    {
        return $this->autoFillMeta;
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
            $this->autoFillMeta = isset($this->config['autofill_meta_tags']) ? $this->config['autofill_meta_tags'] : $this->autoFillMeta;
        }
    }
}
