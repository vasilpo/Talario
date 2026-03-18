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



namespace Tygh\Addons\ProductVariations\Product\Type;


/**
 * Class TypeCollection
 *
 * @package Tygh\Addons\ProductVariations\Product\Type
 */
class TypeCollection
{
    /** @var array  */
    protected $schema = [];

    /** @var \Tygh\Addons\ProductVariations\Product\Type\Type[] */
    protected $instances = [];

    /**
     * TypeCollection constructor.
     *
     * @param array $schema
     */
    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param string $type
     *
     * @return \Tygh\Addons\ProductVariations\Product\Type\Type
     */
    public function get($type)
    {
        if (isset($this->instances[$type])) {
            return $this->instances[$type];
        }

        if (!isset($this->schema[$type])) {
            $type = Type::PRODUCT_TYPE_SIMPLE;
        }

        $this->instances[$type] = new Type($type, $this->schema[$type]);

        return $this->instances[$type];
    }

    /**
     * Gets type to type name map
     *
     * @return array
     */
    public function getTypeNames()
    {
        return array_combine(array_keys($this->schema), array_column($this->schema, 'name'));
    }
}