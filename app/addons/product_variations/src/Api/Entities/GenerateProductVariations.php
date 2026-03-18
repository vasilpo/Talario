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

class GenerateProductVariations extends AEntity
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
    public function create($params)
    {
        if ($this->getParentName() === ProductVariations::ENTITY_NAME) {
            $data = $this->getParentData();
            $params['product_id'] = $data['product_id'];
        }

        if (empty($params['product_id']) || empty($params['combinations'])) {
            return [
                'status' => Response::STATUS_BAD_REQUEST
            ];
        }

        $service = ServiceProvider::getService();
        $group_repository = ServiceProvider::getGroupRepository();
        $product_repository = ServiceProvider::getProductRepository();

        $combinations = (array) $this->safeGet($params, 'combinations', []);
        $combination_ids = [];

        foreach ($combinations as $variant_ids) {
            $combination_ids[] = $product_repository->generateCombinationId($variant_ids);
        }

        $group_id = $group_repository->findGroupIdByProductId($params['product_id']);

        if ($group_id) {
            $result = $service->generateProductsAndAttachToGroup($group_id, $params['product_id'], $combination_ids);
            $status = Response::STATUS_OK;
        } else {
            $features = $this->safeGet($params, 'features', []);

            if ($features) {
                $group_feature_collection = ProductVariationsGroups::convertFeaturesToFeatureCollection($features);
            } else {
                $group_feature_collection = null;
            }

            $result = $service->generateProductsAndCreateGroup(
                $params['product_id'],
                $combination_ids,
                $group_feature_collection
            );
            $status = Response::STATUS_CREATED;
        }

        return ProductVariationsGroups::convertSaveGroupResultToResponse($result, $status);
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
