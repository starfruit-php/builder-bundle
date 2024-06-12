<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ShareSocialExtension extends AbstractExtension
{
    const ALLOW_TYPES = ['facebook', 'twitter', 'linkedin', 'printerest', 'instagram', 'google'];

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('builder_share_social', [$this, 'getShareLink'])
        ];
    }

    /**
     * @param string $type
     * @param string $class
     */
    public function getShareLink($type)
    {
        $type = strtolower($type);
        if (in_array($type, self::ALLOW_TYPES)) {
            $url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

            $link = null;
            switch ($type) {
                case 'facebook':
                    $link = "https://www.facebook.com/sharer/sharer.php?u=" . $url;
                    break;

                case 'twitter':
                    $link = "https://twitter.com/share?ref_src=" . $url;
                    break;

                case 'linkedin':
                    $link = "https://www.linkedin.com/shareArticle?url=" . $url;
                    break;

                case 'pinterest':
                    $link = "https://www.pinterest.com/pin/create/?url=" . $url;
                    break;

                case 'instagram':
                    $link = "https://www.instagram.com/" . $url;
                    break;

                case 'google':
                    $link = "https://plus.google.com/share?url=" . $url;
                    break;
            }

            return $link;
        } else {
            return null;
        }
    }
}
