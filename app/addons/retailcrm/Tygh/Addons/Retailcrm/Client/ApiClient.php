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

namespace Tygh\Addons\Retailcrm\Client;

use RetailCrm\ApiClient as BaseApiClient;
use Tygh\Addons\Retailcrm\Client\Http\Client;
use Tygh\Addons\Retailcrm\Response\ApiResponse;

/**
 * The class wrapper for base RetailCrm Client.
 * Replaces base http client.
 *
 * @package Tygh\Addons\Retailcrm\Client
 */
class ApiClient extends BaseApiClient
{
    /**
     * @inheritdoc
     */
    public function __construct($url, $api_key, $site = null)
    {
        if ('/' !== $url[strlen($url) - 1]) {
            $url .= '/';
        }

        $url = $url . 'api/' . self::VERSION;

        $this->client = new Client($url, array('apiKey' => $api_key));
        $this->siteCode = $site;
    }
}