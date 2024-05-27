<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Pimcore\Twig\Extension\Templating\HeadMeta;
use Pimcore\Twig\Extension\Templating\HeadTitle;
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
                $dataSeo = [
                    'title' => $document->getTitle(),
                    'description' => $document->getDescription(),
                    'url' => $document->getUrl(),
                ];

                $this->renderSeo($dataSeo);
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
                "og:url" => "url",
                "twitter:title" => "title",
                "twitter:description" => "description",
            ];

            foreach ($metas as $key => $field) {
                if (isset($data[$field])) {
                    $this->headMeta->appendProperty($key, $data[$field]);
                }
            }

            if (isset($data['title'])) {
                $this->headTitle->set($data['title']);
            }

            if (isset($data['description'])) {
                $this->headMeta->setDescription($data['description']);
            }
        }

        foreach ($schemaData as $schemaTag) {
            $this->headMeta->addRaw($schemaTag);
        }
    }
}
