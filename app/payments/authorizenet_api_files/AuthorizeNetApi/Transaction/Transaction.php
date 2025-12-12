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

namespace Tygh\AuthorizeNetApi\Transaction;

use DateTime;
use net\authorize\api\contract\v1 as AnetAPI;

/**
 * Class Transaction.
 *
 * Handles the creation of transaction data for Authorize.Net payments.
 */
class Transaction
{
    const TRANSACTION_TYPES = [
        'P' => 'authCaptureTransaction',
        'A' => 'authOnlyTransaction',
    ];

    /** @var string */
    public $transaction_type;

    /** @var string */
    protected $order_prefix;

    /** @var array<string, string|int|array<string, string|int>> */
    protected $order_info;

    /**
     * Constructor
     *
     * @param array<array<string, string|int|bool>>               $processor_data Payment processor data.
     * @param array<string, string|int|array<string, string|int>> $order_info     Order information.
     *
     * @psalm-suppress PossiblyInvalidPropertyAssignmentValue
     */
    public function __construct($processor_data, $order_info)
    {
        $this->transaction_type = $processor_data['processor_params']['transaction_type'];
        $this->order_prefix = $processor_data['processor_params']['order_prefix'];
        $this->order_info = $order_info;
    }

    /**
     * Builds transaction data for the payment request.
     *
     * @return AnetAPI\TransactionRequestType
     */
    public function buildTransactionData()
    {
        $order = $this->setOrderData();
        $bill_to = $this->setBillToData();
        $ship_to = $this->setShipToData();
        $payment = $this->setPaymentData();
        $tax = $this->setTaxSubtotal();
        $shipping_cost = $this->setShippingCost();

        return $this->setTransactionData($order, $bill_to, $ship_to, $payment, $tax, $shipping_cost);
    }

    /**
     * Set order data.
     *
     * @return AnetAPI\OrderType
     */
    public function setOrderData()
    {
        $order = new AnetAPI\OrderType();

        /** @psalm-suppress InvalidOperand */
        $order->setInvoiceNumber($this->order_prefix . $this->order_info['order_id']);

        return $order;
    }

    /**
     * Set billing address data.
     *
     * @return AnetAPI\CustomerAddressType
     *
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function setBillToData()
    {
        $bill_to = new AnetAPI\CustomerAddressType();

        $bill_to->setFirstName($this->order_info['b_firstname']);
        $bill_to->setLastName($this->order_info['b_lastname']);
        $bill_to->setAddress($this->order_info['b_address']);
        $bill_to->setCity($this->order_info['b_city']);
        $bill_to->setState($this->order_info['b_state']);
        $bill_to->setZip($this->order_info['b_zipcode']);
        $bill_to->setCountry($this->order_info['b_country']);
        $bill_to->setPhoneNumber($this->order_info['phone']);
        $bill_to->setEmail($this->order_info['email']);

        return $bill_to;
    }

    /**
     * Set shipping address data.
     *
     * @return AnetAPI\NameAndAddressType
     *
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function setShipToData()
    {
        $ship_to = new AnetAPI\NameAndAddressType();

        $ship_to->setFirstName($this->order_info['s_firstname']);
        $ship_to->setLastName($this->order_info['s_lastname']);
        $ship_to->setAddress($this->order_info['s_address']);
        $ship_to->setCity($this->order_info['s_city']);
        $ship_to->setState($this->order_info['s_state']);
        $ship_to->setZip($this->order_info['s_zipcode']);
        $ship_to->setCountry($this->order_info['s_country']);

        return $ship_to;
    }

    /**
     * Set payment data with credit card details.
     *
     * @return AnetAPI\PaymentType
     *
     * @psalm-suppress PossiblyInvalidArrayOffset
     */
    protected function setPaymentData()
    {
        $credit_card = new AnetAPI\CreditCardType();

        $credit_card->setCardNumber($this->order_info['payment_info']['card_number']);
        $exp_date = DateTime::createFromFormat('y-m', "{$this->order_info['payment_info']['expiry_year']}-{$this->order_info['payment_info']['expiry_month']}")->format('Y-m');
        $credit_card->setExpirationDate($exp_date);
        $credit_card->setCardCode($this->order_info['payment_info']['cvv2']);

        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($credit_card);

        return $payment;
    }

    /**
     * Set tax subtotal value.
     *
     * @return AnetAPI\ExtendedAmountType
     */
    protected function setTaxSubtotal()
    {
        $tax_subtotal = fn_format_price($this->order_info['tax_subtotal']);

        $tax = new AnetAPI\ExtendedAmountType();
        $tax->setAmount($tax_subtotal);
        $tax->setName('Tax');

        return $tax;
    }

    /**
     * Set shipping cost value.
     *
     * @return AnetAPI\ExtendedAmountType
     */
    protected function setShippingCost()
    {
        $shipping_cost = fn_format_price($this->order_info['shipping_cost']);

        $shipping = new AnetAPI\ExtendedAmountType();
        $shipping->setAmount($shipping_cost);
        $shipping->setName('Shipping');

        return $shipping;
    }

    /**
     * Combines all transaction data into a request object.
     *
     * @param AnetAPI\OrderType           $order   Order details.
     * @param AnetAPI\CustomerAddressType $bill_to Billing details.
     * @param AnetAPI\NameAndAddressType  $ship_to Shipping details.
     * @param AnetAPI\PaymentType         $payment Payment details.
     *
     * @return AnetAPI\TransactionRequestType
     */
    protected function setTransactionData(
        AnetAPI\OrderType $order,
        AnetAPI\CustomerAddressType $bill_to,
        AnetAPI\NameAndAddressType $ship_to,
        AnetAPI\PaymentType $payment,
        AnetAPI\ExtendedAmountType $taxes,
        AnetAPI\ExtendedAmountType $shipping_cost
    ) {
        $transaction_data = new AnetAPI\TransactionRequestType();

        $transaction_type = self::TRANSACTION_TYPES[$this->transaction_type];
        $transaction_data->setTransactionType($transaction_type);
        $transaction_data->setAmount(fn_format_price($this->order_info['total']));
        $transaction_data->setOrder($order);
        $transaction_data->setBillTo($bill_to);
        $transaction_data->setShipTo($ship_to);
        $transaction_data->setPayment($payment);
        $transaction_data->setTax($taxes);
        $transaction_data->setShipping($shipping_cost);

        return $transaction_data;
    }
}
