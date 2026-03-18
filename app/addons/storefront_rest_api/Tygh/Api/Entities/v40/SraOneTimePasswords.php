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

use Tygh\Addons\StorefrontRestApi\ASraEntity;
use Tygh\Api\Response;

class SraOneTimePasswords extends ASraEntity
{
    /** @inheritDoc */
    public function index($id = '', $params = [])
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /** @inheritDoc */
    public function create($params)
    {
        $email = $this->safeGet($params, 'email', null);
        if (!$email) {
            return [
                'status' => Response::STATUS_BAD_REQUEST,
                'data'   => [
                    'errors' => [
                        __(
                            'api_required_field',
                            [
                                '[field]' => 'email',
                            ]
                        ),
                    ],
                ],
            ];
        }

        $user_id = (int) fn_is_user_exists(0, ['email' => $email]);
        if (!$user_id) {
            return [
                'status' => Response::STATUS_NOT_FOUND,
            ];
        }

        fn_user_send_otp($user_id);

        return [
            'status' => Response::STATUS_CREATED,
        ];
    }

    /** @inheritDoc */
    public function update($id, $params)
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /** @inheritDoc */
    public function delete($id)
    {
        return [
            'status' => Response::STATUS_FORBIDDEN,
        ];
    }

    /** @inheritDoc */
    public function privileges()
    {
        return [
            'index'  => false,
            'create' => true,
            'update' => false,
            'delete' => false,
        ];
    }

    /** @inheritDoc */
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
