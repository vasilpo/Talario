<?php

namespace Tygh\Tests\Unit\Core;


use Tygh\ExSimpleXmlElement;
use PHPUnit\Framework\TestCase;

class ExSimpleXmlElementTest extends TestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    /**
     * @param $data
     * @param $expected_xml
     * @dataProvider dpArrayXml
     */
    public function testAddFromArray($data, $expected_xml)
    {
        $xml_root = new ExSimpleXmlElement("<root></root>");
        $xml_root->addChildFromArray($data);

        $xml = $xml_root->asXML();

        $this->assertEquals($expected_xml, $xml);
    }

    /**
     * @param $expected_data
     * @param $xml
     * @dataProvider dpArrayXml
     */
    public function testToArray($expected_data, $xml)
    {
        /** @var ExSimpleXmlElement $xml */
        $xml = simplexml_load_string($xml, '\Tygh\ExSimpleXmlElement');

        $this->assertEquals($expected_data, $xml->toArray());
    }

    /**
     * @param $expected_data
     * @param $xpath
     * @param $xml
     * @dataProvider dpRemoveXml
     */
    public function testRemove($expected_data, $xpath, $xml)
    {
        /** @var ExSimpleXmlElement $xml */
        $xml = simplexml_load_string($xml, '\Tygh\ExSimpleXmlElement');

        $this->assertEquals($expected_data, $xml->remove($xpath)->toArray());
    }

    public function dpArrayXml()
    {
        return [
            [
                [
                    'name' => '100g pants', 'code' => 'QWERTY109',
                    'options' => [
                        ['option_id' => 10, 'value' => 100, 'name' => 'Color'],
                        ['option_id' => 20, 'value' => 200, 'name' => 'Size'],
                    ]
                ],
                "<?xml version=\"1.0\"?>\n<root><name><![CDATA[100g pants]]></name><code><![CDATA[QWERTY109]]></code>"
                . '<options>'
                . '<item><option_id>10</option_id><value>100</value><name><![CDATA[Color]]></name></item>'
                . '<item><option_id>20</option_id><value>200</value><name><![CDATA[Size]]></name></item>'
                . '</options>'
                . "</root>\n"
            ],
            [
                [
                    'name' => ['en' => 'Page', 'ru' => 'Страница'],
                    'params' => [
                        [
                            'title' => 'Тип страницы',
                            'type' => 'selectbox',
                            'variants' => [
                                ['name' => 'Обычная', 'value' => 'base'],
                                ['name' => 'Расширенная', 'value' => 'advanced'],
                                ['name' => 'Другая', 'value' => 'other'],
                            ]
                        ],
                        [
                            'title' => 'Размер баннера',
                            'type' => 'input',
                        ]
                    ]
                ],
                "<?xml version=\"1.0\"?>\n<root><name><en><![CDATA[Page]]></en><ru><![CDATA[Страница]]></ru></name>"
                . '<params>'
                . '<item><title><![CDATA[Тип страницы]]></title><type><![CDATA[selectbox]]></type><variants>'
                    . '<item><name><![CDATA[Обычная]]></name><value><![CDATA[base]]></value></item>'
                    . '<item><name><![CDATA[Расширенная]]></name><value><![CDATA[advanced]]></value></item>'
                    . '<item><name><![CDATA[Другая]]></name><value><![CDATA[other]]></value></item>'
                . '</variants></item>'
                . '<item><title><![CDATA[Размер баннера]]></title><type><![CDATA[input]]></type></item>'
                . '</params>'
                . "</root>\n"
            ]
        ];
    }

    public function dpRemoveXml()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <name build="en"><![CDATA[100g pants]]></name>
    <name build="ru"><![CDATA[100г брюки]]></name>
    <code><![CDATA[QWERTY109]]></code>
    <options build="en">
        <item edition="ULTIMATE">
            <option_id>10</option_id>
            <value>100</value>
            <name><![CDATA[Color]]></name>
        </item>
        <item>
            <option_id>20</option_id>
            <value>200</value>
            <name><![CDATA[Size]]></name>
        </item>
        <item edition="MULTIVENDOR">
            <option_id>30</option_id>
            <value>300</value>
            <name><![CDATA[Model]]></name>
        </item>
    </options>
    <options build="ru">
        <item edition="ULTIMATE">
            <option_id>10</option_id>
            <value>100</value>
            <name><![CDATA[Цвет]]></name>
        </item>
        <item>
            <option_id>20</option_id>
            <value>200</value>
            <name><![CDATA[Размер]]></name>
        </item>
        <item edition="MULTIVENDOR">
            <option_id>30</option_id>
            <value>300</value>
            <name><![CDATA[Модель]]></name>
        </item>
    </options>
</root>
XML;

        return [
            [
                [
                    'name' => '100g pants', 'code' => 'QWERTY109',
                    'options' => [
                        ['option_id' => 10, 'value' => 100, 'name' => 'Color'],
                        ['option_id' => 20, 'value' => 200, 'name' => 'Size'],
                    ]
                ],
                "//*[@build and not(contains(@build,'en')) or @edition and not(contains(@edition,'ULTIMATE'))]",
                $xml
            ],
            [
                [
                    'name' => '100g pants', 'code' => 'QWERTY109',
                    'options' => [
                        ['option_id' => 20, 'value' => 200, 'name' => 'Size'],
                        ['option_id' => 30, 'value' => 300, 'name' => 'Model'],
                    ]
                ],
                "//*[@build and not(contains(@build,'en')) or @edition and not(contains(@edition,'MULTIVENDOR'))]",
                $xml
            ],
            [
                [
                    'name' => '100г брюки', 'code' => 'QWERTY109',
                    'options' => [
                        ['option_id' => 10, 'value' => 100, 'name' => 'Цвет'],
                        ['option_id' => 20, 'value' => 200, 'name' => 'Размер'],
                    ]
                ],
                "//*[@build and not(contains(@build,'ru')) or @edition and not(contains(@edition,'ULTIMATE'))]",
                $xml
            ],
            [
                [
                    'name' => '100г брюки', 'code' => 'QWERTY109',
                    'options' => [
                        ['option_id' => 20, 'value' => 200, 'name' => 'Размер'],
                        ['option_id' => 30, 'value' => 300, 'name' => 'Модель'],
                    ]
                ],
                "//*[@build and not(contains(@build,'ru')) or @edition and not(contains(@edition,'MULTIVENDOR'))]",
                $xml
            ],
        ];
    }
}