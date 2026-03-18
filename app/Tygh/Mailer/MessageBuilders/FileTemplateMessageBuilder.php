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
use Tygh\SmartyEngine\Core;
use Tygh\Storefront\Repository;

/**
 * The class responsible for building a message based on the Smarty template files.
 *
 * @package Tygh\Mailer\MessageBuilders
 */
class FileTemplateMessageBuilder extends AMessageBuilder
{
    /** @var Core Smarty templater*/
    protected $view;

    /**
     * FileTemplateMessageBuilder constructor.
     *
     * @param Core  $view   Instance of smarty templater (Tygh\SmartyEngine\Core)
     * @param array $config List of base params (see AMessageBuilder::__construct)
     */
    public function __construct(Core $view, array $config, Repository $storefront_repository)
    {
        $this->view = $view;
        parent::__construct($config, $storefront_repository);
    }

    /** @inheritdoc */
    protected function initMessage(Message $message, $params, $area, $lang_code)
    {
        if (empty($params['tpl'])) {
            return;
        }

        if (!empty($params['data'])) {
            foreach ($params['data'] as $key => $value) {
                $this->view->assign($key, $value);
            }
        }

        $company_id = $params['company_id'];
        $tpl_ext = (string) pathinfo($params['tpl'], PATHINFO_EXTENSION);
        $subj_tpl = str_replace('.' . $tpl_ext, '_subj.' . $tpl_ext, $params['tpl']);

        $body = $this->view->displayMail($params['tpl'], false, $area, $company_id, $lang_code);
        $subject = $this->view->displayMail($subj_tpl, false, $area, $company_id, $lang_code);

        $message->setId($params['tpl']);
        $message->setBody($body);
        $message->setSubject($subject);
    }
}