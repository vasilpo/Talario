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

namespace Tygh\Addons\VendorDataPremoderation;

/**
 * Interface PremoderationSettingsInterface describes a structure of the premoderation settings storage.
 *
 * @package Tygh\Addons\VendorDataPremoderation
 */
interface PremoderationSettingsInterface
{
    const SOURCE_FIELD_SEPARATOR = ':';

    const ALL_FIELDS_SELECTOR = '*';

    /**
     * Checks whether modified data source must be premoderated.
     *
     * @param string $source_name
     *
     * @return bool
     */
    public function getSourcePremoderation($source_name);

    /**
     * Checks whether modified source data field must be premoderated.
     *
     * @param string $source_name
     * @param string $field_name
     *
     * @return bool
     */
    public function getFieldPremoderation($source_name, $field_name);
}
