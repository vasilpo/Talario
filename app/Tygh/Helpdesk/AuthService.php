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

namespace Tygh\Helpdesk;

class AuthService
{
    /**
     * @var \Tygh\Helpdesk\AuthStorage\StorageInterface[]
     */
    protected $storages;

    /**
     * AuthService constructor.
     *
     * @param array<\Tygh\Helpdesk\AuthStorage\StorageInterface> $storages Info storages
     */
    public function __construct(array $storages)
    {
        $this->storages = $storages;
    }

    /**
     * Updates user external ID.
     *
     * @param int $user_id          User ID
     * @param int $external_user_id External user ID
     *
     * @return void
     */
    public function setExternalUserId($user_id, $external_user_id)
    {
        foreach ($this->storages as $storage) {
            $storage->setId($user_id, $external_user_id);
        }

        fn_set_hook('helpdesk_auth_service_set_external_user_id_post', $user_id, $external_user_id);
    }

    /**
     * Gets user external ID.
     *
     * @param int $user_id User ID
     *
     * @return int|null
     */
    public function getExternalUserId($user_id)
    {
        $default_storage = reset($this->storages);

        return $default_storage->getId($user_id);
    }

    /**
     * Resets user external ID.
     *
     * @param int $user_id User ID
     *
     * @return void
     */
    public function resetExternalUserId($user_id)
    {
        foreach ($this->storages as $storage) {
            $storage->resetId($user_id);
        }

        fn_set_hook('helpdesk_auth_service_reset_external_user_id_post', $user_id);
    }
}
