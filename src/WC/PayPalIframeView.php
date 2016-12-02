<?php
/**
 * Created by PhpStorm.
 * User: biont
 * Date: 03.11.16
 * Time: 14:27
 */

namespace PayPalPlusPlugin\WC;

/**
 * Class PayPalIframeView
 *
 * @package PayPalPlusPlugin\WC
 */
class PayPalIframeView {

	/**
	 * @var array
	 */
	private $data;

	/**
	 * PayPalIframeView constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data ) {

		$this->data = $data;
	}

	/**
	 * Render the Paywall iframe
	 */
	public function render() {

		?>
		<div id="<?php echo $this->data['app_config']['placeholder'] ?>"></div>
		<script type="application/javascript">
			if ( typeof PAYPAL != "undefined" ) {
				var ppp = PAYPAL.apps.PPP( <?php echo json_encode( $this->data['app_config'] ) ?>);
			}
		</script>
		<style>
			<?php echo esc_attr('#'.$this->data['app_config']['placeholder']) ?>
			iframe {
				height: 100% !important;
				width: 100% !important;
				*width: 100% !important;
			}
		</style>
		<?php
	}
}