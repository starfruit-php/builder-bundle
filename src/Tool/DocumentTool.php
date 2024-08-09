<?php

namespace Starfruit\BuilderBundle\Tool;

use Pimcore\Config;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable;

class DocumentTool
{
    public static function renderEditableData(mixed $document): ?array
    {
        return self::getDataFromDocument($document, false);
    }

    public static function getEditableData(mixed $document, bool $formatJson = true): ?array
    {
        if (is_string($document)) {
            $document = Document::getByPath($path);
        } else if (is_numeric($document)) {
            $document = Document::getById($id);
        }

        if (!($document instanceof Document) || $document->getType() == 'folder') {
            return null;
        }

        if ($document instanceof Document\Link) {
            $document = Document::getByPath($document->getHref());
        }

        if ($document) {
            return self::getDataFromDocument($document, $formatJson);
        }

        return null;
    }

    private static function getDataFromDocument(Document $document, bool $formatJson)
    {
        $data = [];
        $editables = $document->getEditables();

        foreach ($editables as $field => $editable) {
            if ($editable instanceof Editable\Block) {
                $blockEditables[] = $field;
            }

            $data[$field] = self::getDataByType($editable, $formatJson);
        }

        if (!empty($blockEditables)) {
            foreach ($blockEditables as $field) {
                $totalLoop = $data[$field];
                $data[$field] = [];

                foreach ($totalLoop as $loop) {
                    $loopData = []; // dữ liệu mới cho block

                    foreach ($data as $name => $value) {
                        $find = $field .":". $loop .".";
                        if (strpos($name, $find) !== false) {
                            $elmentName = substr($name, strlen($find));

                            $loopData[$elmentName] = $value;

                            unset($data[$name]);
                        }
                    }

                    $data[$field][] = $loopData;
                }
            }
        }

        return $data;
    }

    private static function getDataByType($editable, $formatJson)
    {
        $type = $editable->getType();
        $editableData = $type == 'checkbox' ? false : null;
        if (!$editable->isEmpty()) {
            $function = 'get' . ucfirst($type);
            if (method_exists(__CLASS__, $function)) {
                $editableData = call_user_func_array(__CLASS__ .'::'. $function, [$editable, $formatJson]);
            } else {
                $editableData = $editable->getData();
            }
        }

        return $editableData;
    }

    private static function getWysiwyg($editable, $formatJson = true)
    {
        return $editable->getData();
    }

    private static function getNumeric($editable, $formatJson = true)
    {
        return (int) $editable->getData();
    }

    private static function getDate($editable, $formatJson = true)
    {
        return $formatJson ? $editable->getData()->format('d-m-Y') : $editable->getData();
    }

    private static function getRelation($editable, $formatJson = true)
    {
        return self::formatRelation($editable->getElement(), $formatJson);
    }

    private static function getRelations($editable, $formatJson = true)
    {
        $elements = [];
        foreach ($editable->getElements() as $element) {
            $relation = self::formatRelation($element, $formatJson);
            if ($relation) {
                $elements[] = $relation;
            }
        }
        
        return $elements;
    }

    private static function getImage($editable, $formatJson = true)
    {
        return $formatJson ? AssetTool::getPath($editable->getImage()) : $editable->getImage();
    }

    private static function getLink($editable, $formatJson = true)
    {
        $data['href'] = $editable->getHref();
        $data['text'] = $editable->getText();

        $internal = $editable->getData()['internal'];
        if ($internal) {
            $internalType = $editable->getData()['internalType'];

            if ($internalType == 'document') {
                $internalId = $editable->getData()['internalId'];

                $page = Document::getById($internalId);

                if ($page) {
                    $data['href'] = self::getPageUrl($page);
                }
            }
        }

        return $data;
    }

    private static function getVideo($editable)
    {
        $editableData = $editable->getData();
        $type = $editableData['type'];
        $id = $editableData['id'];

        $data = [
            'type' => $type,
            'data' => '',
            'src' => $id,
            'poster' => ''
        ];

        if ($type == 'asset') {
            $data['link'] = AssetTool::getPath(Asset::getById($id));
        }

        if ($type == 'youtube') {
            $videoID = explode("?v=", $id);
            if (count($videoID) == 1) {
                $videoID = $videoID[0];
            } else {
                $videoID = $videoID[1];
                $videoID = explode("&", $videoID)[0];
            }

            $data['data'] = $videoID;
            $data['poster'] = "https://img.youtube.com/vi/". $videoID ."/0.jpg";
        }

        return $data;
    }

    private static function getRenderlet($editable)
    {
        if ($editable instanceof Editable\Renderlet) {
            if ($editable->getSubType() == "folder") {
                $folder = $editable->getO();

                if ($folder instanceof Asset\Folder) {
                    $imageFolders = [];
                    $images = $folder->getChildren();

                    foreach ($images as $image) {
                        if ($image instanceof Asset\Image) {
                            $path = AssetTool::getPath($image);

                            if ($path) {
                                $imageFolders[] = $path;
                            }
                        }
                    }

                    return $imageFolders;
                }
            }
        }

        return $editable->getData();
    }

    private static function formatRelation($element, $formatJson)
    {
        if (!$element) {
            return null;
        }

        if (!$formatJson) {
            return $element;
        }

        if ($element instanceof DataObject) {
            if (!$element->getPublished()) {
                return null;
            }

            if (method_exists($element, 'getJson')) {
                return $element->getJson();
            }

            return [
                'id' => $element->getId(),
            ];
        }

        return [
            'id' => $element->getId(),
        ];
    }
}
