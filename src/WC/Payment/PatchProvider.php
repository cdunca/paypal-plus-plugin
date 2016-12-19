<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 05.12.16
 * Time: 10:50
 */

namespace PayPalPlusPlugin\WC\Payment;

use PayPal\Api\Patch;

/**
 * Class PatchProvider
 *
 * @package PayPalPlusPlugin\WC\Payment
 */
class PatchProvider {

	/**
	 * WooCommerce Order object.
	 *
	 * @var \WC_Order
	 */
	private $order;

	/**
	 * PatchProvider constructor.
	 *
	 * @param \WC_Order $order WooCommerce Order object.
	 */
	public function __construct( \WC_Order $order ) {

		$this->order = $order;
	}

	/**
	 * Returns the invoice Patch.
	 *
	 * @param string $invoice_prefix The invoice prefix.
	 *
	 * @return Patch
	 */
	public function get_invoice_patch( $invoice_prefix ) {

		$invoice_number = preg_replace( '/[^a-zA-Z0-9]/', '', $this->order->id );

		$invoice_patch = new Patch();
		$invoice_patch->setOp( 'add' )
		         ->setPath( '/transactions/0/invoice_number' )
		         ->setValue( $invoice_prefix . $invoice_number );

		return $invoice_patch;

	}

	/**
	 * Returns the custom Patch.
	 *
	 * @return Patch
	 */
	public function get_custom_patch() {

		$custom_patch = new Patch();
		$custom_patch->setOp( 'add' )
		                ->setPath( '/transactions/0/custom' )
		                ->setValue( wp_json_encode( [
			                'order_id'  => $this->order->id,
			                'order_key' => $this->order->order_key,
		                ] ) );

		return $custom_patch;

	}

	/**
	 * Returns the payment amount Patch.
	 *
	 * @return Patch
	 */
	public function get_payment_amount_patch() {

		$replace_patch = new Patch();

		$payment_data = [
			'total'    => $this->order
				->get_total(),
			'currency' => get_woocommerce_currency(),
			'details'  => [
				'subtotal' => $this->order->get_subtotal(),
				'shipping' => $this->order->get_total_shipping(),
				'tax'      => $this->order->get_total_tax(),
			],
		];

		$replace_patch->setOp( 'replace' )
		             ->setPath( '/transactions/0/amount' )
		             ->setValue( $payment_data );

		return $replace_patch;

	}

	/**
	 * Returns the billing Patch.
	 *
	 * @return Patch
	 */
	public function get_billing_patch() {

		$billing_data = [
			'recipient_name' => $this->order->shipping_first_name . ' ' . $this->order->shipping_last_name,
			'line1'          => $this->order->shipping_address_1,
			'line2'          => $this->order->shipping_address_2,
			'city'           => $this->order->shipping_city,
			'state'          => $this->order->shipping_state,
			'postal_code'    => $this->order->shipping_postcode,
			'country_code'   => $this->order->shipping_country,
		];

		$billing_patch = new Patch();
		$billing_patch->setOp( 'add' )
		             ->setPath( '/transactions/0/item_list/shipping_address' )
		             ->setValue( $billing_data );

		return $billing_patch;
	}

	/**
	 * Checks if billing should be patched.
	 *
	 * @return bool
	 */
	public function should_patch_billing() {

		return ! empty( $this->order->shipping_country );

	}
}