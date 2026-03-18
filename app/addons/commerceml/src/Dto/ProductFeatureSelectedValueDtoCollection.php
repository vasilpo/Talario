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


namespace Tygh\Addons\CommerceML\Dto;

use ArrayIterator;
use IteratorAggregate;
use Countable;
use Traversable;

/**
 * Class ProductFeatureSelectedValueDtoCollection
 *
 * @package Tygh\Addons\CommerceML\Dto
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 */
class ProductFeatureSelectedValueDtoCollection implements IteratorAggregate, Countable
{
    /**
     * @var array<\Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDto>
     */
    private $values = [];

    /**
     * Adds product feature selected value to collection
     *
     * @param \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDto $feature_selected_value Product feature selected value instance
     *
     * @return void
     */
    public function add(ProductFeatureSelectedValueDto $feature_selected_value)
    {
        $this->values[$feature_selected_value->value_id->getId()] = $feature_selected_value;
    }

    /**
     * Checks if collection has the product feature selected value object
     *
     * @param string $value_id External or local feature value ID
     *
     * @return bool
     */
    public function has($value_id)
    {
        return isset($this->values[$value_id]);
    }

    /**
     * Removes product feature selected value object from collection
     *
     * @param string $value_id External or local feature value ID
     *
     * @return void
     */
    public function remove($value_id)
    {
        unset($this->values[$value_id]);
    }

    /**
     * Gets product feature selected value object from collection
     *
     * @param string     $value_id      External or local feature value ID
     * @param null|mixed $default_value If collection has not property, then method will return new PropertyDto
     *                                  where $default_value used as value on new object
     *
     * @return \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDto
     */
    public function get($value_id, $default_value = null)
    {
        if (!$this->has($value_id)) {
            /**
             * @psalm-suppress PossiblyNullArgument
             */
            return ProductFeatureSelectedValueDto::create($default_value, IdDto::createByExternalId($value_id));
        }

        return $this->values[$value_id];
    }

    /**
     * Gets all product features
     *
     * @return array<\Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDto>
     */
    public function getAll()
    {
        return $this->values;
    }

    /**
     * Merges current collection with $collection
     *
     * @param \Tygh\Addons\CommerceML\Dto\ProductFeatureSelectedValueDtoCollection $collection Product feature selected values collection instance
     *
     * @return void
     */
    public function mergeWith(ProductFeatureSelectedValueDtoCollection $collection)
    {
        foreach ($collection as $item) {
            $this->add($item);
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }
}
