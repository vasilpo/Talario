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

namespace Tygh\SmartyEngine\Blocks;

use Smarty\BlockHandler\BlockHandlerInterface;
use Smarty\Template;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

class Hook implements BlockHandlerInterface
{
    /**
     * @param array{name: string} $params   Params
     * @param string              $content  Content
     * @param Template            $template Template
     * @param bool                $repeat   Repeat
     *
     * @return string
     */
    //phpcs:ignore
    public function handle($params, $content, Template $template, &$repeat)
    {
        static $overrides = [];
        static $addons = null;

        /** @var \Tygh\SmartyEngine\Core $global_smarty */
        $global_smarty = $template->getSmarty() ?: $template;

        $hook_content = '';
        $hook_name = 'thooks_' . $global_smarty->template_area;

        Registry::registerCache($hook_name, ['addons'], Registry::cacheLevel(['static', 'storefront']));
        $hooks = Registry::ifGet($hook_name, []);

        if (!isset($hooks[$params['name']])) {
            list($dir, $name) = explode(':', $params['name']);

            if ($addons === null) {
                $addons = Registry::get('addons');
            }

            $hook_exists = false;
            $hooks_list = [
                'pre'      => [],
                'post'     => [],
                'override' => []
            ];

            foreach ($addons as $addon => $data) {
                if ($data['status'] === ObjectStatuses::DISABLED) {
                    continue;
                }

                $files = [];

                if (!isset($addons[$addon]['has_extended_hooks'])) {
                    $addons[$addon]['has_extended_hooks'] = $global_smarty->templateDirExists('addons/' . $addon . '/addons/');
                }

                if ($addons[$addon]['has_extended_hooks']) {
                    foreach ($addons as $_addon => $_data) {
                        if ($_data['status'] === ObjectStatuses::DISABLED || $_addon === $addon) {
                            continue;
                        }

                        $files[] = 'addons/' . $addon . '/addons/' . $_addon . '/hooks/' . $dir . '/' . $name;
                    }
                }

                $files[] = 'addons/' . $addon . '/hooks/' . $dir . '/' . $name;

                foreach ($files as $file) {
                    $pre_file = $file . '.pre.tpl';
                    $post_file = $file . '.post.tpl';
                    $override_file = $file . '.override.tpl';

                    if ($global_smarty->templateExists($pre_file)) {
                        $hooks_list['pre'][] = $pre_file;
                        $hook_exists = true;
                    }
                    if ($global_smarty->templateExists($post_file)) {
                        $hooks_list['post'][] = $post_file;
                        $hook_exists = true;
                    }
                    //phpcs:ignore
                    if ($global_smarty->templateExists($override_file)) {
                        $hooks_list['override'][$override_file] = $override_file;
                        $hook_exists = true;
                    }
                }
            }

            if (!$hook_exists) {
                $hooks[$params['name']] = [];
            } else {
                $hooks[$params['name']] = $hooks_list;
            }

            Registry::set($hook_name, $hooks);
        }

        if ($content === null) {
            // reset override for current hook
            $overrides[$params['name']] = false;

            // override hook should be call for opened tag to prevent pre/post hook execution
            if (!empty($hooks[$params['name']]['override']) && !isset($hooks[$params['name']]['override'][$template->template_resource])) {
                foreach ($hooks[$params['name']]['override'] as $tpl) {
                    $_hook_content = $global_smarty->fetch($tpl, null, null, $template);
                    //phpcs:ignore
                    if (trim($_hook_content)) {
                        $overrides[$params['name']] = true;
                        $hook_content = $_hook_content;
                    }
                }
            }

            // prehook should be called for the opening {hook} tag to allow variables passed from hook to body
            if (empty($overrides[$params['name']]) && !empty($hooks[$params['name']]['pre'])) {
                foreach ($hooks[$params['name']]['pre'] as $tpl) {
                    $hook_content .= $global_smarty->fetch($tpl, null, null, $template);
                }
            }
        } elseif (empty($overrides[$params['name']])) {
            // post hook should be called only if override hook was no executed
            if (!empty($hooks[$params['name']]['post'])) {
                foreach ($hooks[$params['name']]['post'] as $tpl) {
                    $hook_content .= $global_smarty->fetch($tpl, null, null, $template);
                }
            }

            $hook_content =  $content . "\n" . $hook_content;
        }

        fn_set_hook('smarty_block_hook_post', $params, $content, $overrides, $template, $hook_content);

        return $hook_content;
    }

    /**
     * @return true
     */
    public function isCacheable(): bool
    {
        return true;
    }
}
