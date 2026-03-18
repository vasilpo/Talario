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

namespace Tygh\Tests\Unit;

use \PHPUnit\Framework\TestCase;

/**
 * Abstract class for unit test cases
 */
abstract class ATestCase extends TestCase
{
    /**
     * Require core file from /app dir, e.g. controller or functions file.
     *
     * @param string $path Path in /app dir.
     */
    public function requireCore($path)
    {
        $path = __DIR__ . '/../../../' . $path;
        if (file_exists($path)) {
            require_once $path;
        } else {
            throw new \Exception('Core file not found: ' . $path);
        }
    }

    /**
     * Require mock function from /_tools/unit_tests/mock_functions dir.
     * 
     * @param string $function Function name
     */
    public function requireMockFunction($function)
    {
        $path = __DIR__ . '/../../../../_tools/unit_tests/mock_functions/' . $function . '.php';
        if (file_exists($path)) {
            require_once $path;
        } else {
            throw new \Exception('You need to create mock function file in: ' . $path);
        }
    }
}
