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
 * Class ProductFeatureSelectedValueDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class ProductFeatureSelectedValueDto
{
    /**
     * @var string|null
     */
    public $value;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto|null
     */
    public $value_id;

    /**
     * ProductFeatureSelectedValueDto constructor.
     *
     * @param string|null                            $value    Feature value
     * @param \Tygh\Addons\CommerceML\Dto\IdDto|null $value_id Feature value variant id
     */
    public function __construct($value = null, IdDto $value_id = null)
    {
        $this->value = $value;
        $this->value_id = $value_id;
    }

    /**
     * @param string|null                            $value    Feature value
     * @param \Tygh\Addons\CommerceML\Dto\IdDto|null $value_id Feature value variant id
     *
     * @return \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDto
     */
    public static function create($value = null, IdDto $value_id = null)
    {
        return new self($value, $value_id);
    }
}
