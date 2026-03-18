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

namespace Tygh\SmartyEngine;

use Smarty\Smarty;
use Tygh\Registry;

class BackendResource extends FileResource
{
    /**
     * build template filepath by traversing the template_dir array
     *
     * @param string $file      File
     * @param Smarty $smarty    Source object
     * @param bool   $is_config Is config
     *
     * @return string fully qualified filepath
     */
    public function getFilePath($file, Smarty $smarty, bool $is_config = false)
    {
        $_directory = Registry::get('config.dir.design_backend') . 'templates/';

        // resolve relative path
        if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $file)) {
            // don't we all just love windows?
            $_path = DIRECTORY_SEPARATOR . trim($file, '/');
            $_was_relative = true;
        } else {
            // don't we all just love windows?
            $_path = str_replace('\\', '/', $file);
        }
        $_path = $this->normalizePath($_path, false);
        if (DIRECTORY_SEPARATOR !== '/') {
            // don't we all just love windows?
            $_path = str_replace('/', '\\', $_path);
        }
        // revert to relative
        if (isset($_was_relative)) {
            $_path = substr($_path, 1);
        }

        // this is only required for directories
        $file = rtrim($_path, '/\\');

        // relative file name?
        if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $file)) {
            $_filepath = $_directory . $file;
            if ($this->fileExists($_filepath)) {
                return $this->normalizePath($_filepath);
            }
        }

        // try absolute filepath
        if ($this->fileExists($file)) {
            return $file;
        }

        // give up
        return false;
    }

    /**
     * Test is file exists and save timestamp
     *
     * @param string $file File name
     *
     * @return bool                   true if file exists
     */
    protected function fileExists($file)
    {
        return is_file($file);
    }

    /**
     * Normalize Paths "foo/../bar" to "bar"
     *
     * @param string $_path Path to normalize
     * @param bool   $ds    Respect windows directory separator
     *
     * @return string  normalized path
     */
    protected function normalizePath($_path, $ds = true)
    {
        if ($ds) {
            // don't we all just love windows?
            $_path = str_replace('\\', '/', $_path);
        }

        $offset = 0;

        // resolve simples
        $_path = preg_replace('#/\./(\./)*#', '/', $_path);
        // resolve parents
        while (true) {
            $_parent = strpos($_path, '/../', $offset);
            if (!$_parent) {
                break;
            }
            if ($_path[$_parent - 1] === '.') {
                $offset = $_parent + 3;
                continue;
            }

            $_pos = strrpos($_path, '/', $_parent - strlen($_path) - 1);
            if ($_pos === false) {
                // don't we all just love windows?
                $_pos = $_parent;
            }

            $_path = substr_replace($_path, '', $_pos, $_parent + 3 - $_pos);
        }

        if ($ds && DIRECTORY_SEPARATOR !== '/') {
            // don't we all just love windows?
            $_path = str_replace('/', '\\', $_path);
        }

        return $_path;
    }
}
