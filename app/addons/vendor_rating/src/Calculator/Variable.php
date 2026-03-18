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

namespace Tygh\Addons\VendorRating\Calculator;

/**
 * Class Variable represents a variable used in a formula.
 *
 * @package Tygh\Addons\VendorRating\Calculator
 */
class Variable
{
    /** @var string */
    protected $short_code;

    /** @var string */
    protected $long_code;

    /** @var int|float */
    protected $value;

    public function __construct($short_code, $long_code, $value)
    {
        $this->short_code = $short_code;
        $this->long_code = $long_code;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getShortCode()
    {
        return $this->short_code;
    }

    /**
     * @return string
     */
    public function getLongCode()
    {
        return $this->long_code;
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->value;
    }
}
