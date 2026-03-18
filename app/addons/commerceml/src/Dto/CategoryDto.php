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
 * Class CategoryDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class CategoryDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'category';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\TranslatableValueDto|null
     */
    public $name;

    /**
     * @var string
     */
    public $full_name;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto|null
     */
    public $parent_id;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;

    /**
     * CategoryDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }
}
