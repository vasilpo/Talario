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
 * Class ProductFeatureDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class ProductFeatureDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'product_feature';

    const TYPE_STRING = 'string';

    const TYPE_NUMBER = 'digit';

    const TYPE_DATE_TIME = 'date_time';

    const TYPE_DIRECTORY = 'directory';

    const TYPE_EXTENDED = 'extended';

    const BRAND_EXTERNAL_ID = 'brand1c';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $name;

    /**
     * @var string (string, number, date_time, directory)
     */
    public $type;

    /**
     * @psalm-var array<array-key, ProductFeatureVariantDto>
     *
     * @var \Tygh\Addons\CommerceML\Dto\ProductFeatureVariantDto[]
     */
    public $variants = [];

    /**
     * @var bool
     */
    public $is_variants_processed = false;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;

    /**
     * @var bool
     */
    public $is_multiple = false;

    /**
     * ProductFeatureDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }
}
