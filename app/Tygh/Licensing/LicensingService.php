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

namespace Tygh\Licensing;

use Tygh\Licensing\Rules\CountRule;
use Tygh\Licensing\Rules\CustomRule;
use Tygh\Licensing\Rules\YesNoRule;

class LicensingService
{
    /** @var Plan[] */
    private $plans;

    /** @var Plan */
    private $current_plan;

    /**
     * @param Plan[] $plans        Array of plans
     * @param Plan   $current_plan Current plan
     */
    public function __construct(array $plans, Plan $current_plan)
    {
        $this->plans = $plans;
        $this->current_plan = $current_plan;

        /** @var Plan $plan */
        foreach ($this->plans as $plan) {
            if ($plan->getKey() === $current_plan->getKey()) {
                return;
            }
        }

        trigger_error(__('licensing.current_plan_not_found', ['[plan]' => $current_plan->getKey()]), E_USER_WARNING);
    }

    /**
     * @param string $feature Value of \Tygh\Licensing\Features
     * @param bool   $default Default result
     */
    public function isAllowed($feature, $default = false): bool
    {
        $rule = $this->current_plan->getFeatureCollection()[$feature] ?? null;

        if ($rule === null) {
            trigger_error(str_replace('[feature]', $feature, 'Licensing: Rule for feature \"[feature]\" was not found'), E_USER_WARNING);

            return $default;
        }

        switch (get_class($rule)) {
            case YesNoRule::class:
            case CustomRule::class:
                return $rule->isAllowed();
            case CountRule::class:
                $handler = FeatureRulesMap::getHandler($feature, CountRule::class);

                if (!is_callable($handler)) {
                    trigger_error(str_replace('[feature]', $feature, 'Licensing: Rule handler for feature \"[feature]\" was not found'), E_USER_WARNING);

                    return $default;
                }

                return $rule->isAllowed($handler());
        }

        return $default;
    }

    /**
     * Returns plans
     *
     * @return Plan[]
     */
    public function getPlans(): array
    {
        return $this->plans;
    }

    /**
     * Returns current plan
     */
    public function getCurrentPlan(): Plan
    {
        return $this->current_plan;
    }
}
