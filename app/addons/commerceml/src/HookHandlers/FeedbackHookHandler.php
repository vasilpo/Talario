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

namespace Tygh\Addons\CommerceML\HookHandlers;

use Tygh\Addons\CommerceML\ServiceProvider;

class FeedbackHookHandler
{
    /**
     * The "get_feedback_data" hook handler.
     *
     * Actions performed: Collects feedback data on the settings of the sync commerceml provider
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     *
     * @param mixed[] $data Array for collecting
     * @param string  $mode Dispatch mode
     */
    public static function onGetFeedbackData(array &$data, $mode)
    {
        $provider_id = 'commerceml';
        $company_id = (int) db_get_field('SELECT company_id FROM ?:sync_data_settings WHERE provider_id = ?s', $provider_id);

        if (empty($company_id)) {
            return;
        }

        $sync_settings = fn_get_sync_data_settings($provider_id, $company_id);
        $schema_settings = fn_get_schema('cml', 'settings');

        $result = [];
        foreach ($schema_settings as $setting => $schema) {
            if (empty($schema['feedback'])) {
                continue;
            }
            if (!isset($sync_settings[$setting])) {
                $result[$setting] = '';
                continue;
            }

            $value = ServiceProvider::normalizeValue($schema_settings[$setting], $sync_settings[$setting]);

            switch ($schema['type']) {
                case 'string[]':
                    $value = count($value);
                    break;
                case 'bool':
                    $value = (int) $value;
                    break;
            }

            $result[$setting] = $value;
        }

        if ($mode === 'prepare') {
            $data[__('options_for') . ' ' . $provider_id] = $result;
        } else {
            array_walk($result, static function ($value, $setting) use (&$data, $provider_id) {
                $data['sync_data_settings'][] = [
                    'provider_id' => $provider_id,
                    'setting' => $setting,
                    'value' => $value
                ];
            });
        }
    }
}
