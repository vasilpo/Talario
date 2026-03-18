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
class YoutubeUrlConstructor implements IVideoUrlConstructor
{
    const YOUTUBE_URL_KEY = 'v';
    const YOUTUBE_BASE_URL = 'https://www.youtube.com/watch?v=';
    const YOUTUBE_PREVIEW_HOST = 'https://img.youtube.com/vi/';
    const YOUTUBE_PREVIEW_RES = '/maxresdefault.jpg';
    const YOUTUBE_BASE_IFRAME_URL = 'https://www.youtube.com/embed/';
    const YOUTUBE_IFRAME_JSAPI = '?enablejsapi=1&loop=1&';
    const YOUTUBE_BASE_JSON_URL = 'https://youtube.com/oembed?url=';

    /**
     * Returns Video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getUrlId($video_url)
    {
        return self::getYoutubeId($video_url);
    }

    /**
     * Returns YouTube video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getYoutubeId($video_url)
    {
        $url = filter_var($video_url, FILTER_VALIDATE_URL) ? parse_url($video_url, PHP_URL_QUERY) : $video_url;
        parse_str($url, $my_array_of_params);

        if (array_key_exists(self::YOUTUBE_URL_KEY, $my_array_of_params)) {
            return $my_array_of_params[self::YOUTUBE_URL_KEY];
        }

        return self::getUrlIdAsLastParam($video_url);
    }

    /**
     * Returns YouTube video URL ID as last param.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    protected function getUrlIdAsLastParam($video_url)
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
            'video_url' => self::YOUTUBE_BASE_URL . $video_id,
            'embed_video_url' => self::YOUTUBE_BASE_IFRAME_URL . $video_id . self::YOUTUBE_IFRAME_JSAPI,
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
        return self::YOUTUBE_PREVIEW_HOST . $video_id . self::YOUTUBE_PREVIEW_RES;
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
        $video_id = self::getYoutubeId($video_url);

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
        if (!str_contains($video_url, 'youtube.com') && !str_contains($video_url, 'youtu.be')) {
            return false;
        }

        return self::YOUTUBE_BASE_JSON_URL . $video_url . '&format=json';
    }
}
