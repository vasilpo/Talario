<?php

namespace Tygh\Tests\Unit\Licensing;

use Tygh\Licensing\LicensingService;
use Tygh\Licensing\Features;
use Tygh\Licensing\Plan;
use Tygh\Licensing\Rules\YesNoRule;
use Tygh\Tests\Unit\ATestCase;
use Tygh\Enum\YesNo as YesNoEnum;

class FeatureAvailableTest extends ATestCase
{
    public function setUp(): void
    {
        $this->requireMockFunction('__');
    }

    /**
     * @dataProvider getCases
     */
    public function testPlans($current_plan, $feature, $expected)
    {
        $licensing_server = new LicensingService($this->getPlans(), $current_plan);
        $actual = @$licensing_server->isAllowed($feature, true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider getCasesWithErrors
     */
    public function testPlansWithError($current_plan, $feature, $expected)
    {
        $licensing_server = new LicensingService($this->getPlans(), $current_plan);

        $this->expectError();
        $licensing_server->isAllowed($feature);
    }

    public function getCases()
    {
        $plans = $this->getPlans();

        return [
            [$plans['standard'], Features::WAREHOUSES,           false],
            [$plans['standard'], Features::MULTIPLE_STOREFRONTS, false],
            [$plans['plus'],     Features::WAREHOUSES,           false],
            [$plans['plus'],     Features::MULTIPLE_STOREFRONTS, true],
            [$plans['ultimate'], Features::WAREHOUSES,           true],
            [$plans['ultimate'], Features::MULTIPLE_STOREFRONTS, true],
            [$plans['ultimate'], 'non_existing_feature',         true],
            [$plans['ultimate'], 'non_existing_addon',           true],
        ];
    }

    public function getCasesWithErrors()
    {
        $plans = $this->getPlans();

        return [
            [$plans['ultimate'], 'non_existing_feature', false],
            [$plans['ultimate'], 'non_existing_addon',   false],
        ];
    }

    public function getPlans()
    {
        $feature_collection = [
            Features::WAREHOUSES           => new YesNoRule(YesNoEnum::NO),
            Features::MULTIPLE_STOREFRONTS => new YesNoRule(YesNoEnum::NO),
        ];

        $standard = new Plan('standard', $feature_collection);

        $feature_collection[Features::MULTIPLE_STOREFRONTS] = new YesNoRule(YesNoEnum::YES);
        $plus = new Plan('plus', $feature_collection);

        $feature_collection[Features::WAREHOUSES] = new YesNoRule(YesNoEnum::YES);
        $ultimate = new Plan('ultimate', $feature_collection);

        return [
            'standard' => $standard,
            'plus'     => $plus,
            'ultimate' => $ultimate,
        ];
    }
}
