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

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */

$schema['vendor_communication'] = function () {
    $installation_timestamp = (int) db_get_field('SELECT install_datetime FROM ?:addons WHERE addon = ?s', 'vendor_communication');

    if ($installation_timestamp) {
        $two_days_after = new DateTime();
        $two_days_after
            ->setTimestamp($installation_timestamp)
            ->add(new DateInterval('P2D'));

        return (bool) db_get_field('SELECT COUNT(*) FROM ?:vendor_communication_messages WHERE timestamp > ?i', $two_days_after->getTimestamp());
    } else {
        return false;
    }
};

return $schema;