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

namespace Tygh\Addons\FullPageCache\Provider;

use DateTime;
use DateTimeZone;
use Tygh\Addons\FullPageCache\ProviderInterface;

class VarnishProvider implements ProviderInterface
{
    const HTTP_HEADER_NAME_CACHE_TAGS = 'X-Cache-Tags';
    const HTTP_HEADER_NAME_CACHE_TTL = 'X-Cache-TTL';
    const HTTP_HEADER_NAME_DO_ESI = 'X-Do-ESI';
    const HTTP_HEADER_NAME_LAST_MODIFIED = 'Last-Modified';
    const HTTP_SUCCESS_STATUS_CODE = 204;

    /**
     * @var string Varnish server IP
     */
    protected $server_ip = '127.0.0.1';

    /**
     * @var string Varnish server port
     */
    protected $server_port = '80';

    /**
     * VarnishProvider constructor
     *
     * @param string $varnish_server_ip   Varnish server IP
     * @param string $varnish_server_port Varnish server port
     */
    public function __construct($varnish_server_ip, $varnish_server_port)
    {
        $this->server_ip = trim($varnish_server_ip);
        $this->server_port = trim($varnish_server_port);
    }
    /**
     * @inheritDoc
     */
    public function invalidateCacheByTags(array $tags)
    {
        $tags = array_unique($tags);

        return $this->makeRequest('BAN', [
            sprintf('%s: %s', self::HTTP_HEADER_NAME_CACHE_TAGS, implode('|', $tags))
        ]);
    }

    /**
     * @inheritDoc
     */
    public function buildPageHeaders($ttl = 180, array $tags = [], $is_allow_esi = false)
    {
        $tags = array_unique($tags);

        $headers = [
            'Cache-Control: no-cache, must-revalidate',
            sprintf('%s: %s', self::HTTP_HEADER_NAME_CACHE_TAGS, implode(',', $tags)),
        ];

        if ($ttl) {
            $headers[] = sprintf('%s: %ss', self::HTTP_HEADER_NAME_CACHE_TTL, $ttl);
        }

        if ($is_allow_esi) {
            $headers[] = sprintf('%s: %s', self::HTTP_HEADER_NAME_DO_ESI, (int) $is_allow_esi);
        }

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone('GMT'));

        $headers[] = sprintf('%s: %s', self::HTTP_HEADER_NAME_LAST_MODIFIED, $date->format('D, d M Y H:i:s \G\M\T'));

        return $headers;
    }

    /**
     * @inheritDoc
     */
    public function isEsiRequest()
    {
        return isset($_SERVER['HTTP_X_VARNISH_ESI']) && $_SERVER['HTTP_X_VARNISH_ESI'] === 'true';
    }

    /**
     * @inheritDoc
     */
    public function renderESIBlock($url, $block_content, $debug = false)
    {
        return sprintf(
            '%s<esi:include src="%s"/><esi:remove>%s</esi:remove>',
            $debug ? "<!-- ESI render URL: {$url} -->" : '',
            $url,
            $block_content
        );
    }

    /**
     * @param string $method  Request method
     * @param array  $headers Request headers
     *
     * @return bool
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    protected function makeRequest($method, array $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->server_ip);
        curl_setopt($ch, CURLOPT_PORT, $this->server_port);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        curl_exec($ch);
        curl_errno($ch);
        curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($http_code === self::HTTP_SUCCESS_STATUS_CODE) {
            return true;
        }

        error_log(sprintf(
            '[%s]: Full Page Cache Add-on: Unable to make request on varnish server %s:%s; file %s; line: %s',
            date('d-m-Y H:i:s'),
            $this->server_ip,
            $this->server_port,
            __FILE__,
            __LINE__
        ));

        return false;
    }
}
