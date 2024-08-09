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

class RenderExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var RequestStack $requestStack
     */
    private RequestStack $requestStack;

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
}
