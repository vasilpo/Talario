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

namespace Tygh\Video;

use Tygh\Http;
use Tygh\Licensing\Features;
use Tygh\Video\UrlConstructors\IVideoUrlConstructor;

/**
 *  VideoManager.
 *
 * @package Tygh\Video
 */
class VideoManager
{
    /**
     * @var array<string, array{url_constructor_class: IVideoUrlConstructor}>
     */
    public $video_sources;

    /**
     * Video manager constructor.
     *
     * @param array<string, array{url_constructor_class: IVideoUrlConstructor}> $video_sources Available providers classes data.
     */
    public function __construct(array $video_sources)
    {
        $this->video_sources = $video_sources;
    }

    /**
     * Get source url constructor class.
     *
     * @param string $source Source name.
     *
     * @return IVideoUrlConstructor
     */
    public function getSourceConstructor(string $source)
    {
        return $this->video_sources[$source]['url_constructor_class'];
    }

    /**
     * Checks video is available.
     *
     * @param string               $video_url       Video URL.
     * @param IVideoUrlConstructor $url_constructor Url constructor class name of video provider.
     *
     * @return bool
     */
    public function checkVideoIsAvailable($video_url, IVideoUrlConstructor $url_constructor)
    {
        $result = false;
        $video_url = $url_constructor->buildVideoUrlForRequestData($video_url);

        if ($video_url) {
            /** @psalm-suppress InvalidScalarArgument */
            $video_data = Http::get($video_url, []);
            $result = !empty(json_decode($video_data));
        }

        return $result;
    }

    /**
     * Updates object video data.
     *
     * @param int                                                                                                              $object_id        Object id.
     * @param string                                                                                                           $object_type      Object type.
     * @param array{video_links?: array<int, array<string, int|null>>, video_data: array<int, array<string, int|string|null>>} $videos           Object videos data.
     * @param int                                                                                                              $started_position Started position for updated videos.
     *
     * @return int
     */
    public function updateVideos($object_id, $object_type, array $videos, $started_position = 0)
    {
        $skipped_videos_count = 0;

        if (!empty($videos['video_data'])) {
            $position = $started_position;

            foreach ($videos['video_data'] as $key => &$video) {
                if (empty($video['video_url_id']) || empty($video['source'])) {
                    unset($videos['video_data'][$key], $videos['video_links'][$key]);
                } else {
                    $videos['video_links'][$key]['object_id'] = $object_id;
                    $videos['video_links'][$key]['object_type'] = $object_type;
                    $videos['video_links'][$key]['position'] = $position;

                    if (empty($video['video_id'])) {
                        if ($this->isAvailableProvider((string) $video['source'])) {
                            $video_id = $this->saveNewVideoData($video);
                        }

                        if (empty($video_id)) {
                            unset($videos['video_data'][$key], $videos['video_links'][$key]);
                            ++$skipped_videos_count;

                            continue;
                        }
                        $videos['video_links'][$key]['video_id'] = $video_id;
                    }
                    ++$position;
                }
            }

            if (!empty($videos['video_links'])) {
                db_query('REPLACE INTO ?:videos_links ?m', $videos['video_links']);
            }
        }

        return $skipped_videos_count;
    }

    /**
     * Checks video provider is available.
     *
     * @param string $provider Video provider.
     *
     * @return bool
     */
    public function isAvailableProvider($provider)
    {
        return isset($this->video_sources[$provider]);
    }

    /**
     * Saves new video data.
     *
     * @param array<string, int|string|null> $video Video data.
     *
     * @return array<string, string>|bool
     */
    public function saveNewVideoData(array $video)
    {
        $source = (string) $video['source'];
        $video_url_constructor = $this->getSourceConstructor($source);
        $is_available_video = $this->checkVideoIsAvailable((string) $video['video_url_id'], $video_url_constructor);

        if ($is_available_video) {
            $url_id = $video_url_constructor->getUrlId((string) $video['video_url_id']);

            if ($url_id) {
                $video['video_url_id'] = $url_id;

                return db_query('INSERT INTO ?:videos ?e', $video);
            }
        }

        return false;
    }

    /**
     * Gets object video data from database.
     *
     * @param int    $object_id   Object id.
     * @param string $object_type Object type.
     *
     * @return array<int, array<string, int|string>>
     */
    public function getVideos($object_id, $object_type)
    {
        if ($object_type === 'product' && !fn_is_allowed(Features::PRODUCT_VIDEOS)) {
            return [];
        }

        return db_get_array(
            'SELECT pair_id, v.video_id, video_url_id, source, position FROM ?:videos as v'
            . ' INNER JOIN ?:videos_links as vl ON v.video_id = vl.video_id'
            . ' WHERE object_id = ?i'
            . ' AND vl.object_type = ?s ORDER BY position',
            $object_id,
            $object_type
        );
    }

    /**
     * Delete video pairs.
     *
     * @param int                   $object_id Object id.
     * @param array<string, string> $params    Additional params.
     *
     * @return void
     */
    public function deleteVideoPairs($object_id, array $params = [])
    {
        $condition = '';

        if (isset($params['object_type'])) {
            $condition .= db_quote(' AND object_type = ?s', $params['object_type']);
        }

        if (isset($params['video_ids'])) {
            $condition .= db_quote(' AND video_id IN (' . $params['video_ids'] . ')');
        }

        $pairs = db_get_fields('SELECT pair_id FROM ?:videos_links WHERE object_id = ?i ?p', $object_id, $condition);

        foreach ($pairs as $pair_id) {
            $this->deleteVideoPair($pair_id, $params['object_type']);
        }

        /**
         * Modifies deletion results of video pairs.
         *
         * @param int                       $object_id Object id.
         * @param array<string, int>        $pairs     Object pairs ids
         * @param array<string, string>     $params    Additional params.
         */
        fn_set_hook('delete_video_pairs', $object_id, $pairs, $params);
    }

    /**
     * Delete video pair.
     *
     * @param int    $pair_id     Pair id.
     * @param string $object_type Object type.
     *
     * @return void
     */
    public function deleteVideoPair($pair_id, $object_type)
    {
        $video = db_get_row(
            'SELECT vl.video_id, object_id, object_type, source FROM ?:videos_links as vl'
            . ' INNER JOIN ?:videos as v ON v.video_id = vl.video_id'
            . ' WHERE pair_id = ?i',
            $pair_id
        );

        if (!empty($video)) {
            $this->deleteVideo($video, $pair_id);
        }
    }

    /**
     * Delete video links and video data.
     *
     * @param array<string, int|string> $video   Video data.
     * @param int                       $pair_id Pair id.
     *
     * @return void
     */
    public function deleteVideo(array $video, $pair_id)
    {
        db_query('DELETE FROM ?:videos_links WHERE pair_id = ?i', $pair_id);
        $video_links_still_isset = db_get_field('SELECT COUNT(*) FROM ?:videos_links WHERE video_id = ?i', $video['video_id']);

        if ($video_links_still_isset !== 0) {
            return;
        }

        $this->deleteVideoData((int) $video['video_id']);
    }

    /**
     * Delete video data.
     *
     * @param int $video_id Video id.
     *
     * @return void
     */
    public function deleteVideoData($video_id)
    {
        db_query('DELETE FROM ?:videos WHERE video_id = ?i', $video_id);
    }

    /**
     * Get video source name by video URL.
     *
     * @param string $video_url Video URL.
     *
     * @return string|null
     */
    public function getSourceNameByVideoUrl($video_url)
    {
        $video_url = preg_replace('/^https?:\\/\\//', '', $video_url);
        $video_url = parse_url($video_url, PHP_URL_HOST) ?: $video_url;
        $source = preg_replace('/^(www\\.)?([^\\.]+)\\.[^\\.]+$/', '$2', $video_url);
        $source = ucfirst($source);

        if (empty($source)) {
            return null;
        }

        return $this->isAvailableProvider($source) ? $source : null;
    }
}
