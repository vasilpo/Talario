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

namespace Tygh\Api\Entities;

use Tygh\Api\AEntity;
use Tygh\Api\Response;

class AuthTokens extends AEntity
{
    public function index($id = '', $params = array())
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data' => array()
        );
    }

    /**
     * Creates or updates auth token for requests.
     *
     * @param array $params Request
     *
     * @return array Auth status and data. On success data contains auth token and token time-to-live in seconds.
     *               To determine token expiry time, add TTL to current timestamp.
     */
    public function create($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = array();

        $email = $this->safeGet($params, 'email', '');
        $password = $this->safeGet($params, 'password', '');

        if (!$email) {
            $data['message'] = __('api_required_field', array(
                '[field]' => 'email'
            ));
        } elseif (!$password) {
            $data['message'] = __('api_required_field', array(
                '[field]' => 'password'
            ));
        } else {
            $status = Response::STATUS_NOT_FOUND;

            list($user_exists, $user_data, $login, $password, $salt) = fn_auth_routines(
                array(
                    'user_login' => $email,
                    'password'   => $password,
                ),
                array()
            );

            if ($user_data && fn_user_password_verify((int) $user_data['user_id'], $password, (string) $user_data['password'], $salt)) {
                list($token, $expiry_time) = fn_get_user_auth_token($user_data['user_id']);

                $status = Response::STATUS_CREATED;
                $data = array(
                    'token' => $token,
                    'ttl'   => $expiry_time - TIME,
                );
            }
        }

        return array(
            'status' => $status,
            'data' => $data
        );
    }

    public function update($id, $params)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data' => array()
        );
    }

    public function delete($id)
    {
        return array(
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data' => array()
        );
    }

    public function privilegesCustomer()
    {
        return array(
            'index'  => false,
            'create' => true,
            'update' => false,
            'delete' => false,
        );
    }

    public function privileges()
    {
        return array(
            'index'  => false,
            'create' => true,
            'update' => false,
            'delete' => false,
        );
    }
}
