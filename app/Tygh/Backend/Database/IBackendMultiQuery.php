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

namespace Tygh\Backend\Database;

/**
 * Interface IBackendMultiQuery
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 */
interface IBackendMultiQuery
{
    /**
     * Executes a multi query
     *
     * @param array<array-key, string> $multi_query The multi query to execute
     *
     * @return bool
     */
    public function multiQuery(array $multi_query);

    /**
     * Get a multi-query result
     *
     * @return mixed
     */
    public function getMultiQueryResult();

    /**
     * Check whether there are more results available
     *
     * @return bool
     */
    public function hasMoreResults();

    /**
     * Traverse to the next result
     *
     * @return bool
     */
    public function nextResult();
}
