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

interface StorageInterface
{
    /**
     * @param int $user_id          User ID
     * @param int $external_user_id Helpdesk user ID
     *
     * @return void
     */
    public function setId($user_id, $external_user_id);

    /**
     * @param int $user_id User ID
     *
     * @return void
     */
    public function resetId($user_id);

    /**
     * @param int $user_id User ID
     *
     * @return int|null
     */
    public function getId($user_id);
}
