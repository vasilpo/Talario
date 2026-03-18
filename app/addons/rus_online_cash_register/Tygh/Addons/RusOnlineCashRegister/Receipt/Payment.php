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


namespace Tygh\Addons\RusOnlineCashRegister\Receipt;

/**
 * Model of receipt payment.
 *
 * @package Tygh\Addons\RusOnlineCashRegister\Receipt
 */
class Payment
{
    /** @var int */
    protected $type;

    /** @var float */
    protected $sum;

    /**
     * Payment constructor.
     *
     * @param int    $type  Payment type on cash register.
     * @param string $sum   Paid sum.
     */
    public function __construct($type, $sum)
    {
        $this->type = (int) $type;
        $this->sum = (float) $sum;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'type' => $this->type,
            'sum' => $this->sum,
        );
    }

    /**
     * Create object from array,
     *
     * @param array $data
     *
     * @return Payment
     */
    public static function fromArray(array $data)
    {
        return new self($data['type'], $data['sum']);
    }
}