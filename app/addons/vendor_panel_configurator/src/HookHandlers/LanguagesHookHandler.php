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

namespace Tygh\Addons\VendorPanelConfigurator\HookHandlers;

class LanguagesHookHandler
{
    /**
     * The "init_language_post" hook handler
     *
     * Actions performed:
     *     - Сhanges the language of the product description according to the selected interface language
     *
     * @param array<string, int|string|array> $params                   Request parameters
     * @param string                          $area                     Site area identifer (A for the admininstration panel, C for storefront)
     * @param string                          $default_language         Two-letter default language code
     * @param string                          $session_display_language Display language stored in the session
     * @param array<string, int|string|array> $avail_languages          List of available languages
     * @param string                          $display_language         Display language
     * @param string                          $description_language     Description language
     * @param string                          $browser_language         Browser language
     *
     * @return void
     *
     * @param-out array<array-key, mixed>|int|string $description_language
     *
     * @see \fn_init_language()
     */
    public function onInitLanguagePost($params, $area, $default_language, $session_display_language, $avail_languages, $display_language, &$description_language, $browser_language)
    {
        if (
            ACCOUNT_TYPE !== 'vendor'
            || empty($params['sl'])
        ) {
            return;
        }

        $description_language = $display_language;
    }
}
