<?php

namespace Tygh\Tests\Unit\Licensing;

use Tygh\Licensing\Rules\CountRule;
use Tygh\Licensing\Rules\YesNoRule;
use Tygh\Tests\Unit\ATestCase;
use Tygh\Enum\YesNo as YesNoEnum;

class RulesTest extends ATestCase
{
    /**
     * @dataProvider getCases
     */
    public function testPlans($rule, $expected, $current_value)
    {
        $actual = true;

        switch (get_class($rule)) {
            case YesNoRule::class:
                $actual = $rule->isAllowed();
                break;
            case CountRule::class:
                $actual = $rule->isAllowed($current_value);
                break;
        }

        $this->assertEquals($expected, $actual);
    }

    public function getCases()
    {
        return [
            [new YesNoRule(YesNoEnum::YES), true,   null],
            [new YesNoRule(YesNoEnum::NO),  false,  null],
            [new YesNoRule(true),           true,   null],
            [new YesNoRule(false),          false,  null],
            [new CountRule(1),              true,   0],
            [new CountRule(1),              false,  1],
            [new CountRule(1),              false,  2],
            [new YesNoRule(false),          false,  null],
        ];
    }
}
