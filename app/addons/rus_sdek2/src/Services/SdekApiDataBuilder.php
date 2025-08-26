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

namespace Tygh\Addons\RusSdek2\Services;

use CdekSDK2\BaseTypes\Contact;
use CdekSDK2\BaseTypes\Intake;
use CdekSDK2\BaseTypes\Item;
use CdekSDK2\BaseTypes\Location;
use CdekSDK2\BaseTypes\Money;
use CdekSDK2\BaseTypes\Order;
use CdekSDK2\BaseTypes\Package;
use CdekSDK2\BaseTypes\Phone;
use CdekSDK2\BaseTypes\Seller;
use DateTime;
use Tygh\Addons\RusTaxes\Receipt\Item as ReceiptItem;
use Tygh\Addons\RusTaxes\Receipt\Receipt;
use Tygh\Addons\RusTaxes\TaxType;
use Tygh\Database\Connection;
use Tygh\Enum\YesNo;
use Tygh\Registry;

class SdekApiDataBuilder
{
    /** @var Connection */
    protected $db;

    /** @var \Tygh\Addons\RusSdek2\Services\SdekService */
    protected $sdek_service;

    /** @var array<string> */
    protected $currencies;

    /** @var string */
    protected $default_currency;

    /** @var string */
    protected $company_city;

    /** @var string */
    protected $company_address;

    /** @var string */
    protected $company_name;

    /** @var float */
    protected $symbol_grams;

    /** @var array<array<string>> */
    protected $sdek_tariffs;

    /** @var array<string> */
    protected $currency_sdek;

    /**
     * @param Connection  $db               DB connection
     * @param SdekService $sdek_service     Sdek service
     * @param array       $currencies       Currencies data
     * @param string      $default_currency Default currency
     * @param string      $company_city     Company city
     * @param string      $company_address  Company address
     * @param string      $company_name     Company name
     * @param float       $symbol_grams     Symbol grams
     * @param array       $currency_sdek    SDEK currencies map
     * @param array       $sdek_tariffs     SDEK tariff list
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function __construct(
        Connection $db,
        SdekService $sdek_service,
        array $currencies,
        $default_currency,
        $company_city,
        $company_address,
        $company_name,
        $symbol_grams,
        array $currency_sdek,
        array $sdek_tariffs
    ) {
        $this->db = $db;
        $this->sdek_service = $sdek_service;
        $this->currencies = $currencies;
        $this->default_currency = $default_currency;
        $this->company_city = $company_city;
        $this->company_address = $company_address;
        $this->company_name = $company_name;
        $this->symbol_grams = $symbol_grams;
        $this->currency_sdek = $currency_sdek;
        $this->sdek_tariffs = $sdek_tariffs;
    }

    /**
     * Builds SDEK order data for API request.
     *
     * @param array                                 $order_info      Order data
     * @param array                                 $shipment_info   Shipment data
     * @param array                                 $sdek_order_info SDEK order info from request
     * @param \Tygh\Addons\RusTaxes\Receipt\Receipt $receipt         Receipt item object
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function prepareCreateOrderData(
        array $order_info,
        array $shipment_info,
        array $sdek_order_info,
        Receipt $receipt
    ): Order {
        $shipment_id = $shipment_info['shipment_id'];
        $default_currency = !empty($order_info['secondary_currency']) ? $order_info['secondary_currency'] : $this->default_currency;

        $order_for_sdek = $sdek_order_info;

        foreach ($shipment_info['products'] as $item_id => $amount) {
            $receipt->setItemQuantity($item_id, ReceiptItem::TYPE_PRODUCT, $amount);
        }

        $extra_params = [];
        if ($order_info['b_country'] !== 'RU' && $order_info['s_country'] !== 'RU') {
            if (!empty($this->currency_sdek[$order_info['s_country']])) {
                $extra_params['currency'] = $this->currency_sdek[$order_info['s_country']];
            } elseif (!empty($this->currency_sdek[$order_info['b_country']])) {
                $extra_params['currency'] = $this->currency_sdek[$order_info['b_country']];
            } else {
                $extra_params['currency'] = CART_PRIMARY_CURRENCY;
            }

            $order_for_sdek['seller'] = ['address' => $this->company_city . ', ' . $this->company_address];
            $order_for_sdek['shipper_name'] = $this->company_name;
            $order_for_sdek['shipper_address'] = $order_for_sdek['seller']['address'];
            // phpcs:ignore
            $order_for_sdek['date_invoice'] = date("Y-m-d", $shipment_info['shipment_timestamp']);
        }

        $order_for_sdek['delivery_recipient_cost'] = [
            'value' => !empty($order_for_sdek['delivery_recipient_cost']['value'])
                ? $order_for_sdek['delivery_recipient_cost']['value']
                : '0.00'
        ];

        $recipient_cost = $this->getPriceByCurrency(
            $order_for_sdek['delivery_recipient_cost']['value'],
            $extra_params,
            $this->currencies,
            $default_currency
        );
        if (!empty($recipient_cost)) {
            $order_for_sdek['delivery_recipient_cost']['value'] = $recipient_cost;
        }

        $shipping_receipt_item = $receipt->getItem(0, ReceiptItem::TYPE_SHIPPING);

        if ($order_for_sdek['delivery_recipient_cost']['value'] > 0 && $shipping_receipt_item) {
            $vat_rate = $this->normalizeTaxType($shipping_receipt_item->getTaxType());
            $vat_sum = $this->calculateTaxSum($vat_rate, $order_for_sdek['delivery_recipient_cost']['value']);

            $order_for_sdek['delivery_recipient_cost']['vat_rate'] = $vat_rate;
            $order_for_sdek['delivery_recipient_cost']['vat_sum'] = number_format($vat_sum, 2, '.', '');
        } else {
            $order_for_sdek['delivery_recipient_cost']['vat_rate'] = $this->normalizeTaxType(TaxType::NONE);
            $order_for_sdek['delivery_recipient_cost']['vat_sum'] = '0.00';
        }

        if (!isset($order_for_sdek['delivery_point'])) {
            $order_for_sdek['to_location'] = $order_for_sdek['to_location'] ?? [];
        } else {
            unset($order_for_sdek['to_location']);
        }

        [$sdek_products, $total_weight] = $this->buildProductsDataForSdekOrder(
            $order_info,
            $shipment_info,
            $shipment_id,
            $this->symbol_grams,
            $receipt
        );

        $weight_grams = $total_weight * $this->symbol_grams;

        $order_for_sdek['packages'][$shipment_id] = [
            'number' => $shipment_id,
            'weight' => $weight_grams
        ];

        foreach ($sdek_products as $product) {
            $product_for_sdek = $this->getDataProduct($product, $sdek_order_info);

            $product_for_sdek['cost'] = $this->getPriceByCurrency(
                $product['price'],
                $extra_params,
                $this->currencies,
                $default_currency
            );
            $product_for_sdek['payment']['value'] = $this->getPriceByCurrency(
                $product_for_sdek['payment']['value'],
                $extra_params,
                $this->currencies,
                $default_currency
            );

            $product['weight'] = $this->sdek_service->checkWeight($product['weight'], $this->symbol_grams);
            $product_for_sdek['weight'] = $product['weight'] * $this->symbol_grams;

            $order_for_sdek['packages'][$shipment_id]['items'][] = $product_for_sdek;
        }

        $order_packages = [];
        if (!empty($order_for_sdek['packages'])) {
            foreach ($order_for_sdek['packages'] as $package) {
                if (empty($package['items'])) {
                    continue;
                }

                $package_items = [];
                foreach ($package['items'] as $_item) {
                    $package_items[] = Item::create([
                        'name' => $_item['name'],
                        'ware_key' => $_item['ware_key'],
                        'payment' => Money::create([
                            'value' => $_item['payment']['value'],
                            'vat_sum' => $_item['payment']['vat_sum'],
                            'vat_rate' => $_item['payment']['vat_rate']
                        ]),
                        'cost' => $_item['cost'],
                        'weight' => $_item['weight'],
                        'amount' => $_item['amount'],
                    ]);
                }

                $order_packages[] = Package::create([
                    'number' => $package['number'],
                    'weight' => $package['weight'],
                    //'length' => 12,
                    //'width' => 11,
                    //'height' => 8,
                    'items' => $package_items,
                ]);
            }
        }

        return Order::create([
            'number' => $order_info['order_id'] . '_' . $shipment_id,
            'tariff_code' => $order_for_sdek['tariff_code'],
            'shipment_point' => $order_for_sdek['shipment_point'] ?? null,
            'delivery_point' => $order_for_sdek['delivery_point'] ?? null,
            'developer_key' => 'X$%:Y83F0&uu$^AN?KN-vx6mpl+zXM+G',
            'date_invoice' => $order_for_sdek['date_invoice'] ?? null,
            'shipper_name' => $order_for_sdek['shipper_name'] ?? null,
            'shipper_address' => $order_for_sdek['shipper_address'] ?? null,
            'delivery_recipient_cost' => Money::create([
                'value' => $order_for_sdek['delivery_recipient_cost']['value'],
                'vat_sum' => $order_for_sdek['delivery_recipient_cost']['vat_sum'],
                'vat_rate' => $order_for_sdek['delivery_recipient_cost']['vat_rate']
            ]),
            'sender' => Contact::create([
                'name' => $this->company_name,
            ]),
            'seller' => !empty($order_for_sdek['seller']['address'])
                ? Seller::create(['address' => $order_for_sdek['seller']['address']])
                : null,
            'recipient' => Contact::create([
                'name' => $this->getNameCustomer($order_info),
                'phones' => [
                    Phone::create(['number' => $this->getPhoneCustomer($order_info)])
                ],
                'email' => $order_info['email']
            ]),
            'from_location' => isset($order_for_sdek['from_location']) ? Location::create($order_for_sdek['from_location']) : null,
            'to_location' => isset($order_for_sdek['to_location']) ? Location::create($order_for_sdek['to_location']) : null,
            'packages' => $order_packages,
        ]);
    }

    /**
     * Builds SDEK order data for API request.
     *
     * @param array  $sdek_schedule_info SDEK schedule info from request
     * @param array  $sdek_order_info    SDEK order info from request
     * @param string $sdek_number        SDEK tracking number
     * @param string $sdek_order_uuid    SDEK order UUID
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function prepareCreateScheduleData(
        array $sdek_schedule_info,
        array $sdek_order_info,
        $sdek_number = null,
        $sdek_order_uuid = null
    ): array {
        // phpcs:ignore
        $calendar_format = "d/m/Y";
        if (Registry::get('settings.Appearance.calendar_date_format') === 'month_first') {
            // phpcs:ignore
            $calendar_format = "m/d/Y";
        }

        $sdek_schedule_info['date'] = DateTime::createFromFormat($calendar_format, $sdek_schedule_info['date'])->format('Y-m-d');

        $schedule_data = [
            'cdek_number' => $sdek_number ?? null,
            'order_uuid' => $sdek_order_uuid ?? null,
            'date' => $sdek_schedule_info['date'] ?? null,
            'time_from' => $sdek_schedule_info['time_from'] ?? null,
            'time_to' => $sdek_schedule_info['time_to'] ?? null,
            'comment' => $sdek_schedule_info['comment'] ?? null
        ];

        if (
            !empty($this->sdek_tariffs[$sdek_order_info['tariff_code']]['terminals'])
            && !YesNo::toBool($this->sdek_tariffs[$sdek_order_info['tariff_code']]['terminals'])
        ) {
            $schedule_data['to_location'] = [
                'address' => $sdek_order_info['to_location']['address'] ?? null
            ];
        } else {
            $schedule_data['delivery_point'] = $sdek_order_info['delivery_point'] ?? null;
        }

        return $schedule_data;
    }

    /**
     * Builds SDEK order data for API request.
     *
     * @param array  $sdek_call_courier_info SDEK call courier info from request
     * @param string $sdek_number            SDEK tracking number
     * @param string $sdek_order_uuid        SDEK order UUID
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function prepareCreateCallCourierData(
        array $sdek_call_courier_info,
        $sdek_number = null,
        $sdek_order_uuid = null
    ): Intake {
        if (!empty($sdek_call_courier_info['sender'])) {
            $sender = Contact::create([
                'name' => $sdek_call_courier_info['sender']['name'] ?? '',
                'company' => $sdek_call_courier_info['sender']['company'] ?? null,
                'phones' => [
                    Phone::create(['number' => $sdek_call_courier_info['sender']['phones']['number'] ?? ''])
                ]
            ]);
        } else {
            $sender = null;
        }

        if (!empty($sdek_call_courier_info['from_location'])) {
            $from_location = Location::create($sdek_call_courier_info['from_location']);
        } else {
            $from_location = null;
        }

        $intake = Intake::create([]);
        $intake->cdek_number = $sdek_number;
        $intake->order_uuid = $sdek_order_uuid;
        $intake->intake_date = $sdek_call_courier_info['intake_date'] ?? null;
        $intake->intake_time_from = $sdek_call_courier_info['intake_time_from'] ?? null;
        $intake->intake_time_to = $sdek_call_courier_info['intake_time_to'] ?? null;
        $intake->lunch_time_from = $sdek_call_courier_info['lunch_time_from'] ?? null;
        $intake->lunch_time_to = $sdek_call_courier_info['lunch_time_to'] ?? null;
        $intake->sender = $sender;
        $intake->from_location = $from_location;

        return $intake;
    }

    /**
     * Builds products data for SDEK order.
     *
     * @param array                                 $order_info   Order data
     * @param array                                 $shipment     Shipment data
     * @param int                                   $shipment_id  Shipment ID
     * @param float                                 $symbol_grams Grams symbol
     * @param \Tygh\Addons\RusTaxes\Receipt\Receipt $receipt      Receipt item object
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    protected function buildProductsDataForSdekOrder(
        array $order_info,
        array $shipment,
        $shipment_id,
        $symbol_grams,
        Receipt $receipt
    ) {
        $sdek_products = [];
        $total_weight = '0';

        foreach ($receipt->getItems() as $item) {
            if ($item->getType() !== ReceiptItem::TYPE_PRODUCT || !isset($shipment['products'][$item->getId()])) {
                continue;
            }

            $item_id = $item->getId();
            $amount = $item->getQuantity();

            $data_product = $order_info['products'][$item_id];

            $ware_key = !empty($data_product['product_code'])
                ? $data_product['product_code']
                : $data_product['product_id'];

            // FIXME: Order and shipment id unnessesary?
            $sdek_product = [
                'ware_key' => $ware_key,
                'order_id' => $order_info['order_id'],
                'product' => $data_product['product'],
                'amount' => $amount,
                'shipment_id' => $shipment_id
            ];

            $product_weight = $this->db->getField(
                'SELECT weight FROM ?:products WHERE product_id = ?i',
                $data_product['product_id']
            );

            if (!empty($data_product['product_options'])) {
                $product_options = [];
                foreach ($data_product['product_options'] as $_options) {
                    $product_options[$_options['option_id']] = $_options['value'];
                }

                $product_weight = fn_apply_options_modifiers($product_options, $product_weight, 'W');
            }

            $product_weight = $this->sdek_service->checkWeight($product_weight, $symbol_grams);

            $sdek_product['weight'] = $product_weight;
            $sdek_product['price'] = $item->getPrice();
            $sdek_product['total'] = $item->getTotal();

            if (!empty($sdek_products[$ware_key]['price']) && ($sdek_product['price'] !== $sdek_products[$ware_key]['price'])) {
                $ware_key = !empty($data_product['item_id'])
                    ? $data_product['item_id']
                    : $data_product['product_id'];
                $sdek_product['ware_key'] = $ware_key;
            }

            if (!empty($sdek_products[$ware_key])) {
                if (!empty($sdek_products[$ware_key]['amount'])) {
                    $sdek_products[$ware_key]['amount'] += $sdek_product['amount'];
                }
                if (!empty($sdek_products[$ware_key]['price'])) {
                    $sdek_products[$ware_key]['price'] = $sdek_product['price'];
                }
                if (!empty($sdek_products[$ware_key]['total'])) {
                    $sdek_products[$ware_key]['total'] += $sdek_product['total'];
                }
                if (!empty($sdek_products[$ware_key]['weight'])) {
                    $sdek_products[$ware_key]['weight'] += $sdek_product['weight'];
                }
            } else {
                $sdek_products[$ware_key] = $sdek_product;
            }

            if (empty($sdek_products[$ware_key]['price'])) {
                $sdek_products[$ware_key]['price'] = '0.00';
            }

            if (empty($sdek_products[$ware_key]['total'])) {
                $sdek_products[$ware_key]['total'] = '0.00';
            }

            $tax_code = $item->getTaxType();
            $tax_sum = $item->getTaxSum();

            $sdek_products[$ware_key]['tax'] = $this->normalizeTaxType($tax_code);
            $sdek_products[$ware_key]['tax_sum'] = $tax_sum;

            $product_weight = $this->sdek_service->checkWeight($product_weight, $symbol_grams);

            $total_weight = $total_weight + ($product_weight * $amount);
        }

        $total_weight = $this->sdek_service->checkWeight($total_weight, $symbol_grams);

        return [$sdek_products, $total_weight];
    }

    /**
     * Forms product data for SDEK.
     *
     * @param array $product   Product data
     * @param array $sdek_info SDEK data
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    protected function getDataProduct(array $product, array $sdek_info)
    {
        $payment = '0.00';
        if (!empty($sdek_info['use_imposed']) && $sdek_info['use_imposed'] === YesNo::YES) {
            $payment = (!empty($sdek_info['cash_delivery'])) ? $sdek_info['cash_delivery'] : '0.00';

            if (!empty($sdek_info['use_product']) && $sdek_info['use_product'] === YesNo::YES) {
                $payment += $product['price'];
            }
        }

        $product_for_sdek = [
            'name' => $product['product'],
            'ware_key' => $product['ware_key'],
            'cost' => number_format($product['price'], 2, '.', ''),
            'amount' => $product['amount']
        ];

        $product_for_sdek['payment'] = [
            'value' => number_format($payment, 2, '.', ''),
            'vat_sum' => number_format($this->calculateTaxSum($product['tax'], $payment), 2, '.', ''),
            'vat_rate' => $this->normalizeTaxType($product['tax'])
        ];

        return $product_for_sdek;
    }

    /**
     * Normalizes tax type for sdek service using.
     *
     * @param string $tax_type Tax type
     *
     * @return int|string|null
     */
    public function normalizeTaxType($tax_type)
    {
        $map = [
            TaxType::VAT_0 => 0,
            TaxType::VAT_10 => 10,
            TaxType::VAT_20 => 20,
            TaxType::VAT_110 => 10,
            TaxType::VAT_120 => 20,
        ];

        if ($tax_type === TaxType::NONE) {
            $tax_type = null;
        } else {
            $tax_type = $map[$tax_type] ?? $tax_type;
        }

        return $tax_type;
    }

    /**
     * Calculates tax sum from price.
     *
     * @param int   $tax_type Tax type
     * @param float $price    Price
     *
     * @return float
     */
    public function calculateTaxSum($tax_type, $price)
    {
        $tax_type = !empty($tax_type) ? strtolower($tax_type) : '';

        switch ($tax_type) {
            case TaxType::VAT_10:
                $result = $price * 10 / 110;
                break;
            case TaxType::VAT_20:
                $result = $price * 20 / 120;
                break;
            default:
                $result = 0;
                break;
        }

        return round($result, 2);
    }

    /**
     * Gets price by current currency.
     *
     * @param int|string|float $price            Product price
     * @param array            $extra_params     Extra params
     * @param array            $currencies       Currencies list
     * @param string           $default_currency Default currency code
     *
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getPriceByCurrency($price, array $extra_params, array $currencies, $default_currency)
    {
        if (!empty($extra_params['currency'])) {
            if (!empty($currencies[$extra_params['currency']])) {
                $price = fn_format_price_by_currency($price, $extra_params['currency'], $default_currency);
            }
        }

        if ($price === 0 || $price === '0') {
            $price = '0.00';
        }

        return (string) $price;
    }

    /**
     * Gets customer name from order.
     *
     * @param array $order_info Order data
     *
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getNameCustomer(array $order_info)
    {
        $firstname = $lastname = '';

        if (!empty($order_info['lastname'])) {
            $lastname = $order_info['lastname'];
        } elseif (!empty($order_info['s_lastname'])) {
            $lastname = $order_info['s_lastname'];
        } elseif (!empty($order_info['b_lastname'])) {
            $lastname = $order_info['b_lastname'];
        }

        if (!empty($order_info['firstname'])) {
            $firstname = $order_info['firstname'];
        } elseif (!empty($order_info['s_firstname'])) {
            $firstname = $order_info['s_firstname'];
        } elseif (!empty($order_info['b_firstname'])) {
            $firstname = $order_info['b_firstname'];
        }

        return $lastname . ' ' . $firstname;
    }

    /**
     * Gets customer phone from order.
     *
     * @param array $order_info Order data
     *
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function getPhoneCustomer(array $order_info)
    {
        $phone = '-';

        if (!empty($order_info['phone'])) {
            $phone = $order_info['phone'];
        } elseif (!empty($order_info['s_phone'])) {
            $phone = $order_info['s_phone'];
        } elseif (!empty($order_info['b_phone'])) {
            $phone = $order_info['b_phone'];
        }

        if (empty($phone)) {
            return $phone;
        }

        return $phone;
    }
}
