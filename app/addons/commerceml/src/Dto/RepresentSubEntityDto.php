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
 * Interface RepresentSubEntityDto
 *
 * @package Tygh\Addons\CommerceML\Dto
 */
interface RepresentSubEntityDto
{
    /**
     * Gets parent type of entity (product, product_feature, category, etc)
     *
     * @return string
     */
    public static function getParentEntityType();

    /**
     * Gets parent entity ID
     *
     * @return string
     */
    public function getParentExternalId();
}
