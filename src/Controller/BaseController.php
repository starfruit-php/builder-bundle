<?php

namespace Starfruit\BuilderBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Translation\Translator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Starfruit\BuilderBundle\Validator\Validator;

class BaseController extends FrontendController
{
    protected $request;
    protected $translator;
    protected $validator;
    protected $paginator;

    public function __construct(
        RequestStack $requestStack, 
        Translator $translator, 
        ValidatorInterface $validator,
        PaginatorInterface $paginator
        )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;

        if ($this->request->headers->has('locale')) {
            $this->translator->setLocale($this->request->headers->get('locale'));
        }

        $this->validator = new Validator($validator, $this->translator);
        $this->paginator = $paginator;
    }

    /**
     * Paginator helper.
     */
    public function paginator($listing, $page, $limit)
    {
        $pagination = $this->paginator->paginate(
            $listing,
            $page,
            $limit,
        );

        return $pagination;
    }

    /**
     * Assign language to request.
     */
    public function setLocaleRequest()
    {
        if ($this->request->get('_locale')) {
            $this->request->setLocale($this->request->get('_locale'));
        }
    }

    public function validateRequest(array $condition)
    {
        return $this->validator->validate($condition, $this->request);
    }
}
