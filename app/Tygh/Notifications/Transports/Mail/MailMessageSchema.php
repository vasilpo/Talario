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

namespace Tygh\Notifications\Transports\Mail;

use Tygh\Notifications\Transports\BaseMessageSchema;

class MailMessageSchema extends BaseMessageSchema
{
    /**
     * @var array|string
     */
    public $to;

    /**
     * @var array|string
     */
    public $from;

    /**
     * @var string|null
     */
    public $reply_to = null;

    /**
     * @var array|null|callable
     */
    public $data_modifier = null;

    /**
     * @var string
     */
    public $template_code;

    /**
     * @var string
     */
    public $legacy_template;

    /**
     * @var string
     */
    public $language_code;

    /**
     * A company that sends a message
     *
     * @var int|null
     */
    public $company_id;

    /**
     * A company that receives a message
     *
     * @var int|null
     */
    public $to_company_id;

    /**
     * @var string
     */
    public $area;

    /**
     * @var int
     */
    public $storefront_id;

    /**
     * @var int
     */
    public $to_storefront_id;

    /**
     * @var array
     */
    public $attachments = [];

    public static function create(array $schema)
    {
        $self = new self();

        $self->to = self::get($schema, 'to');
        $self->from = self::get($schema, 'from');
        $self->reply_to = self::get($schema, 'reply_to');
        $self->data_modifier = self::get($schema, 'data_modifier');
        $self->template_code = self::get($schema, 'template_code');
        $self->legacy_template = self::get($schema, 'legacy_template');
        $self->language_code = self::get($schema, 'language_code');
        $self->company_id = self::get($schema, 'company_id');
        $self->to_company_id = self::get($schema, 'to_company_id');
        $self->area = self::get($schema, 'area');
        $self->attachments = self::get($schema, 'attachments', []);
        $self->storefront_id = self::get($schema, 'storefront_id');
        $self->to_storefront_id = self::get($schema, 'to_storefront_id');

        //TODO validate schema

        return $self;
    }
}
