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


namespace Tygh\Addons\Warehouses\CommerceML\Dto;


use Tygh\Addons\CommerceML\Dto\IdDto;

/**
 * Class ProductWarehouseQuantityDto
 *
 * @package Tygh\Warehouses\Addons\CommerceML\Dto
 */
class ProductWarehouseQuantityDto
{
    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $warehouse_id;

    /**
     * @var int
     */
    public $quantity;

    /**
     * ProductWarehouseQuantityDto constructor.
     *
     * @param \Tygh\Addons\CommerceML\Dto\IdDto $warehouse_id Warehouse ID
     * @param int                               $quantity     Warehouse quantity
     */
    public function __construct(IdDto $warehouse_id, $quantity = 0)
    {
        $this->warehouse_id = $warehouse_id;
        $this->quantity = $quantity;
    }

    /**
     * @param \Tygh\Addons\CommerceML\Dto\IdDto $warehouse_id Warehouse ID
     * @param int                               $quantity     Warehouse quantity
     *
     * @return \Tygh\Addons\Warehouses\CommerceML\Dto\ProductWarehouseQuantityDto
     */
    public static function create(IdDto $warehouse_id, $quantity = 0)
    {
        return new self($warehouse_id, $quantity);
    }
}
