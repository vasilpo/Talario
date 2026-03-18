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

namespace Tygh\Enum;

/**
 * Class SyncDataStatuses
 *
 * @package Tygh\Enum
 */
class SyncDataStatuses
{
    /**
     * New synchronization
     */
    const STATUS_NEW = 'N';

    /**
     * Synchronization in progress
     */
    const STATUS_PROGRESS = 'P';

    /**
     * Synchronization is successfully finished
     */
    const STATUS_SUCCESS = 'S';

    /**
     * Synchronization is unsuccessfully finished
     */
    const STATUS_UNSUCCESS = 'U';
}
