<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 27.01.17
 * Time: 15:30
 */

namespace PayPalPlusPlugin\WC\Payment;

class OrderDiscountData implements OrderItemDataProvider {

	use OrderDataProcessor;
	/**
	 * @var array
	 */
	private $data;

	public function __construct( array $data ) {

		$this->data = $data;
	}

	public function get_price() {

		return $this->format( $this->data['line_subtotal'] / $this->get_quantity() );
	}

	public function get_quantity() {

		return intval( $this->data['qty'] );
	}

	/**
	 * @return string
	 */
	public function get_name() {

		return 'Discount';
	}

	/**
	 * @return string|null
	 */
	public function get_sku() {

		return null;
	}
}