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

use Tygh\Addons\ProductVariations\ServiceProvider;
use Tygh\Api\AEntity;
use Tygh\Api\Response;

class DetachProductVariation extends AEntity
{
    /**
     * @inheritDoc
     */
    public function index($id = '', $params = [])
    {
        return [
            'status' => Response::STATUS_METHOD_NOT_ALLOWED,
        ];
    }

    /**
     * @inheritDoc
     */
    public function create($params = [])
    {
        if ($this->getParentName() === ProductVariations::ENTITY_NAME) {
            $data = $this->getParentData();
            $params['product_id'] = $data['product_id'];
        }

        if (empty($params['product_id'])) {
            return [
                'status' => Response::STATUS_BAD_REQUEST
            ];
        }

        $service = ServiceProvider::getService();
        $group_repository = ServiceProvider::getGroupRepository();

        $group_id = $group_repository->findGroupIdByProductId($params['product_id']);

        $result = $service->detachProductFromGroup($group_id, $params['product_id']);

        if ($result->isSuccess()) {
            return [
                'status' => Response::STATUS_CREATED,
                'data'   => [
                    'message' => implode("\n", $result->getWarnings()),
                ],
            ];
        } else {
            return [
                'status' => Response::STATUS_BAD_REQUEST,
                'data' => [
                    'message' => implode("\n", $result->getErrors())
                ]
            ];
        }
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

    /**
     * @inheritDoc
     */
    public function privileges()
    {
        return [
            'create' => 'manage_catalog',
            'update' => 'manage_catalog',
            'delete' => 'manage_catalog',
            'index'  => 'view_catalog'
        ];
    }
}