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
// phpcs:disable

namespace Tygh\ContextMenu;

interface MenuItemInterface
{
    /**
     * Provides path for a template to render menu item with.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $request
     * @param array $auth
     * @param array $runtime
     *
     * @return bool
     */
    public function isAvailable(array $request, array $auth, array $runtime);
}
