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
 * Interface BackendInterface describes a backend for calculator that can evaluate formulas with variables.
 *
 * @package Tygh\Addons\VendorRating\Calculator
 */
interface BackendInterface
{
    /**
     * @param string                                          $formula
     * @param \Tygh\Addons\VendorRating\Calculator\Variable[] $variables
     *
     * @return int|float
     * @throws \Tygh\Addons\VendorRating\Exception\CalculationException
     * @throws \DivisionByZeroError
     */
    public function evaluate($formula, array $variables);
}
