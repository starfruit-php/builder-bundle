<?php

namespace Starfruit\BuilderBundle\Config;

use Pimcore\Model\Element\ValidationException;
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
    private $updateAfterPublish;
    private $insertIdToSlug;

    private $sitemap;
    private $seoFields;

    // $object is id (int) or object
    public function __construct($object, $locale = null)
    {
        $object = $object instanceof DataObject ? $object : DataObject::getById($object);

        $this->valid = true;
        $this->locale = $locale ?: LanguageTool::getLocale();
        $this->seoFields = [];
        $this->object = $object;
        $this->className = $object?->getClassName();
        $this->setup();
    }

    public function valid()
    {
        return $this->valid;
    }

    public function sitemapAutoGenerate()
    {
        if (!empty($this->sitemap) && isset($this->sitemap['auto_regenerate'])) {
            return $this->sitemap['auto_regenerate'];
        }

        return false;
    }

    public static function getListClass()
    {
        return \Pimcore::getContainer()->getParameter(self::CONFIG_NAME);
    }

    public static function getListClassName()
    {
        $classConfig = self::getListClass();
        return array_column($classConfig, 'class_name');
    }

    public function setSlug(string $slug)
    {
        if (!$this->valid || !$this->updateWhileEmpty) {
            return;
        }

        $setSlugFunc = 'set' . ucfirst($this->fieldForSlug);

        if (!(method_exists($this->object, $setSlugFunc)))
        {
            throw new \Exception('Invalid method set slug. Checking config again!');
        }

        $slug = TextTool::str2slug($slug, $this->locale);
        $urlslug = new UrlSlug($slug);

        $this->object->$setSlugFunc([$urlslug], $this->locale);

        try {
            $this->object->save();

            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    // use in SlugListener
    public function setSlugs()
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
        $default = LanguageTool::getDefault();
        $defaultValue = $this->object->$getValueFunc($default);

        $languages = LanguageTool::getList();
        foreach ($languages as $language) {
            $currentSlug = $this->object->$getSlugFunc($language);

            if (empty($currentSlug) || $this->updateAfterPublish) {
                $value = $this->object->$getValueFunc($language);

                if ($value) {
                    $slug = strtolower(TextTool::getPretty($value));
                    $slug = $this->insertIdToSlug ? "/$language/$slug-$id" : "/$language/$slug";

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
        $classConfig = self::getListClass();
        $classNames = self::getListClassName();

        if (is_null($this->className) || !in_array($this->className, $classNames)) {
            $this->valid = false;
            return null;
        }

        return $classConfig[array_keys($classConfig)[array_search($this->className, $classNames)]];
    }

    private function setup()
    {
        $this->config = $this->getConfig();

        if ($this->valid) {
            $this->fieldForSlug = $this->config['field_for_slug'];
            $this->fieldCreateSlug = $this->config['field_create_slug'];
            $this->updateWhileEmpty = $this->config['update_while_empty'];
            $this->updateAfterPublish = $this->config['update_after_publish'];
            $this->insertIdToSlug = $this->config['insert_id_to_slug'];

            if (isset($this->config['sitemap'])) {
                $this->sitemap = $this->config['sitemap'];
            }

            if (isset($this->config['seo_fields'])) {
                $this->seoFields = $this->config['seo_fields'];
            }
        }
    }
}
