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


namespace Tygh\Addons\CommerceML\Xml\Exceptions;


use RuntimeException;

/**
 * Class XmlParserException
 *
 * @package Tygh\Addons\CommerceML\Xml\Exceptions
 */
class XmlParserException extends RuntimeException
{
    /**
     * Throws exception by libxml errors
     *
     * @throws \Tygh\Addons\CommerceML\Xml\Exceptions\XmlParserException If parsing failed.
     */
    public static function libxmlErrors()
    {
        $errors = libxml_get_errors();

        if (empty($errors)) {
            throw new self('Undefined error');
        }

        $messages = [];

        foreach ($errors as $error) {
            $message = '';

            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    $message .= "Warning {$error->code}: ";
                    break;
                case LIBXML_ERR_ERROR:
                    $message .= "Error {$error->code}: ";
                    break;
                case LIBXML_ERR_FATAL:
                    $message .= "Fatal Error {$error->code}: ";
                    break;
            }

            $message .= trim($error->message);
            $message .= "\n  Line: {$error->line}";
            $message .= "\n  Column: {$error->column}";

            if ($error->file) {
                $message .= "\n  File: {$error->file}";
            }

            $messages[] = $message;
        }

        throw new self(implode(';' . PHP_EOL, $messages));
    }
}
