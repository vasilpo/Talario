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

namespace Tygh\Helpdesk\AuthStorage;

use Tygh\Registry;
use Tygh\Web\Session;

class RuntimeStorage implements StorageInterface
{
    /**
     * @var \Tygh\Web\Session
     */
    protected $session;

    /**
     * @param \Tygh\Web\Session $session Session instance
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /** @inheritdoc */
    public function setId($user_id, $external_user_id)
    {
        if ((int) $this->session['auth']['user_id'] === $user_id) {
            $this->session['auth']['helpdesk_user_id'] = $external_user_id;
        }

        if ((int) Registry::get('user_info.user_id') === $user_id) {
            Registry::set('user_info.helpdesk_user_id', $external_user_id);
        }

        return;
    }

    /** @inheritdoc */
    public function resetId($user_id)
    {
        $this->setId($user_id, 0);
    }

    /** @inheritdoc */
    public function getId($user_id)
    {
        if (
            ((int) $this->session['auth']['user_id'] === $user_id)
            && isset($this->session['auth']['helpdesk_user_id'])
        ) {
            return (int) $this->session['auth']['helpdesk_user_id'];
        }

        return null;
    }
}
