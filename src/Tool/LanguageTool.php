<?php

namespace Starfruit\BuilderBundle\Tool;

class LanguageTool
{
    public static function getLocale()
    {
        return \Pimcore::getContainer()->get(\Pimcore\Localization\LocaleServiceInterface::class)->getLocale();
    }
}
