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

use Smarty\Template;
use Tygh\Addons\ProductBundles\ServiceProvider;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array{product: array{product_id: int}} $params   Block params
 * @param string                                 $content  Block content
 * @param Template                               $template Smarty template
 *
 * @return string
 */
function smarty_component_product_bundles_product_bundles(
    array $params,
    $content,
    Template $template
) {
    $bundles_params = [
        'full_info'             => true,
        'status'                => ObjectStatuses::ACTIVE,
    ];

    if (isset($params['product_id'])) {
        $bundles_params['product_id'] = $params['product_id'];
    }

    if (isset($params['bundle_id'])) {
        $bundles_params['bundle_id'] = $params['bundle_id'];
    }

    if (isset($params['show_header'])) {
        $bundles_params['show_header'] = $params['show_header'];
    }

    if (isset($params['show_block_header'])) {
        $bundles_params['show_block_header'] = $params['show_block_header'];
    }

    if (isset($params['enable_padding'])) {
        $bundles_params['enable_padding'] = $params['enable_padding'];
    }

    if (isset($params['show_on_products_page'])) {
        $bundles_params['show_on_products_page'] = $params['show_on_products_page'];
    }

    if (isset($params['show_on_tab'])) {
        $bundles_params['show_on_tab'] = $params['show_on_tab'];
    }

    if (isset($_REQUEST['product_id'], $_REQUEST['product_data'][$_REQUEST['product_id']]['product_options'])) {
        $product_id = $_REQUEST['product_id'];
        $product_options = $_REQUEST['product_data'][$product_id]['product_options'];

        $selected_options = [
            $product_id => [
                'selected_options' => $product_options
            ],
        ];
    } else {
        $selected_options = [];
    }

    $bundles_params['selected_options'] = $selected_options;

    $bundle_service = ServiceProvider::getService();
    if (Registry::isExist($bundle_service::BUNDLE_CACHE_KEY)) {
        $modified_bundle = Registry::get($bundle_service::BUNDLE_CACHE_KEY);
    }

    list($bundles,) = $bundle_service->getBundles($bundles_params);
    if (!empty($modified_bundle)) {
        $modified_bundle = reset($modified_bundle);
        foreach ($bundles as &$bundle) {
            if ($bundle['bundle_id'] === $modified_bundle['bundle_id']) {
                $bundle = $modified_bundle;
                Registry::del($bundle_service::BUNDLE_CACHE_KEY);
                break;
            }
        }
        unset($bundle);
    }

    $bundles_params['bundles'] = $bundles;

    $template->assign($bundles_params);

    /** @var \Tygh\SmartyEngine\Core $smarty */
    $smarty = $template->getSmarty();

    try {
        return $smarty->fetch('addons/product_bundles/components/common/product_bundles.tpl', null, null, $template);
    } catch (Exception $e) {
        return '';
    }
}
