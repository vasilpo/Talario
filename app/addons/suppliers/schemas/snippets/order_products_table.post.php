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

$schema['supplier'] = array(
    'class' => '\Tygh\Template\Document\Variables\GenericVariable',
    'data' => function (\Tygh\Template\Snippet\Table\ItemContext $context) {
        static $suppliers = array();
        $data = array();
        $product = $context->getItem();

        if (!empty($product['extra']['supplier_id'])) {
            $supplier_id = $product['extra']['supplier_id'];

            if (!isset($suppliers[$supplier_id])) {
                $suppliers[$supplier_id] = fn_get_supplier_data($supplier_id);
            }

            $data = $suppliers[$supplier_id];
        }

        return $data;
    },
    'arguments' => array('#context', '#config', '@formatter'),
    'attributes' => array(
        'supplier_id', 'company_id', 'name', 'address', 'city', 'state', 'country', 'zipcode',
        'email', 'phone', 'fax'
    )
);

return $schema;