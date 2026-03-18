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
 * Class ImportStatuses contains import statuses.
 *
 * @package Tygh\Enum\Addons\Searchanise
 */
class ImportStatuses
{
    public const NONE       = 'none';
    public const QUEUED     = 'queued';
    public const PROCESSING = 'processing';
    public const SENT       = 'sent';
    public const DONE       = 'done';
    public const ERROR      = 'sync_error';
    public const SUSPENDED  = 'suspended';
}
