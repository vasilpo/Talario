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

namespace Tygh;

class Logger
{
    private static $instance = NULL;

    private $logfile = '';

    public function __set($name, $value)
    {
        switch ($name) {
            case 'logfile':
                if (!file_exists($value)) {
                    clearstatcache();
                    if (!file_exists($value)) {
                        $h = fopen($value, 'w');
                        fclose($h);
                    }
                }

                if (!is_writeable($value)) {
                    throw new \Exception("$value is not a valid file path");
                }
                $this->logfile = $value;
                break;

            default:
                throw new \Exception("$name cannot be set");
        }
    }

    public function write($message, $file = null, $line = null)
    {
        if (!empty($this->logfile)) {
            $message = date('Y-m-d H:i:s', time()) . ': ' . $message;
            $message .= is_null($file) ? '' : " in $file";
            $message .= is_null($line) ? '' : " on line $line";
            $message .= "\n";

            return file_put_contents($this->logfile, $message, FILE_APPEND);
        } else {
            return false;
        }
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Logger;
        }

        return self::$instance;
    }
}
