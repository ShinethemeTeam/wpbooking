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
	class WPBooking_Order
	{
		static $_inst;

		function __construct()
		{
			add_action('wp_ajax_wpbooking_do_checkout', array($this, 'do_checkout'));
			add_action('wp_ajax_nopriv_wpbooking_do_checkout', array($this, 'do_checkout'));

			add_action('wp_ajax_wpbooking_add_to_cart', array($this, 'add_to_cart'));
			add_action('wp_ajax_nopriv_wpbooking_add_to_cart', array($this, 'add_to_cart'));

			add_action('init', array($this, '_register_shortcode'));
			add_action('template_redirect', array($this, '_delete_cart_item'));

			add_action('template_redirect', array($this, '_complete_purchase_validate'));

			add_filter('the_content', array($this, '_show_order_information'));

		}


		/**
		 * Ajax Add To Cart Handler
		 * @since 1.0
		 * @return string
		 */
		function add_to_cart()
		{
			$res = array();

			$post_id = WPBooking_Input::post('post_id');

			$service_type = get_post_meta($post_id, 'service_type', TRUE);


			$order_form_id = $this->get_order_form_id($service_type);
			$fields = wpbooking_get_form_fields($order_form_id);

			// Validate Order Form
			$is_validate = TRUE;

			// Validate Post and Post Type
			if (!$post_id or get_post_type($post_id) != 'wpbooking_service') {
				$is_validate = FALSE;
				wpbooking_set_message(__("You do not select any service", 'wpbooking'), 'error');
			}

			// Validate Form
			$validator = new WPBooking_Form_Validator();
			if (!empty($fields) and $is_validate) {
				foreach ($fields as $key => $value) {
					$validator->set_rules($key, $value['title'], $value['rule']);
				}

				if (!$validator->run()) {
					$is_validate = FALSE;
					wpbooking_set_message($validator->error_string(), 'error');
					$res['error_fields'] = $validator->get_error_fields();
				}
			}


			if ($is_validate) {
				$is_validate = apply_filters('wpbooking_add_to_cart_validate', $is_validate, $service_type, $post_id);
				$is_validate = apply_filters('wpbooking_add_to_cart_validate_' . $service_type, $is_validate, $service_type, $post_id);
			}


			if (!$is_validate) {
				$res['status']=FALSE;
				$res['message']=wpbooking_get_message(TRUE);

			} else {
				if (!empty($fields)) {
					foreach ($fields as $key => $value) {
						$fields[$key]['value'] = WPBooking_Input::post($key);
					}
				}
				$cart = WPBooking_Session::get('wpbooking_cart', array());

				$cart_params = array(
					'post_id'               => $post_id,
					'service_type'          => $service_type,
					'order_form'            => $fields,
					'base_price'            => get_post_meta($post_id, 'price', TRUE),
					'currency'              => WPBooking_Currency::get_current_currency('currency'),
					'customer_id'           => is_user_logged_in() ? get_current_user_id() : FALSE,
					'deposit'               => get_post_meta($post_id, 'deposit', TRUE),
					'deposit_amount'        => get_post_meta($post_id, 'deposit_amount', TRUE),
					'need_customer_confirm' => apply_filters('wpbooking_service_need_customer_confirm', 0, $post_id, $service_type),
					'need_partner_confirm'  => apply_filters('wpbooking_service_need_partner_confirm', 0, $post_id, $service_type),
					'sub_total'             => get_post_meta($post_id, 'price', TRUE), // Subtotal of item, without extra price,
					'extra_prices'          => WPBooking_Input::post('extra_prices')
				);

				// Convert Check In and Check Out to Timstamp if available
				if (!empty($fields['check_in']['value'])) {
					$cart_params['check_in_timestamp'] = strtotime($fields['check_in']['value']);

					if (!empty($fields['check_out']['value'])) {
						$cart_params['check_out_timestamp'] = strtotime($fields['check_out']['value']);
					} else {
						$cart_params['check_out_timestamp'] = $cart_params['check_in_timestamp'];
					}
				}

				$cart_params = apply_filters('wpbooking_cart_item_params', $cart_params, $post_id, $service_type);
				$cart_params = apply_filters('wpbooking_cart_item_params_' . $service_type, $cart_params, $post_id);

				$cart[md5($post_id . time() . rand(0, 999))] = $cart_params;

				WPBooking_Session::set('wpbooking_cart', $cart);

				wpbooking_set_message(sprintf(__('Add to %s success', 'wpbooking'), sprintf('<a href="%s">%s</a>', $this->get_cart_url(), __('cart', 'wpbooking'))), 'success');
				$res = array(
					'status'  => 1,
					'message' => wpbooking_get_message(TRUE)
				);
			}
			$res['updated_content'] = apply_filters('wpbooking_cart_updated_content', array());

			$res = apply_filters('wpbooking_ajax_add_to_cart', $res, $post_id);
			$res = apply_filters('wpbooking_ajax_add_to_cart_' . $service_type, $res, $post_id);

			echo json_encode($res);

			die;
		}

		/**
		 * Ajax Checkout Handler
		 * @since 1.0
		 */
		function do_checkout()
		{

			$cart = WPBooking_Session::get('wpbooking_cart');

			$res = array();
			$form_id = $this->get_checkout_form_id();
			$fields = wpbooking_get_form_fields($form_id);

			// Validate Order Form
			$is_validate = TRUE;

			if (empty($cart)) {
				$is_validate = FALSE;
				wpbooking_set_message(__("Sorry! Your cart is currently empty", 'wpbooking'), 'error');
			}

			if ($is_validate and !wpbooking_get_option('allow_guest_checkout') and !is_user_logged_in()) {
				$is_validate = FALSE;
				$res['redirect'] = wp_login_url(get_permalink(wpbooking_get_option('checkout_page')));
				wpbooking_set_message(__("You need login to do this!", 'wpbooking'), 'error');
			}

			// Require Payment Gateways
			$gateway_manage = WPBooking_Payment_Gateways::inst();
			$selected_gateway = WPBooking_Input::post('payment_gateway');
			$pay_amount = $this->get_cart_pay_amount();
			$available_gateways = $gateway_manage->get_available_gateways();

			if ($is_validate and $pay_amount) {
				if (!empty($available_gateways) and !$selected_gateway) {
					$is_validate = FALSE;
					wpbooking_set_message(__("Please select at least one Payment Gateway", 'wpbooking'), 'error');
				} elseif (empty($available_gateways) or !array_key_exists($selected_gateway, $available_gateways)) {
					$is_validate = FALSE;
					wpbooking_set_message(sprintf(__("Gateway: %s is not ready to use, please choose other gateway", 'wpbooking'), $selected_gateway), 'error');
				}

			}

			// Validate Form
			$validator = new WPBooking_Form_Validator();
			if (!empty($fields) and $is_validate) {
				foreach ($fields as $key => $value) {
					$validator->set_rules($key, $value['title'], $value['rule']);
				}
				if ($is_validate and !$validator->run()) {
					$is_validate = FALSE;
					wpbooking_set_message($validator->error_string(), 'error');
					$res['error_type'] = 'form_validate';
					$res['error_fields'] = $validator->get_error_fields();

				}
			}


			if ($is_validate) {
				$is_validate = apply_filters('wpbooking_do_checkout_validate', $is_validate, $cart);
			}


			if (!$is_validate) {
				$res ['status'] = 0;
				$res['message'] = wpbooking_get_message(TRUE);
			} else {

				// Checkout form data
				if (!empty($fields)) {
					foreach ($fields as $k => $v) {
						$fields[$k]['value'] = WPBooking_Input::post($k);
					}
				}
				$order_model = WPBooking_Order_Model::inst();
				$order_id = $order_model->create($cart, $fields);
				if ($order_id) {
					$data = array(
						'status' => 1
					);
					$res['status'] = 1;

					// Clear the Cart after create new order,
					WPBooking_Session::set('wpbooking_cart', array());

					// Only work with Order Table bellow

					try {
						if ($selected_gateway) {
							$data = WPBooking_Payment_Gateways::inst()->do_checkout($selected_gateway, $order_id);
							if (!$data['status']) {
								$res = array(
									'status'  => 0,
									'message' => wpbooking_get_message(TRUE),
									'data'    => $data
								);
							}

						}

						if ($res['status']) {
							wpbooking_set_message(__('Booking Success', 'wpbooking'));
							//do checkout
							$res['data'] = $data;
							$res['message'] = wpbooking_get_message(TRUE);
						}


					} catch (Exception $e) {
						wpbooking_set_message($e->getMessage(), 'error');
						//do checkout
						$res = array(
							'status'  => 0,
							'message' => wpbooking_get_message(TRUE),

						);
					}

					if (empty($data['redirect'])) {
						$res['redirect'] = get_permalink($order_id);
					}

					if (!empty($data['redirect'])) {
						$res['redirect'] = $data['redirect'];
					}

					do_action('wpbooking_after_checkout_success', $order_id);

				} else {
					$res = array(
						'status'  => 0,
						'message' => __('Can not create the order. Please contact the Admin', 'wpbooking')
					);
				}

			}


			$res = apply_filters('wpbooking_ajax_do_checkout', $res, $cart);

			echo json_encode($res);
			die;
		}

		function _delete_cart_item()
		{
			if (isset($_GET['delete_cart_item'])) {
				$index = WPBooking_Input::get('delete_cart_item');
				$all = WPBooking_Session::get('wpbooking_cart');
				unset($all[$index]);
				WPBooking_Session::set('wpbooking_cart', $all);
				wpbooking_set_message(__("Delete cart item successfully", 'wpbooking'), 'success');
			}

		}

		function _complete_purchase_validate()
		{
			if (is_singular('wpbooking_order')) {
				$action = WPBooking_Input::get('action');
				$payment_id = WPBooking_Input::get('payment_id');
				$order_id = get_the_ID();
				switch ($action) {
					case "cancel_purchase":

						break;
					case "complete_purchase":
						WPBooking_Payment_Gateways::inst()->complete_purchase($payment_id, $order_id);
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
			$cart = WPBooking_Session::get('wpbooking_cart', array());
			if (!empty($cart)) {
				foreach ($cart as $key => $value) {

					if ($value['need_customer_confirm'] or $value['need_partner_confirm']) continue;

					$item_price = $value['sub_total'];
					$item_price = apply_filters('wpbooking_cart_item_pay_amount', $item_price, $value);
					$item_price = apply_filters('wpbooking_cart_item_pay_amount_' . $value['service_type'], $item_price, $value);

					$item_price = WPBooking_Currency::convert_money($item_price, array('currency' => $value['currency']));

					$price += $item_price;
				}
			}

			$price = apply_filters('wpbooking_get_cart_pay_amount', $price, $cart);

			return $price;
		}

		/**
		 * Get Total amount of Cart
		 * @return int|mixed|void
		 */
		function get_cart_total()
		{
			$price = 0;
			$cart = WPBooking_Session::get('wpbooking_cart', array());
			if (!empty($cart)) {
				foreach ($cart as $key => $value) {
					$price += $this->get_cart_item_total($value, TRUE);
				}
			}

			$price = apply_filters('wpbooking_get_cart_total', $price, $cart);

			return $price;
		}

		/**
		 * Get Price Amount for one Cart Item
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $cart_item
		 * @param bool @need_convert Need Convert To Currency
		 * @return mixed|void
		 */
		function get_cart_item_total($cart_item, $need_convert = FALSE)
		{

			$item_price = $cart_item['sub_total'];
			$item_price = apply_filters('wpbooking_cart_item_price', $item_price, $cart_item);
			$item_price = apply_filters('wpbooking_cart_item_price_' . $cart_item['service_type'], $item_price, $cart_item);

			// Convert to current currency
			if ($need_convert) {
				$item_price = WPBooking_Currency::convert_money($item_price, array(
					'currency' => $cart_item['currency']
				));
			}


			return $item_price;
		}

		/**
		 * Get Price HTML for an Cart Item, including convert currency to current one
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $cart_item
		 * @return string
		 */
		function get_cart_item_total_html($cart_item)
		{
			$item_price = $this->get_cart_item_total($cart_item, TRUE);

			return $price_html = WPBooking_Currency::format_money($item_price);
		}

		/**
		 * Get Order Form HTML based on Service Type ID
		 * @param $service_type
		 * @return mixed|void
		 */
		function get_order_form($service_type)
		{
			$form = apply_filters('wpbooking_get_order_form', FALSE, $service_type);

			return $form = apply_filters('wpbooking_get_order_form_' . $service_type, $form);
		}

		function get_order_form_id($service_type)
		{
			$form = apply_filters('wpbooking_get_order_form_id', FALSE, $service_type);

			return $form = apply_filters('wpbooking_get_order_form_id_' . $service_type, $form);
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
			$form_id = wpbooking_get_option('checkout_form');
			if ($form_id and $post = get_post($form_id)) {
				return $content = apply_filters('the_content', $post->post_content);
			}
		}

		function get_checkout_form_id()
		{
			return wpbooking_get_option('checkout_form');
		}

		/**
		 * Get total price by the given order id
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $order_id
		 * @return int|mixed|void
		 */
		function get_order_total($order_id)
		{
			$total = 0;

			$order_items = $this->get_order_items($order_id);

			if (!empty($order_id)) {
				foreach ($order_items as $key => $value) {
					$total += $this->get_order_item_total($value, TRUE);
				}
			}

			$total = apply_filters('wpbooking_get_order_total', $total);

			return $total;
		}

		/**
		 * Get total order item price by the given item object
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $item mixed
		 * @param bool $need_convert Need convert to currency
		 * @return int|mixed|void
		 */
		function get_order_item_total($item, $need_convert = FALSE)
		{
			$item_price = $item['sub_total'];
			$item_price = apply_filters('wpbooking_order_item_total', $item_price, $item, $item['service_type']);
			$item_price = apply_filters('wpbooking_order_item_total_' . $item['service_type'], $item_price, $item);

			// Convert to current currency
			if ($need_convert) {
				$item_price = WPBooking_Currency::convert_money($item_price, array(
					'currency' => $item['currency']
				));
			}

			return $item_price;
		}

		function get_order_item_total_html($item)
		{

			$item_price = $this->get_order_item_total($item, TRUE);

			return WPBooking_Currency::format_money($item_price);
		}

		/**
		 * Get total of Order only with Instant Booking
		 * @param $order_id
		 * @return int|mixed|void
		 */
		function get_order_pay_amount($order_id)
		{
			$total = 0;

			$order_model = WPBooking_Order_Model::inst();

			$order_items = $order_model->get_order_items($order_id);

			if (!empty($order_items)) {
				foreach ($order_items as $key => $value) {

					// Payment Completed -> Ignore
					if ($value['payment_status'] == 'completed') continue;

					// Payment On-Paying -> Ignore
					if ($value['payment_status'] == 'on-paying') continue;

					if ($value['need_customer_confirm'] === 1 or $value['need_partner_confirm'] === 1) continue;

					$total += $this->get_order_item_total($value, TRUE);
				}
			}

			$total = apply_filters('wpbooking_get_order_pay_amount', $total);

			return $total;
		}

		/**
		 * Get all cart items
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @return array
		 */
		function get_cart()
		{
			return WPBooking_Session::get('wpbooking_cart');
		}

		/**
		 * Return permalink of the Cart Page
		 * @return false|string
		 */
		function get_cart_url()
		{
			return get_permalink(wpbooking_get_option('cart_page'));
		}


		/**
		 * Return the permalink of the Checkout Page
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @return false|string
		 */
		function get_checkout_url()
		{
			return get_permalink(wpbooking_get_option('checkout_page'));
		}

		function _register_shortcode()
		{
			add_shortcode('wpbooking_cart_page', array($this, '_render_cart_shortcode'));
			add_shortcode('wpbooking_checkout_page', array($this, '_render_checkout_shortcode'));
		}

		function _render_cart_shortcode($attr = array(), $content = FALSE)
		{
			return wpbooking_load_view('cart/index');
		}

		function _render_checkout_shortcode($attr = array(), $content = FALSE)
		{
			return wpbooking_load_view('checkout/index');
		}

		function _show_order_information($content)
		{
			if (get_post_type() == 'wpbooking_order')
				$content .= wpbooking_load_view('order/content');

			return $content;
		}

		function get_order_items($order_id)
		{
			return WPBooking_Order_Model::inst()->get_order_items($order_id);
		}

		function get_order_item_row_info($order_id = FALSE)
		{
			if (!$order_id) $order_id = get_the_ID();

			return WPBooking_Order_Model::inst()->find_by('order_id', $order_id);
		}

		function get_order_form_datas($order_id = FALSE)
		{
			if (!$order_id) $order_id = get_the_ID();

			return get_post_meta($order_id, 'checkout_form_data', TRUE);

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