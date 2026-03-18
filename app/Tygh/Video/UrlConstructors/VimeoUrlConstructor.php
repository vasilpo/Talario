<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\Video\UrlConstructors;

/**
 *  UrlConstructor.
 *
 * @package Tygh\Video\UrlConstructors
 */
class VimeoUrlConstructor implements IVideoUrlConstructor
{
    const VIMEO_BASE_URL = 'https://vimeo.com/';
    const VIMEO_PREVIEW_HOST = 'https://vumbnail.com/';
    const VIMEO_BASE_IFRAME_URL = 'https://player.vimeo.com/video/';
    const VIMEO_BASE_JSON_URL = 'https://vimeo.com/api/oembed.json?url=';

    /**
     * Returns Video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getUrlId($video_url)
    {
        return self::getVimeoId($video_url);
    }

    /**
     * Returns Vimeo video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getVimeoId($video_url)
    {
        $url_parts = explode('/', $video_url);
        $prospect = end($url_parts);
        $prospect_and_params = preg_split('/(\?|\=|\&)/', $prospect);

        if ($prospect_and_params) {
            return $prospect_and_params[0];
        } else {
            return $prospect;
        }

        return null;
    }

    /**
     * Build video URL by video URL ID.
     *
     * @param string $video_id Video URL ID.
     *
     * @return array<string, string>
     */
    public function buildVideoUrlById($video_id)
    {
        return [
            'video_url' => self::VIMEO_BASE_URL . $video_id,
            'embed_video_url' => self::VIMEO_BASE_IFRAME_URL . $video_id . '?loop=1'
        ];
    }

    /**
     * Build video preview URL by video URL ID.
     *
     * @param string $video_id Video URL ID.
     *
     * @return string
     */
    public function buildPreviewUrlByVideoID($video_id)
    {
        return self::VIMEO_PREVIEW_HOST . $video_id . '.jpg';
    }

    /**
     * Build video preview URL by video URL.
     *
     * @param string $video_url Video URL ID.
     *
     * @return string|bool
     */
    public function buildPreviewUrlByVideoUrl($video_url)
    {
        $video_id = self::getVimeoId($video_url);

        return $video_id ? self::buildPreviewUrlByVideoID($video_id) : false;
    }

    /**
     * Build video URL for request video data.
     *
     * @param string $video_url Video URL ID.
     *
     * @return string|bool
     */
    public function buildVideoUrlForRequestData($video_url)
    {
        if (!str_contains($video_url, 'vimeo.com')) {
            return false;
        }

        return self::VIMEO_BASE_JSON_URL . $video_url;
    }
}
