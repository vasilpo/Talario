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

namespace Tygh\ContextMenu\Items;

use Tygh\ContextMenu\MenuItemInterface;

class DividerItem implements MenuItemInterface
{
    /** @inheritDoc */
    public function getTemplate()
    {
        return 'components/context_menu/items/divider.tpl';
    }

    /** @inheritDoc */
    public function getData()
    {
        return [];
    }

    /** @inheritDoc */
    public function isAvailable(array $request, array $auth, array $runtime)
    {
        return true;
    }
}
