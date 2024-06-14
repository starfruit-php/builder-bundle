<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;
use Pimcore\Twig\Extension\Templating\Placeholder;
use Pimcore\Model\Document;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\UrlSlug;

use Starfruit\BuilderBundle\Tool\LanguageTool;
use Starfruit\BuilderBundle\Model\Seo;

class SeoExtension extends AbstractExtension
{
    protected $locale;

    public function __construct(
        protected HeadMeta $headMeta,
        protected HeadTitle $headTitle,
        protected Placeholder $headLink,
        protected RequestStack $requestStack
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('builder_seo', [$this, 'setupSeoData']),
        ];
    }

    public function setupSeoData()
    {
        $this->locale = LanguageTool::getLocale();
        $mainRequest =  $this->requestStack?->getMainRequest();
        $urlSlug = $mainRequest?->attributes?->get('urlSlug');

        if ($urlSlug && $urlSlug instanceof UrlSlug) {
            $objectId = $urlSlug->getObjectId();
            $object = DataObject::getById($objectId);

            if ($object && $object->getPublished()) {
                $seo = Seo::getOrCreate($object, $this->locale);
                $this->renderSeo($seo->getSeoData(), $seo->getSchemaData());
            }
        } else {
            $document = $mainRequest?->attributes?->get('contentDocument');

            if ($document instanceof Document\Link) {
                $document = $document->getElement();
            }

            if ($document instanceof Document\Page) {
                $seo = Seo::getOrCreate($document, $this->locale);
                $this->renderSeo($seo->getSeoData(), $seo->getSchemaData());
            }
        }
    }

    private function renderSeo($data, $schemaData = [])
    {
        $defaultMetas = [
            "og:locale" => $this->locale,
            "og:type" => "website",
        ];

        foreach ($defaultMetas as $key => $value) {
            $this->headMeta->appendProperty($key, $value);
        }

        if (!empty($data)) {
            $metas = [
                "og:title" => "title",
                "og:description" => "description",
                "og:image" => "image",
                "og:image:alt" => "title",
                "og:url" => "slug",
                "twitter:title" => "title",
                "twitter:description" => "description",
            ];

            foreach ($metas as $key => $field) {
                if (isset($data[$field]) && !empty($data[$field])) {
                    $this->headMeta->appendProperty($key, $data[$field]);
                }
            }

            $field = 'title';
            if (isset($data[$field]) && !empty($data[$field])) {
                $this->headTitle->set($data[$field]);
            }

            $field = 'description';
            if (isset($data[$field]) && !empty($data[$field])) {
                $this->headMeta->setDescription($data[$field]);
            }

            $indexFollow = [
                $data['index'] ? 'index' : 'noindex',
                $data['nofollow'] ? 'nofollow' : 'follow',
            ];
            $indexFollowContent = implode(',', $indexFollow);
            $this->headMeta->addRaw('<meta name="robots" content="' . $indexFollowContent . '">');
            $this->headMeta->addRaw('<meta name="googlebot" content="' . $indexFollowContent . '">');

            $canonicalUrl = $data['canonicalUrl'] ?: $data['slug'];
            $this->headMeta->addRaw('<link rel="canonical" href="' . $canonicalUrl . '" />');
        }

        foreach ($schemaData as $schemaTag) {
            if (!empty($schemaTag)) {
                $this->headMeta->addRaw($schemaTag);
            }
        }
    }
}
