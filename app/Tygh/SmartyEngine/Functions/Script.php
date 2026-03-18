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

namespace Tygh\SmartyEngine\Functions;

use Smarty\FunctionHandler\Base;
use Smarty\Template;
use Tygh;
use Tygh\Registry;
use Tygh\SmartyEngine\Blocks\InlineScript;
use Tygh\Themes\Themes;

class Script extends Base
{
    /**
     * Converts array to html hidden fields
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string|void
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        static $scripts = [];

        if (isset($scripts[$params['src']])) {
            return;
        }

        if (strpos($params['src'], '//') === false) {
            /** @var Tygh\SmartyEngine\Core $smarty */
            $smarty = $template->getSmarty();
            // load missing js from the parent theme
            if ($file = $smarty->theme->getContentPath(DIR_ROOT . '/' . $params['src'])) {
                $params['src'] = $file[Themes::PATH_RELATIVE];
            }
            $src = Registry::get('config.current_location') . '/' . fn_link_attach($params['src'], 'ver=' . Tygh::$app['assets_cache_key']);
        } else {
            $src = $params['src'];
        }

        $scripts[$params['src']] = '<script'
            . (!empty($params['class']) ? ' class="' . $params['class'] . '" ' : '')
            . (!empty($params['async']) ? ' async ' : '')
            . (!empty($params['defer']) ? ' defer ' : '')
            . ' src="' . $src . '" ' . (isset($params['charset']) ? ('charset="' . $params['charset'] . '"') : '')
            . (isset($params['escape']) ? '><\/script>' : '></script>');

        /**
         * Allows you to apply additional attributes to the script
         *
         * @param array  $scripts List of scripts
         * @param array  $params  Script parameters and attributes
         * @param string $src     Script path
         */
        fn_set_hook('smarty_function_script_after_formation', $scripts, $params, $src);

        if (defined('AJAX_REQUEST') || Registry::get('runtime.inside_scripts')) {
            return $scripts[$params['src']];
        }

        if (isset($params['no-defer']) && $params['no-defer']) {
            return $scripts[$params['src']];
        }

        $cache_name = $template->getTemplateVars('block_cache_name');
        if (!empty($cache_name)) {
            $cached_content = Registry::get($cache_name);
            if (!isset($cached_content['javascript'])) {
                $cached_content['javascript'] = '';
            }
            $cached_content['javascript'] .= $scripts[$params['src']];

            Registry::set($cache_name, $cached_content, true);
        }
        $repeat = false;
        $inline_script = new InlineScript();
        $inline_script->handle([], $scripts[$params['src']], $template, $repeat);

        return '<!-- Inline script moved to the bottom of the page -->';
    }
}
