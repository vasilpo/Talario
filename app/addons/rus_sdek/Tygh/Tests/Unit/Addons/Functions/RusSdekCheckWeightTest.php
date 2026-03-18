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

namespace Tygh\Tests\Unit\Addons\Functions;

use Tygh\Tests\Unit\ATestCase;
use Tygh\Registry;

class RusSdekCheckWeightTest extends ATestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    protected function setUp(): void
    {
        define('AREA', 'A');
        define('ACCOUNT_TYPE', 'admin');

        $this->requireCore('addons/rus_sdek/func.php');
    }

    /**
     * @dataProvider dpCheckWeight
     */
    public function testCheckWeight($sdek_weight, $symbol_grams, $expected)
    {
        $weight = fn_sdek_check_weight($sdek_weight, $symbol_grams);

        $this->assertEquals($weight, $expected);
    }

    public function dpCheckWeight()
    {
        return array(
            array(0, 1, 100),
            array(20, 1000, 20),
            array(30, 1, 30),
            array(0.5, 1000, 0.5),
            array(0.001, 1, 0.001)
        );
    }
}