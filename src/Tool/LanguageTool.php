<?php

namespace Starfruit\BuilderBundle\Tool;

class LanguageTool
{
    public static function getLocale()
    {
        return \Pimcore::getContainer()->get(\Pimcore\Localization\LocaleServiceInterface::class)->getLocale();
    }

    public static function getList()
    {
        return \Pimcore\Tool::getValidLanguages();
    }

    public static function isValid($language)
    {
        return \Pimcore\Tool::isValidLanguage($language);
    }

    public static function getDefault()
    {
        return \Pimcore\Tool::getDefaultLanguage();
    }
}
