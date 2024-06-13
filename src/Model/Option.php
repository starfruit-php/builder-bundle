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

class Option extends AbstractModel
{
    const CODE_HEAD_NAME = 'code_head';
    const CODE_BODY_NAME = 'code_body';
    const MAIN_DOMAIN_NAME = 'main_domain';

    public ?int $id = null;
    public ?string $name = null;
    public ?string $content = null;

    public static function getById(int $id): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getById($id);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Option with id $id not found");
        }

        return null;
    }

    public static function getByName(string $name): ?self
    {
        try {
            $obj = new self;
            $obj->getDao()->getByName($name);
            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Option with name $name not found");
        }

        return null;
    }

    public static function getOrCreate(?string $name): ?self
    {
        try {
            $obj = self::getByName($name);

            if (!$obj) {
                $obj = new self;
                $obj->setName($name);

                $obj->save();
            }

            return $obj;
        }
        catch (NotFoundException $ex) {
            \Pimcore\Logger::warn("Builder Option can not get or create");
        }

        return null;
    }

    public static function setCodeHead(?string $content): void
    {
        $obj = self::getOrCreate(self::CODE_HEAD_NAME);
        $obj->setContent($content);
        $obj->save();
    }

    public static function getCodeHead(): ?string
    {
        $obj = self::getOrCreate(self::CODE_HEAD_NAME);
        return $obj->getContent();
    }

    public static function setCodeBody(?string $content): void
    {
        $obj = self::getOrCreate(self::CODE_BODY_NAME);
        $obj->setContent($content);
        $obj->save();
    }

    public static function getCodeBody(): ?string
    {
        $obj = self::getOrCreate(self::CODE_BODY_NAME);
        return $obj->getContent();
    }

    public static function getMainDomain(): ?string
    {
        $obj = self::getOrCreate(self::MAIN_DOMAIN_NAME);
        return $obj->getContent();
    }
    
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        return $this->content;
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