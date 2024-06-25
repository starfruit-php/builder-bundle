<?php

namespace Starfruit\BuilderBundle\Config;

class TemplateConfig
{
    const CONFIG_NAME = 'starfruit_builder.template';

    private $config;
    private $hideEditmodeStyle;
    private $customStyles;

    public function __construct()
    {
        $this->hideEditmodeStyle = false;
        $this->customStyles = [];
        $this->setup();
    }

    public function getEditmodeConfig()
    {
        return [
            'hideStyle' => $this->hideEditmodeStyle,
            'customStyles' => $this->customStyles,
        ];
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
            $this->hideEditmodeStyle = isset($this->config['editmode']['hide_default_css']) ? $this->config['editmode']['hide_default_css'] : $this->hideEditmodeStyle;
            $this->customStyles = isset($this->config['editmode']['custom_css_files']) ? $this->config['editmode']['custom_css_files'] : $this->customStyles;
        }
    }
}
