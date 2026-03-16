<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Addons\PartnerSites\Repository\PartnerSiteClickRepository;
use Tygh\Addons\PartnerSites\Repository\ProductSiteRepository;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('BOOTSTRAP') or die('Access denied');
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * Hook handler for `get_product_data_post`.
 *
 * Loads partner website into product data.
 *
 * @param array  $product_data Product data
 * @param array  $auth         Authorization data
 * @param bool   $preview      Preview mode flag
 * @param string $lang_code    Language code
 *
 * @return void
 *
 * @see fn_get_product_data()
 */
function fn_partner_sites_get_product_data_post(&$product_data, $auth, $preview, $lang_code): void
{
    if (empty($product_data['product_id'])) {
        return;
    }

    $site = ProductSiteRepository::create()->findByProductId((int) $product_data['product_id']);
    if ($site !== '') {
        $product_data['partner_site_url'] = $site;
    }
}

/**
 * Hook handler for `update_product_post`.
 *
 * Persists partner website for the product.
 *
 * @param array  $product_data Product data
 * @param int    $product_id   Product identifier
 * @param string $lang_code    Language code
 * @param bool   $create       Whether product is newly created
 *
 * @return void
 *
 * @see fn_update_product()
 */
function fn_partner_sites_update_product_post($product_data, $product_id, $lang_code, $create): void
{
    if (!isset($product_data['partner_site_url'])) {
        return;
    }

    $repository = ProductSiteRepository::create();
    $site = trim((string) $product_data['partner_site_url']);

    if ($site === '') {
        $repository->deleteByProductId((int) $product_id);
        return;
    }

    $repository->save((int) $product_id, $site);
}

/**
 * Hook handler for `delete_product_post`.
 *
 * Cleans partner website data after product removal.
 *
 * @param int  $product_id      Product identifier
 * @param bool $product_deleted Deletion result flag
 *
 * @return void
 *
 * @see fn_delete_product()
 */
function fn_partner_sites_delete_product_post($product_id, $product_deleted): void
{
    if (!$product_deleted) {
        return;
    }

    ProductSiteRepository::create()->deleteByProductId((int) $product_id);
    PartnerSiteClickRepository::create()->deleteByProductId((int) $product_id);
}
