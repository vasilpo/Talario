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
 * Interface RepresentEntityDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
interface RepresentEntityDto
{
    /**
     * Gets represented type of entity (product, product_feature, category, etc)
     *
     * @return string
     */
    public function getEntityType();

    /**
     * Gets entity ID
     *
     * @return \Tygh\Addons\CommerceML\Dto\IdDto
     */
    public function getEntityId();

    /**
     * Gets entity name
     *
     * @return string
     */
    public function getEntityName();
}
