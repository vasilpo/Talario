<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Enum\SiteArea;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Tracks product search analytics for storefront search requests with a text query.
 *
 * @param array       $products  Found products
 * @param array       $params    Product search params
 * @param string|null $lang_code Language code
 *
 * @return void
 */
function fn_lr_search_analytics_get_products_post(array &$products, array &$params, &$lang_code)
{
    if (!fn_lr_search_analytics_is_trackable_search($params)) {
        return;
    }

    $query = fn_lr_search_analytics_prepare_query((string) $params['q']);
    if ($query === '') {
        return;
    }

    $search_count = isset($params['total_items']) ? (int) $params['total_items'] : count($products);

    fn_lr_search_analytics_track_query($query, $search_count === 0);
}

/**
 * Checks whether the current product loading context is a storefront text search.
 *
 * @param array $params Product search params
 *
 * @return bool
 */
function fn_lr_search_analytics_is_trackable_search(array $params)
{
    if (empty($params['q'])) {
        return false;
    }

    if (($params['area'] ?? AREA) !== SiteArea::STOREFRONT) {
        return false;
    }

    if (($params['dispatch'] ?? '') !== 'products.search') {
        return false;
    }

    if (empty($params['search_performed'])) {
        return false;
    }

    return true;
}

/**
 * Normalizes a search query before aggregation.
 *
 * @param string $query Search query
 *
 * @return string
 */
function fn_lr_search_analytics_prepare_query($query)
{
    return mb_strtolower(trim($query), 'UTF-8');
}

/**
 * Saves aggregated analytics for a search query.
 *
 * @param string $query        Normalized query
 * @param bool   $is_not_found Whether the search returned zero items
 *
 * @return void
 */
function fn_lr_search_analytics_track_query($query, $is_not_found)
{
    $existing_record = db_get_row(
        'SELECT search_analytics_id, search_count, not_found_count'
        . ' FROM ?:lr_search_analytics'
        . ' WHERE query = ?s',
        $query
    );

    if ($existing_record) {
        db_query(
            'UPDATE ?:lr_search_analytics'
            . ' SET search_count = ?i, not_found_count = ?i, last_searched_at = ?i'
            . ' WHERE search_analytics_id = ?i',
            (int) $existing_record['search_count'] + 1,
            (int) $existing_record['not_found_count'] + ($is_not_found ? 1 : 0),
            TIME,
            (int) $existing_record['search_analytics_id']
        );

        return;
    }

    db_query(
        'INSERT INTO ?:lr_search_analytics ?e',
        [
            'query'            => $query,
            'search_count'     => 1,
            'not_found_count'  => $is_not_found ? 1 : 0,
            'last_searched_at' => TIME,
        ]
    );
}

/**
 * Returns analytics rows for CSV export.
 *
 * @return array<int, array<string, int|string>>
 */
function fn_lr_search_analytics_get_report_rows()
{
    return db_get_array(
        'SELECT query, search_count, not_found_count, last_searched_at'
        . ' FROM ?:lr_search_analytics'
        . ' ORDER BY search_count DESC, query ASC'
    );
}

/**
 * Streams analytics report as CSV.
 *
 * @return void
 */
function fn_lr_search_analytics_export_report()
{
    $rows = fn_lr_search_analytics_get_report_rows();
    $file_name = 'search_analytics_report_' . date('Y-m-d_H-i-s') . '.csv';
    $stream = fopen('php://temp', 'w+');

    if ($stream === false) {
        return;
    }

    // Add UTF-8 BOM to keep Cyrillic readable in spreadsheet applications.
    fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($stream, [
        __('lr_search_analytics.report.query'),
        __('lr_search_analytics.report.search_count'),
        __('lr_search_analytics.report.not_found_count'),
        __('lr_search_analytics.report.last_searched_at'),
    ]);

    foreach ($rows as $row) {
        fputcsv($stream, [
            $row['query'],
            (int) $row['search_count'],
            (int) $row['not_found_count'],
            date('Y-m-d H:i:s', (int) $row['last_searched_at']),
        ]);
    }

    rewind($stream);

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    fpassthru($stream);
    fclose($stream);
    exit;
}
