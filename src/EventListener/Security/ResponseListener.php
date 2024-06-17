<?php

namespace Starfruit\BuilderBundle\EventListener\Security;

use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PreviewRequestTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Config;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use Starfruit\BuilderBundle\Config\SecurityConfig;

class ResponseListener
{
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;
    use PreviewRequestTrait;

    public function onKernelResponse(ResponseEvent $event): void
    {
        // if (!$this->isEnabled()) {
        //     return;
        // }

        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // only inject tag manager code on non-admin requests
        // if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
        //     return;
        // }

        // if (!Tool::useFrontendOutputFilters()) {
        //     return;
        // }

        // if ($this->isPreviewRequest($request)) {
        //     return;
        // }

        $response = $event->getResponse();

        // if (!$this->isHtmlResponse($response)) {
        //     return;
        // }

        $config = new SecurityConfig;

        // HSTS
        $customHSTS = $config->getCustomHSTS();
        $response->headers->set('Strict-Transport-Security', $customHSTS);

        // CSP
        $customCSP = $config->getCustomCSP();

        if ($customCSP) {
            $response->headers->set("Content-Security-Policy", $customCSP);
            $response->headers->set("X-Content-Security-Policy", $customCSP);
            $response->headers->set("X-WebKit-CSP", $customCSP);
        }

        // remove some headers
        $removeHeaders = $config->getRemoveHeaders();
        if (!empty($removeHeaders)) {
            foreach ($removeHeaders as $headerName) {
                $response->headers->remove($headerName);
                header_remove($headerName);
            }
        }
    }
}
