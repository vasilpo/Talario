<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Shippings\Services;

use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Shippings\IService;
use Tygh\Http;

/**
 * FedEx shipping service.
 * Uses FedEx Rest API
 */
class Fedexrest implements IService
{
    /**
     * API version
     */
    private const VERSION = 'v1';
    /**
     * Production service URL
     */
    private const URL_PRODUCTION  = 'https://apis.fedex.com/rate/' . self::VERSION;
    /**
     * Development service URL
     */
    private const URL_DEVELOPMENT = 'https://apis-sandbox.fedex.com/rate/' . self::VERSION;
    /**
     * Production URL of getting the access token
     */
    private const URL_PRODUCTION_GET_TOKEN = 'https://apis.fedex.com/oauth/token';
    /**
     * Development URL of getting the access token
     */
    private const URL_DEVELOPMENT_GET_TOKEN = 'https://apis-sandbox.fedex.com/oauth/token';
    /**
     * Special services type: Package
     */
    private const SPECIAL_PACKAGE = 'package';
    /**
     * Special services type: Shipment
     */
    private const SPECIAL_SHIPMENT = 'shipment';
    /**
     * Special services type: Package
     */
    private const REGULAR_SHIPMENT_ENDPOINT = '/rates/quotes';
    /**
     * Special services type: Shipment
     */
    private const FREIGHT_SHIPMENT_ENDPOINT = '/freight/rates/quotes';
    /**
     * Shipment type: Regular
     */
    private const REGULAR_SHIPMENT = 'R';
    /**
     * Shipment type: Freight
     */
    private const FREIGHT_SHIPMENT = 'F';
    /**
     * Weight: pounds
     */
    const WEIGHT_POUNDS = 'LB';
    /**
     * Weight: kilograms
     */
    const WEIGHT_KILOGRAMS = 'KG';
    /**
     * Length: inches
     */
    const LENGTH_INCHES = 'IN';
    /**
     * Length: centimetres
     */
    const LENGTH_CENTIMETRES = 'CM';
    /**
     * Name of the token cache key
     */
    private const ACCESS_TOKEN_CACHE_NAME = 'fedex_api_access_key';
    /**
     * @var array<string, array<string, string>> $package
     */
    private $package;
    /**
     * Stored shipping information
     *
     * @var array<string, array<string, string>> $shipping_info
     */
    private $shipping_info = [];
    /**
     * @var string Calculation currency info
     */
    public $calculation_currency;
    /**
     * @var string URL with the API endpoint
     */
    private $service_url;
    /**
     * @var string Freight_shipment_id
     */
    private $freight_shipment_id;

    /**
     * @param string $calculation_currency Calculation currency
     */
    public function __construct($calculation_currency = CART_PRIMARY_CURRENCY)
    {
        $this->calculation_currency = $calculation_currency;
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
            : 'live';

        Registry::registerCache(
            self::ACCESS_TOKEN_CACHE_NAME . $current_mode,
            SECONDS_IN_HOUR,
            Registry::cacheLevel(['time'])
        );

        if (!Registry::isExist(self::ACCESS_TOKEN_CACHE_NAME . $current_mode)) {
            $params = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->shipping_info['service_params']['api_key'],
                'client_secret' => $this->shipping_info['service_params']['secret_key']
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
     * @inheritdoc
     *
     * @psalm-suppress MissingParamType
     */
    public function prepareData($shipping_info): void
    {
        $this->shipping_info = $shipping_info;

        $this->package = array_merge($this->shipping_info['package_info'], [
            'origination' => $this->prepareAddress($this->shipping_info['package_info']['origination'] ?? []),
            'location' => $this->prepareAddress($this->shipping_info['package_info']['location'] ?? []),
        ]);

        $endpoint = $this->getShipmentType() === self::FREIGHT_SHIPMENT
            ? self::FREIGHT_SHIPMENT_ENDPOINT
            : self::REGULAR_SHIPMENT_ENDPOINT;

        $this->service_url = $this->shipping_info['service_params']['test_mode'] === YesNo::YES
            ? self::URL_DEVELOPMENT . $endpoint
            : self::URL_PRODUCTION . $endpoint;
    }

    /**
     * Fill required address fields
     * TODO: Add to \Tygh\Shippings\IService
     *
     * @param array<string> $address Address data
     *
     * @return array<string> Filled address data
     */
    public function prepareAddress(array $address): array
    {
        $default_fields = [
            'address' => '',
            'zipcode' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'name' => '',
        ];

        return array_merge($default_fields, $address);
    }

    /**
     * @inheritdoc
     */
    public function processResponse($response): array
    {
        if (empty($response)) {
            return $this->processErrors($response);
        }
        // FedEx returns gzip format of response body on errors
        $decoded_response = json_decode($response, true);
        $response = !$decoded_response ? json_decode(gzdecode($response), true) : $decoded_response;

        $return = [
            'cost' => false,
            'error' => false,
            'delivery_time' => false,
        ];

        $code = $this->shipping_info['service_code'];
        $rates = $this->processRates($response);

        if (isset($rates[$code])) {
            $return['cost'] = $rates[$code]['rate'];

            if (isset($rates[$code]['delivery_time'])) {
                $return['delivery_time'] = $rates[$code]['delivery_time'];
            }
        } else {
            $return['error'] = $this->processErrors($response);
        }

        return $return;
    }

    /**
     * Processes the response from Shipping service with rate information
     *
     * @param array<string, array<string, string>> $response Response from Shipping service server
     *
     * @return array<string, array<string, string>>
     */
    protected function processRates(array $response): array
    {
        $return = [];

        if (
            !empty($response['output']['rateReplyDetails'])
            && !isset($response['errors'])
        ) {
            $response_body = (array) $response['output']['rateReplyDetails'];

            foreach ($response_body as $shipment) {
                $total_charge = reset($shipment['ratedShipmentDetails']);
                $service_code = (string) $shipment['serviceType'];

                if (
                    empty($service_code)
                    || empty($total_charge['totalNetCharge'])
                ) {
                    continue;
                }

                $return[$service_code] = [
                    'rate' => (string) $total_charge['totalNetCharge']
                ];
                $return[$service_code]['delivery_time'] = $shipment['operationalDetail']['publishedDeliveryTime'] ?? '';
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function processErrors($response): string
    {
        $errors = !empty($response['errors']) ? $response['errors'] : [];
        $return = '';

        if (!empty($errors) && !empty($response)) {
            foreach ($errors as $error) {
                $return .= $error['message'] . ' (Error code: ' . $error['code'] . ') ';
            }
        } else {
            $return .= __('shippings.fedex.error_empty_response');
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function allowMultithreading()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getSimpleRates(): array
    {
        $data = $this->getRequestData();
        return Http::post($data['url'], $data['data'], $data['headers']);
    }

    /**
     * @inheritdoc
     */
    public function getRequestData(): array
    {
        return [
            'method' => Http::POST,
            'url' => $this->service_url,
            'data' => json_encode($this->getRateRequestBody()),
            'headers' => $this->getRateRequestHeaders(),
        ];
    }

    /**
     * Provides content of Access Request.
     *
     * @return array<array-key, string> Access Request
     */
    private function getRateRequestHeaders(): array
    {
        return [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * Provides content for rates retrieving
     *
     * @return array<string, string|int|array|object|float> Rate request body
     */
    public function getRateRequestBody(): array
    {
        return self::getShipmentType() === self::FREIGHT_SHIPMENT
            ? $this->prepareFreightShipmentDetails()
            : $this->prepareRegularShipmentDetails();
    }

    /**
     * Prepares shipping information for request data
     *
     * @param array<string> $address      Address data (Zipcode, Country, State, etc)
     * @param string        $address_type A type of 'recipient' or 'shipper'
     * @param string        $code         Service code (E.g.: SMART_POST)
     *
     * @psalm-return array{address: array{city: string, countryCode: string, postalCode: string, residential?: bool, stateOrProvinceCode: string}}
     *
     * @return array<string, string|bool|int> Shipping info
     */
    private function prepareShippingInfo(array $address, string $address_type = 'shipper', string $code = ''): array
    {
        $shipping_info = [
            'address' => [
                'city' => $address['city'],
                'stateOrProvinceCode' => (string) (strlen($address['state']) > 2) ? '' : $address['state'],
                'postalCode' => self::formatPostalCode($address['zipcode']),
                'countryCode' => $address['country']
            ]
        ];

        if ($address_type === 'recipient' && $code === 'FEDEX_GROUND') {
            $shipping_info['address']['residential'] = false;
        }
        if ($address_type === 'recipient' && ($code === 'GROUND_HOME_DELIVERY' || empty($address['address_type']))) {
            $shipping_info['address']['residential'] = true;
        }

        return $shipping_info;
    }

    /**
     * Formats postal code
     *
     * @param string $code Not formatted postal code
     *
     * @return string Formatted postal code
     */
    public static function formatPostalCode($code): string
    {
        if (preg_match_all('/[\d\w]/', $code, $matches)) {
            return implode('', $matches[0]);
        }

        return '';
    }

    /**
     * Formats special services details for the package
     *
     * @param string $type Special service type
     *
     * @return array{
     *    dangerousGoodsDetail?: array{
     *        accessibility: string,
     *        options: array<empty, empty>|string
     *    },
     *    packageCODDetail?: array{
     *        codCollectionAmount: array{
     *            amount: float,
     *            currency: string
     *        },
     *        codCollectionType: string
     *    },
     *    shipmentCODDetail?: array{
     *        codCollectionType: string
     *    },
     *    specialServiceTypes?: non-empty-list<int>
     * }
     */
    private function prepareSpecialServices($type = self::SPECIAL_SHIPMENT): array
    {
        // DANGEROUS_GOODS type is not allowed on the shipment level
        $special_services = [];

        if (
            !empty($this->shipping_info['service_params']['options'])
        ) {
            foreach ((array) $this->shipping_info['service_params']['options'] as $code => $value) { // phpcs:disable
                switch ($code) {
                    case 'COD_AMOUNT':
                    case 'COD_COLLECTION_TYPE':
                    case 'DANGEROUS_GOODS_OPTIONS':
                    case 'DANGEROUS_GOODS_ACCESSIBILITY':
                        continue 2;
                    case 'COD':
                        if (
                            isset($this->shipping_info['service_code'])
                            && $this->isCodAvailable($this->shipping_info['service_code'], $type)
                        ) {
                            $special_services['specialServiceTypes'][] = $code;
                            if ($type === self::SPECIAL_SHIPMENT) {
                                $special_services['shipmentCODDetail'] = [
                                    'codCollectionType' => (string) $this->shipping_info['service_params']['options']['COD_COLLECTION_TYPE']
                                ];
                            } elseif ($type === self::SPECIAL_PACKAGE) {
                                $special_services['packageCODDetail'] = [
                                    'codCollectionType' => $this->shipping_info['service_params']['COD_COLLECTION_TYPE'],
                                    'codCollectionAmount' => [
                                        'amount' => (float) $this->shipping_info['service_params']['COD_AMOUNT'],
                                        'currency' => $this->calculation_currency,
                                    ]
                                ];
                            }
                        }
                        break;
                    case 'DANGEROUS_GOODS':
                        if ($type === self::SPECIAL_PACKAGE) {
                            $special_services['specialServiceTypes'][] = $code;
                            $special_services['dangerousGoodsDetail'] = [
                                'accessibility' => $this->shipping_info['service_params']['DANGEROUS_GOODS_ACCESSIBILITY'],
                                'options' => isset($this->shipping_info['service_params']['DANGEROUS_GOODS_OPTIONS'])
                                    ? $this->shipping_info['service_params']['DANGEROUS_GOODS_OPTIONS']
                                    : [],
                            ];
                        }
                        break;
                    default:
                        if ($type === self::SPECIAL_SHIPMENT) {
                            $special_services['specialServiceTypes'][] = $code;
                        }
                }
            }
        }
        return $special_services;
    }

    /**
     * Checks if COD could be specified at the shipment or at the package level
     *
     * @param string $service_code Service code
     * @param string $level        Level (shipment or package)
     *
     * @return bool True COD is allowed at the level
     */
    private function isCodAvailable($service_code, $level = self::SPECIAL_SHIPMENT): bool
    {
        if (in_array($service_code, ['FEDEX_GROUND', 'GROUND_HOME_DELIVERY'])) {
            return $level === self::SPECIAL_PACKAGE;
        }

        return $level === self::SPECIAL_SHIPMENT;
    }

    /**
     * Prepares Smart Post information
     *
     * @psalm-return array{ancillaryEndorsement?: non-falsy-string, hubId?: non-falsy-string, indicia?: non-falsy-string, specialServices?: "USPS_DELIVERY_CONFIRMATION"}
     *
     * @return array<string, array<string, string|int>> Data for SmartPost section in the request
     */
    private function prepareSmartPostDetails(): array
    {
        $smart_post = [];

        if (
            $this->shipping_info['service_code'] === 'SMART_POST'
            && !empty($this->shipping_info['service_params']['hub_id'])
            && !empty($this->shipping_info['service_params']['indicia'])
        ) {
            $smart_post['indicia'] = $this->shipping_info['service_params']['indicia'];
            if (!empty($this->shipping_info['service_params']['ancillary_endorsement'])) {
                $smart_post['ancillaryEndorsement'] = $this->shipping_info['service_params']['ancillary_endorsement'];
            }
            if (
                !empty($this->shipping_info['service_params']['special_services'])
                && $this->shipping_info['service_params']['special_services'] === 'Y'
            ) {
                $smart_post['specialServices'] = 'USPS_DELIVERY_CONFIRMATION';
            }
            $smart_post['hubId'] = $this->shipping_info['service_params']['hub_id'];
        }

        return $smart_post;
    }

    /**
     * Prepares packages information
     *
     * @param bool $is_freight If true, packages will be calculated for the freight shipment.
     *                                  Otherwise - for the regular shipment
     *
     * @psalm-return array{array{associatedFreightLineItems?: array{array{id: string}}, declaredValue: array{amount: float, currency: string}, dimensions: array{height: int, length: int, units: string, width: int}, packageSpecialServices: object, subPackagingType: string, weight: array{units: string, value: float}}}
     *
     * @return array<string, array<string, string|int|float>> Prepared packages information
     */
    private function preparePackages(bool $is_freight = false): array
    {
        $packages = [];
        [$weight_unit, $length_unit] = $this->getMeasurementUnits();
        $weight = !isset($this->package['W']) ? 0.0 : (float) $this->package['W'];

        $default_dimensions = [
            'box_length' => $this->shipping_info['service_params']['length'] ?? 0,
            'box_width' => $this->shipping_info['service_params']['width'] ?? 0,
            'box_height' => $this->shipping_info['service_params']['height'] ?? 0,
            'weight' => $this->prepareWeight($weight, $weight_unit),
        ];
        if (empty($this->package['packages'])) {
            $packages[] = [
                'shipping_params' => [
                    'box_length' => $default_dimensions['box_length'],
                    'box_width' => $default_dimensions['box_width'],
                    'box_height' => $default_dimensions['box_height'],
                ],
                'weight' => $default_dimensions['weight'],
                'cost' => $this->package['C'],
                'amount' => $this->package['I'],
            ];
        } else {
            $packages = $this->package['packages'];
        }

        $packages_node = [];

        foreach ($packages as $package) {
            $box_length = isset($package['shipping_params']['box_length'])
                ? $package['shipping_params']['box_length']
                : $default_dimensions['box_length'];
            $box_width = isset($package['shipping_params']['box_width'])
                ? $package['shipping_params']['box_width']
                : $default_dimensions['box_width'];
            $box_height = isset($package['shipping_params']['box_height'])
                ? $package['shipping_params']['box_height']
                : $default_dimensions['box_height'];
            $package_weight = empty($package['weight'])
                ? $default_dimensions['weight']
                : $this->prepareWeight($package['weight'], $weight_unit);

            $package_node = [
                'subPackagingType' => $this->shipping_info['service_params']['subpackage_type'],
                'groupPackageCount' => 1,
                'weight' => [
                    'value' => $package_weight,
                    'units' => $weight_unit,
                ],
                'dimensions' => [
                    'length' => (int) $box_length,
                    'width' => (int) $box_width,
                    'height' => (int) $box_height,
                    'units' => $length_unit
                ],
                'packageSpecialServices' => (object) $this->prepareSpecialServices(self::SPECIAL_PACKAGE),
                'declaredValue' => [
                    'amount' => (float) $package['amount'],
                    'currency' => $this->calculation_currency
                ]
            ];

            if ($is_freight) {
                $package_node['associatedFreightLineItems'] = [
                    [
                        'id' => $this->freight_shipment_id,
                    ]
                ];
            }
            $packages_node[] = $package_node;
        }

        return $packages_node;
    }

    /**
     * Prepares array with regular service shipment details
     *
     * @return array<string, string|int|array|object|float> Prepared data for regular shipment
     */
    private function prepareRegularShipmentDetails(): array
    {
        return [
            'accountNumber' => [
                'value' => $this->shipping_info['service_params']['account_number'],
            ],
            'requestedShipment' => [
                'shipper' => $this->prepareShippingInfo($this->package['origination']),
                'recipient' => $this->prepareShippingInfo(
                    $this->package['location'],
                    'recipient',
                    $this->shipping_info['service_code']
                ),
                'serviceType' => $this->shipping_info['service_code'],
                'preferredCurrency' => $this->calculation_currency,
                'rateRequestType' => ['ACCOUNT'],
                'packagingType' => $this->shipping_info['service_params']['package_type'],
                'pickupType' => $this->shipping_info['service_params']['pickup_type'],
                'smartPostInfoDetail' => (object) $this->prepareSmartPostDetails(),
                'shipmentSpecialServices' => (object) $this->prepareSpecialServices(),
                'requestedPackageLineItems' => $this->preparePackages(),
                'customsClearanceDetail' => [
                    'dutiesPayment' => [
                        'payor' => [
                            'responsibleParty' => [
                                'accountNumber' => [
                                    'value' => $this->shipping_info['service_params']['account_number']
                                ],
                                'address' => $this->prepareShippingInfo($this->package['origination'])
                            ]
                        ],
                        'paymentType' => 'SENDER',
                    ],
                ]
            ]
        ];
    }

    /**
     * Prepares array with freight service shipment details
     *
     * @return array<string, string|int|array|object|float> Prepared data for freight shipment
     */
    private function prepareFreightShipmentDetails(): array
    {
        $this->freight_shipment_id = uniqid();
        $params = reset($this->package['packages']);
        [$weight_unit, $length_unit] = $this->getMeasurementUnits();

        $default_length = $this->shipping_info['service_params']['length'] ?? 0;
        $default_width  = $this->shipping_info['service_params']['width'] ?? 0;
        $default_height = $this->shipping_info['service_params']['height'] ?? 0;

        $dimensions = [
            'length' => isset($params['shipping_params']['box_length'])
                ? $params['shipping_params']['box_length']
                : $default_length,
            'width' => isset($params['shipping_params']['box_width'])
                ? $params['shipping_params']['box_width']
                : $default_width,
            'height' => isset($params['shipping_params']['box_height'])
                ? $params['shipping_params']['box_height']
                : $default_height,
            'weight' => $this->prepareWeight($this->package['W'], $weight_unit),
        ];

        $package_item_count = $this->package['I'] ?? 0;

        return [
            'accountNumber' => [
                'value' => $this->shipping_info['service_params']['account_number'],
            ],
            'freightRequestedShipment' => [
                'shipper' => $this->prepareShippingInfo($this->package['origination']),
                'recipient' => $this->prepareShippingInfo(
                    $this->package['location'],
                    'recipient',
                    $this->shipping_info['service_code']
                ),
                'serviceType' => $this->shipping_info['service_code'],
                'preferredCurrency' => $this->calculation_currency,
                'rateRequestType' => ['ACCOUNT'],
                'shippingChargesPayment' => [
                    'paymentType' => 'SENDER',
                    'payor' => [
                        'responsibleParty' => [
                            'accountNumber' => [
                                'value' => $this->shipping_info['service_params']['freight_account_number'],
                            ],
                        ],
                    ],
                ],
                'freightShipmentDetail' => [
                    'role' => 'SHIPPER',
                    'accountNumber' => [
                        'value' => $this->shipping_info['service_params']['freight_account_number'],
                    ],
                    'shipmentDimensions' => [
                        'length' => $dimensions['length'],
                        'width' => $dimensions['width'],
                        'height' => $dimensions['height'],
                        'units' => $length_unit
                    ],
                    'lineItem' => [
                        [
                            'handlingUnits' => 0,
                            'subPackagingType' => $this->shipping_info['service_params']['subpackage_type'],
                            'weight' => [
                                'value' => $dimensions['weight'],
                                'units' => $weight_unit,
                            ],
                            'pieces' => 0,
                            'freightClass' => self::getFreightClass(
                                $dimensions['length'],
                                $dimensions['width'],
                                $dimensions['height'],
                                $dimensions['weight']
                            ),
                            'id' => $this->freight_shipment_id,
                        ]
                    ],
                    'totalHandlingUnits' => $package_item_count,
                    'alternateBillingParty' => [
                        'address' => $this->prepareShippingInfo($this->package['location']),
                        'accountNumber' => [
                            'value' => $this->shipping_info['service_params']['freight_account_number'],
                        ],
                    ],
                ],
                'requestedPackageLineItems' => $this->preparePackages(true),
            ],
        ];
    }

    /**
     * Returns shipping service information
     *
     * @return array<string> information
     */
    public static function getInfo(): array
    {
        return [
            'name' => __('carrier_fedexrest'),
            'tracking_url' => 'https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber=%s'
        ];
    }

    /**
     * Determines freight class of the package.
     *
     * @param float $length Length in inches
     * @param float $width  Width in inches
     * @param float $height Height in inches
     * @param float $weight Weight in pounds
     *
     * @return string Freight class
     */
    protected static function getFreightClass($length, $width, $height, $weight): string
    {
        $class = '500';

        $volume = $length * $width * $height / pow(12, 3); // volume in cubic feet

        if ($volume > 0) {
            $density = $weight / $volume; // density in lbs per cubic feet
            $classes = [
                '50' => [50, INF],
                '55' => [35, 50],
                '60' => [30, 35],
                '65' => [22.5, 30],
                '70' => [15, 22.5],
                '77.5' => [13.5, 15],
                '85' => [12, 13.5],
                '92.5' => [10.5, 12],
                '100' => [9, 10.5],
                '110' => [8, 9],
                '125' => [7, 8],
                '150' => [6, 7],
                '175' => [5, 6],
                '200' => [4, 5],
                '250' => [3, 4],
                '300' => [2, 3],
                '400' => [1, 2],
                '500' => [-INF, 1]
            ];

            foreach ($classes as $class => $limits) {
                if ($density >= $limits[0] && $density < $limits[1]) {
                    break;
                }
            }
        }

        return 'CLASS_' . ((float) $class < 100 ? '0' : '') . str_replace('.', '_', (string) $class);
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

    /**
     * Returns the shipment service to be used in calculations
     *
     * @return string Shipment type
     */
    protected function getShipmentType(): string
    {
        if (
            in_array($this->shipping_info['service_code'], [
                'FEDEX_FREIGHT_PRIORITY',
                'FEDEX_FREIGHT_ECONOMY',
                'FEDEX_NEXT_DAY_FREIGHT'
            ])
            && !empty($this->shipping_info['service_params']['freight_account_number'])
        ) {
            return self::FREIGHT_SHIPMENT;
        }

        return self::REGULAR_SHIPMENT;
    }

    /**
     * Gets measurement units for the specified country.
     *
     * @param string $country Country code
     *
     * @return array<string> Weight unit and length unit
     */
    protected function getMeasurementUnits($country = ''): array
    {
        if (empty($country)) {
            $country = $this->package['origination']['country'];
        }

        if (in_array($country, ['US', 'DO', 'PR'])) {
            $weight_unit = self::WEIGHT_POUNDS;
            $length_unit = self::LENGTH_INCHES;
        } else {
            $weight_unit = self::WEIGHT_KILOGRAMS;
            $length_unit = self::LENGTH_CENTIMETRES;
        }

        return [$weight_unit, $length_unit];
    }

    /**
     * Converts weight to pounds or kilograms depending on the origination country.
     *
     * @param float  $weight Weight of the package in the primary weight unit
     * @param string $unit   Weight unit: pounds (self::WEIGHT_POUNDS) or kilograms (self::WEIGHT_KILOGRAMS)
     *
     * @return float Weight in the selected unit
     */
    private function prepareWeight($weight, $unit): float
    {
        if ($unit === self::WEIGHT_POUNDS) {
            $weight = fn_convert_weight_to_imperial_units($weight);
            $weight = $weight['full_pounds'];
        } else {
            $weight = $weight / 1000 * Registry::get('settings.General.weight_symbol_grams');
        }

        return $weight;
    }
}
