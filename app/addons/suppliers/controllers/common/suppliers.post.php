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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// Ajax content
if ($mode == 'get_suppliers_list') {

    $params = $_REQUEST;
    $condition = '';
    $pattern = !empty($params['pattern']) ? $params['pattern'] : '';
    $start = !empty($params['start']) ? $params['start'] : 0;
    $limit = (!empty($params['limit']) ? $params['limit'] : 10) + 1;

    if (AREA == 'C') {
        $condition .= " AND ?:suppliers.status = 'A' ";
    }

    if (isset($params['exclude_supplier_id'])) {
        $condition .= db_quote(" AND ?:suppliers.supplier_id != ?i", intval($params['exclude_supplier_id']));
    }

    if (isset($params['company_id']) || Registry::get('runtime.company_id')) {
        $copmpany_id = isset($params['company_id']) ? intval($params['company_id']) : Registry::get('runtime.company_id');
        $condition .= fn_get_company_condition("?:suppliers.company_id", true, $copmpany_id);
    }

    $suppliers = db_get_hash_array("SELECT ?:suppliers.supplier_id as value, ?:suppliers.name FROM ?:suppliers WHERE 1 ?p AND ?:suppliers.name LIKE ?l ORDER BY ?:suppliers.name LIMIT ?i, ?i", 'value', $condition, $pattern . '%', $start, $limit);

    if (!$start) {
        array_unshift($suppliers, array('value' => 0, 'name' => '-' . __('none') . '-'));
    }

    if (defined('AJAX_REQUEST') && sizeof($suppliers) < $limit) {
        Tygh::$app['ajax']->assign('completed', true);
    } else {
        array_pop($suppliers);
    }

    Tygh::$app['view']->assign('objects', $suppliers);
    Tygh::$app['view']->assign('id', $params['result_ids']);
    Tygh::$app['view']->display('common/ajax_select_object.tpl');
    exit;
}
