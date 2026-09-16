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

/**
 * @phpcs:disable
 */
class Vkontakte extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $scope = 'email';

    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.vk.com/method/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://oauth.vk.com/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://oauth.vk.com/token';

    /**
     * @var array<string> Default user fields map
     */
    public $fields = [
        // Old that saved for backward-compability
        'identifier' => 'id',
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'displayName' => 'screen_name',
        'gender' => 'sex',
        'photoURL' => 'photo_big',
        'home_town' => 'home_town',
        'profileURL' => 'domain',      // Will be converted in getUserByResponse()
        // New
        'nickname' => 'nickname',
        'bdate' => 'bdate',
        'timezone' => 'timezone',
        'photo_rec' => 'photo_rec',
        'domain' => 'domain',
        'photo_max' => 'photo_max_orig',
        'home_phone' => 'home_phone',
        'city' => 'city',        // Will be converted in getUserByResponse()
        'country' => 'country',     // Will be converted in getUserByResponse()
    ];

    /**
     * @var string Default VK API version
     */
    public $version = '5.95';

    /**
     * Load the user profile from the IDp api client
     *
     * @return \Hybridauth\User\Profile
     */
    public function getUserProfile()
    {
        $params = [
            'fields' => implode(',', $this->fields),
            'v' => $this->version
        ];

        $response = $this->apiRequest('users.get', 'GET', $params);

        if (isset($response->error) || !isset($response->response[0]) || !isset($response->response[0]->id)) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        // Fill datas
        $response = reset($response->response);
        return $this->getUserByResponse($response, true);
    }

    /**
     * Load the user contacts
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getUserContacts()
    {
        $params = [
            'fields' => implode(',', $this->fields),
        ];

        $response = $this->apiRequest('friends.get', 'GET', $params);

        if (empty($response) || empty($response->response)) {
            return [];
        }

        $contacts = [];
        foreach ($response->response as $item) {
            $contacts[] = $this->getUserByResponse($item);
        }

        return $contacts;
    }

    /**
     * @param object $response                 Response for user API request
     * @param bool   $with_additional_requests True to get some full fields like 'city' or 'country'
     *                                       (requires additional responses to vk api!)
     *
     * @return \Hybridauth\User\Profile
     */
    protected function getUserByResponse(object $response, $with_additional_requests = false)
    {
        $user = new User\Profile();

        foreach ($this->fields as $field => $map) {
            if (!property_exists($user, $field)) {
                $user->data[$field] = $response->$map ?? null;
                continue;
            }
            $user->$field = $response->$map ?? null;
        }

        if (!empty($user->profileURL)) {
            $user->profileURL = 'http://vk.com/' . $user->profileURL;
        }

        if (isset($user->gender)) {
            switch ($user->gender) {
                case 1:
                    $user->gender = 'female';
                    break;

                case 2:
                    $user->gender = 'male';
                    break;

                default:
                    $user->gender = null;
                    break;
            }
        }

        if (!empty($user->bdate)) {
            $birthday = explode('.', $user->bdate);
            switch (count($birthday)) {
                case 3:
                    $user->birthDay = (int) $birthday[0];
                    $user->birthMonth = (int) $birthday[1];
                    $user->birthYear = (int) $birthday[2];
                    break;

                case 2:
                    $user->birthDay = (int) $birthday[0];
                    $user->birthMonth = (int) $birthday[1];
                    break;
            }
        }

        if (!empty($user->city) && $with_additional_requests) {
            $params = [
                'v' => $this->version,
                'city_ids' => $user->city
            ];
            $cities = (array) $this->apiRequest('database.getCitiesById', 'GET', $params);
            $city = reset($cities);

            if (is_array($city)) {
                $city = reset($city);
            }

            if (is_object($city) || is_string($city)) {
                $user->city = $city->title ?? null;
            }
        }

        $user->country = '';  //api returns country as title and country resets on checkout

        return $user;
    }
}
