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

use Tygh\BlockManager\Block;
use Tygh\BlockManager\Grid;
use Tygh\BlockManager\RenderManager;

define('AREA', 'C');

// In case of this file is symlinked to the CS-Cart installation directory,
// there will be no 'init.php' file at the __DIR__ directory.
$root_dir = __DIR__;
if (isset($_SERVER['SCRIPT_FILENAME']) && dirname($_SERVER['SCRIPT_FILENAME']) !== $root_dir) {
    $root_dir = dirname($_SERVER['SCRIPT_FILENAME']);
}


if (
    isset(
        $_REQUEST['block_id'],
        $_REQUEST['snapping_id'],
        $_REQUEST['lang_code'],
        $_REQUEST['requested_uri']
    )
) {
    $block_id = (int) $_REQUEST['block_id'];
    $snapping_id = (int) $_REQUEST['snapping_id'];
    $lang_code = $_REQUEST['lang_code'];

    $requested_uri = rawurldecode($_REQUEST['requested_uri']);

    $_SERVER['QUERY_STRING'] = parse_url($requested_uri, PHP_URL_QUERY);
    $_SERVER['REQUEST_URI'] = $requested_uri;
    $_SERVER['SCRIPT_NAME'] = 'index.php';

    unset($_REQUEST['block_id'], $_REQUEST['snapping_id'], $_REQUEST['lang_code'], $_REQUEST['requested_uri']);
    unset($_GET['block_id'], $_GET['snapping_id'], $_GET['lang_code'], $_GET['requested_uri']);

    if ($_SERVER['QUERY_STRING']) {
        parse_str($_SERVER['QUERY_STRING'], $vars);
        $_REQUEST = $_GET = $vars;
    }

    require_once $root_dir . '/init.php';

    defined('BOOTSTRAP') or die('Access denied');

    $block = Block::instance()->getById($block_id, $snapping_id, [], $lang_code);
    $parent_grid = Grid::getById($block['grid_id'], $lang_code);

    $content = RenderManager::renderBlock($block, $parent_grid, 'C', [
        'esi_enabled' => false,
        'use_cache' => false,
        'parse_js' => false,
    ]);

    header('Content-Type: text/html');
    header('X-ESI-Response: true');

    echo $content;

    exit;
}
