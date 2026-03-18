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

namespace Tygh\Addons\ProductVariations\Request;

use Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection;

class GenerateProductsAndCreateGroupRequest extends ABaseGenerateProductsRequest
{
    /**
     * @var null|\Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection
     */
    protected $group_features = null;

    /**
     * GenerateProductsAndCreateVariationGroupCommand constructor.
     *
     * @param int                                                                      $base_product_id
     * @param array<int, string>                                                       $combination_ids
     * @param array<string, array>                                                     $combinations_data
     * @param \Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection|null $group_features
     */
    public function __construct(
        $base_product_id,
        array $combinations_data,
        GroupFeatureCollection $group_features = null
    ) {
        $this->group_features = $group_features;

        parent::__construct($base_product_id, $combinations_data);
    }

    /**
     * @return \Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection|null
     */
    public function getGroupFeatures()
    {
        return $this->group_features;
    }

    public static function create($base_product_id, array $combination_ids, GroupFeatureCollection $group_features = null)
    {
        $self = new self($base_product_id, [], $group_features);
        $self->setCombinationIds($combination_ids);

        return $self;
    }
}
