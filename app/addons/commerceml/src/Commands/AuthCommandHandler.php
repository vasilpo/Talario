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


use Tygh\Common\OperationResult;
use Tygh\Enum\UserTypes;

/**
 * Class AuthCommandHandler
 *
 * @package Tygh\Addons\CommerceML\Commands
 */
class AuthCommandHandler
{
    /**
     * @var string
     */
    private $permission = 'commerceml';

    /**
     * @var int
     */
    private $forced_company_id;

    /**
     * @var bool
     */
    private $is_ultimate;

    /**
     * AuthCommandHandler constructor.
     *
     * @param int  $forced_company_id Forced company id
     * @param bool $is_ultimate       Flag if version is Ultimate
     */
    public function __construct($forced_company_id, $is_ultimate)
    {
        $this->forced_company_id = (int) $forced_company_id;
        $this->is_ultimate = $is_ultimate;
    }

    /**
     * Executes auth
     *
     * @param \Tygh\Addons\CommerceML\Commands\AuthCommand $command Command instance
     *
     * @return \Tygh\Common\OperationResult
     */
    public function handle(AuthCommand $command)
    {
        $result = new OperationResult();

        $data = [
            'user_login' => $command->auth_login,
        ];

        list(, $user_data, $user_login, , $salt) = fn_auth_routines($data, []);

        if (
            $command->auth_login !== $user_login
            || empty($user_data['password'])
            || !fn_user_password_verify((int) $user_data['user_id'], $command->auth_password, (string) $user_data['password'], $salt)
        ) {
            $result->addError('login_error', 'Error in user login or password');
            return $result;
        }

        if (!fn_check_user_access($user_data['user_id'], $this->permission)) {
            $result->addError('privileges_error', 'Privileges for user not set');
            return $result;
        }

        $result->setData($user_data);

        $result->setSuccess(true);

        return $result;
    }
}
