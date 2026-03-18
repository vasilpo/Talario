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


namespace Tygh\Commerceml\Dto\Offers;


class OfferWarehouse
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $amount;

    /**
     * OfferWarehouse constructor.
     *
     * @param int $id
     * @param int $amount
     */
    protected function __construct($id, $amount)
    {
        $this->id = (int) $id;
        $this->amount = (int) $amount;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $warehouse_id
     * @param int $amount
     *
     * @return \Tygh\Commerceml\Dto\Offers\OfferWarehouse
     */
    public static function create($warehouse_id, $amount)
    {
        return new self($warehouse_id, $amount);
    }
}