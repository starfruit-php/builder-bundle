<?php

namespace Starfruit\BuilderBundle\Extension;

use Starfruit\BuilderBundle\Tool\Text;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Service;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Post;

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
        $translations = $this->documentService->getTranslations($document);
        $request = $this->requestStack->getCurrentRequest();
        $links = [];

        $languages = Tool::getValidLanguages();
        foreach ($languages as $language) {
            $check = '';
            $target = '/' . $language;
            //skip if root document for local is missing
            $checkDocument = Document::getByPath($target);
            if (!($checkDocument instanceof Document and !$checkDocument->getProperty('navigation_exclude'))) {
                continue;
            }

            if ($language != 'vi') {
                $document = Document::getByPath('/' . $language);
            }

            //kiểm tra có đa ngôn ngữ chưa
            if (isset($translations[$language])) {
                $localizedDocument = Document::getById($translations[$language]);
                if ($localizedDocument) {
                    $target = $localizedDocument->getFullPath();
                }
            }

            $links[$language] = [
                'link' => !empty($check) ? $check : $target,
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
        if (Tool::isValidLanguage($language)) {
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
