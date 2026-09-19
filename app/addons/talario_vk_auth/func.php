<?php

defined('BOOTSTRAP') or die('Access denied');

/**
 * Keeps the VK ID callback path free of storefront index/query rewriting.
 *
 * @param string                               $_url
 * @param string                               $area
 * @param string                               $url
 * @param string                               $protocol
 * @param int|false                            $company_id_in_url
 * @param string                               $lang_code
 * @param array<string, array<string, string>> $locations
 *
 * @return void
 */
function fn_talario_vk_auth_url_post(
    &$_url,
    $area,
    $url,
    $protocol,
    $company_id_in_url,
    $lang_code,
    $locations
) {
    if (
        $url === '/auth/vkontakte'
        && isset($locations[$area], $locations[$area][$protocol])
    ) {
        $_url = $locations[$area][$protocol] . $url;
    }
}
