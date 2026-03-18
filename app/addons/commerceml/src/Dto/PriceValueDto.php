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
 * Class PriceValueDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class PriceValueDto
{
    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $price_type_id;

    /**
     * @var float
     */
    public $price;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto|null
     */
    public $currency_code;

    /**
     * @param \Tygh\Addons\CommerceML\Dto\IdDto      $price_type_id Price type object instance
     * @param float                                  $price         Price
     * @param \Tygh\Addons\CommerceML\Dto\IdDto|null $currency_code Currency code
     *
     * @return \Tygh\Addons\CommerceML\Dto\PriceValueDto
     */
    public static function create(IdDto $price_type_id, $price, IdDto $currency_code = null)
    {
        $object = new self();
        $object->price_type_id = $price_type_id;
        $object->price = (float) $price;
        $object->currency_code = $currency_code;

        return $object;
    }
}
