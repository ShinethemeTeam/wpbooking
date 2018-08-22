<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_Payment_Gateways'))
{
	class WPBooking_Payment_Gateways{

		static $_inst;

		protected $gateways=array();

		function __construct()
		{
			// load abstract class
			WPBooking_Loader::inst()->load_library('gateways/abstract-payment-gateway');
			// We used Omnipay for our default payment Gateways
			if (!version_compare(phpversion(), '5.3', '<')) {
				// default gateways
				$defaults=array(
					'submit-form',
					'paypal'
				);
				foreach($defaults as $value){
					WPBooking_Loader::inst()->load_library('gateways/'.$value);
				}
			}else{
				add_action( 'admin_notices', array($this,'add_php_version_notices') );
			}
			add_filter('wpbooking_settings',array($this,'_add_settings'));
		}


		/**
		 * Get all registered gateways
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return WPBooking_Abstract_Payment_Gateway[]
		 */
		function get_gateways()
		{
			return apply_filters('wpbooking_payment_gateways',$this->gateways);
		}

		/**
		 * Get Gateway Item by ID
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $gateway_id
		 * @return WPBooking_Abstract_Payment_Gateway
		 */
		function get_gateway($gateway_id=FALSE)
		{
			$all=$this->get_gateways();
			if(!empty($all) and !empty($all[$gateway_id])) return $all[$gateway_id];
		}


		/**
		 * Return only enabled gateway in the Dashboard
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array
		 */
		function get_available_gateways()
		{
			$all=$this->get_gateways();
			$news=array();
			if(!empty($all))
			{
				foreach($all as $key=>$value)
				{
					if($value->is_available())
					{
						$news[$key]=$value;
					}
				}
			}

			return $news;
		}

		function _add_settings($settings)
		{
			$settings['payment_gateways']=array(
				'name'=>esc_html__("Payment",'wp-booking-management-system'),
				'sections'=>apply_filters('wpbooking_payment_settings_sections',array())
			);
			return $settings;
		}

		function add_php_version_notices()
		{
			?>
			<div id="setting-error-tgmpa" class="updated settings-error notice is-dismissible">
				<p>
					<strong>
						<?php printf(esc_html__('You must upgrade your PHP version to 5.3.0 at least to use WPBooking Plugin. Your current version is %s','wp-booking-management-system'),phpversion()) ?>
					</strong>
				</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__('Dismiss this notice.','wp-booking-management-system')?></span></button>
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
			if(empty($data['status'])){
				$data['status']=0;
				$data['error_step']='payment_checkout';
			}

			return $data;
		}

		/**
		 * Do Validate Purchase After Redirect From Gateway
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $gateway
		 * @param $order_id
		 * @return bool
		 */
		function complete_purchase($gateway,$order_id)
		{

			$selected_gateway=$this->get_gateway($gateway);
			$data=FALSE;
			if(method_exists($selected_gateway,'complete_purchase'))
			{
				do_action('wpbooking_before_payment_complete_purchase');
				$data= $selected_gateway->complete_purchase($order_id);
				do_action('wpbooking_after_payment_complete_purchase');

			}

			$data=apply_filters('wpbooking_gateway_complete_purchase',$data);
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

	WPBooking_Payment_Gateways::inst();
}