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

namespace Tygh\Enum\Addons\Gdpr;

class CookiesPolicyManager
{
    /***
     * TODO: Currently these constants duplicate those from the Tygh\Addons\Gdpr\CookiesPolicyManager, so they need to be changed in both places. This duplication will need to be removed later.
     */
    const AGREEMENT_TYPE_COOKIES = 'cookies';
    const REQUEST_ACCEPTANCE_FLAG = 'cookies_accepted';

    const COOKIE_POLICY_IMPLICIT = 'implicit';
    const COOKIE_POLICY_EXPLICIT = 'explicit';
    const COOKIE_POLICY_NONE = 'none';
}
