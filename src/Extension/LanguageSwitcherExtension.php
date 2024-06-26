<?php

namespace Starfruit\BuilderBundle\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Pimcore\Tool;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Service;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Starfruit\BuilderBundle\Config\ObjectConfig;
use Starfruit\BuilderBundle\Tool\LanguageTool;

class LanguageSwitcherExtension extends AbstractExtension
{
    /**
     * @var Service|Service\Dao
     */
    private $documentService;

    /**
     * @var RequestStack $requestStack
     */
    private RequestStack $requestStack;

    public function __construct(
        Service $documentService,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->documentService = $documentService;
        $this->requestStack = $requestStack;
    }

    public function getLocalizedLinks(Document $document)
    {
        $languages = LanguageTool::getList();

        $mainRequest =  $this->requestStack?->getMainRequest();
        $document = $mainRequest?->attributes?->get('contentDocument') ?: $document;

        $urlSlug = $mainRequest?->attributes?->get('urlSlug');

        $config = null;
        if ($urlSlug instanceof UrlSlug) {
            $objectId = $urlSlug->getObjectId();
            $object = DataObject::getById($objectId);

            if ($object && $object->getPublished()) {
                $config = new ObjectConfig($object);
                $config = $config->valid() ? $config : null;
            }
        }

        $translations = $this->documentService->getTranslations($document);
        $request = $this->requestStack->getCurrentRequest();
        $links = [];

        foreach ($languages as $language) {
            $languageRoot = '/' . $language;
            //skip if root document for local is missing
            $languageDocument = Document::getByPath($languageRoot);
            if (!($languageDocument instanceof Document && $languageDocument->getPublished())) {
                continue;
            }

            $target = null;

            // exist value for slug -> get it
            if ($config) {
                $target = $config->getSlug(['locale' => $language]);

                if (!$target) {
                    continue;
                }
            }

            if (!$target) {
                $target = $languageRoot;
                if (isset($translations[$language])) {
                    $localizedDocument = Document::getById($translations[$language]);
                    if ($localizedDocument) {
                        $target = $localizedDocument->getFullPath();
                    }
                }
            }

            $links[$language] = [
                'link' => $target,
                'language' => $language,
                'text' => \Locale::getDisplayLanguage($language),
                'image' => self::getLanguageFlagFile($language),
            ];
        }

        return $links;
    }

    /**
     * @param string $language
     * @return string
     */
    public function getLanguageFlag($language)
    {
        $flag = '';
        if (LanguageTool::isValid($language)) {
            $flag = self::getLanguageFlagFile($language);
        }
        $flag = preg_replace('@^' . preg_quote(PIMCORE_WEB_ROOT, '@') . '@', '', $flag);

        return $flag;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('builder_languages', [$this, 'getLocalizedLinks']),
            new TwigFunction('builder_language_flag', [$this, 'getLanguageFlag'])
        ];
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public static function getLanguageFlagFile($language)
    {
        $basePath = '/bundles/pimcoreadmin/img/flags';
        $code = strtolower($language);
        $iconPath = $basePath . '/countries/_unknown.svg';

        $languageCountryMapping = [
            'aa' => 'er', 'af' => 'za', 'am' => 'et', 'as' => 'in', 'ast' => 'es', 'asa' => 'tz',
            'az' => 'az', 'bas' => 'cm', 'eu' => 'es', 'be' => 'by', 'bem' => 'zm', 'bez' => 'tz', 'bg' => 'bg',
            'bm' => 'ml', 'bn' => 'bd', 'br' => 'fr', 'brx' => 'in', 'bs' => 'ba', 'cs' => 'cz', 'da' => 'dk',
            'de' => 'de', 'dz' => 'bt', 'el' => 'gr', 'en' => 'gb', 'es' => 'es', 'et' => 'ee', 'fi' => 'fi',
            'fo' => 'fo', 'fr' => 'fr', 'ga' => 'ie', 'gv' => 'im', 'he' => 'il', 'hi' => 'in', 'hr' => 'hr',
            'hu' => 'hu', 'hy' => 'am', 'id' => 'id', 'ig' => 'ng', 'is' => 'is', 'it' => 'it', 'ja' => 'jp',
            'ka' => 'ge', 'os' => 'ge', 'kea' => 'cv', 'kk' => 'kz', 'kl' => 'gl', 'km' => 'kh', 'ko' => 'kr',
            'lg' => 'ug', 'lo' => 'la', 'lt' => 'lt', 'mg' => 'mg', 'mk' => 'mk', 'mn' => 'mn', 'ms' => 'my',
            'mt' => 'mt', 'my' => 'mm', 'nb' => 'no', 'ne' => 'np', 'nl' => 'nl', 'nn' => 'no', 'pl' => 'pl',
            'pt' => 'pt', 'ro' => 'ro', 'ru' => 'ru', 'sg' => 'cf', 'sk' => 'sk', 'sl' => 'si', 'sq' => 'al',
            'sr' => 'rs', 'sv' => 'se', 'swc' => 'cd', 'th' => 'th', 'to' => 'to', 'tr' => 'tr', 'tzm' => 'ma',
            'uk' => 'ua', 'uz' => 'uz', 'vi' => 'vn', 'zh' => 'cn', 'gd' => 'gb-sct', 'gd-gb' => 'gb-sct',
            'cy' => 'gb-wls', 'cy-gb' => 'gb-wls', 'fy' => 'nl', 'xh' => 'za', 'yo' => 'bj', 'zu' => 'za',
            'ta' => 'lk', 'te' => 'in', 'ss' => 'za', 'sw' => 'ke', 'so' => 'so', 'si' => 'lk', 'ii' => 'cn',
            'zh-hans' => 'cn', 'sn' => 'zw', 'rm' => 'ch', 'pa' => 'in', 'fa' => 'ir', 'lv' => 'lv', 'gl' => 'es',
            'fil' => 'ph',
        ];

        if (array_key_exists($code, $languageCountryMapping)) {
            $iconPath = $basePath . '/countries/' . $languageCountryMapping[$code] . '.svg';
        }

        return $iconPath;
    }
}
