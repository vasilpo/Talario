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
 * Describes a way to get a vendor ID for currently viewed object: product, page, vendor store, etc
 * (see ::fn_mve_init_company_id())
 *
 * Structure:
 *
 * 'dispatch as stated for a layout page' => array(
 *     'callable' => array('function to get vendor ID', array('request parameter to pass', 'param_name', 'param_name')),
 *
 *     'table' => 'table to check ownership with',
 *     'owner_field' => 'field name where vendor ID is stored',
 *     'table_field' => 'table field where object ID is stored',
 *     'request_param' => 'request parameter where object ID is passed',
 *
 *     'can_edit_blocks' => true, // if true, vendor can edit blocks of this layout page
 * )
 *
 * When 'callable' is specified, 'table', 'owner_field', 'table_field' and 'request_param' are not used.
 *
 */
$schema = array(
    'products.view' => array(
        'table' => 'products',
        'owner_field' => 'company_id',
        'request_param' => 'product_id',
        'table_field' => 'product_id',
        'can_edit_blocks' => true,
    ),
    'companies.products' => array(
        'table' => 'companies',
        'owner_field' => 'company_id',
        'request_param' => 'company_id',
        'table_field' => 'company_id',
        'can_edit_blocks' => true,
    ),
    'pages.view' => array(
        'table' => 'pages',
        'owner_field' => 'company_id',
        'request_param' => 'page_id',
        'table_field' => 'page_id',
        'can_edit_blocks' => true,
    ),
    'companies.view' => array(
        'table' => 'companies',
        'owner_field' => 'company_id',
        'request_param' => 'company_id',
        'table_field' => 'company_id',
        'can_edit_blocks' => true,
     ),
    'theme_editor.view' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
    'theme_editor.get_style' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
    'theme_editor.get_css' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
    'theme_editor.save' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
    'theme_editor.duplicate' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
    'theme_editor.delete_style' => array(
        'can_edit_blocks' => false,
        'callable' => array('fn_mve_get_vendor_id_from_customization_mode', array()),
    ),
);

return $schema;
