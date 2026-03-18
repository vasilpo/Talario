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

namespace Tygh\Addons\YandexCheckout\HookHandlers;

class CompaniesHookHandler
{
    /**
     * The "get_company_data" hook handler.
     *
     * Actions performed:
     *     - Adds Yandex required info into a list of fetched fields.
     *
     * @see \fn_get_company_data()
     */
    public function onGetCompanyData($company_id, $lang_code, $extra, &$fields, $join, $condition)
    {
        $fields[] = db_quote('yandex_checkout_shopid');
        $fields[] = db_quote('agent_type');
        $fields[] = db_quote('tax_number');
        $fields[] = db_quote('yandex_tax_code');
    }
}