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


namespace Tygh\Commerceml\Dto\Warehouses;


class Warehouse
{
    /** @var string */
    protected $uid;

    /** @var string */
    protected $name;

    /** @var int|null */
    protected $id;

    /** @var string */
    protected $city = '';

    /** @var string */
    protected $address = '';

    /**
     * Warehouse constructor.
     *
     * @param string $uid
     * @param string $name
     * @param string $address
     */
    protected function __construct($uid, $name, $address)
    {
        $this->uid = (string) $uid;
        $this->name = (string) $name;
        $this->address = (string) $address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $warehouse_uid
     * @param string $name
     * @param string $address
     *
     * @return \Tygh\Commerceml\Dto\Warehouses\Warehouse
     */
    public static function create($warehouse_uid, $name, $address)
    {
        return new self($warehouse_uid, $name, $address);
    }
}