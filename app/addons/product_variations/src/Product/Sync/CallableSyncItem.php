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


namespace Tygh\Addons\ProductVariations\Product\Sync;


class CallableSyncItem implements ISyncItem
{
    protected $callable;

    public function __construct(Callable $callable)
    {
        $this->callable = $callable;
    }

    public function sync($source_product_id, array $destination_product_ids, array $conditions = [])
    {
        call_user_func($this->callable, $source_product_id, $destination_product_ids, $conditions);
    }

    public static function create(Callable $callable)
    {
        return new self($callable);
    }
}