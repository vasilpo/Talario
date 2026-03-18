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

use Tygh\Addons\CommerceML\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Gets last sync information
 *
 * @param string $provider_id Provider identifier
 * @param int    $company_id  Company identifier
 *
 * @return array{status: string, last_sync_timestamp: int, log_file_url: string, status_code?: string}
 */
function fn_sync_data_commerceml_get_last_sync_info($provider_id, $company_id)
{
    return ServiceProvider::getLastSyncInfo(['company_id' => $company_id]);
}
