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

use Tygh\Tools\ErrorHandler;

abstract class AException extends \Exception
{
    /**
     * Outputs exception information
     *
     * @deprecated
     * @see \Tygh\Tools\ErrorHandler::showErrorMessage
     */
    public function output()
    {
        ErrorHandler::showErrorMessage($this);
    }

    /**
     * Returns debug information
     *
     * @param boolean $plain_text output as plain text
     *
     * @return string Formatted debug info
     * @deprecated
     * @see \Tygh\Tools\ErrorHandler::getDebugInfo
     */
    protected function printDebug($plain_text = false)
    {
        return ErrorHandler::getDebugInfo($this, $plain_text);
    }

    public function getErrorTitle()
    {
        return __CLASS__;
    }
}
