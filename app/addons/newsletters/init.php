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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// types
define('NEWSLETTER_TYPE_NEWSLETTER', 'N');
define('NEWSLETTER_TYPE_TEMPLATE', 'T');
define('NEWSLETTER_TYPE_AUTORESPONDER', 'A');

fn_register_hooks(
    'get_predefined_statuses',
    'tools_update_status_before_query',
    'settings_variants_image_verification_use_for',
    ['newsletters_update_subscriptions_post', '', 'gdpr']
);
