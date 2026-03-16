<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Addons\PartnerSites\Repository\PartnerSiteClickRepository;
use Tygh\Addons\PartnerSites\Repository\ProductSiteRepository;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode === 'partner_site_click') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    if ($product_id <= 0) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = ProductSiteRepository::create()->findByProductId($product_id);
    if ($site === '') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_partner_sites_normalize_site_url($site);
    if ($site === '') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $auth = isset(Tygh::$app['session']['auth']) ? (array) Tygh::$app['session']['auth'] : [];
    fn_partner_sites_log_click($product_id, $auth);

    fn_redirect(
        fn_link_attach($site, 'utm_source=talario&utm_medium=partner&utm_campaign=partner_site'),
        true
    );
}

/**
 * Normalizes partner URL before redirect.
 *
 * @param string $url Raw website URL
 *
 * @return string
 */
function fn_partner_sites_normalize_site_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    if (!preg_match('~^https?://~i', $url)) {
        $url = 'http://' . $url;
    }

    return $url;
}

/**
 * Stores partner site click data.
 *
 * @param int   $product_id Product identifier
 * @param array $auth       Authorization data
 *
 * @return void
 */
function fn_partner_sites_log_click(int $product_id, array $auth): void
{
    $user_id = isset($auth['user_id']) ? (int) $auth['user_id'] : 0;
    $email = '';

    if ($user_id > 0) {
        $email = !empty($auth['email']) ? (string) $auth['email'] : '';

        if ($email === '') {
            $user_data = fn_get_user_info($user_id);
            $email = !empty($user_data['email']) ? (string) $user_data['email'] : '';
        }
    }

    PartnerSiteClickRepository::create()->createClick([
        'user_id'    => $user_id,
        'email'      => $email,
        'product_id' => $product_id,
        'timestamp'  => TIME,
    ]);
}
