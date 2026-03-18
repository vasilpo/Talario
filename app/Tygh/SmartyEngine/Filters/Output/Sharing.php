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

namespace Tygh\SmartyEngine\Filters\Output;

use Smarty\Filter\FilterInterface;
use Smarty\Template;
use Tygh\Registry;

class Sharing implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * phpcs:ignore
     * @return array|mixed|string|string[]|null
     */
    public function filter($code, Template $template)
    {
        if (!fn_allowed_for('ULTIMATE')) {
            return $code;
        }

        if (Registry::get('runtime.simple_ultimate')) {
            return $code;
        }

        $sharing = Registry::get('sharing');
        $code_expr = '/<!--Content-->.*?<!--\/Content-->/is';

        if (defined('AJAX_REQUEST')) {
            $central_content = $code;
        } elseif (preg_match($code_expr, $code, $central_content)) {
            $central_content = $central_content[0];
        }

        if (!empty($central_content)) {
            if (!empty($sharing['tpl_tabs'])) {
                foreach ($sharing['tpl_tabs'] as $data) {
                    // Add a new tab
                    $tab_expr = '/(<div[^>]+?class[^>]*?tabs.*?>.*?<ul.*?>)(.*?)(<\/ul>)/is';
                    //phpcs:ignore
                    if (preg_match($tab_expr, $central_content, $matches)) {
                        if (!empty($matches[2])) {
                            // Add a new tab
                            $tab_content = $matches[1] . $matches[2] . '<li id="tab_share_object'
                                . $data['params']['object_id'] . '" class="cm-js cm-ajax"><a href="'
                                . fn_url('companies.get_object_share?object=' . $data['params']['object']
                                . '&object_id=' . $data['params']['object_id']) . '">' . __('storefronts')
                                . '</a></li>' . $matches[3];

                            $central_content = preg_replace($tab_expr, fn_preg_replacement_quote($tab_content), $central_content, 1);
                        }

                        // Get main form to add tab content inside.
                        $form_content_expr = '/<form.*?>.*?<\/form>/is';
                        //phpcs:ignore
                        if (preg_match($form_content_expr, $central_content, $matches)) {
                            $form = $matches[0];

                            // Add tab content
                            $tab_content_expr = '/<div[^>]+?id[^>]*?content_.*?>/is';
                            //phpcs:ignore
                            if (preg_match($tab_content_expr, $form, $tab_matches)) {
                                $tab_content = '<div class="cm-tabs-content hidden" id="content_tab_share_object'
                                    . $data['params']['object_id'] . '"></div>' . $tab_matches[0];

                                $form = preg_replace($tab_content_expr, fn_preg_replacement_quote($tab_content), $form, 1);
                                $central_content = preg_replace($form_content_expr, fn_preg_replacement_quote($form), $central_content, 1);
                            }
                        }
                    }
                }

                if (defined('AJAX_REQUEST')) {
                    $code = $central_content;
                } else {
                    $code = preg_replace($code_expr, fn_preg_replacement_quote($central_content), $code, 1);
                }
            }
        }

        return $code;
    }
}
