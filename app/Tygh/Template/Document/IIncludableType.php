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


namespace Tygh\Template\Document;

/**
 * The interface for the document type that allows to include the document into email notification templates.
 *
 * @package Tygh\Template\Document
 */
interface IIncludableType
{
    /**
     * Include document into email template.
     *
     * @param string $code      Template code.
     * @param string $lang_code Language code.
     * @param array $params     Including params.
     *
     * @return string
     */
    public function includeDocument($code, $lang_code, $params);
}