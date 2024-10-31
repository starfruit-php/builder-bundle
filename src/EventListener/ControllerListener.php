<?php

namespace Starfruit\BuilderBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;
use Starfruit\BuilderBundle\Tool\DocumentTool;

class ControllerListener {
    /**
     * @var \Environment $twig
     */
    private $twig;

    /**
     * @var RequestStack $requestStack
     */
    private RequestStack $requestStack;

    public function __construct(
        Environment $twig,
        RequestStack $requestStack
    ) {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $requestUri = $request->getRequestUri();
        if (substr($requestUri, 0, 7) == '/admin/' || substr($requestUri, 0, 4) == '/js/') {
            return;
        }

        $document = $request?->attributes?->get('contentDocument');
        if ($document) {
            $editaleData = DocumentTool::renderEditableData($document);
            $this->twig->addGlobal('builderEditables', $editaleData);
        }

        $this->twig->addGlobal('builderEditablePath', "@StarfruitBuilder/editmode/editables");
        $this->twig->addGlobal('builderEditmodeLayout', "@StarfruitBuilder/editmode/editables/layout/layout.html.twig");
        $this->twig->addGlobal('builderEditmodeTitle', "@StarfruitBuilder/editmode/editables/layout/title.html.twig");

        $this->twig->addGlobal('builderJsListingObject', "/bundles/starfruitbuilder/js/helper/listing-object.js");
    }
}