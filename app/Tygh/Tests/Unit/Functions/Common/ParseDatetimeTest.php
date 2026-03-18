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

namespace Tygh\Tests\Unit\Functions\Common;


use Tygh\Tests\Unit\ATestCase;
use Tygh\Registry;

class ParseDatetimeTest extends ATestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        define('SECONDS_IN_HOUR', 60 * 60);

        Registry::set('settings.Appearance.calendar_date_format', 'day_first');

        $this->requireCore('functions/fn.common.php');
    }

    /**
     * @dataProvider dpParseDatetime
     */
    public function testParseDatetime($datetime, $expected)
    {
        $this->assertEquals($expected, fn_parse_datetime($datetime));
    }

    public function dpParseDatetime()
    {
        return array(
            array(
                '11/08/2013 16:45',
                '1376239500'
            ),
            array(
                '11/08/2013 6:45',
                '1376203500'
            ),
            array(
                '11/08/2013 16:5',
                '1376237100'
            ),
            array(
                '11/08/2013 6:5',
                '1376201100'
            ),
        );
    }
}