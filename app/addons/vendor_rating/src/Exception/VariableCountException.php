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

namespace Tygh\Addons\VendorRating\Exception;

use Exception;

/**
 * Class VariableCountException represents an exception when too much variables are registered in the product.
 *
 * @package Tygh\Addons\VendorRating\Exception
 */
class VariableCountException extends Exception
{
    /** @var int */
    protected $allowed_variables_count;

    /** @var int */
    protected $passed_variables_count;

    /**
     * @return int
     */
    public function getAllowedVariablesCount()
    {
        return $this->allowed_variables_count;
    }

    /**
     * @param int $allowed_variables_count
     */
    public function setAllowedVariablesCount($allowed_variables_count)
    {
        $this->allowed_variables_count = $allowed_variables_count;
    }

    /**
     * @return int
     */
    public function getPassedVariablesCount()
    {
        return $this->passed_variables_count;
    }

    /**
     * @param int $passed_variables_count
     */
    public function setPassedVariablesCount($passed_variables_count)
    {
        $this->passed_variables_count = $passed_variables_count;
    }
}
