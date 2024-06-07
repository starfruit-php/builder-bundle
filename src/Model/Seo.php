<?php

namespace Starfruit\BuilderBundle\Model;

use Pimcore\Model\AbstractModel;
use Pimcore\Model\Document;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Exception\NotFoundException;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Seo\SeoScore;
use Starfruit\BuilderBundle\Seo\SeoSchema;
use Starfruit\BuilderBundle\Config\SeoConfig;
use Starfruit\BuilderBundle\Tool\AssetTool;

class Seo extends AbstractModel
{
    const OBJECT_TYPE = 'object';
    const DOCUMENT_TYPE = 'document';

    const FULL_FIELDS = ['indexing', 'nofollow', 'canonicalUrl', 'redirectLink', 'redirectType', 'destinationUrl', 'schemaBlock'];

    public ?int $id = null;
    public ?string $elementType = null;
    public ?int $element = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $keyword = null;
    public ?string $language = null;
    public ?bool $indexing = false;
    public ?bool $nofollow = false;
    public ?string $canonicalUrl = null;
    public ?bool $redirectLink = false;
    public ?string $redirectType = null;
    public ?string $destinationUrl = null;
    public ?string $schemaBlock = null;
    public ?string $image = null;
    public ?int $imageAsset = null;

    public static function getById(int $id): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Seo with id $id not found");
        }

        return null;
    }

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
                $obj->setElementType($element->getType() == self::OBJECT_TYPE ? self::OBJECT_TYPE : self::DOCUMENT_TYPE);
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

    public static function getByKeyword($keyword, $language = null)
    {
        try {
            if (!$language) {
                $language = LanguageTool::getLocale();
            }

            $list = new \Starfruit\BuilderBundle\Model\Seo\Listing();
            $list->setCondition("keyword = ?", [$keyword]);

            return $list->getData();
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Builder SEO with keyword $keyword not found");
        }

        return null;
    }

    public function getScoring($getFullFields = false)
    {
        if ($this->elementType == self::OBJECT_TYPE) {
            return $this->getObjectScoring($getFullFields);
        }

        return null;
    }

    public function getSchemaData(): ?array
    {
        return SeoSchema::getSchemaData($this->schemaBlock);
    }

    public function getSeoData(): ?array
    {
        if ($this->elementType == self::OBJECT_TYPE) {
            return $this->getObjectSeoData();
        }

        if ($this->elementType == self::DOCUMENT_TYPE) {
            return $this->getDocumentSeoData();
        }

        return null;
    }

    private function renderImage()
    {
        $image = $this->image;
        if ($this->imageAsset) {
            $asset = Image::getById($this->imageAsset);

            if ($asset) {
                $seoConfig = new SeoConfig;
                $thumbnail = $seoConfig->getImageThumbnail();
                $image = AssetTool::getFrontendFullPath($asset, $thumbnail);
            }
        }

        return $image;
    }

    private function getObjectScoring($getFullFields = false): ?array
    {
        $objectConfig = new ObjectConfig($this->element, $this->language);

        if (!$objectConfig->valid()) {
            return null;
        }

        $seoData = $objectConfig->getSeoData();
        $slug = $objectConfig->getSlug();

        $title = $this->title ?: $seoData['title'];
        $description = $this->description ?: $seoData['description'];
        $content = $seoData['content'];
        $keyword = $this->getKeyword();

        $seoScore = new SeoScore($title, $description, $content, $keyword, $slug);
        $scoring = $seoScore->scoring();

        $fullFields = [];
        if ($getFullFields) {
            foreach (self::FULL_FIELDS as $field) {
                $fullFields[$field] = $this->{$field};
            }
        }
        $scoring['fullFields'] = $fullFields;

        return $scoring;
    }

    private function getDocumentSeoData(): ?array
    {
        $document = Document::getById($this->element);
        $title = $this->title ?: $document->getTitle();
        $description = $this->description ?: $document->getDescription();
        $slug = $document->getUrl();
        $image = $this->renderImage();

        return compact('title', 'description', 'image', 'slug');
    }

    private function getObjectSeoData(): ?array
    {
        $objectConfig = new ObjectConfig($this->element, $this->language);

        if (!$objectConfig->valid()) {
            return null;
        }

        $seoData = $objectConfig->getSeoData();

        if (empty($seoData)) {
            return [];
        }

        $slug = $objectConfig->getSlug();

        $title = $this->title ?: $seoData['title'];
        $description = $this->description ?: $seoData['description'];
        $image = $this->renderImage() ?: $seoData['image'];

        return compact('title', 'description', 'image', 'slug');
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

    public function setIndexing(?bool $indexing): void
    {
        $this->indexing = $indexing;
    }

    public function getIndexing(): ?bool
    {
        return $this->indexing;
    }

    public function setNofollow(?bool $nofollow): void
    {
        $this->nofollow = $nofollow;
    }

    public function getNofollow(): ?bool
    {
        return $this->nofollow;
    }

    public function setRedirectLink(?bool $redirectLink): void
    {
        $this->redirectLink = $redirectLink;
    }

    public function getRedirectLink(): ?bool
    {
        return $this->redirectLink;
    }

    public function setCanonicalUrl(?string $canonicalUrl): void
    {
        $this->canonicalUrl = $canonicalUrl;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonicalUrl;
    }

    public function setRedirectType(?string $redirectType): void
    {
        $this->redirectType = $redirectType;
    }

    public function getRedirectType(): ?string
    {
        return $this->redirectType;
    }

    public function setDestinationUrl(?string $destinationUrl): void
    {
        $this->destinationUrl = $destinationUrl;
    }

    public function getDestinationUrl(): ?string
    {
        return $this->destinationUrl;
    }

    public function setSchemaBlock(?string $schemaBlock): void
    {
        $this->schemaBlock = $schemaBlock;
    }

    public function getSchemaBlock(): ?string
    {
        return $this->schemaBlock;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImageAsset(?int $imageAsset): void
    {
        $this->imageAsset = $imageAsset;
    }

    public function getImageAsset(): ?int
    {
        return $this->imageAsset;
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