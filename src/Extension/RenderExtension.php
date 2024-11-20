<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Config;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Symfony\Component\HttpFoundation\RequestStack;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;
use Starfruit\BuilderBundle\Service\EditableService;
use Starfruit\BuilderBundle\Tool\DocumentTool;
use Pimcore\Tool\DeviceDetector;

class RenderExtension extends \Twig\Extension\AbstractExtension
{
    private $requestStack;
    private $deviceDetector;

    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_render_image', [$this, 'renderImage']),
            new TwigFunction('builder_render_wysiwyg', ['\Starfruit\BuilderBundle\Tool\TextTool', 'formatWysiwyg']),
            new TwigFunction('builder_render_editables', [$this, 'renderEditables']),
            new TwigFunction('builder_render_editable_image', [$this, 'renderEditableImageByDevice']),
        ];
    }

    /**
     * @deprecated
     * 
     * @param string $thumbnailName
     */
    public function renderEditables()
    {
        $request = $this->requestStack->getCurrentRequest();
        $document = $request?->attributes?->get('contentDocument');

        return DocumentTool::renderEditableData($document);
    }

    /**
     * @param object $image
     * @param string $class
     * @param string $thumbnailName
     */
    public function renderImage($image, $class = '', $thumbnailName = '', $alt = '')
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
            if (!$alt) {
                $alt = isset($infoImage['alt']) ? $infoImage['alt'] : Config::getWebsiteConfig()['default_image_alt'];
            }
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

    /**
     * @param array $images - array of image for device types
     * @param array $params
     *
     * keys in $params
     * @param string `class` - class value of img tag
     * @param string `alt` - alt value of img tag
     * @param array `phone` - key of phone image in $images
     *      keys in `phone`
     *      @param string `name` - name of field for phone device in $images
     *      @param string `thumbnail` - name of thumbnail for phone device in $images
     * @param array `desktop` - key of desktop image in $images
     *      keys in `desktop`
     *      @param string `name` - name of field for desktop device in $images
     *      @param string `thumbnail` - name of thumbnail for desktop device in $images
     */
    public function renderEditableImageByDevice(array $images, array $params)
    {
        $image = null;
        $thumbnailName = null;

        $deviceDetector = DeviceDetector::getInstance();
        $isPhone = DeviceDetector::getInstance()->isPhone();
        if ($isPhone) {
            $name = $params['phone']['name'];
            $thumbnailName = isset($params['phone']['thumbnail']) ? $params['phone']['thumbnail'] : $thumbnailName;
            $image = $images[$name];
        } else {
            $name = $params['desktop']['name'];
            $thumbnailName = isset($params['desktop']['thumbnail']) ? $params['desktop']['thumbnail'] : $thumbnailName;
            $image = $images[$name];
        }

        if ($image) {
            $class = isset($params['class']) ? $params['class'] : '';
            $alt = isset($params['alt']) ? $params['alt'] : '';

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
            if (!$alt) {
                $alt = isset($infoImage['alt']) ? $infoImage['alt'] : (isset(Config::getWebsiteConfig()['default_image_alt']) ? Config::getWebsiteConfig()['default_image_alt'] : '');
            }
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
