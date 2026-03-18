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


namespace Tygh\Addons\CommerceML\Dto;


/**
 * Class OrderDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class OrderDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'order';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $status;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\OrderProductDto[]
     */
    public $products;

    /**
     * @var float
     */
    public $subtotal;

    /**
     * @var float
     */
    public $subtotal_discount;

    /**
     * @var float
     */
    public $shipping_cost;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;

    /**
     * @var int
     */
    public $updated_at = 0;

    /**
     * PriceTypeDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }
}
