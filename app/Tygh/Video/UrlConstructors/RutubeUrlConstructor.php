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
class RutubeUrlConstructor implements IVideoUrlConstructor
{
    const RUTUBE_BASE_URL = 'https://rutube.ru/';
    const RUTUBE_PREVIEW_PARAM = '/thumbnail/?redirect=1';
    const RUTUBE_BASE_IFRAME_URL = 'https://rutube.ru/play/embed/';
    const RUTUBE_BASE_JSON_URL = 'https://rutube.ru/api/video/';

    /**
     * Returns Video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getUrlId($video_url)
    {
        return self::getRutubeId($video_url);
    }

    /**
     * Returns Rutube video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getRutubeId($video_url)
    {
        if (preg_match('#/([^/]+)/$#', $video_url, $matches)) {
            return $matches[1];
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
            'video_url' => self::RUTUBE_BASE_URL . $video_id . '/',
            'embed_video_url' => self::RUTUBE_BASE_IFRAME_URL . $video_id . '/',
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
        return self::RUTUBE_BASE_JSON_URL . $video_id . self::RUTUBE_PREVIEW_PARAM;
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
        $video_id = self::getRutubeId($video_url);

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
        $video_id = self::getRutubeId($video_url);

        if (!str_contains($video_url, 'rutube.ru') || empty($video_id)) {
            return false;
        }

        return self::RUTUBE_BASE_JSON_URL . $video_id . '/';
    }
}
