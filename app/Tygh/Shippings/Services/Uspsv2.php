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

namespace Tygh\Shippings\Services;

use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Shippings\IService;
use Tygh\Http;

/**
 * USPS v2 shipping service
 */
class Uspsv2 implements IService
{
    /**
     * Production service URL
     */
    const URL_PRODUCTION  = 'https://apis.usps.com/shipments/v3';
    /**
     * Development service URL
     */
    const URL_DEVELOPMENT = 'https://apis-tem.usps.com/shipments/v3';

    /**
     * Production URL of getting the access token
     */
    const URL_PRODUCTION_GET_TOKEN = 'https://apis.usps.com/oauth2/v3/token';
    /**
     * Development URL of getting the access token
     */
    const URL_DEVELOPMENT_GET_TOKEN = 'https://apis-tem.usps.com/oauth2/v3/token';

    /**
     * Name of the token cache key
     */
    const ACCESS_TOKEN_CACHE_NAME = 'usps_api_access_key';

    /**
     * @var bool Availability multithreading in this module
     */
    private $allow_multithreading = false;

    /**
     * @var array Stored shipping information
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    private $shipping_info = [];

    /**
     * Flag to mark shipping as domestic/international
     *
     * @var bool $is_domestic
     */
    private $is_domestic = true;

    /**
     * @inheritdoc
     */
    public function prepareData($shipping_info)
    {
        $this->shipping_info = $shipping_info;
    }

    /**
     * @inheritdoc
     */
    public function allowMultithreading()
    {
        return $this->allow_multithreading;
    }

    /**
     * Retrieves access token
     *
     * @return string Access token from the cache
     */
    protected function getAccessToken(): string
    {
        $current_mode = YesNo::isTrue($this->shipping_info['service_params']['test_mode'])
            ? 'test'
            : 'prod';

        Registry::registerCache(
            self::ACCESS_TOKEN_CACHE_NAME . $current_mode,
            SECONDS_IN_HOUR,
            Registry::cacheLevel(['time'])
        );

        if (!Registry::isExist(self::ACCESS_TOKEN_CACHE_NAME . $current_mode)) {
            $params = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->shipping_info['service_params']['client_id'],
                'client_secret' => $this->shipping_info['service_params']['client_secret'],
            ];

            $extra = [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-type'  => 'application/x-www-form-urlencoded',
                ],
            ];

            $url = $current_mode === 'test'
                ? self::URL_DEVELOPMENT_GET_TOKEN
                : self::URL_PRODUCTION_GET_TOKEN;

            $response = $this->execute($url, Http::POST, $params, $extra);

            if (!isset($response['access_token'])) {
                return '';
            }
            Registry::set(self::ACCESS_TOKEN_CACHE_NAME . $current_mode, $response['access_token']);
        }

        return Registry::get(self::ACCESS_TOKEN_CACHE_NAME . $current_mode);
    }

    /**
     * Provides content of Access Request.
     *
     * @return array<array-key, string> Access Request
     */
    private function getRequestHeaders(): array
    {
        return [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRequestData()
    {
        $result = [];

        /** @var array $shipping_settings */
        $shipping_settings = $this->shipping_info['service_params'];

        $packages = $this->shipping_info['package_info']['packages'] ?? [];
        if (!empty($packages)) {
            $width = !empty($shipping_settings['package_width']) ? $shipping_settings['package_width'] : 1;
            $length = !empty($shipping_settings['package_length']) ? $shipping_settings['package_length'] : 1;
            $height = !empty($shipping_settings['package_height']) ? $shipping_settings['package_height'] : 1;

            $package_girth = !empty($shipping_settings['package_girth']) ? $shipping_settings['package_girth'] : 1;
            $default_package_weight = !empty($shipping_settings['package_weight']) ? $shipping_settings['package_weight'] : 1;

            $origination = $this->prepareAddress($this->shipping_info['package_info']['origination']);
            $location = $this->prepareAddress($this->shipping_info['package_info']['location']);

            $mail_class = $this->shipping_info['service_code'];
            $price_type = $shipping_settings['price_type'];

            $us_dependent_territories = [
                'AS', // American Samoa
                'VI', // U.S. Virgin Islands
                'PR', // Puerto Rico
                'GU', // Guam
                'MP', // Northern Mariana Islands
                'FM', // Micronesia
                'MH', // Marshall Islands,
                'PW' // Palau
            ];
            if (in_array($origination['country'], $us_dependent_territories)) {
                $origination['country'] = 'US';
            }

            foreach ($packages as $package) {
                $package_cost = $package['cost'] ?? 0;

                $package_width = !empty($package['shipping_params']['box_width']) ? $package['shipping_params']['box_width'] : $width;
                $package_length = !empty($package['shipping_params']['box_length']) ? $package['shipping_params']['box_length'] : $length;
                $package_height = !empty($package['shipping_params']['box_height']) ? $package['shipping_params']['box_height'] : $height;

                $weight_data = fn_convert_weight_to_imperial_units($package['weight']);
                $package_weight = $weight_data['pounds'] ?? $default_package_weight;

                // Check weight is not 0
                $symbol_grams = Registry::get('settings.General.weight_symbol_grams');
                if (empty($package_weight)) {
                    $package_weight = (100 / $symbol_grams);
                }

                // Any USPS mail can only be sent from US
                if ($origination['country'] === 'US') {
                    $data = [
                        'pricingOptions' => [
                            [
                                'priceType' => $price_type
                            ]
                        ],
                        'originZIPCode' => $origination['zipcode'],
                        'packageDescription' => [
                            'weight' => $package_weight,
                            'length' => (int) $package_length,
                            'height' => (int) $package_height,
                            'width' => (int) $package_width,
                            'girth' => (int) $package_girth,
                            'mailClass' => $mail_class,
                            'packageValue' => $package_cost
                        ]
                    ];

                    // Domestic (only in US and US dependent territories)
                    if ($origination['country'] === $location['country']) {
                        $this->is_domestic = true;
                        $_services = [];

                        foreach ($shipping_settings as $service => $service_enabled) {
                            if (strpos($service, 'domestic_service_') !== 0 || $service_enabled !== YesNo::YES) {
                                continue;
                            }
                            $_services[] = (int) substr($service, strlen('domestic_service_'));
                        }

                        $data['destinationZIPCode'] = $location['zipcode'];

                        // International
                    } else {
                        $this->is_domestic = false;
                        $_services = [];

                        foreach ($shipping_settings as $service => $service_enabled) {
                            if (strpos($service, 'international_service_') !== 0 || $service_enabled !== YesNo::YES) {
                                continue;
                            }
                            $_services[] = (int) substr($service, strlen('international_service_'));
                        }

                        $data['foreignPostalCode'] = $location['zipcode'];
                        $data['destinationCountryCode'] = $location['country'];
                    }

                    if (!empty($_services)) {
                        $data['packageDescription']['extraServices'] = $_services;
                    }
                }

                if (!empty($data)) {
                    $result[] = $data;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function processResponse($response)
    {
        $return = [
            'cost' => false,
            'error' => false,
            'delivery_time' => false,
        ];

        if (empty($response)) {
            $return['error'] = $this->processErrors($response);
        } else {
            $response = (array) $response;
            $cost = 0.0;

            foreach ($response as $response_item) {
                $rates = $this->processRates($response_item, $this->is_domestic, $this->shipping_info['service_params']);

                if (isset($rates['cost'])) {
                    $cost += $rates['cost'];
                    $return['delivery_time'] = $rates['delivery_time'] ?? false;

                    continue;
                }

                $return['error'] = $this->processErrors($response_item);

                return $return;
            }

            $return['cost'] = $cost;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function processErrors($response)
    {
        $response = json_decode($response);
        $error = !empty($response->error) ? $response->error : [];

        if (!empty($error) && !empty($response)) {
            $return = $error->message . ' (Error code: ' . $error->code . ') ';

            if (!empty($error->errors)) {
                foreach ($error->errors as $_error) {
                    if (empty($_error->detail) || empty($_error->title)) {
                        continue;
                    }
                    $return .= $_error->title . ' ' . $_error->detail;
                }
            }
        } else {
            $return = __('shippings.usps.error_empty_response');
        }
        return $return;
    }

    /**
     * Gets shipping service rate
     *
     * @param string $response          Response from shipping service server
     * @param bool   $is_domestic       Flag of domestic delivery
     * @param array  $shipping_settings Shipping service settings
     *
     * @return array|false
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function processRates($response, $is_domestic, array $shipping_settings)
    {
        if (empty($response)) {
            return false;
        }

        if ($is_domestic) {
            $processing_category = $shipping_settings['processing_category'] ?? null;
            $rate_indicator = $shipping_settings['rate_indicator'] ?? null;
            $destination_entry_facility_type = $shipping_settings['destination_entry_type'] ?? null;
        } else {
            $processing_category = $shipping_settings['intl_processing_category'] ?? null;
            $rate_indicator = $shipping_settings['intl_rate_indicator'] ?? null;
            $destination_entry_facility_type = $shipping_settings['intl_destination_entry_type'] ?? null;
        }

        if ($processing_category === null || $rate_indicator === null || $destination_entry_facility_type === null) {
            return false;
        }

        $return = [];

        $response = json_decode($response);

        $pricing_options = $response->pricingOptions[0] ?? null;

        if ($pricing_options === null) {
            return false;
        }

        $shipping_options = $pricing_options->shippingOptions[0] ?? null;

        if ($shipping_options === null) {
            return false;
        }

        $rate_options = $shipping_options->rateOptions ?? null;

        if ($rate_options === null) {
            return false;
        }

        $rates_found = false;
        foreach ($rate_options as $rate_option) {
            if ($rates_found) {
                continue;
            }

            $rates = $rate_option->rates[0] ?? null;

            if (
                !isset($rates->processingCategory) || $processing_category !== $rates->processingCategory
                || !isset($rates->rateIndicator) || $rate_indicator !== $rates->rateIndicator
                || !isset($rates->destinationEntryFacilityType) || $destination_entry_facility_type !== $rates->destinationEntryFacilityType
            ) {
                continue;
            }

            $rates_found = true;

            $total_price = $rate_option->totalPrice ?? $rate_option->totalBasePrice ?? false;
            $return['cost'] = $total_price;

            if (!empty($rate_option->commitment->name)) {
                $return['delivery_time'] = $rate_option->commitment->name;
            }
        }

        return $return;
    }

    /**
     * Process simple request to shipping service server
     *
     * @return array<string, string>
     */
    public function getSimpleRates()
    {
        /** @var array $shipping_settings */
        $shipping_settings = $this->shipping_info['service_params'];
        $url = YesNo::toBool($shipping_settings['test_mode']) ? self::URL_DEVELOPMENT . '/options/search' : self::URL_PRODUCTION . '/options/search';

        $multi_data = $this->getRequestData();

        foreach ($multi_data as $data) {
            Http::mpost(
                $url,
                json_encode($data),
                ['headers' => $this->getRequestHeaders()]
            );
        }

        return Http::processMultiRequest();
    }

    /**
     * Fill required address fields
     *
     * @param array<string> $address Address data
     *
     * @return array<string>
     */
    public function prepareAddress(array $address)
    {
        $default_fields = [
            'zipcode' => '',
            'country' => ''
        ];

        // The zip code should be in 5 digit format so we cut all digits after "-"
        $address = array_merge($default_fields, $address);
        $address['zipcode'] = preg_replace('/-\d*/i', '', trim($address['zipcode']));

        return $address;
    }

    /**
     * Returns shipping service information
     *
     * @return array<string>
     */
    public static function getInfo()
    {
        return [
            'name' => __('carrier_uspsv2'),
            'tracking_url' => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%s'
        ];
    }

    /**
     * Makes request to API endpoint.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @param string                $endpoint API endpoint.
     * @param string                $method   Type of the request API.
     * @param array<string, string> $params   Encoded body of request.
     * @param array<string, mixed>  $headers  Request headers.
     *
     * @return array<string, string>
     */
    protected function execute($endpoint, $method, array $params, array $headers = []): array
    {
        switch ($method) {
            case Http::GET:
                $answer = Http::get($endpoint, $params, $headers);
                break;
            case Http::POST:
                $answer = Http::post($endpoint, $params, $headers);
                break;
            default:
                $answer = '';
                break;
        }

        return json_decode($answer, true);
    }
}
