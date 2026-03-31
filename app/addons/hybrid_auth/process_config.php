<?php
/*!
* HybridAuth
* https://hybridauth.github.io | http://github.com/hybridauth/hybridauth
* (c) 2009-2019, HybridAuth authors | https://hybridauth.github.io/license.html
*/

use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

$config = [
    'callback' => fn_url('auth.process'),

    // if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
    'debug_mode' => false,
    'debug_file' => Registry::get('config.dir.var') . 'oauth.log',
];

$providers_schema = fn_get_schema('hybrid_auth', 'providers');
$available_providers = fn_hybrid_auth_get_providers_list();
foreach ($available_providers as $provider_data) {
    $provider_name = $providers_schema[$provider_data['provider']]['provider'];
    $config['providers'][$provider_name] = array(
        'enabled' => $provider_data['status'] === ObjectStatuses::ACTIVE,
    );

    if (isset($providers_schema[$provider_data['provider']])) {

        $provider_keys = $providers_schema[$provider_data['provider']]['keys'] ?? [];
        foreach ($provider_keys as $key => $key_data) {
            if (isset($key_data['db_field']) && isset($provider_data[$key_data['db_field']])) {
                $config['providers'][$provider_name]['keys'][$key] = $provider_data[$key_data['db_field']];
            }
        }

        if (isset($providers_schema[$provider_data['provider']]['callback'])) {
            $config['providers'][$provider_name]['callback'] = $providers_schema[$provider_data['provider']]['callback'];
        }

        $provider_params = $providers_schema[$provider_data['provider']]['params'] ?? [];
        foreach ($provider_params as $param_id => $param_data) {
            if (isset($provider_data['params'][$param_id])) {
                $config['providers'][$provider_name][$param_id] = $provider_data['params'][$param_id];

            } elseif ($param_data['type'] == 'hidden' && !empty($param_data['value'])) {
                $config['providers'][$provider_name][$param_id] = $param_data['value'];
            }
        }
        if (isset($config['providers'][$provider_name]['version']) && isset($providers_schema[$provider_data['provider']]['versions'])) {
            $version = $config['providers'][$provider_name]['version'];
            if (isset($providers_schema[$provider_data['provider']]['versions'][$version]['adapter'])) {
                $config['providers'][$provider_name]['adapter'] = $providers_schema[$provider_data['provider']]['versions'][$version]['adapter'];
            }
        } elseif (isset($providers_schema[$provider_data['provider']]['adapter'])) {
            $config['providers'][$provider_name]['adapter'] = $providers_schema[$provider_data['provider']]['adapter'];
        }
    }
}

return $config;
