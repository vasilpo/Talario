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

class GenerateProductsAndAttachToGroupRequest extends ABaseGenerateProductsRequest
{
    /**
     * @var int
     */
    protected $group_id = 0;

    /**
     * GenerateProductsAndAttachToVariationGroupCommand constructor.
     *
     * @param int      $group_id
     * @param int      $base_product_id
     * @param string[] $combination_ids
     * @param array    $combinations_data
     */
    public function __construct($group_id, $base_product_id, array $combinations_data)
    {
        $this->group_id = (int) $group_id;

        parent::__construct($base_product_id, $combinations_data);
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    public static function create($group_id, $base_product_id, array $combination_ids)
    {
        $self = new self($group_id, $base_product_id, []);
        $self->setCombinationIds($combination_ids);

        return $self;
    }
}