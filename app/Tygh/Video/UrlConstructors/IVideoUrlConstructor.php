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
 *  IVideoUrlConstructor.
 *
 * @package Tygh\Video\UrlConstructors
 */
interface IVideoUrlConstructor
{
    /**
     * Returns Video URL ID.
     *
     * @param string $video_url Video URL
     *
     * @return string|null
     */
    public function getUrlId($video_url);

    /**
     * Build video URL by video URL ID.
     *
     * @param string $video_id Video URL ID.
     *
     * @return array<string, string>
     */
    public function buildVideoUrlById($video_id);

    /**
     * Build video preview URL by video URL ID.
     *
     * @param string $video_id Video URL ID.
     *
     * @return string
     */
    public function buildPreviewUrlByVideoID($video_id);

    /**
     * Build video preview URL by video URL.
     *
     * @param string $video_url Video URL ID.
     *
     * @return string|bool
     */
    public function buildPreviewUrlByVideoUrl($video_url);

    /**
     * Build video URL for request video data.
     *
     * @param string $video_url Video URL ID.
     *
     * @return string|bool
     */
    public function buildVideoUrlForRequestData($video_url);
}
