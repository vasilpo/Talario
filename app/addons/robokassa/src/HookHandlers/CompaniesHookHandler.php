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

namespace Tygh\Addons\Robokassa\HookHandlers;

use Tygh\Application;

/**
 * This class describes the hook handlers related to company management
 *
 * @package Tygh\Addons\Robokassa\HookHandlers
 */
class CompaniesHookHandler
{
    /** @var Application $application */
    protected $application;

    /**
     * CompaniesHookHandler constructor.
     *
     * @param Application $application Application
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * The "get_companies" hook handler.
     *
     * Actions performed:
     * - Adds field robokassa_store_id and robokassa_account_number to the list of retrieved fields.
     *
     * @param array<string, string|int> $params Companies search params
     * @param array<string>             $fields Fields that should be retrieved
     *
     * @return void
     *
     * @see fn_get_companies()
     */
    public function onGetCompanies($params, array &$fields)
    {
        $fields[] = db_quote('?:companies.robokassa_store_id');
        $fields[] = db_quote('?:companies.robokassa_account_number');
    }
}
