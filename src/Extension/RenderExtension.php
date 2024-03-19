<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;

class RenderExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_render_image', [$this, 'renderImage']),
            new TwigFunction('builder_render_wysiwyg', ['\Starfruit\BuilderBundle\Tool\TextTool', 'formatWysiwyg']),
        ];
    }

    /**
     * @param object $image
     * @param string $class
     * @param string $thumbnailName
     */
    public function renderImage($image, $class = '', $thumbnailName = '')
    {
        if ($image) {
            if ($image instanceof Thumbnail) {
                $image = $image->getAsset();
            }
            $url = $image->getFullPath();

            if ($thumbnailName) {
                $thumbnail = Thumbnail\Config::getByName($thumbnailName);

                if ($thumbnail) {
                    $thumbnailAsset = $image->getThumbnail($thumbnail);
                    if (!$thumbnailAsset->exists()) {
                        $deferred = false;
                        $thumbnailAsset = $image->getThumbnail($thumbnail, $deferred);
                    }
                    $url = $thumbnailAsset->getPath();
                }
            }

            $infoImage = $image->getMetaData();
            $alt = isset($infoImage['alt']) ? $infoImage['alt'] : '';
            $title = isset($infoImage['title']) ? $infoImage['title'] : '';
            
            $link = "<img
                    src='$url'
                    class='$class'
                    alt='$alt'
                    title='$title'>";
            return $link;
        } else {
            return null;
        }
    }
}
