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

namespace Tygh\Enum\Addons\Searchanise;

/**
 * Class ServerErrors contains returned serachanise server errors.
 *
 * @package Tygh\Enum\Addons\Searchanise
 */
class ServerErrors
{
    public const EMPTY_API_KEY                   = 'EMPTY_API_KEY';
    public const INVALID_API_KEY                 = 'INVALID_API_KEY';
    public const TO_BIG_START_INDEX              = 'TO_BIG_START_INDEX';
    public const SEARCH_DATA_NOT_IMPORTED        = 'SEARCH_DATA_NOT_IMPORTED';
    public const FULL_IMPORT_PROCESSED           = 'FULL_IMPORT_PROCESSED';
    public const FACET_ERROR_TOO_MANY_ATTRIBUTES = 'FACET_ERROR_TOO_MANY_ATTRIBUTES';
    public const NEED_RESYNC_YOUR_CATALOG        = 'NEED_RESYNC_YOUR_CATALOG';
    public const ENGINE_SUSPENDED                = 'ENGINE_SUSPENDED';
}
