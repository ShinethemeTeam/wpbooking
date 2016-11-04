<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/25/2016
 * Time: 11:52 AM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Order')) {
	class WPBooking_Order extends WPBooking_Controller
	{
		static $_inst;

		function __construct()
		{
            add_action('template_redirect', array($this, '_complete_purchase_validate'));
            add_filter('the_content', array($this, '_show_order_information'));
		}

        /**
         * Complete Purchase Validate
         *
         * @since 1.0
         * @author quandq
         */
		function _complete_purchase_validate()
		{
			if (is_singular('wpbooking_order')) {
				$action = WPBooking_Input::get('action');
				$gateway = WPBooking_Input::get('gateway');
				$order_id = get_the_ID();
				$order=new WB_Order($order_id);
				switch ($action) {
					case "cancel_purchase":
						wpbooking_set_message(esc_html__('You cancelled the payment','wpbooking'),'info');
						$order->cancel_purchase();

						break;
					case "complete_purchase":
						$return=WPBooking_Payment_Gateways::inst()->complete_purchase($gateway, $order_id);

						if($return){

							// Update the Order Items
							$order->complete_purchase();
							wpbooking_set_message(__('Thank you! Your booking is completed','wpbooking'),'success');

						}else{

							$order->payment_failed();
							wpbooking_set_message(__('Sorry! Can not complete your payment','wpbooking'),'danger');
						}


						break;
				}
			}
		}

        /**
         * Get Content Order Information
         * @since 1.0
         * @author quandq
         *
         * @param $content
         * @return string
         */
		function _show_order_information($content)
		{
			if (get_post_type() == 'wpbooking_order')
				$content .= wpbooking_load_view('order/content');
			return $content;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Order::inst();
}