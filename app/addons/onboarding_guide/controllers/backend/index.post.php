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

use Tygh\Addons\OnboardingGuide\OnboardingGuide;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */
/** @var array $auth */

if ($mode === 'index') {
    if (
        $auth['user_type'] === UserTypes::ADMIN
        && $auth['is_root'] === YesNo::YES
    ) {
        if (OnboardingGuide::isDismissed()) {
            return [CONTROLLER_STATUS_OK];
        }

        $onboarding_guide_is_demo = Registry::ifGet('config.demo_mode', false);

        Tygh::$app['view']->assign([
            'onboarding_guide_steps'            => OnboardingGuide::getSteps(),
            'onboarding_guide_progress'         => OnboardingGuide::calculateProgress(),
            'onboarding_guide_is_store_builder' => fn_allowed_for('ULTIMATE'),
            'onboarding_guide_is_demo'          => $onboarding_guide_is_demo
        ]);
    }
}
