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

$schema = fn_get_schema('documents', 'order');

$schema['supplier'] = array(
    'class' => '\Tygh\Template\Document\Variables\GenericVariable',
    'data' => function (\Tygh\Addons\Suppliers\Documents\SupplierOrder\Context $context) {
        /** @var \Tygh\Tools\Formatter $formatter */
        $formatter = Tygh::$app['formatter'];
        $data = $context->getSupplier();
        $data['cost'] = $formatter->asPrice($data['cost']);

        return $data;
    },
    'arguments' => array('#context', '#config', '@formatter'),
    'alias' => 's',
    'attributes' => array(
        'name', 'company_id', 'cost', 'supplier_id',
        'shippings' => array(
            '[0..N]' => array(
                'shipping_id', 'shipping', 'delivery_time', 'rate_calculation', 'destination',
                'min_weight', 'max_weight', 'service_code', 'module', 'rate', 'group_name'
            )
        ),
    )
);

return $schema;