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
 * Class PropertyDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class PropertyDto
{
    /**
     * @var string Property ID (short_name, variation_code, etc)
     */
    public $property_id;

    /**
     * @var string|float|null|bool|\Tygh\Addons\CommerceML\Dto\ProductPropertyValue
     */
    public $value;

    /**
     * Creates property object
     *
     * @param string                                                                  $property_id Property ID (short_name, variation_code, etc)
     * @param string|float|null|bool|\Tygh\Addons\CommerceML\Dto\ProductPropertyValue $value       Property value
     *
     * @return \Tygh\Addons\CommerceML\Dto\PropertyDto
     */
    public static function create($property_id, $value)
    {
        $object = new self();
        $object->property_id = (string) $property_id;
        $object->value = $value;

        return $object;
    }
}
