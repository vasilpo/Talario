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

namespace Tygh\Addons\RusHybridAuth\Providers;

use Hybridauth\Adapter\OAuth2;
use Hybridauth\Exception\UnexpectedValueException;
use Hybridauth\User;
use Hybridauth\Data;

/**
 * @phpcs:disable
 */
class Odnoklassniki extends OAuth2
{
    /**
     * Default Base URL to provider API
     */
    protected $apiBaseUrl = 'https://api.ok.ru/fb.do';

    /**
     * Default Authorization Endpoint
     */
    protected $authorizeUrl = 'https://connect.ok.ru/oauth/authorize';

    /**
     * Default Access Token Endpoint
     */
    protected $accessTokenUrl = 'https://api.ok.ru/oauth/token.do';

    /**
     * Rewrite method to fix bug with slash on the end of API base URL.
     */
    public function apiRequest($url, $method = 'GET', $parameters = [], $headers = [], $multipart = false)
    {
        // refresh tokens if needed
        $this->maintainToken();
        if ($this->hasAccessTokenExpired() === true) {
            // Add params for successful access token refresh
            $this->tokenRefreshParameters['client_id'] = $this->clientId;
            $this->tokenRefreshParameters['client_secret'] = $this->clientSecret;

            $this->refreshAccessToken();
        }

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            // Redundant slash removed
            $url = rtrim($this->apiBaseUrl, '/') . ltrim($url, '/');
        }

        $parameters = array_replace($this->apiRequestParameters, (array)$parameters);
        $headers = array_replace($this->apiRequestHeaders, (array)$headers);

        $response = $this->httpClient->request(
            $url,
            $method,     // HTTP Request Method. Defaults to GET.
            $parameters, // Request Parameters
            $headers,    // Request Headers
            $multipart   // Is request multipart
        );

        $this->validateApiResponse('Signed API request to ' . $url . ' has returned an error');

        $response = (new Data\Parser())->parse($response);

        return $response;
    }

    /**
     * Load the user profile.
     */
    function getUserProfile()
    {
        $fields = 'UID,LOCALE,FIRST_NAME,LAST_NAME,NAME,GENDER,AGE,BIRTHDAY,HAS_EMAIL,EMAIL,CURRENT_STATUS,CURRENT_STATUS_ID,CURRENT_STATUS_DATE,ONLINE,PHOTO_ID,PIC190X190,PIC640X480,LOCATION';

        $access_token_data = $this->getAccessToken();
        // Signature
        $sig = md5('application_key=' . $this->config->get('keys')['key'] . 'fields=' . $fields . 'method=users.getCurrentUser' . md5($access_token_data['access_token'] . $this->clientSecret));
        // Signed request
        $response = $this->apiRequest('?application_key=' . $this->config->get('keys')['key'] . '&fields=' . $fields . '&method=users.getCurrentUser&sig=' . $sig . '&access_token=' . $access_token_data['access_token']);

        if (!isset($response->uid)) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        $userProfile = new User\Profile();

        $userProfile->identifier = (property_exists($response,'uid')) ? $response->uid : '';
        $userProfile->firstName = (property_exists($response,'first_name')) ? $response->first_name : '';
        $userProfile->lastName = (property_exists($response,'last_name')) ? $response->last_name : '';
        $userProfile->displayName = (property_exists($response,'name')) ? $response->name : '';
        $userProfile->photoURL = (property_exists($response,'pic640x480')) ? $response->pic640x480 : '';
        $userProfile->profileURL = (property_exists($response,'link')) ? $response->link : '';
        $userProfile->gender = (property_exists($response,'gender')) ? $response->gender : '';
        $userProfile->email = (property_exists($response,'email')) ? $response->email : '';
        $userProfile->emailVerified = (property_exists($response,'email')) ? $response->email : '';
        if (property_exists($response, 'birthday')) {
            [$birthday_year, $birthday_month, $birthday_day] = explode('-', $response->birthday);
            $userProfile->birthDay = (int) $birthday_day;
            $userProfile->birthMonth = (int) $birthday_month;
            $userProfile->birthYear = (int) $birthday_year;
        }
        return $userProfile;
    }
}
