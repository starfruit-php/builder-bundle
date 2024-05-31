<?php

namespace Starfruit\BuilderBundle\Config;

class MailConfig
{
    const CONFIG_NAME = 'starfruit_builder.mail';

    private $config;

    public function __construct()
    {
        $this->ignoreDebugMode = false;
        $this->setup();
    }

    public function getIgnoreDebugMode()
    {
        return $this->ignoreDebugMode;
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
            $this->ignoreDebugMode = isset($this->config['ignore_debug_mode']) ? $this->config['ignore_debug_mode'] : $this->ignoreDebugMode;
        }
    }
}