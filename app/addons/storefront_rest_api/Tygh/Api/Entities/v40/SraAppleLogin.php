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

namespace Tygh\Api\Entities\v40;

use AppleSignIn\ASDecoder;
use Tygh\Api\Response;
use Tygh\Addons\StorefrontRestApi\ASraEntity;
use AppleSignIn\Vendor\JWT;

class SraAppleLogin extends ASraEntity
{

    /** @inheritdoc */
    public function index($id = '', $params = [])
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        ];
    }

    /**
     * @inheritDoc
     */
    public function create($params)
    {
        $status = Response::STATUS_BAD_REQUEST;
        $data = [];
        $identity_token = $this->safeGet($params, 'identity_token', null);

        if (!$identity_token) {
            $data['message'] = __('api_required_field', [
                '[field]' => 'identity_token'
            ]);

            return [
                'status' => $status,
                'data' => $data
            ];
        }

        JWT::$leeway = 60;

        $apple_sign_in_payload = ASDecoder::getAppleSignInPayload($identity_token);

        if (!$apple_sign_in_payload) {
            return [
                'status' => $status,
                'data' => $data
            ];
        }

        $email = $apple_sign_in_payload->getEmail();

        list( , $user_data) = fn_auth_routines(['user_login' => $email], []);

        if (empty($user_data)) {
            $user_id = 0;
            $auth = $this->auth;
            $password = fn_generate_password();
            $new_user = [
                'email'      => $email,
                'user_login' => $email,
                'password1'  => $password,
                'password2'  => $password,
            ];
            list($user_id, ) = fn_update_user($user_id, $new_user, $auth, false, false);

            if ($user_id) {
                list( , $user_data) = fn_auth_routines(['user_login' => $email], []);
            }
        }

        if (!empty($user_data)) {
            list($token, $expiry_time) = fn_get_user_auth_token($user_data['user_id']);

            $status = Response::STATUS_CREATED;
            $data = [
                'token' => $token,
                'ttl'   => $expiry_time - TIME,
            ];
        }

        return [
            'status' => $status,
            'data' => $data
        ];
    }

    /**
     * @inheritDoc
     */
    public function update($id, $params)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        ];
    }

    /** @inheritdoc */
    public function privilegesCustomer()
    {
        return [
            'index'  => false,
            'create' => true,
            'update' => false,
            'delete' => false,
        ];
    }
}
