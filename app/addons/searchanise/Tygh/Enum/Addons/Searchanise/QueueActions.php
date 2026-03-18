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
 * Class QueueActions contains searchanise queue actions
 *
 * @package Tygh\Enum\Addons\Searchanise
 */
class QueueActions
{
    public const UPDATE_PRODUCTS     = 'update';
    public const UPDATE_CATEGORIES   = 'categories_update';
    public const UPDATE_PAGES        = 'pages_update';
    public const UPDATE_VENDORS      = 'vendors_update';
    public const UPDATE_FACETS       = 'facet_update';

    public const DELETE_PRODUCTS     = 'delete';
    public const DELETE_CATEGORIES   = 'categories_delete';
    public const DELETE_PAGES        = 'pages_delete';
    public const DELETE_VENDORS      = 'vendors_delete';
    public const DELETE_FACETS       = 'facet_delete';

    public const DELETE_PRODUCTS_ALL = 'delete_all';
    public const DELETE_FACETS_ALL   = 'facet_delete_all';

    public const PREPARE_FULL_IMPORT = 'prepare_full_import';
    public const START_FULL_IMPORT   = 'start_full_import';
    public const END_FULL_IMPORT     = 'end_full_import';
}
