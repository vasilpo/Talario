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

use Tygh\Enum\YesNo;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * Prepare RMA fields "is_returnable" and "return_period".
 *
 * @param string $object_id Product identifier
 * @param array  $object    Product data
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 *
 * @return void
 *
 * @param-out array $object
 */
function fn_import_prepare_rma_data($object_id, array &$object)
{
    $rma_settings = Registry::get('addons.rma');

    if (
        (
            empty($object['is_returnable'])
            || !in_array($object['is_returnable'], [YesNo::YES, YesNo::NO])
        )
        && empty($object_id)
    ) {
        $object['is_returnable'] = $rma_settings['returnable'];
    }

    if (
        !empty($object['return_period'])
        && is_numeric($object['return_period'])
        || !empty($object_id)
    ) {
        return;
    }

    $object['return_period'] = $rma_settings['return_period'];
}
