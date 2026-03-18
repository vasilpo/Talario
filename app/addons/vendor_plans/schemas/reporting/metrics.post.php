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

use Tygh\Settings;

/** @var array $schema */

$schema['vendor_plans'] = function () {
    $timestamp = Settings::instance()->getSettingDataByName('current_timestamp');
    $trial_timestamp = (int) $timestamp['value'];
    $install_timestamp = new DateTime();
    $install_timestamp
        ->setTimestamp($trial_timestamp)
        ->sub(new DateInterval('P30D')); // minus 30 days
    $install_timestamp = $install_timestamp->getTimestamp();

    if ($install_timestamp) {
        $company_id = db_get_row(
            'SELECT company_id FROM ?:companies WHERE status = ?s AND timestamp > ?i AND plan_id <> ?i',
            'A',
            $install_timestamp,
            0
        );
    }

    return !empty($company_id);
};

return $schema;