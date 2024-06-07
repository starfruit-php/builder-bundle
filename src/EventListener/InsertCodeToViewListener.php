<?php

namespace Starfruit\BuilderBundle\EventListener;

use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\PreviewRequestTrait;
use Pimcore\Bundle\CoreBundle\EventListener\Traits\ResponseInjectionTrait;
use Pimcore\Config;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Tool;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use Starfruit\BuilderBundle\Model\Option;
use Starfruit\BuilderBundle\Config\NotificationConfig;

class InsertCodeToViewListener
{
    use ResponseInjectionTrait;
    use PimcoreContextAwareTrait;
    use PreviewRequestTrait;

    public function onKernelResponse(ResponseEvent $event): void
    {
        // if (!$this->isEnabled()) {
        //     return;
        // }

        $request = $event->getRequest();
        if (!$event->isMainRequest()) {
            return;
        }

        // only inject tag manager code on non-admin requests
        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        if (!Tool::useFrontendOutputFilters()) {
            return;
        }

        if ($this->isPreviewRequest($request)) {
            return;
        }

        $response = $event->getResponse();
        if (!$this->isHtmlResponse($response)) {
            return;
        }

        $codeHead = Option::getCodeHead();
        $codeBody = Option::getCodeBody();

        $config = new NotificationConfig;
        $codeHead .= $config->getCodeHead();

        $content = $response->getContent();

        if (!empty($codeHead)) {
            $codeHead = '
                <!-- Builder CodeHead -->
                    '. $codeHead .'
                <!-- END Builder CodeHead -->';

            // search for the end <head> tag, and insert code before
            // this method is much faster than using simple_html_dom and uses less memory
            $headEndPosition = stripos($content, '</head>');
            if ($headEndPosition !== false) {
                $content = substr_replace($content, $codeHead . '</head>', $headEndPosition, 7);
            }
        }

        if (!empty($codeBody)) {
            $codeBody = '
                <!-- Builder CodeBody -->
                    '. $codeBody .'
                <!-- END Builder CodeBody -->';

            // insert code after the opening <body> tag
            $content = preg_replace('@<body(>|.*?[^?]>)@', "<body$1\n\n" . $codeBody, $content);
        }

        $response->setContent($content);
    }
}
