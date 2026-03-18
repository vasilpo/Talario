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
use Tygh;
use Tygh\Exceptions\DeveloperException;

class Component implements BlockHandlerInterface
{
    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param string   $content  Content
     * @param Template $template Template
     * @param bool     $repeat   Repeat
     *
     * phpcs:ignore
     * @return mixed
     *
     * @throws DeveloperException Dev exception.
     */
    //phpcs:ignore
    public function handle($params, $content, Template $template, &$repeat)
    {
        if ($repeat === true) {
            return $content;
        }

        if (!isset($params['name'])) {
            throw new DeveloperException('Component must have name');
        }

        $function_name = $this->includeComponentFile($params['name']);

        unset($params['name']);

        return $function_name($params, $content, $template);
    }

    /**
     * @return true
     */
    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * Includes file with component function
     *
     * @param string $component_name Component name
     *
     * @return string Function name
     */
    public function includeComponentFile($component_name)
    {
        static $component_files;

        if ($component_files === null) {
            /** @var Tygh\SmartyEngine\Core $smarty */
            $smarty = Tygh::$app['view'];
            $func_read_dir = static function ($dir, $prefix = null) use (&$component_files, &$func_read_dir) {
                if (!is_dir($dir)) {
                    return;
                }

                $dh = opendir($dir);

                if ($dh === false) {
                    return;
                }

                while ($file = readdir($dh)) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }

                    $filepath = $dir . '/' . $file;

                    if (strpos($file, 'component.') !== false) {
                        $func = sprintf(
                            'smarty_component_%s%s',
                            $prefix ? $prefix . '_' : '',
                            str_replace('component.', '', basename($file, '.php'))
                        );
                        $component_files[$func] = $filepath;
                    } elseif (is_dir($filepath)) {
                        $func_read_dir($filepath, $file);
                    }
                }

                closedir($dh);
            };

            foreach ($smarty->getComponentDirs() as $dir) {
                $func_read_dir(rtrim($dir, '/'));
            }
        }

        $function_name = sprintf('smarty_component_%s', str_replace('.', '_', $component_name));

        if (!function_exists($function_name) && isset($component_files[$function_name])) {
            require_once $component_files[$function_name];
        }

        if (!function_exists($function_name)) {
            throw new DeveloperException(sprintf('Component %s function %s not found', $component_name, $function_name));
        }

        return $function_name;
    }
}
