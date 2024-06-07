<?php

namespace Starfruit\BuilderBundle\Config;

class NotificationConfig
{
    const CONFIG_NAME = 'starfruit_builder.notification';

    private $config;

    public function __construct()
    {
        $this->enable = false;
        $this->serivce = null;
        $this->setup();
    }

    public function getEnable()
    {
        return $this->enable;
    }

    public function getCodeHead()
    {
        $codeHead = null;
        if ($this->enable && $this->service && $this->service == 'onesignal') {
            $sdkLink = "https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js";
            if (isset($this->config['custom_config']['onesignal']['sdk_link'])) {
                $sdkLink = $this->config['custom_config']['onesignal']['sdk_link'];
            }

            $codeHead = '
                <script src="'. $sdkLink .'" defer></script>
                <script>
                  window.OneSignalDeferred = window.OneSignalDeferred || [];
                  OneSignalDeferred.push(function(OneSignal) {
                    OneSignal.init({
                      appId: "'. $_ENV['ONESIGNAL_APP_ID'] .'",
                    });
                  });

                  function builderNotificationGetPlayerId()
                  {
                    return OneSignal.User.onesignalId;
                  }
                </script>
            ';
        }

        return $codeHead;
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
            $this->enable = isset($this->config['enable']) ? $this->config['enable'] : $this->enable;
            $this->service = isset($this->config['service']) ? $this->config['service'] : $this->service;
        }
    }
}