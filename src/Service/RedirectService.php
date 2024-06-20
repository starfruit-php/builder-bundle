<?php

namespace Starfruit\BuilderBundle\Service;

use Pimcore\Bundle\SeoBundle\Model\Redirect;

class RedirectService
{
    const PRIORITY = 99;

    public static function getId($seo)
    {
        $redirectId = $seo->getRedirectId();
        $redirect = $redirectId ? Redirect::getById($redirectId) : null;

        $needRedirect = $seo->getRedirectLink();

        if ($needRedirect) {
            if (!$redirect) {
                $redirect = self::create($seo->getRedirectType());
            }

            $redirect->setSource($seo->getSlug(false));
            $redirect->setTarget($seo->getDestinationUrl());
            $redirect->setStatusCode((int) $seo->getRedirectType());
            $redirect->setActive($needRedirect);
            $redirect->save();
        } else {
           if ($redirect) {
                $redirect->setActive($needRedirect);
                $redirect->save();
            }
        }

        return $redirect?->getId();
    }

    public static function create(): Redirect
    {
        $redirect = new Redirect();
        $redirect->setPriority(self::PRIORITY);
        $redirect->setType(Redirect::TYPE_PATH_QUERY);
        $redirect->save();

        return $redirect;
    }
}
