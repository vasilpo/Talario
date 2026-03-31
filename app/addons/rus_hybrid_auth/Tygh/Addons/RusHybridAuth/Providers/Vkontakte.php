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
use Hybridauth\Data;
use Hybridauth\Exception\InvalidAccessTokenException;
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
    protected $apiBaseUrl = 'https://id.vk.ru/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://id.vk.ru/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://id.vk.ru/oauth2/auth';

    /**
     * {@inheritdoc}
     */
    protected $tokenExchangeMethod = 'POST';

    /**
     * @var int
     */
    protected $codeVerifierLength = 64;

    /**
     * @var string
     */
    protected $codeVerifierStorageKey = 'vkid_code_verifier';

    /**
     * @var string
     */
    protected $deviceIdStorageKey = 'vkid_device_id';

    /**
     * Load the user profile from VK ID API.
     *
     * @return \Hybridauth\User\Profile
     */
    public function getUserProfile()
    {
        $response = $this->apiRequest(
            'oauth2/user_info',
            'POST',
            [
                'access_token' => $this->getStoredData('access_token'),
                'client_id' => $this->clientId,
            ],
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ]
        );

        if (isset($response->error) || !isset($response->user) || !isset($response->user->user_id)) {
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
        }

        return $this->getUserByResponse($response->user);
    }

    /**
     * User contacts are not supported by VK ID OAuth API.
     *
     * @return array
     */
    public function getUserContacts()
    {
        return [];
    }

    /**
     * Build Authorization URL for VK ID authorization request using PKCE.
     *
     * @param array $parameters
     *
     * @return string
     */
    protected function getAuthorizeUrl($parameters = [])
    {
        $code_verifier = $this->generateCodeVerifier();
        $code_challenge = $this->generateCodeChallenge($code_verifier);
        $authorize_url = parent::getAuthorizeUrl($parameters);

        $this->storeData($this->codeVerifierStorageKey, $code_verifier);

        $query = parse_url($authorize_url, PHP_URL_QUERY);
        parse_str((string) $query, $query_params);
        $query_params['code_challenge'] = $code_challenge;
        $query_params['code_challenge_method'] = 'S256';

        return $this->authorizeUrl . '?' . http_build_query($query_params, '', '&', $this->AuthorizeUrlParametersEncType);
    }

    /**
     * Exchange VK ID authorization code for access token.
     *
     * @param string $code
     *
     * @return string
     */
    protected function exchangeCodeForAccessToken($code)
    {
        $state = filter_input($_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET, 'state');
        $device_id = filter_input($_SERVER['REQUEST_METHOD'] === 'POST' ? INPUT_POST : INPUT_GET, 'device_id');
        $code_verifier = $this->getStoredData($this->codeVerifierStorageKey);

        if (empty($code_verifier)) {
            throw new InvalidAccessTokenException('Provider returned no code_verifier for token exchange.');
        }

        if (empty($device_id)) {
            throw new InvalidAccessTokenException('Provider returned no device_id for token exchange.');
        }

        $this->tokenExchangeParameters = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callback,
            'code' => $code,
            'state' => $state,
            'device_id' => $device_id,
            'code_verifier' => $code_verifier,
        ];
        $this->tokenExchangeHeaders = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];

        $response = parent::exchangeCodeForAccessToken($code);
        $this->storeData($this->deviceIdStorageKey, $device_id);

        return $response;
    }

    /**
     * Validate VK ID access token response.
     *
     * @param string $response
     *
     * @return \Hybridauth\Data\Collection
     */
    protected function validateAccessTokenExchange($response)
    {
        $data = (new Data\Parser())->parse($response);
        $collection = new Data\Collection($data);

        if (!$collection->exists('access_token')) {
            throw new InvalidAccessTokenException(
                'Provider returned no access_token: ' . htmlentities($response)
            );
        }

        $this->storeData('access_token', $collection->get('access_token'));
        $this->storeData('token_type', $collection->get('token_type'));
        $this->storeData('refresh_token', $collection->get('refresh_token'));
        $this->storeData('id_token', $collection->get('id_token'));
        $this->storeData('user_id', $collection->get('user_id'));

        if ($collection->exists('expires_in')) {
            $expires_at = time() + (int) $collection->get('expires_in');
            $this->storeData('expires_in', $collection->get('expires_in'));
            $this->storeData('expires_at', $expires_at);
        }

        $this->deleteStoredData('authorization_state');
        $this->deleteStoredData($this->codeVerifierStorageKey);
        $this->initialize();

        return $collection;
    }

    /**
     * @param object $response
     *
     * @return \Hybridauth\User\Profile
     */
    protected function getUserByResponse(object $response)
    {
        $user = new User\Profile();

        $user->identifier = (string) ($response->user_id ?? '');
        $user->firstName = (string) ($response->first_name ?? '');
        $user->lastName = (string) ($response->last_name ?? '');
        $user->displayName = trim($user->firstName . ' ' . $user->lastName);
        $user->displayName = $user->displayName !== '' ? $user->displayName : $user->identifier;
        $user->photoURL = (string) ($response->avatar ?? '');
        $user->email = (string) ($response->email ?? '');
        $user->emailVerified = !empty($response->email) ? (string) $response->email : '';
        $user->phone = (string) ($response->phone ?? '');
        $user->gender = $this->mapGender($response->sex ?? null);
        $user->profileURL = $user->identifier !== '' ? 'https://vk.com/id' . $user->identifier : '';
        $this->mapBirthday($user, (string) ($response->birthday ?? ''));
        $user->country = '';

        return $user;
    }

    /**
     * @return string
     */
    protected function generateCodeVerifier()
    {
        $verifier = rtrim(strtr(base64_encode(random_bytes($this->codeVerifierLength)), '+/', '-_'), '=');

        return substr($verifier, 0, 86);
    }

    /**
     * @param string $code_verifier
     *
     * @return string
     */
    protected function generateCodeChallenge($code_verifier)
    {
        return rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
    }

    /**
     * @param mixed $sex
     *
     * @return string|null
     */
    protected function mapGender($sex)
    {
        if ((int) $sex === 1) {
            return 'female';
        }

        if ((int) $sex === 2) {
            return 'male';
        }

        return null;
    }

    /**
     * @param \Hybridauth\User\Profile $user
     * @param string                   $birthday
     *
     * @return void
     */
    protected function mapBirthday(User\Profile $user, $birthday)
    {
        if ($birthday === '') {
            return;
        }

        $birthday_parts = explode('.', $birthday);

        if (!empty($birthday_parts[0])) {
            $user->birthDay = (int) $birthday_parts[0];
        }

        if (!empty($birthday_parts[1])) {
            $user->birthMonth = (int) $birthday_parts[1];
        }

        if (!empty($birthday_parts[2])) {
            $user->birthYear = (int) $birthday_parts[2];
        }
    }
}
