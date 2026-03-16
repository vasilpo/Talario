<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Tests\Unit\Addons\RusOfdFerma;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Tygh\Addons\OfdFerma\OfdFerma;
use Tygh\Registry;

class OfdFermaTest extends TestCase
{
    public $runTestInSeparateProcess = true;
    public $backupGlobals = false;
    public $preserveGlobalState = false;

    public function testBuildPaymentAgentInfoUsesMarketplaceFallbackForSupplierData(): void
    {
        Registry::set('settings', array(
            'Company' => array(
                'company_phone' => '+79000000001',
                'company_name' => 'Marketplace LLC',
                'company_country' => 'Russia',
                'company_city' => 'Moscow',
                'company_address' => 'Lenina st. 77',
            ),
        ));
        Registry::set('addons', array(
            'rus_ofd_ferma' => array(
                'setting_inn' => '0123456789',
            ),
        ));

        $method = new ReflectionMethod(OfdFerma::class, 'buildPaymentAgentInfo');
        $method->setAccessible(true);

        $payment_agent_info = $method->invoke(new OfdFerma(), array());

        $this->assertSame(array(
            'AgentType' => 'BANK_PAYMENT_AGENT',
            'TransferAgentPhone' => '+79000000001',
            'TransferAgentName' => 'Marketplace LLC',
            'TransferAgentAddress' => 'Russia, Moscow, Lenina st. 77',
            'TransferAgentINN' => '0123456789',
            'SupplierInn' => '0123456789',
            'SupplierName' => 'Marketplace LLC',
            'SupplierPhone' => '+79000000001',
        ), $payment_agent_info);
    }
}
