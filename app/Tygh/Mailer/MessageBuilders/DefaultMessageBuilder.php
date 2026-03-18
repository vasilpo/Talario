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

namespace Tygh\Mailer\MessageBuilders;


use Tygh\Mailer\AMessageBuilder;
use Tygh\Mailer\Message;

/**
 * The class responsible for building the message based on the message parameters only. This class is used when the message body is passed in the parameters.
 *
 * @package Tygh\Mailer\MessageBuilders
 */
class DefaultMessageBuilder extends AMessageBuilder
{
    /** @inheritdoc */
    protected function initMessage(Message $message, $params, $area, $lang_code)
    {
        if (!empty($params['body'])) {
            $message->setBody($params['body']);
        }

        if (!empty($params['subject'])) {
            $message->setSubject($params['subject']);
        }

        if (!empty($params['subj'])) {
            $message->setSubject($params['subj']);
        }
    }
}