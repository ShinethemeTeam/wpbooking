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

			add_action('init', array($this, '_register_shortcode'));
			add_action('template_redirect', array($this, '_delete_cart_item'));

			add_action('template_redirect', array($this, '_complete_purchase_validate'));

			add_filter('the_content',array($this,'_show_order_information'));

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


			$order_form_id = $this->get_order_form_id($service_type);
			$fields = traveler_get_form_fields($order_form_id);

			// Validate Order Form
			$is_validate = TRUE;

			// Validate Post and Post Type
			if (!$post_id or get_post_type($post_id) != 'traveler_service') {
				$is_validate = FALSE;
				traveler_set_message(__("You do not select any service", 'traveler-booking'), 'error');
			}

			// Validate Form
			$validator = new Traveler_Validator();
			if (!empty($fields) and $is_validate) {
				foreach ($fields as $key => $value) {
					$validator->set_rules($key, $value['title'], $value['rule']);
				}

				if (!$validator->run()) {
					$is_validate = FALSE;
					traveler_set_message($validator->error_string(), 'error');
				}
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
					'need_customer_confirm' => apply_filters('traveler_service_need_customer_confirm', 0, $post_id, $service_type),
					'need_partner_confirm'  => apply_filters('traveler_service_need_partner_confirm', 0, $post_id, $service_type)
				);
				$cart_params = apply_filters('traveler_cart_item_params', $cart_params, $post_id, $service_type);
				$cart_params = apply_filters('traveler_cart_item_params_' . $service_type, $cart_params, $post_id);
				$cart[] = $cart_params;

				Traveler_Session::set('traveler_cart', $cart);

				traveler_set_message(sprintf(__('Add to %s success', 'traveler-booking'), sprintf('<a href="%s">%s</a>', $this->get_cart_url(), __('cart', 'traveler-booking'))),'success');
				$res = array(
					'status'  => 1,
					'message' => traveler_get_message(TRUE)
				);
			}

			$res = apply_filters('traveler_ajax_add_to_cart', $res, $post_id);
			$res = apply_filters('traveler_ajax_add_to_cart_' . $service_type, $res, $post_id);

			echo json_encode($res);

			die;
		}

		/**
		 * Ajax Checkout Handler
		 * @since 1.0
		 */
		function do_checkout()
		{

			$cart = Traveler_Session::get('traveler_cart');

			$res = array();
			$form_id = $this->get_checkout_form_id();
			$fields = traveler_get_form_fields($form_id);

			// Validate Order Form
			$is_validate = TRUE;

			if (empty($cart)) {
				$is_validate = FALSE;
				traveler_set_message(__("Sorry! Your cart is currently empty", 'traveler-booking'), 'error');
			}

			if ($is_validate and !traveler_get_option('allow_guest_checkout') and !is_user_logged_in()) {
				$is_validate = FALSE;
				$res['redirect'] = wp_login_url(get_permalink(traveler_get_option('checkout_page')));
				traveler_set_message(__("You need login to do this!", 'traveler-booking'), 'error');
			}

			// Require Payment Gateways
			$gateway_manage = Traveler_Payment_Gateways::inst();
			$selected_gateway = Traveler_Input::post('payment_gateway');
			$pay_amount = $this->get_cart_pay_amount();
			$available_gateways = $gateway_manage->get_available_gateways();

			if ($is_validate and $pay_amount) {
				if (!empty($available_gateways) and !$selected_gateway) {
					$is_validate = FALSE;
					traveler_set_message(__("Please select at least one Payment Gateway", 'traveler-booking'), 'error');
				} elseif (empty($available_gateways) or !array_key_exists($selected_gateway, $available_gateways)) {
					$is_validate = FALSE;
					traveler_set_message(sprintf(__("Gateway: %s is not ready to use, please choose other gateway", 'traveler-booking'), $selected_gateway), 'error');
				}

			}


			// Validate Form
			$validator = new Traveler_Validator();
			if (!empty($fields) and $is_validate) {
				foreach ($fields as $key => $value) {
					$validator->set_rules($key, $value['title'], $value['rule']);
				}
				if ($is_validate and !$validator->run()) {
					$is_validate = FALSE;
					traveler_set_message($validator->error_string());
				}
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
					$data = array(
						'status' => 1
					);
					$res=array(
						'status'=>1
					);
					// Clear the Cart after create new order,
					Traveler_Session::set('traveler_cart',array());

					// Only work with Order Table bellow

					try {
						if ($selected_gateway) {
							$data = Traveler_Payment_Gateways::inst()->do_checkout($selected_gateway, $order_id);
							if (!$data['status']) {
								$res = array(
									'status'  => 0,
									'message' => traveler_get_message(TRUE),
									'data'    => $data
								);
							}

						}

						if ($res['status']) {
							//do checkout
							$res = array(
								'message' => __('Booking Success', 'traveler-booking'),
								'data'    => $data,
							);
						}


					} catch (Exception $e) {
						traveler_set_message($e->getMessage(), 'error');
						//do checkout
						$res = array(
							'status'  => 0,
							'message' => traveler_get_message(TRUE),

						);
					}

					if (empty($data['redirect']) and empty($data['status'])) {
						$res['redirect'] = get_permalink($order_id);
					}

					if(!empty($data['redirect'])){
						$res['redirect'] = $data['redirect'];
					}

					do_action('traveler_after_checkout_success', $order_id);

				} else {
					$res = array(
						'status'  => 0,
						'message' => __('Can not create the order. Please contact the Admin', 'traveler-booking')
					);
				}

			}


			$res = apply_filters('traveler_ajax_do_checkout', $res, $cart);

			echo json_encode($res);
			die;
		}

		function _delete_cart_item()
		{
			if (isset($_GET['delete_cart_item'])) {
				$index = Traveler_Input::get('delete_cart_item');
				$all = Traveler_Session::get('traveler_cart');
				unset($all[$index]);
				$all = array_values($all);
				Traveler_Session::set('traveler_cart', $all);
				traveler_set_message(__("Delete cart item successfully", 'traveler-booking'), 'success');
			}

		}

		function _complete_purchase_validate()
		{
			if (is_singular('traveler_order')) {
				$action = Traveler_Input::get('action');
				$payment_id = Traveler_Input::get('payment_id');
				$order_id=get_the_ID();
				switch ($action) {
					case "cancel_purchase":

						break;
					case "complete_purchase":
						Traveler_Payment_Gateways::inst()->complete_purchase($payment_id,$order_id);
						break;
				}
			}
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

					if ($value['need_customer_confirm'] or $value['need_partner_confirm']) continue;

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
					$price += $this->get_cart_item_total($value);
				}
			}

			$price = apply_filters('traveler_get_cart_total', $price, $cart);

			return $price;
		}

		/**
		 * Get Price Amount for one Cart Item
		 * @param $cart_item
		 * @return mixed|void
		 */
		function get_cart_item_total($cart_item)
		{

			$item_price = $cart_item['base_price'];
			$item_price = apply_filters('traveler_cart_item_price', $item_price, $cart_item);
			$item_price = apply_filters('traveler_cart_item_price_' . $cart_item['service_type'], $item_price, $cart_item);

			return $item_price;
		}


		/**
		 * Get Order Form HTML based on Service Type ID
		 * @param $service_type
		 * @return mixed|void
		 */
		function get_order_form($service_type)
		{
			$form = apply_filters('traveler_get_order_form', FALSE, $service_type);

			return $form = apply_filters('traveler_get_order_form_' . $service_type, $form);
		}

		function get_order_form_id($service_type)
		{
			$form = apply_filters('traveler_get_order_form_id', FALSE, $service_type);

			return $form = apply_filters('traveler_get_order_form_id_' . $service_type, $form);
		}

		/**
		 * Get Order Form HTML based on Post ID
		 * @param $post_id
		 * @return mixed|void
		 */
		function get_order_form_by_post_id($post_id = FALSE)
		{
			if (!$post_id) $post_id = get_the_ID();
			$service_type = get_post_meta($post_id, 'service_type', TRUE);

			return $this->get_order_form($service_type);
		}


		/**
		 * Get checkout form HTML from Settings page
		 *
		 * @return bool|mixed|void
		 */
		function get_checkout_form()
		{
			$form_id = traveler_get_option('checkout_form');
			if ($post = get_post($form_id)) {
				return $content = apply_filters('the_content', $post->post_content);
			}
		}

		function get_checkout_form_id()
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
			$total = 0;

			$order_items = $this->get_order_items($order_id);

			if (!empty($order_id)) {
				foreach ($order_items as $key => $value) {
					$total += $this->get_order_item_total($value);
				}
			}

			$total = apply_filters('traveler_get_order_total', $total);

			return $total;
		}

		/**
		 * Get total order item price by the given item object
		 * @param $item mixed
		 * @return int|mixed|void
		 */
		function get_order_item_total($item)
		{
			$item_price = $item['base_price'];
			$item_price = apply_filters('traveler_order_item_total', $item_price, $item, $item['service_type']);
			$item_price = apply_filters('traveler_order_item_total_' . $item['service_type'], $item_price, $item);

			return $item_price;
		}

		/**
		 * Get total of Order only with Instant Booking
		 * @param $order_id
		 * @return int|mixed|void
		 */
		function get_order_pay_amount($order_id)
		{
			$total = 0;

			$order_model = Traveler_Order_Model::inst();

			$order_items = $order_model->get_order_items($order_id);

			if (!empty($order_items)) {
				foreach ($order_items as $key => $value) {

					// Payment Completed -> Ignore
					if ($value['payment_status'] == 'completed') continue;

					// Payment On-Paying -> Ignore
					if ($value['payment_status'] == 'on-paying') continue;

					if ($value['need_customer_confirm'] === 1 or $value['need_partner_confirm'] === 1) continue;

					$total += $this->get_order_item_total($value);
				}
			}

			$total = apply_filters('traveler_get_order_pay_amount', $total);

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
			add_shortcode('traveler_cart_page', array($this, '_render_cart_shortcode'));
			add_shortcode('traveler_checkout_page', array($this, '_render_checkout_shortcode'));
		}

		function _render_cart_shortcode($attr = array(), $content = FALSE)
		{
			return traveler_load_view('cart/index');
		}

		function _render_checkout_shortcode($attr = array(), $content = FALSE)
		{
			return traveler_load_view('checkout/index');
		}

		function _show_order_information($content)
		{
			if(get_post_type()=='traveler_booking')
			$content.=traveler_load_view('order/content');
			return $content;
		}

		function get_order_items($order_id)
		{
			return Traveler_Order_Model::inst()->get_order_items($order_id);
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