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
if (!class_exists('Traveler_Booking')) {
	class Traveler_Booking
	{
		static $_inst;

		function __construct()
		{
			add_action('wp_ajax_traveler_do_checkout', array($this, 'do_checkout'));
			add_action('wp_ajax_nopriv_traveler_do_checkout', array($this, 'do_checkout'));

			add_action('wp_ajax_traveler_add_to_cart', array($this, 'add_to_cart'));
			add_action('wp_ajax_nopriv_traveler_add_to_cart', array($this, 'add_to_cart'));

			add_action('init',array($this,'_register_shortcode'));
		}


		/**
		 * Ajax Add To Cart Handler
		 * @since 1.0
		 * @return string
		 */
		function add_to_cart()
		{
			$res = array();

			$post_id = Traveler_Input::post('post_id');
			$service_type = get_post_meta($post_id, 'service_type', TRUE);

			$fields = array();

			// Validate Order Form
			$is_validate = TRUE;
			// Validate Form
			$validator = new Traveler_Validator();
			if (!empty($fields)) {
				foreach ($fields as $key => $value) {
					$validator->set_rules($key, $value['title'], $value['rule']);
				}
			}

			if (!$validator->run()) {
				$is_validate = FALSE;
				traveler_set_message($validator->error_string());
			}

			if ($is_validate) {
				$is_validate = apply_filters('traveler_add_to_cart_validate', $is_validate, $service_type, $post_id);
				$is_validate = apply_filters('traveler_add_to_cart_validate_' . $service_type, $is_validate, $service_type, $post_id);
			}


			if (!$is_validate) {
				$res = array(
					'status'  => 0,
					'message' => traveler_get_message(TRUE)
				);
			} else {
				$order_form = array();
				if (!empty($fields)) {
					foreach ($fields as $key => $value) {
						$order_form[$key] = Traveler_Input::post($key);
					}
				}
				$cart = Traveler_Session::get('traveler_cart', array());
				$cart_params = array(
					'post_id'               => $post_id,
					'service_type'          => $service_type,
					'order_form'            => $order_form,
					'base_price'            => get_post_meta($post_id, 'price', TRUE),
					'customer_id'           => is_user_logged_in() ? get_current_user_id() : FALSE,
					'need_customer_confirm' => apply_filters('traveler_service_need_customer_confirm', FALSE, $post_id, $service_type),
					'need_partner_confirm'  => apply_filters('traveler_service_need_partner_confirm', FALSE, $post_id, $service_type)
				);
				$cart_params = apply_filters('traveler_cart_item_params', $cart_params, $post_id, $service_type);
				$cart_params = apply_filters('traveler_cart_item_params_' . $service_type, $cart_params, $post_id);
				$cart[] = $cart_params;

				Traveler_Session::set('traveler_cat', $cart);

				$res = array(
					'status'  => 1,
					'message' => __('Add to cart success', 'traveler-booking')
				);
			}

			$res = apply_filters('traveler_ajax_add_to_cart', $res, $post_id);
			$res = apply_filters('traveler_ajax_add_to_cart_' . $service_type, $res, $post_id);

			echo json_encode($res);

			die;
		}


		/**
		 * Get Total Pay Amount. Only for Instant Booking
		 * @return int|mixed|void
		 */
		function get_cart_pay_amount()
		{
			$price = 0;
			$cart = Traveler_Session::get('traveler_cart', array());
			if (!empty($cart)) {
				foreach ($cart as $key => $value) {

					if($value['need_customer_confirm'] or $value['need_partner_confirm']) continue;

					$item_price = $value['base_price'];
					$item_price = apply_filters('traveler_cart_item_pay_amount', $item_price, $value);
					$item_price = apply_filters('traveler_cart_item_pay_amount_' . $value['service_type'], $item_price, $value);

					$price += $item_price;
				}
			}

			$price = apply_filters('traveler_get_cart_pay_amount', $price, $cart);

			return $price;
		}
		/**
		 * Get Total amount of Cart
		 * @return int|mixed|void
		 */
		function get_cart_total()
		{
			$price = 0;
			$cart = Traveler_Session::get('traveler_cart', array());
			if (!empty($cart)) {
				foreach ($cart as $key => $value) {
					$item_price = $value['base_price'];
					$item_price = apply_filters('traveler_cart_item_price', $item_price, $value);
					$item_price = apply_filters('traveler_cart_item_price_' . $value['service_type'], $item_price, $value);

					$price += $item_price;
				}
			}

			$price = apply_filters('traveler_get_cart_total', $price, $cart);

			return $price;
		}

		/**
		 * Get Order Form based on Service Type ID
		 * @param $service_type
		 * @return mixed|void
		 */
		function get_order_form($service_type)
		{
			$form = apply_filters('traveler_get_order_form', FALSE, $service_type);

			return $form = apply_filters('traveler_get_order_form_' . $service_type, $form);
		}


		/**
		 * Ajax Checkout Handler
		 * @since 1.0
		 */
		function do_checkout()
		{

			$cart = Traveler_Session::get('traveler_cart');

			if (!traveler_get_option('allow_guest_checkout')) {
				$res = array(
					'status'   => 0,
					'message'  => __("You need login to do this!", 'traveler-booking'),
					'redirect' => wp_login_url(get_permalink(traveler_get_option('checkout_page')))
				);
			} else {
				$res = array();
				$form_id = $this->get_checkout_form();
				$fields = array();

				// Validate Order Form
				$is_validate = TRUE;

				// Require Payment Gateways
				$selected_gateway=Traveler_Input::post('payment_gateway');
				$pay_amount=$this->get_cart_pay_amount();
				if($pay_amount and !$selected_gateway){
					$is_validate=FALSE;
					traveler_set_message(__("Please select at least one Payment Gateway",'traveler-booking'));
				}


				// Validate Form
				$validator = new Traveler_Validator();
				if (!empty($fields)) {
					foreach ($fields as $key => $value) {
						$validator->set_rules($key, $value['title'], $value['rule']);
					}
				}

				if ($is_validate and !$validator->run()) {
					$is_validate = FALSE;
					traveler_set_message($validator->error_string());
				}

				if ($is_validate) {
					$is_validate = apply_filters('traveler_do_checkout_validate', $is_validate, $cart);
				}


				if (!$is_validate) {
					$res = array(
						'status'  => 0,
						'message' => traveler_get_message(TRUE)
					);
				} else {

					$order_model = Traveler_Order_Model::inst();
					$order_id = $order_model->create($cart);
					if ($order_id) {
						$data=array();
						if($selected_gateway){
							$data=Traveler_Payment_Gateways::inst()->do_checkout($selected_gateway,$order_id);
						}
						//do checkout

						$res = array(
							'status'  => 1,
							'message' => __('Add to cart success', 'traveler-booking'),
							'data'=>$data
						);
					} else {
						$res = array(
							'status'  => 0,
							'message' => __('Can not create the order. Please contact the Admin', 'traveler-booking')
						);
					}


				}
			}


			$res = apply_filters('traveler_ajax_do_checkout', $res, $cart);

			echo json_encode($res);
			die;
		}

		/**
		 * Get checkout form id from Settings page
		 *
		 * @return bool|mixed|void
		 */
		function get_checkout_form()
		{
			return traveler_get_option('checkout_form');
		}

		/**
		 * Get total price by the given order id
		 * @param $order_id
		 * @return int|mixed|void
		 */
		function get_order_total($order_id)
		{
			$total=0;

			$order_model=Traveler_Order_Model::inst();

			$order_items=$order_model->find_by('order_id',$order_id);

			if(!empty($order_id))
			{
				foreach($order_items as $key=>$value)
				{
					$item_price=$value['base_price'];
					$item_price=apply_filters('traveler_order_item_total',$item_price,$value,$value['service_type']);
					$item_price=apply_filters('traveler_order_item_total_'.$value['service_type'],$item_price,$value);

					$total+=$item_price;
				}
			}

			$total=apply_filters('traveler_get_order_total',$total);
			return $total;
		}

		/**
		 * Get total of Order only with Instant Booking
		 * @param $order_id
		 * @return int|mixed|void
		 */
		function get_order_pay_amount($order_id)
		{
			$total=0;

			$order_model=Traveler_Order_Model::inst();

			$order_items=$order_model->find_by('order_id',$order_id);

			if(!empty($order_id))
			{
				foreach($order_items as $key=>$value)
				{
					$item_price=$value['base_price'];
					$item_price=apply_filters('traveler_order_item_total',$item_price,$value,$value['service_type']);
					$item_price=apply_filters('traveler_order_item_total_'.$value['service_type'],$item_price,$value);

					$total+=$item_price;
				}
			}

			$total=apply_filters('traveler_get_order_total',$total);
			return $total;
		}

		/**
		 * Get all cart items
		 *
		 * @return array
		 */
		function get_cart()
		{
			return Traveler_Session::get('traveler_cart');
		}
		/**
		 * Return permalink of the Cart Page
		 * @return false|string
		 */
		function get_cart_url()
		{
			return get_permalink(traveler_get_option('cart_page'));
		}


		/**
		 * Return the permalink of the Checkout Page
		 * @return false|string
		 */
		function get_checkout_url()
		{
			return get_permalink(traveler_get_option('checkout_page'));
		}
		function _register_shortcode()
		{
			add_shortcode('traveler_cart_page',array($this,'_render_cart_shortcode'));
			add_shortcode('traveler_checkout_page',array($this,'_render_checkout_shortcode'));
		}
		function _render_cart_shortcode($attr=array(),$content=FALSE)
		{
			return traveler_load_view('cart/index');
		}
		function _render_checkout_shortcode($attr=array(),$content=FALSE)
		{
			return traveler_load_view('checkout/index');
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	Traveler_Booking::inst();
}