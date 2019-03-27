<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Payment;

use Inpsyde\Lib\PayPal\Api\Patch;
use WC_Order;
use InvalidArgumentException;
use WCPayPalPlus\Utils\PriceFormatterTrait;

/**
 * Class PatchProvider
 *
 * @package WCPayPalPlus\Payment
 */
class PatchProvider
{
    use PriceFormatterTrait;

    const RECEIPT_NAME = 'recipient_name';
    const LINE_ONE = 'line1';
    const LINE_TWO = 'line2';
    const CITY = 'city';
    const STATE = 'state';
    const POSTAL_CODE = 'postal_code';
    const COUNTRY_CODE = 'country_code';

    /**
     * WooCommerce Order object.
     *
     * @var WC_Order
     */
    private $order;

    /**
     * @var OrderData
     */
    private $orderData;

    /**
     * PatchProvider constructor.
     *
     * @param WC_Order $order WooCommerce Order object.
     * @param OrderData $orderData Order data provider.
     */
    public function __construct(WC_Order $order, OrderData $orderData)
    {
        $this->order = $order;
        $this->orderData = $orderData;
    }

    /**
     * @param $invoice_prefix
     * @return Patch
     * @throws InvalidArgumentException
     */
    public function get_invoice_patch($invoice_prefix)
    {
        $invoice_number = preg_replace('/[^a-zA-Z0-9]/', '', $this->order->get_order_number());

        $invoice_patch = new Patch();
        $invoice_patch
            ->setOp('add')
            ->setPath('/transactions/0/invoice_number')
            ->setValue($invoice_prefix . $invoice_number);

        return $invoice_patch;
    }

    /**
     * @return Patch
     * @throws InvalidArgumentException
     */
    public function get_custom_patch()
    {
        $custom_patch = new Patch();
        $custom_patch
            ->setOp('add')
            ->setPath('/transactions/0/custom')
            ->setValue($this->order->get_order_key());

        return $custom_patch;
    }

    /**
     * @return Patch
     * @throws InvalidArgumentException
     */
    public function get_payment_amount_patch()
    {
        $replacePatch = new Patch();

        $paymentData = [
            'total' => $this->orderData->total(),
            'currency' => get_woocommerce_currency(),
            'details' => [
                'subtotal' => $this->orderData->subTotal(),
                'shipping' => $this->orderData->shippingTotal(),
                'tax' => $this->orderData->totalTaxes(),
            ],
        ];

        $replacePatch
            ->setOp('replace')
            ->setPath('/transactions/0/amount')
            ->setValue($paymentData);

        return $replacePatch;
    }

    /**
     * @return Patch
     * @throws InvalidArgumentException
     */
    public function get_shipping_patch()
    {
        $addressData = $this->has_shipping_data()
            ? $this->get_shipping_address_data()
            : $this->get_billing_address_data();

        $shippingPatch = new Patch();
        $shippingPatch
            ->setOp('add')
            ->setPath('/transactions/0/item_list/shipping_address')
            ->setValue($addressData);

        return $shippingPatch;
    }

    /**
     * Checks if there is shipping address data.
     *
     * @return bool
     */
    private function has_shipping_data()
    {
        return !empty($this->order->get_shipping_country());
    }

    /**
     * Returns the order's shipping address data.
     *
     * @return array
     */
    private function get_shipping_address_data()
    {
        return [
            self::RECEIPT_NAME => $this->order->get_shipping_first_name() . ' ' . $this->order->get_shipping_last_name(),
            self::LINE_ONE => $this->order->get_shipping_address_1(),
            self::LINE_TWO => $this->order->get_shipping_address_2(),
            self::CITY => $this->order->get_shipping_city(),
            self::STATE => $this->order->get_shipping_state(),
            self::POSTAL_CODE => $this->order->get_shipping_postcode(),
            self::COUNTRY_CODE => $this->order->get_shipping_country(),
        ];
    }

    /**
     * Returns the order's billing address data.
     *
     * @return array
     */
    private function get_billing_address_data()
    {
        return [
            self::RECEIPT_NAME => $this->order->get_billing_first_name() . ' ' . $this->order->get_billing_last_name(),
            self::LINE_ONE => $this->order->get_billing_address_1(),
            self::LINE_TWO => $this->order->get_billing_address_2(),
            self::CITY => $this->order->get_billing_city(),
            self::STATE => $this->order->get_billing_state(),
            self::POSTAL_CODE => $this->order->get_billing_postcode(),
            self::COUNTRY_CODE => $this->order->get_billing_country(),
        ];
    }
}
