<?php

namespace Starfruit\BuilderBundle\Tool;

use Pimcore\Config;

class TextTool
{
    public static function getStringAsOneLine($string)
    {
        $string = str_replace("\r\n", ' ', $string);
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\r", ' ', $string);
        $string = str_replace("\t", '', $string);
        $string = preg_replace('#[ ]+#', ' ', $string);

        return $string;
    }

    public static function cutStringRespectingWhitespace($string, $length)
    {
        if ($length < strlen($string)) {
            $text = substr($string, 0, $length);
            if (false !== ($length = strrpos($text, ' '))) {
                $text = substr($text, 0, $length);
            }
            $string = $text.'...';
        }

        return $string;
    }

    public static function getPretty($text)
    {
        // to ASCII
        $text = trim(transliterator_transliterate('Any-Latin; Latin-ASCII; [^\u001F-\u007f] remove', $text));

        $search = ['?', '\'', '"', '/', '-', '+', '.', ',', ';', '(', ')', ' ', '&', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß', 'É', 'é', 'È', 'è', 'Ê', 'ê', 'E', 'e', 'Ë', 'ë',
                         'À', 'à', 'Á', 'á', 'Å', 'å', 'a', 'Â', 'â', 'Ã', 'ã', 'ª', 'Æ', 'æ', 'C', 'c', 'Ç', 'ç', 'C', 'c', 'Í', 'í', 'Ì', 'ì', 'Î', 'î', 'Ï', 'ï',
                         'Ó', 'ó', 'Ò', 'ò', 'Ô', 'ô', 'º', 'Õ', 'õ', 'Œ', 'O', 'o', 'Ø', 'ø', 'Ú', 'ú', 'Ù', 'ù', 'Û', 'û', 'U', 'u', 'U', 'u', 'Š', 'š', 'S', 's',
                         'Ž', 'ž', 'Z', 'z', 'Z', 'z', 'L', 'l', 'N', 'n', 'Ñ', 'ñ', '¡', '¿',  'Ÿ', 'ÿ', '_', ':' ];
        $replace = ['', '', '', '', '-', '', '', '-', '-', '', '', '-', '', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e',
                         'A', 'a', 'A', 'a', 'A', 'a', 'a', 'A', 'a', 'A', 'a', 'a', 'AE', 'ae', 'C', 'c', 'C', 'c', 'C', 'c', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
                         'O', 'o', 'O', 'o', 'O', 'o', 'o', 'O', 'o', 'OE', 'O', 'o', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'S', 's', 'S', 's',
                         'Z', 'z', 'Z', 'z', 'Z', 'z', 'L', 'l', 'N', 'n', 'N', 'n', '', '', 'Y', 'y', '-', '-' ];

        $text = preg_replace("/[\/\~`!@#$%^&*()_+=\$]/", "-", $text);
        $value = urlencode(str_replace($search, $replace, $text));
        $value = implode('-', array_filter(explode('-', $value)));

        return $value;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function toUrl($text)
    {
        // to ASCII
        $text = trim(transliterator_transliterate('Any-Latin; Latin-ASCII; [^\u001F-\u007f] remove', $text));

        $search = ['?', '\'', '"', '/', '-', '+', '.', ',', ';', '(', ')', ' ', '&', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß', 'É', 'é', 'È', 'è', 'Ê', 'ê', 'E', 'e', 'Ë', 'ë',
                         'À', 'à', 'Á', 'á', 'Å', 'å', 'a', 'Â', 'â', 'Ã', 'ã', 'ª', 'Æ', 'æ', 'C', 'c', 'Ç', 'ç', 'C', 'c', 'Í', 'í', 'Ì', 'ì', 'Î', 'î', 'Ï', 'ï',
                         'Ó', 'ó', 'Ò', 'ò', 'Ô', 'ô', 'º', 'Õ', 'õ', 'Œ', 'O', 'o', 'Ø', 'ø', 'Ú', 'ú', 'Ù', 'ù', 'Û', 'û', 'U', 'u', 'U', 'u', 'Š', 'š', 'S', 's',
                         'Ž', 'ž', 'Z', 'z', 'Z', 'z', 'L', 'l', 'N', 'n', 'Ñ', 'ñ', '¡', '¿',  'Ÿ', 'ÿ', '_', ':' ];
        $replace = ['', '', '', '', '-', '', '', '-', '-', '', '', '-', '', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e',
                         'A', 'a', 'A', 'a', 'A', 'a', 'a', 'A', 'a', 'A', 'a', 'a', 'AE', 'ae', 'C', 'c', 'C', 'c', 'C', 'c', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i',
                         'O', 'o', 'O', 'o', 'O', 'o', 'o', 'O', 'o', 'OE', 'O', 'o', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'S', 's', 'S', 's',
                         'Z', 'z', 'Z', 'z', 'Z', 'z', 'L', 'l', 'N', 'n', 'N', 'n', '', '', 'Y', 'y', '-', '-' ];

        $value = urlencode(str_replace($search, $replace, $text));

        return $value;
    }

    public static function formatWysiwyg($text)
    {
        if (empty($text)) {
            return $text;
        }

        $format = null;

        $domain = \Pimcore::getContainer()->getParameter('pimcore.config')['assets']['frontend_prefixes']['source'];

        $findSrcTags = explode('src="/', $text);

        if (count($findSrcTags) >= 2) {
            $format = $findSrcTags[0];
            foreach ($findSrcTags as $key => $srcTag) {
                if ($key != 0) {
                    $newSrcTag = 'src="' . $domain .'/'. $srcTag;
                    $format .= $newSrcTag;
                }
            }
        }

        $format = $format ?? $text;

        // replace: srcset -> data-srcset
        $format = str_replace('srcset', 'data-srcset', $format);

        // replate \n \n
        $format = str_replace(["<hr />\n"], ["<hr>"], $format);
        $format = str_replace(["<br />\n"], ["\n"], $format);
        $format = str_replace("</p>\n\n", "</p>\n", $format);
        $format = str_replace("</p>\n", "</p>", $format);
        $format = str_replace("<p>\n", "<p>", $format);

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.CollectErrors', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_parent', '_top']);
        // dd($format);
        // Có hay không cho phép thẻ iframe
        // $htmlSafeIframe = (bool) Config::getWebsiteConfig()->get('HTML_SafeIframe');
        // $config->set('HTML.SafeIframe', $htmlSafeIframe);

        // if ($htmlSafeIframe) {
        //     // regex các iframe uri được phép hiển thị
        //     $uriSafeIframeRegexp = Config::getWebsiteConfig()->get('URI_SafeIframeRegexp');
        //     $config->set('URI.SafeIframeRegexp', $uriSafeIframeRegexp);
        // }

        $purifier = new \HTMLPurifier($config);
        $format = $purifier->purify($format);

        return $format;
    }
}
