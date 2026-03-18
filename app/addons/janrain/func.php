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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_janrain_generate_info()
{
    return __('janrain_general_info');
}

function fn_janrain_parse_app_domain($url)
{
    $result = parse_url($url);

    if (!empty($result['host'])) {
        return str_replace('.rpxnow.com', '', $result['host']);
    }

    return false;
}

function fn_janrain_fill_user_fields(&$exclude)
{
        $exclude[] = 'janrain_identifier';
}

/**
 * Hook handler: disabled CSRF validation when authenticating via Janrain provider.
 */
function fn_janrain_csrf_validate_request_pre(&$params, &$validation_required)
{
    if (is_null($validation_required)
        && $params['server']['REQUEST_METHOD'] == 'POST'
        && $params['area'] == 'C'
        && $params['controller'] == 'auth'
        && $params['mode'] == 'login'
        && !empty($params['request']['token'])
    ) {
        $validation_required = false;
    }
}
