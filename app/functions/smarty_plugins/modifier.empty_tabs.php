<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier<br>
 * Name:     empty_tabs<br>
 * Purpose:  find ids of empty tabs
 * Example:  {$a|empty_tabs}
 * -------------------------------------------------------------
 */
/**
 * Find ids of empty tabs
 *
 * @param string                                   $content Tabs content
 * @param array<string, array<string, int|string>> $tabs    Optional navigation tabs
 *
 * @return array<int, string>
 */
function smarty_modifier_empty_tabs($content, $tabs = [])
{
    if (!empty($content)) {
        preg_match_all('/\<div( class="[\w\- ]*")* id="([\w]*)"( class="[\w\- ]*")*>[\n\r\t ]*(\<\!--([\w]*)--\>)?[\n\r\t ]*\<\/div>/is', $content, $matches);

        if (!empty($matches[2])) {
            $tab_ids = array_map('smarty_change_tab_id', $matches[2]);

            // Exclude tabs that load via Ajax
            if (!empty($tabs)) {
                foreach ($tab_ids as $tab_id_key => $tab_id) {
                    if (!empty($tabs[$tab_id]) && $tabs[$tab_id]['ajax']) {
                        unset($tab_ids[$tab_id_key]);
                    }
                }
            }

            return $tab_ids;
        }
    }

    return array();
}

function smarty_change_tab_id($str)
{
    return substr($str, strpos($str, '_') + 1);
}

/* vim: set expandtab: */
