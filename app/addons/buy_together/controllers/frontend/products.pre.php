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

/**
 * @var string $mode
 * @var array $auth
 */
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'options') {
    if (!empty($_REQUEST['appearance']['bt_chain'])) {
        $products = array();

        foreach ($_REQUEST['product_data'] as $id => $options) {
            if (isset($_REQUEST['product_data'][$id]['product_options'])) {
                $products[$id]['selected_options'] = $_REQUEST['product_data'][$id]['product_options'];
            }

            if (isset($_REQUEST['changed_option'][$id])) {
                $products[$id]['changed_option'] = $_REQUEST['changed_option'][$id];
            }

            unset($products[$id]['selected_options']['AOC']);
        }

        $params = array(
            'chain_id' => $_REQUEST['appearance']['bt_chain'],
            'status' => 'A',
            'full_info' => true,
            'date' => true,
            'selected_options' => $products,
        );

        $chains = fn_buy_together_get_chains($params, $auth);

        if (!empty($chains)) {
            Tygh::$app['view']->assign('chains', $chains);
            Tygh::$app['view']->display('addons/buy_together/blocks/product_tabs/buy_together.tpl');

            exit();
        }
    }

}
