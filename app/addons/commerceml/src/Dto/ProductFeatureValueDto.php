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
 * Class ProductFeatureValueDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class ProductFeatureValueDto
{
    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $feature_id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDtoCollection
     */
    public $selected_values;

    /**
     * ProductFeatureValueDto constructor.
     *
     * @param \Tygh\Addons\CommerceML\Dto\IdDto                                    $feature_id      Freature ID
     * @param \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDtoCollection $selected_values Selected feature values
     */
    public function __construct(IdDto $feature_id, ProductFeatureSelectedValueDtoCollection $selected_values)
    {
        $this->feature_id = $feature_id;
        $this->selected_values = $selected_values;
    }

    /**
     * @param \Tygh\Addons\CommerceML\Dto\IdDto                                    $feature_id      Freature ID
     * @param \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDtoCollection $selected_values Selected feature values
     *
     * @return \Tygh\Addons\CommerceML\Dto\ProductFeatureValueDto
     */
    public static function create(IdDto $feature_id, ProductFeatureSelectedValueDtoCollection $selected_values)
    {
        return new self($feature_id, $selected_values);
    }
}
