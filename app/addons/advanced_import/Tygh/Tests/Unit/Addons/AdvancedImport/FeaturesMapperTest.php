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

namespace Tygh\Tests\Unit\Addons\RusEximFileCommerceml;

use Tygh\Tests\Unit\ATestCase;
use Tygh\Addons\AdvancedImport\FeaturesMapper;

class FeaturesMapperTest extends ATestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    /** @var FeaturesMapper */
    protected $mapper;

    protected function setUp(): void
    {
        define('AREA', 'A');

        $this->mapper = new FeaturesMapper();
    }

    /**
     * @dataProvider dpRemap
     */
    public function testRemap($input, $expected)
    {
        $actual = $this->mapper->remap($input);

        $this->assertEquals($expected, $actual);
    }

    public function dpRemap()
    {
        return array(
            array(
                array(
                    'en' => array(
                        1 => 'Sony',
                        2 => 'Red///Blue',
                    ),
                    'ru' => array(
                        1 => 'Сони',
                        2 => 'Красный///Синий',
                    ),
                ),
                array(
                    1 => array(
                        'feature_id' => 1,
                        'variants'   => array(
                            array(
                                'name'  => 'Sony',
                                'names' => array(
                                    'en' => 'Sony',
                                    'ru' => 'Сони',
                                ),
                            ),
                        ),
                    ),
                    2 => array(
                        'feature_id' => 2,
                        'variants'   => array(
                            array(
                                'name'  => 'Red',
                                'names' => array(
                                    'en' => 'Red',
                                    'ru' => 'Красный',
                                ),
                            ),
                            array(
                                'name'  => 'Blue',
                                'names' => array(
                                    'en' => 'Blue',
                                    'ru' => 'Синий',
                                ),
                            ),
                        ),
                    )
                ),
            ),
            array(
                array(),
                array(),
            ),
            array(
                array('en' => array(), 'ru' => array()),
                array(),
            ),
        );
    }
}