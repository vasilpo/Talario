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
class Mailru extends OAuth2
{
    /**
     * Default Base URL to provider API
     */
    protected $apiBaseUrl = 'https://oauth.mail.ru/';

    /**
     * Default Authorization Endpoint
     */
    protected $authorizeUrl = 'https://oauth.mail.ru/login';

    /**
     * Default Access Token Endpoint
     */
    protected $accessTokenUrl = 'https://oauth.mail.ru/token';

    /**
     * Access Token name
     */
    protected $accessTokenName = 'session_key';

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
        $access_token_data = $this->getAccessToken();
        $params = [
            'access_token' => $access_token_data['access_token']
        ];

        $response = $this->apiRequest('userinfo', 'GET', $params);
        if (!isset($response->id)){
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
		}

        $userProfile = new User\Profile();
    
        $userProfile->identifier = (property_exists($response,'id')) ? $response->id : '';
		$userProfile->firstName = (property_exists($response,'first_name')) ? $response->first_name : '';
		$userProfile->lastName = (property_exists($response,'last_name')) ? $response->last_name : '';
		$userProfile->displayName = (property_exists($response,'nickname')) ? $response->nickname : '';
		$userProfile->photoURL = (property_exists($response,'image')) ? $response->image : '';
		$userProfile->gender = (property_exists($response,'gender')) ? $response->gender : '';
		$userProfile->email = (property_exists($response,'email')) ? $response->email : '';
		$userProfile->emailVerified = (property_exists($response,'email')) ? $response->email : '';

		if (property_exists($response,'birthday')) {
			[$birthday_day, $birthday_month, $birthday_year] = explode('.', $response->birthday);

			$userProfile->birthDay = (int) $birthday_day;
			$userProfile->birthMonth = (int) $birthday_month;
			$userProfile->birthYear = (int) $birthday_year;
		}

		return $userProfile;
	}
}
