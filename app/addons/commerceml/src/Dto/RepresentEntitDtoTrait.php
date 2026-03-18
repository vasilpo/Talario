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
 * Trait RepresentEntitDtoTrait
 *
 * @property \Tygh\Addons\CommerceML\Dto\IdDto $id
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
trait RepresentEntitDtoTrait
{
    /**
     * Gets type of entity (product, product_feature, category, etc)
     *
     * @return string
     */
    public function getEntityType()
    {
        return static::REPRESENT_ENTITY_TYPE;
    }

    /**
     * Gets entity ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public function getEntityId()
    {
        return $this->id;
    }

    /**
     * Gets entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        $name = isset($this->name) ? $this->name : '';
        $name = isset($this->full_name) ? $this->full_name : $name;

        if ($name instanceof TranslatableValueDto) {
            return $name->default_value;
        }

        return (string) $name;
    }
}
