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

namespace Tygh\Addons\Discussion\Notifications\EventIdProviders;

use Tygh\Notifications\EventIdProviders\IProvider;

class DiscussionProvider implements IProvider
{
    /**
     * @var string
     */
    protected $prefix = 'discussion.';

    /**
     * @var string
     */
    protected $id;

    public function __construct(array $discussion_data)
    {
        $this->id = $this->prefix . $discussion_data['post_id'];
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }
}