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


namespace Tygh\Template;

/**
 * The interface for the context of documents and snippets.
 *
 * @package Tygh\Template
 */
interface IContext
{
    /**
     * Gets language code.
     * 
     * @return string
     */
    public function getLangCode();

    /**
     * Get language direction.
     *
     * @return string Language direction
     */
    public function getLanguageDirection();

    /**
     * Gets area.
     *
     * @return string Area identifier.
     */
    public function getArea();
}
