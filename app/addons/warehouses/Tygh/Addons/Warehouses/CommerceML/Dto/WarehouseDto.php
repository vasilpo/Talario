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


use Tygh\Addons\CommerceML\Dto\RepresentEntityDto;
use Tygh\Addons\CommerceML\Dto\RepresentEntitDtoTrait;
use Tygh\Addons\CommerceML\Dto\PropertyDtoCollection;

/**
 * Class WarehouseDto
 *
 * @package Tygh\Addons\Warehouses\CommerceML\Dto
 */
class WarehouseDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'warehouse';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $name;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $city;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $address;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;


    /**
     * ProductFeatureDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }
}
