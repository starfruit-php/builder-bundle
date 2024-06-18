<?php

namespace Starfruit\BuilderBundle\Config;

class SecurityConfig
{
    const CONFIG_NAME = 'starfruit_builder.security';

    private $config;
    private $removeHeaders;
    private $customHSTS;
    private $customCSP;

    public function __construct()
    {
        $this->removeHeaders = [];
        $this->customHSTS = 'max-age=7776000';
        $this->customCSP = "";
        $this->setup();
    }

    public function getRemoveHeaders()
    {
        return array_merge([
            'x-powered-by',
            'server',
            'Server',
        ], $this->removeHeaders);
    }

    public function getCustomHSTS()
    {
        return $this->customHSTS;
    }

    public function getCustomCSP()
    {
        return $this->customCSP;
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
            $this->removeHeaders = isset($this->config['response']['remove_headers']) ? $this->config['response']['remove_headers'] : $this->removeHeaders;

            $this->customHSTS = isset($this->config['response']['custom_hsts_value']) ? $this->config['response']['custom_hsts_value'] : $this->customHSTS;

            $this->customCSP = isset($this->config['response']['custom_csp_value']) ? $this->config['response']['custom_csp_value'] : $this->customCSP;
        }
    }
}