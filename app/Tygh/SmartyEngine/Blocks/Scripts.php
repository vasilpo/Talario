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

use JShrink\Minifier;
use Smarty\Template;
use Tygh;
use Tygh\Development;
use Tygh\Registry;
use Tygh\Storage;
use function Tygh\SmartyEngine\Plugins\Blocks\jsmin;

class Scripts extends InlineScript
{
    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param string   $content  Content
     * @param Template $template Template
     * @param bool     $repeat   Repeat
     *
     * @return string|void
     *
     * @throws \Exception Exception.
     */
    //phpcs:ignore
    public function handle($params, $content, Template $template, &$repeat)
    {
        if ($repeat) {
            Registry::set('runtime.inside_scripts', 1);

            return;
        }

        if (Registry::get('config.tweaks.dev_js')) {
            $content .= $this->inlineScripts($params, $content, $template, $repeat);

            return $content;
        }

        $scripts = [];
        $external_scripts = [];
        $dir_root = Registry::get('config.dir.root');
        $return = '';
        $current_location = Registry::get('config.current_location');

        if (preg_match_all('/\<script(.*?)\>(.*?)\<\/script\>/s', $content, $m)) {
            $contents = '';

            foreach ($m[1] as $src) {
                if (!empty($src) && preg_match('/src ?= ?"([^"]+)"/', $src, $_m)) {
                    if (strpos($_m[1], $current_location) !== false) {
                        $scripts[] = str_replace($current_location, '', preg_replace('/\?.*?$/', '', $_m[1]));
                    } else {
                        $external_scripts[] = $_m[1];
                    }
                }
            }

            // Check file changes in dev mode
            $names = $scripts;
            if (Development::isEnabled('compile_check')) {
                foreach ($names as $index => $name) {
                    if (is_file($dir_root . '/' . $name)) {
                        $names[$index] .= filemtime($dir_root . '/' . $name);
                    }
                }
            }

            $filename = 'js/tygh/scripts-' . md5(implode(',', $names)) . fn_get_storage_data('cache_id') . '.js';
            $file_exists = Storage::instance('assets')->isExist($filename);

            if (!$file_exists) {
                /** @var \Tygh\Lock\Factory $lock_factory */
                $lock_factory = Tygh::$app['lock.factory'];

                $lock = $lock_factory->createLock($filename);

                if (!$lock->acquire() && $lock->wait()) {
                    $file_exists = Storage::instance('assets')->isExist($filename);
                }
            }

            if (!$file_exists) {
                foreach ($scripts as $src) {
                    $contents .= fn_get_contents(Registry::get('config.dir.root') . $src);
                }

                $contents = str_replace('[files]', implode("\n", $scripts), Registry::get('config.js_css_cache_msg')) . $contents;

                if (function_exists('jsmin')) {
                    $contents = jsmin($contents);
                } else {
                    $contents = Minifier::minify($contents, [
                        'flaggedComments' => false
                    ]);
                }

                Storage::instance('assets')->put($filename, [
                    'contents' => $contents,
                    'compress' => false,
                    'caching'  => true
                ]);

                if (isset($lock)) {
                    $lock->release();
                }
            }

            $return = '<script src="' .
                Storage::instance('assets')->getUrl($filename) .
                '?' . fn_get_storage_data('cache_id') .
                '"></script>' . "\n";

            if (!empty($external_scripts)) {
                foreach ($external_scripts as $sc) {
                    $return .= '<script src="' . $sc . '"></script>' . "\n";
                }
            }

            foreach ($m[2] as $sc) {
                if (!empty($sc)) {
                    $return .= '<script>' . $sc . '</script>' . "\n";
                }
            }
        }

        return $return . $this->inlineScripts($params, $content, $template, $repeat);
    }

    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param string   $content  Content
     * @param Template $template Template
     * @param bool     $repeat   Repeat
     */
    public function inlineScripts(array $params, $content, Template &$template, &$repeat): string
    {
        Registry::del('runtime.inside_scripts');
        // Get inline scripts
        $repeat = false;

        $inline_scripts = "\n\n<!-- Inline scripts -->\n" . parent::handle(['output' => true], '', $template, $repeat);

        // FIXME: Backward compatibility. If {scripts} included at the TOP of the page, do not grab inline scripts.
        Registry::set('runtime.inside_scripts', 1);

        return $inline_scripts;
    }

    /**
     * @return true
     */
    public function isCacheable(): bool
    {
        return true;
    }
}
