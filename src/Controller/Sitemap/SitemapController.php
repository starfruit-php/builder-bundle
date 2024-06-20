<?php

namespace Starfruit\BuilderBundle\Controller\Sitemap;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides action to render sitemap files
 */
class SitemapController
{
    /**
     * list sitemaps
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $sitemapindex = $this->generator->fetch('root');

        if (!$sitemapindex) {
            throw new NotFoundHttpException('Not found');
        }

        $response = new Response($sitemapindex->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        // $response->setClientTtl($this->ttl);

        return $response;
    }

    /**
     * list urls of a section
     *
     * @param string $name
     *
     * @return Response
     */
    public function sectionAction(string $name): Response
    {
        $section = $this->generator->fetch($name);

        if (!$section) {
            throw new NotFoundHttpException('Not found');
        }

        $response = new Response($section->toXml());
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        // $response->setClientTtl($this->ttl);

        return $response;
    }
}
