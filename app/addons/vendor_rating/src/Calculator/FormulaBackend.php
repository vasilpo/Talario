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

use DivisionByZeroError;
use Exception;
use socialist\formula\Formula;
use Tygh\Addons\VendorRating\Exception\CalculationException;

/**
 * Class FormulaBackend implements a calculator backend that uses advanced formula evaluation.
 *
 * @package Tygh\Addons\VendorRating\Calculator
 */
class FormulaBackend implements BackendInterface
{
    /**
     * @param string                                          $formula
     * @param \Tygh\Addons\VendorRating\Calculator\Variable[] $variables
     *
     * @return float|int
     * @throws \Tygh\Addons\VendorRating\Exception\CalculationException
     * @throws \DivisionByZeroError
     */
    public function evaluate($formula, array $variables)
    {
        $formula = new Formula($formula);
        foreach ($variables as $variable) {
            $formula->setVariable($variable->getShortCode(), $variable->getValue());
        }

        try {
            $result = @$formula->calculate();
            if (is_infinite($result)) {
                throw new DivisionByZeroError();
            }
        } catch (Exception $e) {
            throw new CalculationException($e->getMessage(), $e->getCode());
        }

        return $result;
    }
}
