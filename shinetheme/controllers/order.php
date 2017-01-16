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

            /**
             * Check Order Details Permission
             *
             * @since 1.0
             * @author quandq
             */
            add_action('template_redirect', array($this, '_check_order_details_permission'));

            /**
             * Get customer information
             *
             * @since 1.0
             */
            add_action('wpbooking_after_order_information_table', array($this,'_customer_information_html'), 15);
		}

        /**
         * Check Order Details Permission
         *
         * @since 1.0
         * @author quandq
         */
		function _check_order_details_permission(){
            if(is_singular('wpbooking_order')){
                $order_id = get_the_ID();
                $my_user = wp_get_current_user();
                $user_book = get_post_meta($order_id,'user_id',true);

                $is_checked = true;
                if(!is_user_logged_in()){
                    $is_checked = false;
                }

                if($user_book != $my_user->ID ){
                    $is_checked = false;
                }
                if(current_user_can('manage_options')){
                    $is_checked = true;
                }

                if(!WPBooking_Input::request('wpbooking_detail')){
                    $is_checked = true;
                }

                if($is_checked == false){
                    wp_redirect(home_url());
                }
            }
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
                        //$order->cancel_purchase();
						break;
					case "complete_purchase":
					    if(in_array($order->get_status(),array('payment_failed','on_hold'))){
                            $return=WPBooking_Payment_Gateways::inst()->complete_purchase($gateway, $order_id);
                            if($return){
                                $order->complete_purchase();
                            }else{
                                $order->payment_failed();
                            }
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

        /**
         * Customer information content html
         *
         * @since 1.0
         */
        function _customer_information_html($order_id){
            $order=new WB_Order($order_id);
            $order_data=$order->get_order_data();
            $service_type = $order_data['service_type'];

            $html = wpbooking_load_view('order/customer_information', array('order_id' => $order_id, 'service_type' => $service_type, 'order_data' => $order_data));
            echo apply_filters('wpbooking_customer_information_html',$html, $order_id);

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