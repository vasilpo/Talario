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

use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if (
    $mode === 'update'
    && $_REQUEST['addon'] === 'discussion'
) {
    /** @var array<string, array> $options */
    $options = Tygh::$app['view']->getTemplateVars('options');
    /** @var array<string, array> $subsections */
    $subsections = Tygh::$app['view']->getTemplateVars('subsections');
    unset($options['orders'], $subsections['orders']);

    if (Registry::get('addons.product_reviews.status') === ObjectStatuses::ACTIVE) {
        unset($options['products'], $subsections['products']);
    }

    Tygh::$app['view']->assign([
        'options'     => $options,
        'subsections' => $subsections,
    ]);
}
