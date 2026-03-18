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

namespace Tygh\Addons\GraphqlApi;

class Cursor
{
    const SEPARATOR = ':';

    public $page;

    public $items_per_page;

    public function __construct($page, $items_per_page)
    {

        $this->page = $page;
        $this->items_per_page = $items_per_page;
    }

    public static function instanceByCursor($cursor)
    {
        $cursor_decoded = base64_decode($cursor);
        list($page, $items_per_page) = explode(self::SEPARATOR, $cursor_decoded);

        return new self($page, $items_per_page);
    }

    public function getValue()
    {
        $cursor = sprintf('%d%s%d', $this->page, self::SEPARATOR, $this->items_per_page);

        $cursor_encoded = base64_encode($cursor);

        return $cursor_encoded;
    }

    public static function getValueByPagination($page, $items_per_page)
    {
        $cursor = new self($page, $items_per_page);

        return $cursor->getValue();
    }
}
