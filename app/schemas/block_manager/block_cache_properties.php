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

// Global block cache parameters,
// which are applied to every block type that should be cached
$schema = array(

    // Cache of an every block should be deleted in case of:
    'update_handlers' => array(

        // Any add-on was installed/removed/enabled/disabled
        'addons',

        // Store settings were changed
        'settings_objects',
        'settings_vendor_values',

        // Blocks were modified
        'bm_blocks',
        'bm_blocks_descriptions',
        'bm_blocks_content',
        'bm_block_statuses',
        'bm_snapping',

        // The anguages were installed or removed
        'languages',

        // Language values were modified
        'language_values',

        // Promotions were modified
        'promotions',
    ),

    'request_handlers' => array(),
    'session_handlers' => array(),
    'cookie_handlers' => array(),
    'auth_handlers' => array(),
    'callable_handlers' => array(),
);

if (fn_allowed_for('ULTIMATE')) {
    // Very common block cache dependency
    $schema['update_handlers'][] = 'ult_objects_sharing';
}

return $schema;