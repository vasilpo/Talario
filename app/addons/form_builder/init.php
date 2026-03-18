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

// form page type
define('PAGE_TYPE_FORM', 'F');

// Form element types
define('FORM_CHECKBOX', 'C');
define('FORM_HEADER', 'H');
define('FORM_INPUT', 'I');
define('FORM_MULTIPLE_CB', 'N');
define('FORM_MULTIPLE_SB', 'M');
define('FORM_RADIO', 'R');
define('FORM_SELECT', 'S');
define('FORM_SEPARATOR', 'D');
define('FORM_TEXTAREA', 'T');
define('FORM_DATE', 'V');
define('FORM_EMAIL', 'Y');
define('FORM_NUMBER', 'Z');
define('FORM_PHONE', 'P');
define('FORM_COUNTRIES', 'X');
define('FORM_STATES', 'W');
define('FORM_FILE', 'F');
define('FORM_REFERER', 'A');
define('FORM_IP_ADDRESS', 'B');

// Special types
define('FORM_VARIANT', 'G');
define('FORM_RECIPIENT', 'J');
define('FORM_SUBMIT', 'L');
define('FORM_SUBJECT', 'E');
define('FORM_SUBJECT_TEXT', 'K');

fn_register_hooks(
    'delete_page',
    'update_page_post',
    'get_page_data',
    'page_object_by_type',
    'clone_page',
    'settings_variants_image_verification_use_for'
);
