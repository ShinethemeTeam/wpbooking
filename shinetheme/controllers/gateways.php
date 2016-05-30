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
					'bank-transfer',
					'paypal',
					'stripe',
					'payfast',
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
		 * @return mixed|void
		 */
		function get_gateways()
		{
			return apply_filters('wpbooking_payment_gateways',$this->gateways);
		}

		/**
		 * Return only enabled gateway in the Dashboard
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
				'name'=>__("Payment",'wpbooking'),
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
						<?php printf(__('You must upgrade your PHP version to at lease 5.3.0 to use WPBooking Plugin. Your current is %s','wpbooking'),phpversion()) ?>
					</strong>
				</p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.','travel-booking')?></span></button>
			</div>
			<?php
		}

		function do_checkout($gateway,$order_id)
		{
			$order_model=WPBooking_Order_Model::inst();

			$data=array();
			$all_gateways=$this->get_gateways();
			if(isset($all_gateways[$gateway]))
			{
				// Create Payment
				$payment=WPBooking_Payment_Model::inst();
				$payment_id=$payment->create_payment($order_id,$gateway);
				// Get payable order item ids
				$order_model->prepare_paying($order_id,$payment_id);

				$selected_gateway=$all_gateways[$gateway];

				if(method_exists($selected_gateway,'do_checkout'))
				{
					$data=$selected_gateway->do_checkout($order_id,$payment_id);
				}
			}
			if(empty($data['status'])){
				$data['status']=0;
				$data['error_step']='payment_checkout';
			}

			return $data;
		}

		function complete_purchase($payment_id,$order_id)
		{
			$payment=WPBooking_Payment_Model::inst();
			$payment_object=$payment->find($payment_id);
			$data=FALSE;

			if($payment_object)
			{
				$gateway=$payment_object['gateway'];
				$all_gateways=$this->get_gateways();
				if(array_key_exists($gateway,$all_gateways))
				{
					$selected_gateway=$all_gateways[$gateway];
					if(method_exists($selected_gateway,'do_checkout'))
					{
						do_action('wpbooking_before_payment_complete_purchase');
						$data= $selected_gateway->complete_purchase($payment_id,$order_id);
						if($data)
						{
							// Update the Order Items
							$order_model=WPBooking_Order_Model::inst();
							$order_model->complete_purchase($payment_id,$order_id);
							wpbooking_set_message(__('Thank you! Your booking is completed','wpbooking'),'success');
						}
						do_action('wpbooking_after_payment_complete_purchase');

					}
				}

			}

			$data=apply_filters('wpbooking_complete_purchase',$data);
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