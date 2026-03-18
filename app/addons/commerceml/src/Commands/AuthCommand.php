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


namespace Tygh\Addons\CommerceML\Commands;


/**
 * Class AuthCommand
 *
 * @package Tygh\Addons\CommerceML\Commands
 *
 * @see \Tygh\Addons\CommerceML\Commands\AuthCommandHandler
 */
class AuthCommand
{
    /**
     * @var string
     */
    public $auth_login;

    /**
     * @var string
     */
    public $auth_password;

    /**
     * Creates command by $_SERVER
     *
     * @param array<string, string> $server The $_SERVER super global variable
     *
     * @return \Tygh\Addons\CommerceML\Commands\AuthCommand
     */
    public static function createFromServer(array $server)
    {
        $self = new self();
        $self->auth_login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
        $self->auth_password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

        return $self;
    }
}
