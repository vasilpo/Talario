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

namespace Tygh\UpgradeCenter\Migrations;

use Tygh\UpgradeCenter\Log;

class Output extends \Symfony\Component\Console\Output\Output
{
    protected $buffer;
    protected $config = array();

    public function doWrite($message, $newline)
    {
        Log::instance($this->config['package_id'])->add($message);
        $this->buffer .= $message . ($newline ? PHP_EOL : '');
    }

    public function getBuffer()
    {
        return $this->buffer;
    }

    public function setConfig($config)
    {
        return $this->config = $config;
    }
}
