<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Handles AJAX loading of category-based lesson dropdown items for homepage search filters.
 *
 * @var string $mode
 */
if ($mode === 'get_products') {
    Tygh::$app['ajax']->assign(
        'lr_homepage_search_filters_products',
        fn_lr_design_changes_get_homepage_search_filters_products_response($_REQUEST)
    );

    return [CONTROLLER_STATUS_NO_CONTENT];
}
