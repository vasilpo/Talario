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

namespace Tygh\Tests\Unit\Addons\Paypal;

use Tygh\Tests\Unit\ATestCase;

class SetOrdersLockTest extends ATestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    protected function setUp(): void
    {
        define('AREA', 'A');
        define('ACCOUNT_TYPE', 'admin');

        $this->requireCore('addons/paypal/func.php');
        $this->requireMockFunction('fn_set_storage_data');
    }

    /**
     * @dataProvider dpTestGeneral
     */
    public function testGeneral($order_ids, $are_locked, $locked_orders_ids, $expected)
    {
        $actual = fn_pp_set_orders_lock($order_ids, $are_locked, $locked_orders_ids);

        $this->assertEquals($expected, $actual);
    }

    public function dpTestGeneral()
    {
        $locked_orders_ids = array(1, 2, 3);

        return array(
            array(array(2, 3, 4), true, $locked_orders_ids, array(1, 2, 3, 4)),
            array(array(2, 3, 4), false, $locked_orders_ids, array(1)),
            array(4, true, $locked_orders_ids, array(1, 2, 3, 4)),
            array(3, false, $locked_orders_ids, array(1, 2)),
            array(array(), true, $locked_orders_ids, array(1, 2, 3)),
            array(array(), false,$locked_orders_ids, array(1, 2, 3)),
        );
    }

}
