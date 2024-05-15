<?php

namespace Starfruit\BuilderBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\Exception\NotFoundException;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Seo\SeoScore;

class Seo extends AbstractModel
{
    const OBJECT_TYPE = 'object';
    const DOCUMENT_TYPE = 'document';

    public ?int $id = null;
    public ?string $elementType = null;
    public ?int $element = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $keyword = null;
    public ?string $language = null;

    public static function getOrCreate($element, $language = null): ?self
    {
        try {
            if (!$language) {
                $language = LanguageTool::getLocale();
            }

            $obj = self::getByElement($element, $language);

            if (!$obj) {
                $obj = new self;
                $obj->setElement($element->getId());
                $obj->setElementType($element->getType());
                $obj->setLanguage($language);

                $obj->save();
            }

            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Builder SEO can not get or create");
        }

        return null;
    }

    public static function getByElement($element, $language = null): ?self
    {
        try {
            if (!$language) {
                $language = LanguageTool::getLocale();
            }
            $id = $element->getId();

            $obj = new self;
            $obj->getDao()->getByElement($id, $language);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Builder SEO with id $id not found");
        }

        return null;
    }

    public function getScoring()
    {
        if ($this->elementType == self::OBJECT_TYPE) {
            return $this->getObjectScoring();
        }

        return null;
    }

    private function getObjectScoring()
    {
        $objectConfig = new ObjectConfig($this->element, $this->language);

        if (!$objectConfig->valid()) {
            return null;
        }

        $seoData = $objectConfig->getSeoData();
        $slug = $objectConfig->getSlug();

        $title = $this->title ?: $seoData['title'];
        $description = $this->getDescription() ?: $seoData['description'];
        $content = $seoData['content'];
        $keyword = $this->getKeyword();

        $seoScore = new SeoScore($title, $description, $content, $keyword, $slug);

        return $seoScore->scoring();
    }

    public function setElementType(?string $elementType): void
    {
        $this->elementType = $elementType;
    }

    public function getElementType(): ?string
    {
        return $this->elementType;
    }

    public function setElement(?int $element): void
    {
        $this->element = $element;
    }

    public function getElement(): ?int
    {
        return $this->element;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setKeyword(?string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}