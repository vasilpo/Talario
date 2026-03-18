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

/** @var array $schema */

use Tygh\Addons\Gdpr\CookiesPolicyManager;
use Tygh\Registry;

$schema['gdpr'] = function () {
    return (bool) db_get_field(
        'SELECT COUNT(*) AS cnt FROM ?:gdpr_user_agreements WHERE timestamp >= ?i',
        strtotime('-30 days')
    );
};

$schema['cookie_consent_implicit'] = static function () {
    return Registry::get('addons.gdpr.gdpr_cookie_consent') === CookiesPolicyManager::COOKIE_POLICY_IMPLICIT;
};

$schema['cookie_consent_explicit'] = static function () {
    return Registry::get('addons.gdpr.gdpr_cookie_consent') === CookiesPolicyManager::COOKIE_POLICY_EXPLICIT;
};

return $schema;