<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 27.01.17
 * Time: 15:30
 */

namespace PayPalPlusPlugin\WC\Payment;

/**
 * Class OrderDiscountData
 *
 * @package PayPalPlusPlugin\WC\Payment
 */
class OrderDiscountData implements OrderItemDataProvider {

	use OrderDataProcessor;
	/**
	 * Item data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * OrderDiscountData constructor.
	 *
	 * @param array $data Item data.
	 */
	public function __construct( array $data ) {

		$this->data = $data;
	}

	/**
	 * Returns the discount amount.
	 *
	 * @return string
	 */
	public function get_price() {

		return $this->format( $this->data['line_subtotal'] / $this->get_quantity() );
	}

	/**
	 * Returns the item quantity.
	 * TODO Can you have more than one of the same discount on an order?
	 * If not, maybe hardcode 'return 1;' here.
	 *
	 * @return int
	 */
	public function get_quantity() {

		return intval( $this->data['qty'] );
	}

	/**
	 * Returns the item name.
	 * TODO: Maybe we want to add the discount name here..?
	 *
	 * @return string
	 */
	public function get_name() {

		return 'Discount';
	}

	/**
	 * Returns no SKU.
	 *
	 * @return string|null
	 */
	public function get_sku() {

		return null;
	}
}