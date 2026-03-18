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

namespace Tygh\Exceptions;

class PHPErrorException extends AException
{
    public function __construct($message, $type, $filename, $line_number)
    {
        parent::__construct($message, $type);
        $this->file = $filename;
        $this->line = $line_number;
    }

    public function getErrorTitle()
    {
        $titles = array(
            E_ERROR => 'PHP Fatal Error',
            E_PARSE => 'PHP Parse Error',
            E_CORE_ERROR => 'PHP Core Error',
            E_CORE_WARNING => 'PHP Core Warning',
            E_COMPILE_ERROR => 'PHP Compile Error',
            E_COMPILE_WARNING => 'PHP Compile Warning',

            E_NOTICE => 'PHP Notice',
            E_USER_NOTICE => 'Notice',
            E_WARNING => 'PHP Warning',
            E_USER_WARNING => 'Warning',
            E_DEPRECATED => 'PHP Deprecated',
            E_USER_DEPRECATED => 'Deprecated',
        );

        return isset($titles[$this->code]) ? $titles[$this->code] : 'PHP Error';
    }

    public function __toString()
    {
        return "{$this->getErrorTitle()}: $this->message in {$this->file} on line {$this->line}";
    }
}