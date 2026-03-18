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

namespace Tygh\Notifications\Receivers;

use Tygh\Exceptions\DeveloperException;

/**
 * Class SearchCondition represents a message receiver search condition.
 *
 * @package Tygh\Notifications\Receivers
 */
class SearchCondition
{
    /**
     * @var string
     *
     * @see \Tygh\Enum\ReceiverSearchMethods
     */
    protected $method;

    /** @var string */
    protected $criterion;

    /**
     * ReceiverSearchCondition constructor.
     *
     *
     * @param string $method
     * @param string $criterion
     */
    public function __construct($method, $criterion)
    {
        $this->method = $method;
        $this->criterion = $criterion;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getCriterion()
    {
        return $this->criterion;
    }

    /**
     * @param array $data
     *
     * @return \Tygh\Notifications\Receivers\SearchCondition
     */
    public static function makeOne(array $data)
    {
        if (!isset($data['method']) || !isset($data['criterion'])) {
            throw new DeveloperException('`method` and `criterion` must be specified for \Tygh\Notifications\Receivers\SearchCondition');
        }

        return new self((string) $data['method'], (string) $data['criterion']);
    }

    /**
     * @param array $list
     *
     * @return \Tygh\Notifications\Receivers\SearchCondition[]
     */
    public static function makeList(array $list)
    {
        foreach ($list as &$data) {
            $data = static::makeOne($data);
        }
        unset($data);

        return $list;
    }
}
