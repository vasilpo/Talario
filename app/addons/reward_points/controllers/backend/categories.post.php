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

if ($_SERVER['REQUEST_METHOD']	== 'POST') {
    return;
}

if ($mode == 'update') {

    // Add new tab to page sections
    // [Page sections]
    // Add new tab to page sections
    Registry::set('navigation.tabs.reward_points', array (
        'title' => __('reward_points'),
        'js' => true
    ));

    // [/Page sections]

    Tygh::$app['view']->assign('reward_points', fn_get_reward_points($_REQUEST['category_id'], CATEGORY_REWARD_POINTS));
    Tygh::$app['view']->assign('object_type', CATEGORY_REWARD_POINTS);

} elseif ($mode == 'add') {

    // Add new tab to page sections
    // [Page sections]
    Registry::set('navigation.tabs.reward_points', array (
        'title' => __('reward_points'),
        'js' => true
    ));
    // [/Page sections]

    Tygh::$app['view']->assign('object_type', CATEGORY_REWARD_POINTS);
}

Tygh::$app['view']->assign(
    'reward_usergroups',
    fn_get_usergroups(
        array(
            'type'            => 'C',
            'status'          => array('A', 'H'),
            'include_default' => true
        )
    )
);

/** /Body **/
