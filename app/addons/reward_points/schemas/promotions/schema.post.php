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

$schema['conditions']['reward_points'] = array (
    'operators' => array ('eq', 'neq', 'lte', 'gte', 'lt', 'gt'),
    'type' => 'input',
    'field' => '@auth.points',
    'zones' => array('catalog', 'cart'),
    'filter' => 'fn_promotions_filter_int_condition_value'
);

$schema['bonuses']['give_points'] = array (
    'type' => 'input',
    'function' => array('fn_reward_points_promotion_give_points', '#this', '@cart', '@auth', '@cart_products'),
    'zones' => array('cart'),
    'filter' => 'intval'
);

return $schema;
