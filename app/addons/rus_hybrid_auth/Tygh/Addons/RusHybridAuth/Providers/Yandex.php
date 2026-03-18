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
use Hybridauth\User;
use UnexpectedValueException;

/**
 * @phpcs:disable
 */
class Yandex extends OAuth2
{
    /**
     * Default Base URL to provider API
     */
    protected $apiBaseUrl = 'https://login.yandex.ru/info';

    /**
     * Default Authorization Endpoint
     */
    protected $authorizeUrl = 'https://oauth.yandex.ru/authorize';

    /**
     * Default Access Token Endpoint
     */
    protected $accessTokenUrl = 'https://oauth.yandex.ru/token';

    /**
     * Access Token name
     */
    protected $accessTokenName = 'oauth_token';

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		$response = $this->apiRequest('?format=json');
		if (!isset($response->id)){
            throw new UnexpectedValueException('Provider API returned an unexpected response.');
		}

        $userProfile = new User\Profile();

        $userProfile->identifier = (property_exists($response,'id')) ? $response->id : '';
		$userProfile->firstName = (property_exists($response,'real_name')) ? $response->real_name : '';
		$userProfile->lastName = (property_exists($response,'family_name')) ? $response->family_name : '';
		$userProfile->displayName = (property_exists($response,'display_name')) ? $response->display_name : '';
		$userProfile->photoURL = 'http://upics.yandex.net/'. $userProfile->identifier .'/normal';
		$userProfile->profileURL = '';
		$userProfile->gender = (property_exists($response,'sex')) ? $response->sex : '';
		$userProfile->email = (property_exists($response,'default_email')) ? $response->default_email : '';
		$userProfile->emailVerified = (property_exists($response,'default_email')) ? $response->default_email : '';

		if (property_exists($response,'birthday') && !empty($response->birthday)) {
			[$birthday_year, $birthday_month, $birthday_day] = explode('-', $response->birthday);

			$userProfile->birthDay = (int) $birthday_day;
			$userProfile->birthMonth = (int) $birthday_month;
			$userProfile->birthYear = (int) $birthday_year;
		}

		return $userProfile;
	}
}
