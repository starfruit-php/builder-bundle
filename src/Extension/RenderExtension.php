<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Pimcore\Config;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Symfony\Component\HttpFoundation\RequestStack;
use Starfruit\BuilderBundle\LinkGenerator\AbstractLinkGenerator;
use Starfruit\BuilderBundle\Service\EditableService;

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
     * @param object $image
     * @param string $class
     * @param string $thumbnailName
     */
    public function renderEditables($customLayouts)
    {
        $request = $this->requestStack->getCurrentRequest();
        $document = $request?->attributes?->get('contentDocument');
        $editables = $document?->getEditables();

        $editableData = [];
        foreach ($customLayouts as $customLayout) {
            $layouts = isset($customLayout['layouts']) ? $customLayout['layouts'] : [];
            if (!empty($layouts)) {
                foreach ($layouts as $layout) {
                    $editable = isset($layout['editable']) ? $layout['editable'] : null;

                    if ($editable) {
                        $name = isset($layout['params']['name']) ? $layout['params']['name'] : null;

                        if (!$name && $editable == 'list') {
                            $fields = isset($layout['params']['fields']) ? $layout['params']['fields'] : [];
                            $fields = array_column($fields, 'name');
                            $name = isset($layout['params']['prefix']) ? $layout['params']['prefix'] . 'List' : null;
                        }

                        if ($name) {
                            $value = $document->getEditable($name);

                            if ($editable == 'list') {
                                $value = EditableService::getBlock($value, $fields);
                            } else {
                                $value = EditableService::getEditdata($value);
                            }

                            $editableData[$name] = $value;
                        }
                    }
                }
            }
        }

        return $editableData;
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
