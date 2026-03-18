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

class Versions extends AEntity
{
    /**
     * @param string        $id     Entity id
     * @param array<string> $params Request params
     *
     * @return array{status: int, data: string[]}
     */
    // phpcs:ignore
    public function index($id = '', $params = [])
    {
        return [
            'status' => Response::STATUS_OK,
            'data'   => [
                '1.0',
                '2.0',
                '2.1',
                '4.0',
                '4.1'
            ]
        ];
    }

    /**
     * @param array<string> $params Request params
     *
     * @return array{status: int, data: array<void>}
     */
    // phpcs:ignore
    public function create($params)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data'   => []
        ];
    }

    /**
     * @param int           $id     Entity id
     * @param array<string> $params Request params
     *
     * @return array{status: int, data: array<void>}
     */
    // phpcs:ignore
    public function update($id, $params)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data'   => []
        ];
    }

    /**
     * @param int $id Entity id
     *
     * @return array{status: int, data: array<void>}
     */
    public function delete($id)
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
            'data'   => []
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function privilegesCustomer()
    {
        return [
            'index'  => true,
            'create' => false,
            'update' => false,
            'delete' => false,
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function privileges()
    {
        return [
            'index'  => true,
            'create' => false,
            'update' => false,
            'delete' => false,
        ];
    }
}
