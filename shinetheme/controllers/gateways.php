<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:34 PM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Payment_Gateways'))
{
	class Traveler_Payment_Gateways{

		static $_inst;

		protected $gateways=array();

		function __construct()
		{
			// load abstract class
			Traveler_Loader::inst()->load_library('gateways/abstract-payment-gateway');

			// We used Omnipay for our default payment Gateways
			if (!version_compare(phpversion(), '5.3', '<')) {
				// default gateways
				$defaults=array(
					'bank-transfer',
					'paypal',
					'stripe',
					'payfast',
				);
				foreach($defaults as $value){
					Traveler_Loader::inst()->load_library('gateways/'.$value);
				}
			}else{
				add_action( 'admin_notices', array($this,'add_php_version_notices') );
			}

			add_filter('traveler_booking_settings',array($this,'_add_settings'));

		}


		function get_gateways()
		{
			return apply_filters('traveler_payment_gateways',$this->gateways);
		}

		function _add_settings($settings)
		{
			$settings['payment_gateways']=array(
				'name'=>__("Payment",'traveler-booking'),
				'sections'=>apply_filters('traveler_payment_settings_sections',array())
			);
			return $settings;
		}

		function add_php_version_notices()
		{
			?>
			<div id="setting-error-tgmpa" class="updated settings-error notice is-dismissible">
				<p>
					<strong>
						<?php printf(__('You must upgrade your PHP version to at lease 5.3.0 to use Traveler Booking Plugin. Your current is %s','traveler-booking'),phpversion()) ?>
					</strong>
				</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.','travel-booking')?></span></button>
			</div>
			<?php
		}

		function do_checkout($gateway,$order_id)
		{
			$data=array();
			$all_gateways=$this->get_gateways();
			if(isset($all_gateways[$gateway]))
			{
				$selected_gateway=$all_gateways[$gateway];

				if(method_exists($selected_gateway,'do_checkout'))
				{
					$data=$selected_gateway->do_checkout($order_id);
				}
			}

			return $data;
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}

	}

	Traveler_Payment_Gateways::inst();
}