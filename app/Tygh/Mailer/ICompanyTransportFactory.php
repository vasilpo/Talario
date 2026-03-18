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


namespace Tygh\Mailer;

/**
 * The interface of the class responsible for the creation of transport object by company_id.
 * Needed for backward compatibility.
 * 
 * @package Tygh\Mailer
 */
interface ICompanyTransportFactory
{
    /**
     * Create transport instance by company identifier
     *
     * @param   int    $company_id  Сompany identifier
     *
     * @return ITransport
     */
    public function createTransportByCompanyId($company_id);
}