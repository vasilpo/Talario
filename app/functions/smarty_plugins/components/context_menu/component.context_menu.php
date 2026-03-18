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

use Smarty\Template;
use Tygh\ContextMenu\ContextMenu;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array<string, string> $params   Block params
 * @param string                $content  Block content
 * @param Template              $template Smarty template
 *
 * @return string
 */
function smarty_component_context_menu_context_menu(array $params, $content, Template $template)
{
    if (!isset($params['object'])) {
        return false;
    }

    $object = $params['object'];
    $schema = fn_get_schema('context_menu', $object);

    if (!$schema) {
        return false;
    }

    $request = $_REQUEST;
    $auth = Tygh::$app['session']['auth'];
    $runtime = Registry::get('runtime');

    $context_menu = ContextMenu::createFromSchema($schema);

    $template->assign(
        [
            'status_selector'    => $context_menu->getStatusSelector(),
            'context_menu_items' => $context_menu->getAvailableItems($request, $auth, $runtime),
            'params'             => $params,
        ]
    );

    /** @var \Tygh\SmartyEngine\Core $smarty */
    $smarty = $template->getSmarty();

    return $smarty->fetch($context_menu->getTemplate(), null, null, $template);
}
