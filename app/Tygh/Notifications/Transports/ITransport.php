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

namespace Tygh\Notifications\Transports;

/**
 * Interface ITransport describes a transport that processes event messages.
 *
 * @package Tygh\Events\Transports
 */
interface ITransport
{
    /**
     * Provides transport ID.
     *
     * @return string
     */
    public static function getId();

    /**
     * Processes a transport message schema of an event to notification.
     *
     * @param \Tygh\Notifications\Transports\BaseMessageSchema $schema
     * @param \Tygh\Notifications\Receivers\SearchCondition[]  $receiver_search_conditions
     *
     * @return bool Whether a notification was successfully processed
     */
    public function process(BaseMessageSchema $schema, array $receiver_search_conditions);
}
