<?php

namespace Starfruit\BuilderBundle\Tool;

use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;

class AssetTool
{
    public static function getFrontendFullPath(Asset $asset, $thumbnailName = null)
    {
        if ($thumbnailName) {
            $thumbnail = Thumbnail\Config::getByName($thumbnailName);
            if ($thumbnail) {
                $thumbnailAsset = $asset->getThumbnail($thumbnail);

                if (!$thumbnailAsset->exists()) {
                    $deferred = false;
                    $thumbnailAsset = $asset->getThumbnail($thumbnail, $deferred);
                }

                $frontendPath = $thumbnailAsset->getFrontendPath();
            }
        } else {
            $frontendPath = $asset->getFrontendFullPath();
        }

        $url = preg_match('/^http(s)?:\\/\\/.+/', $frontendPath) ?
            $frontendPath :
            \Symfony\Component\HttpFoundation\Request::createFromGlobals()->getSchemeAndHttpHost() . $frontendPath;

        return $url;
    }
}
