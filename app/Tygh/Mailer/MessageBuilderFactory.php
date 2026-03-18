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


namespace Tygh\Mailer;


use Pimple\Container;
use Tygh\Mailer\MessageBuilders\DBTemplateMessageBuilder;
use Tygh\Mailer\MessageBuilders\DefaultMessageBuilder;
use Tygh\Mailer\MessageBuilders\FileTemplateMessageBuilder;
use Tygh\Registry;

/**
 * The class factory responsible for creating message builder objects.
 * 
 * @package Tygh\Mailer
 */
class MessageBuilderFactory implements IMessageBuilderFactory
{
    /** @var Container */
    protected $app;

    /**
     * MessageBuilderFactory constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /** @inheritdoc */
    public function createBuilder($type)
    {
        switch ($type) {
            case 'db_template':
                return new DBTemplateMessageBuilder(
                    $this->app['template.renderer'],
                    $this->app['template.mail.repository'],
                    $this->app['mailer.message_style_formatter'],
                    Registry::get('config'),
                    $this->app['storefront.repository']
                );
                break;
            case 'file_template':
                return new FileTemplateMessageBuilder(
                    $this->app['view'],
                    Registry::get('config'),
                    $this->app['storefront.repository']
                );
                break;
            case 'default':
                return new DefaultMessageBuilder(
                    Registry::get('config'),
                    $this->app['storefront.repository']
                );
                break;
            default:
                throw new MailerException("Undefined message builder: {$type}");
                break;
        }
    }
}