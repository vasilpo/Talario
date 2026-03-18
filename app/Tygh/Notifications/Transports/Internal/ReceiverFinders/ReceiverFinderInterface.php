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

namespace Tygh\Notifications\Transports\Internal\ReceiverFinders;

use Tygh\Notifications\Transports\Internal\InternalMessageSchema;

/**
 * Interface ReceiverFinderInterface describes class that is used to find receivers for internal notifications.
 *
 * @package Tygh\Notifications\Transports\Internal\ReceiverFinders
 */
interface ReceiverFinderInterface
{
    /**
     * @param int|string                                                    $criterion      Searching criterion
     * @param \Tygh\Notifications\Transports\Internal\InternalMessageSchema $message_schema Schema that describes message
     *
     * @return array<int, string>
     */
    public function find($criterion, InternalMessageSchema $message_schema);
}
