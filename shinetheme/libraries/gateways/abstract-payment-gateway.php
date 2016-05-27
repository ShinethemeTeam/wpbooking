<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:35 PM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Abstract_Payment_Gateway')) {
	class WPBooking_Abstract_Payment_Gateway
	{
		protected $gateway_id = FALSE;
		protected $gateway_info = array();
		protected $settings = array();

		function __construct()
		{
			if (!$this->gateway_id) return FALSE;
			$this->gateway_info = wp_parse_args($this->gateway_info, array(
				'label'       => '',
				'description' => ''
			));

			add_filter('wpbooking_payment_gateways', array($this, '_register_gateway'));
			add_filter('wpbooking_payment_settings_sections', array($this, '_add_setting_section'));
		}

		function _add_setting_section($sections = array())
		{
			$settings = $this->get_settings_fields();
			if (!empty($settings)) {
				foreach ($settings as $key => $value) {
					if(!empty($value['id']))
					$settings[$key]['id'] = 'gateway_' . $this->gateway_id . '_' . $value['id'];
				}
			}
			$sections['payment_' . $this->gateway_id] = array(
				'id'     => 'payment_' . $this->gateway_id,
				'label'  => $this->get_info('label'),
				'fields' => $settings
			);

			return $sections;
		}

		function get_settings_fields()
		{
			return apply_filters('wpbooking_payment_' . $this->gateway_id . '_settings_fields', $this->settings);
		}

		function get_info($key = FALSE)
		{
			$info = apply_filters('wpbooking_gateway_info', $this->gateway_info);
			$info = apply_filters('wpbooking_gateway_' . $this->gateway_id . '_info', $info);

			if ($key) {

				$data = isset($info[$key]) ? $info[$key] : FALSE;

				$data = apply_filters('wpbooking_gateway_info_' . $key, $data);
				$data = apply_filters('wpbooking_gateway_' . $this->gateway_id . '_info_' . $key, $data);

				return $data;
			}

			return $info;
		}

		function get_option($key, $default = FALSE)
		{

			return wpbooking_get_option('gateway_' . $this->gateway_id . '_' . $key, $default);
		}

		function is_available()
		{
			return $this->get_option('enable') ? TRUE : FALSE;
		}

		function get_cancel_url($order_id, $payment_id)
		{

			$array = array(
				'payment_id' => $payment_id,
				'action'     => 'cancel_purchase'
			);

			return add_query_arg($array, get_permalink($order_id));
		}

		function get_return_url($order_id, $payment_id)
		{

			$array = array(
				'payment_id' => $payment_id,
				'action'     => 'complete_purchase'
			);

			return add_query_arg($array, get_permalink($order_id));

		}

		function getRedirectForm($res)
		{
			$hiddenFields = '';
			foreach ($res->getRedirectData() as $key => $value) {
				$hiddenFields .= sprintf(
						'<input type="hidden" name="%1$s" value="%2$s" />',
						htmlentities($key, ENT_QUOTES, 'UTF-8', FALSE),
						htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE)
					) . "\n";
			}

			$url = htmlentities($res->getRedirectUrl(), ENT_QUOTES, 'UTF-8', FALSE);

			return sprintf('<form action="%s" method="post" id="wpbooking_payment_redirect_form">

    						<script>document.getElementById(\'wpbooking_payment_redirect_form\').submit();</script>
							%s
						</form>', $url, $hiddenFields);
		}

		/**
		 * Do Complete Purchase Action
		 *
		 * @param $payment_id
		 * @return bool
		 * @since 1.0
		 */
		function complete_purchase($payment_id,$order_id)
		{
			return true;
		}

		/**
		 * Check out functions
		 * @param $order_id
		 * @param $payment_id
		 */
		function do_checkout($order_id,$payment_id)
		{
			// On-hold for Offline Gateways
			$order=WPBooking_Order_Model::inst();
			$order->onhold_purchase($payment_id);
		}

		function _register_gateway($gateways = array())
		{
			$gateways[$this->gateway_id] = $this;

			return $gateways;
		}
	}
}