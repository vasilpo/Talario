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


namespace Tygh\Addons\CommerceML\Tests\Unit\Convertors;


use Tygh\Addons\CommerceML\Convertors\PriceTypeConvertor;
use Tygh\Addons\CommerceML\Dto\CurrencyDto;
use Tygh\Addons\CommerceML\Dto\IdDto;
use Tygh\Addons\CommerceML\Dto\PriceTypeDto;
use Tygh\Addons\CommerceML\Dto\PropertyDto;
use Tygh\Addons\CommerceML\Tests\Unit\StorageBasedTestCase;
use Tygh\Addons\CommerceML\Xml\SimpleXmlElement;

class PriceTypeConvertorXmlTest extends StorageBasedTestCase
{
    public $entities = [];

    protected function setUp(): void
    {
        $this->requireMockFunction('fn_set_hook');

        parent::setUp();
    }

    public function getConvertor()
    {
        return new PriceTypeConvertor();
    }

    /**
     * @param $xml
     * @param $expected_entities
     * @dataProvider dpConvert
     */
    public function testConvert($xml, $expected_entities)
    {
        $convertor = $this->getConvertor();
        $import_storage = $this->getImportStorage();

        $convertor->convert(simplexml_load_string($xml, SimpleXmlElement::class, LIBXML_NOCDATA), $import_storage);

        $this->assertEquals($expected_entities, $import_storage->getImportEntityRepository()->getEntites());
    }

    public function dpConvert()
    {
        $price_type = new PriceTypeDto();
        $price_type->id = IdDto::createByExternalId('b4d52911-af52-11e8-8309-b010418127da');
        $price_type->name = 'ОПТ';
        $price_type->properties->add(PropertyDto::create('currency', 'RUB'));

        $currency = new CurrencyDto();
        $currency->name = 'RUB';
        $currency->id = IdDto::createByExternalId('RUB');

        $xml = <<<XML
<ТипЦены>
    <Ид>b4d52911-af52-11e8-8309-b010418127da</Ид>
    <Наименование>ОПТ</Наименование>
    <Валюта>RUB</Валюта>
    <Налог>
        <Наименование>НДС</Наименование>
        <УчтеноВСумме>false</УчтеноВСумме>
        <Акциз>false</Акциз>
    </Налог>
</ТипЦены>
XML;

        return [
            [
                $xml,
                [$price_type, $currency]
            ]
        ];
    }
}