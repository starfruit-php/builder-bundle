<?php

namespace Starfruit\BuilderBundle\Config;

use Pimcore\Tool;
use Pimcore\Model\DataObject;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Starfruit\BuilderBundle\Tool\TextTool;
use Starfruit\BuilderBundle\Tool\AssetTool;
use Starfruit\BuilderBundle\Tool\LanguageTool;

class ObjectConfig
{
    const CONFIG_NAME = 'starfruit_builder.object';

    private $valid;
    private $locale;
    private $className;
    private $object;
    private $config;

    private $fieldForSlug;
    private $fieldCreateSlug;
    private $updateWhileEmpty;

    private $seoFields;

    // $object is id (int) or object
    public function __construct($object, $locale = null)
    {
        $object = $object instanceof DataObject ? $object : DataObject::getById($object);

        $this->valid = true;
        $this->locale = $locale ?: LanguageTool::getLocale();
        $this->seoFields = [];
        $this->object = $object;
        $this->className = $object->getClassName();
        $this->setup();
    }

    public function valid()
    {
        return $this->valid;
    }

    public function setSlug()
    {
        if (!$this->valid || !$this->updateWhileEmpty) {
            return;
        }

        $setSlugFunc = 'set' . ucfirst($this->fieldForSlug);
        $getSlugFunc = 'get' . ucfirst($this->fieldForSlug);
        $getValueFunc = 'get' . ucfirst($this->fieldCreateSlug);

        if (!(method_exists($this->object, $setSlugFunc)
            && method_exists($this->object, $getSlugFunc)
            && method_exists($this->object, $getValueFunc)))
        {
            throw new \Exception('Invalid method set slug. Checking config again!');
        }

        $id = $this->object->getId();
        $default = Tool::getDefaultLanguage();
        $defaultValue = $this->object->$getValueFunc($default);

        $languages = Tool::getValidLanguages();
        foreach ($languages as $language) {
            $currentSlug = $this->object->$getSlugFunc($language);

            if (empty($currentSlug)) {
                $value = $this->object->$getValueFunc($language) ?: $defaultValue;

                if ($value) {
                    $slug = strtolower(TextTool::getPretty($value));
                    $slug = "/$language/$slug-$id";

                    $urlslug = new UrlSlug($slug);
                    $this->object->$setSlugFunc([$urlslug], $language);
                }
            }
        }
    }

    public function getSlug($params = [])
    {
        if (!$this->valid) {
            throw new \Exception('Invalid Class name. Checking config again!');
        }

        $function = 'get' . ucfirst($this->fieldForSlug);

        if (!method_exists($this->object, $function)) {
            throw new \Exception('Invalid method get slug. Checking config again!');
        }

        $locale = isset($params['locale']) ? $params['locale'] : $this->locale;
        $slugs = $locale ? $this->object->$function($locale) : $this->object->$function();

        if (!is_array($slugs) || empty($slugs)) {
            return '';
        }

        $slug = reset($slugs);
        return $slug instanceof UrlSlug ? $slug->getSlug() : '';
    }

    public function getSeoData()
    {
        $seoData = [];
        if (!empty($this->seoFields)) {
            foreach ($this->seoFields as $key => $field) {
                $function = 'get' . ucfirst($field);

                if (!method_exists($this->object, $function)) {
                    throw new \Exception('Invalid seo field: '. $field .'. Checking config again!');
                }

                $value = $this->object->$function($this->locale);

                if (is_string($value)) {
                    $value = TextTool::getStringAsOneLine($value);
                }

                if ($value instanceof Image) {
                    $seoConfig = new SeoConfig;
                    $thumbnail = $seoConfig->getImageThumbnail();
                    $value = AssetTool::getFrontendFullPath($value, $thumbnail);
                }

                $seoData[$key] = (string) $value;
            }
        }

        return $seoData;
    }

    private function getConfig()
    {
        $config = \Pimcore::getContainer()->getParameter(self::CONFIG_NAME);
        $classNames = array_column($config, 'class_name');

        if (!in_array($this->className, $classNames)) {
            $this->valid = false;
            return null;
        }

        return $config[array_keys($config)[array_search($this->className, $classNames)]];
    }

    private function setup()
    {
        $this->config = $this->getConfig();

        if ($this->valid) {
            $this->fieldForSlug = $this->config['field_for_slug'];
            $this->fieldCreateSlug = $this->config['field_create_slug'];
            $this->updateWhileEmpty = $this->config['update_while_empty'];

            if (isset($this->config['seo_fields'])) {
                $this->seoFields = $this->config['seo_fields'];
            }
        }
    }
}
