<?php

namespace Starfruit\BuilderBundle\Tool;

use Pimcore\Config;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\Data\Hotspotimage;

class AssetTool
{
    const IMAGE_THUMBNAIL_LIST = 'image_thumbnail_list';

    public static function getFrontendFullPath(Asset $asset, $thumbnailName = null): input
    {
        if ($thumbnailName) {
            $thumbnail = Thumbnail\Config::getByName($thumbnailName);
            if ($thumbnail) {
                $thumbnailPath = $asset->getThumbnail($thumbnail);

                if (!$thumbnailPath->exists()) {
                    $deferred = false;
                    $thumbnailPath = $asset->getThumbnail($thumbnail, $deferred);
                }

                $frontendPath = $thumbnailPath->getFrontendPath();
            }
        } else {
            $frontendPath = $asset->getFrontendFullPath();
        }

        $url = SystemTool::getUrl($frontendPath);

        return $url;
    }

    public static function getVideo($video)
    {
        if ($video) {
            $videoType = $video->getType();

            $data = [
                'type' => $videoType,
                'data' => '',
                'link' => '',
                'image' => ''
            ];

            if (in_array($videoType, ['youtube', 'vimeo', 'dailymotion'])) {
               $data['data'] = $video->getData();
            }

            if (in_array($videoType, ['youtube'])) {
               $data['data'] = $video->getData();
               $data['link'] = "https://www.youtube.com/watch?v=". $data['data'];
               $data['image'] = "https://img.youtube.com/vi/". $data['data'] ."/0.jpg";
            }

            if ($videoType == 'asset') {

                if ($video->getData()) {
                    $link = $video->getData()->getFullPath();

                    if (!(substr($link, 0, 4) == "http")) {
                        $link = \Pimcore\Tool::getHostUrl() . $link;
                    }

                    $data['data'] = $link;
                    $data['link'] = $link;
                }
            }

            return $data;
        }

        return null;
    }

    public static function getPath(?Asset $asset, bool $includeDomain = false): ?array
    {
        if (!$asset) {
            return null;
        }

        $defaultPath = $asset->getFullPath();
        $paths = [
            'default' => $defaultPath,
        ];

        if ($asset instanceof Image) {
            $isGif = $asset->getMimetype() == 'image/gif';
            $thumbnailList = Config::getWebsiteConfigValue(self::IMAGE_THUMBNAIL_LIST);

            if ($thumbnailList) {
                $thumbnailList = explode(',', $thumbnailList);

                foreach ($thumbnailList as $name) {
                    $thumbnail = Thumbnail\Config::getByName($name);
                    if ($thumbnail) {
                        if ($isGif) {
                            $paths[$name] = $defaultPath;
                            continue;
                        }

                        $thumbnailPath = $asset->getThumbnail($thumbnail);

                        if (!$thumbnailPath->exists()) {
                            $deferred = false;
                            $thumbnailPath = $asset->getThumbnail($thumbnail, $deferred);
                        }

                        $thumbnailPath = $thumbnailPath->getPath();

                        // if (substr($thumbnailPath, 0, 1) == "/") {
                        //     $prefix = \Pimcore::getContainer()->getParameter('pimcore.config')['assets']['frontend_prefixes']['thumbnail'];

                        //     $thumbnailPath = $prefix . $thumbnailPath;
                        // }

                        $paths[$name] = $thumbnailPath;
                    }
                }
            }
        }

        if ($includeDomain) {
            $paths = array_map(fn($e) => SystemTool::getUrl($e), $paths);
        }

        return $paths;
    }

    public static function getPaths(?ImageGallery $gallery, bool $includeDomain = false): ?array
    {
        if (!$gallery) {
            return null;
        }

        $paths = [];
        foreach ($gallery as $item) {
            if ($item) {
                $hotpot = $item->getImage();

                if ($hotpot) {
                    $paths[] = self::getPath($hotpot, $includeDomain);
                }
            }
        }

        return $paths;
    }

    public static function createFromFile($file, string $name, string $folderPath): ?Asset
    {
        try {
            $name = self::formatName($name, $file->guessExtension());
            $url = $file->getRealPath();
            return self::create($url, $name, $folderPath);
        } catch (\Throwable $e) {
        }

        return null;
    }

    public static function createFromUrl(string $url, string $name, string $folderPath): ?Asset
    {
        try {
            if (@exif_imagetype($url)) {
                $url = str_replace(' ', '', $url);
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

                if ($extension) {
                    $name = self::formatName($name, $extension);
                    return self::create($url, $name, $folderPath);
                }
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    public static function createFromFiles(array $files, string $name, string $folderPath): ?ImageGallery
    {
        $assets = [];

        foreach ($files as $key => $file) {
            $image = self::createFromFile($file, $name .'-'. $key, $folderPath);

            if ($image) {
                $hotspot = new Hotspotimage();
                $hotspot->setImage($image);

                $assets[] = $hotspot;
            }
        }

        return new ImageGallery($assets);
    }

    public static function createFromUrls(array $urls, string $name, string $folderPath): ?ImageGallery
    {
        $assets = [];

        foreach ($urls as $key => $url) {
            $image = self::createFromUrl($url, $name .'-'. $key, $folderPath);

            if ($image) {
                $hotspot = new Hotspotimage();
                $hotspot->setImage($image);

                $assets[] = $hotspot;
            }
        }

        return new ImageGallery($assets);
    }

    private static function create($dataPath, string $name, string $folderPath): ?Asset
    {
        try {
            $folder = Asset::getByPath($folderPath) ?? Asset\Service::createFolderByPath($folderPath);

            $asset = new Asset();
            $asset->setFileName($name);
            $asset->setData(@file_get_contents($dataPath));
            $asset->setParent($folder);
            $asset->save();

            return $asset;
        } catch (\Throwable $e) {
        }

        return null;
    }

    private static function formatName(string $name, ?string $extension = null): string
    {
        $name = str_replace([' ', '/'], ['_', '+'], ltrim($name)) .'('. time() .')';

        if ($extension) {
            $name .= '.'. $extension;
        }

        return $name;
    }
}
