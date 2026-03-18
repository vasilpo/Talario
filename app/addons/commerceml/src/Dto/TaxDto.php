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
 * Class TaxDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
class TaxDto implements RepresentEntityDto
{
    use RepresentEntitDtoTrait;

    const REPRESENT_ENTITY_TYPE = 'tax';

    /**
     * @var \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \Tygh\Addons\CommerceML\Dto\PropertyDtoCollection
     */
    public $properties;

    /**
     * TaxDto constructor.
     */
    public function __construct()
    {
        $this->properties = new PropertyDtoCollection();
    }

    /**
     * Creates tax instance
     *
     * @param \Tygh\Addons\CommerceML\Dto\IdDto $id   Tax ID
     * @param string                            $name Tax name
     *
     * @return \Tygh\Addons\CommerceML\Dto\TaxDto
     */
    public static function create(IdDto $id, $name)
    {
        $self = new self();

        $self->id = $id;
        $self->name = (string) $name;

        return $self;
    }
}
