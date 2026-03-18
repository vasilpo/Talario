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


namespace Tygh\Mailer;

/**
 * The interface of the class factory responsible for creating the message sender object.
 * 
 * @package Tygh\Mailer
 */
interface ITransportFactory
{
    /**
     * Create transport instance by type
     *
     * @param string    $type       Type of transport (smtp|mail|sendmail)
     * @param array     $settings   Data of transport settings
     *
     * @return ITransport
     */
    public function createTransport($type, $settings);
}