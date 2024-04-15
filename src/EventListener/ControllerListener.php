<?php

namespace Starfruit\BuilderBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $this->twig->addGlobal('builderEditablePath', "@StarfruitBuilder/editmode/editables");
        $this->twig->addGlobal('builderEditmodeLayout', "@StarfruitBuilder/editmode/editables/layout/layout.html.twig");
        $this->twig->addGlobal('builderEditmodeTitle', "@StarfruitBuilder/editmode/editables/layout/title.html.twig");
    }
}