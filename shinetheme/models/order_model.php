<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/1/2016
 * Time: 2:41 PM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Order_Model')) {
	class WPBooking_Order_Model extends WPBooking_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_order_item';
			$this->table_version = '1.1.4';
			$this->columns = array(
				'id'                    => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'order_id'              => array('type' => "INT"),
				'post_id'               => array('type' => "INT"),
				'base_price'            => array('type' => "FLOAT"),
				'sub_total'             => array('type' => "FLOAT"),
				'currency'              => array('type' => "VARCHAR", 'length' => 50),
				'is_main_currency'      => array('type' => "INT"),
				'raw_data'              => array('type' => "text"),
				'order_form'            => array('type' => "text"),
				'service_type'          => array('type' => "VARCHAR", 'length' => 50),
				'check_out_timestamp'   => array('type' => "INT"),
				'check_in_timestamp'    => array('type' => "INT"),
				'adult_number'          => array('type' => "INT"),
				'child_number'          => array('type' => "INT"),
				'infant_number'         => array('type' => "INT"),
				'customer_id'           => array('type' => "INT"),
				'partner_id'            => array('type' => "INT"),
				'deposit'               => array('type' => "varchar", 'length' => 50),
				'deposit_amount'        => array('type' => "FLOAT"),
				'need_customer_confirm' => array('type' => 'INT'),
				'customer_confirm_code' => array('type' => "varchar", 'length' => 255),
				'partner_confirm_code'  => array('type' => "varchar", 'length' => 255),
				'need_partner_confirm'  => array('type' => 'INT'),
				'created_at'            => array('type' => 'INT'),
				'payment_status'        => array('type' => "varchar", 'length' => 50),
				'payment_id'            => array('type' => "varchar", 'length' => 50),
				'status'                => array('type' => "varchar", 'length' => 50),
			);
			parent::__construct();
		}

		function save_order_item($cart_item, $order_id, $customer_id = FALSE)
		{
			if (!$customer_id) $customer_id = is_user_logged_in() ? get_current_user_id() : FALSE;

			$cart_item = wp_parse_args($cart_item, array(
				'post_id'               => '',
				'base_price'            => 0,
				'sub_total'             => 0,
				'service_type'          => '',
				'currency'              => '',
				'order_form'            => array(),
				'check_in_timestamp'    => '',
				'check_out_timestamp'   => '',
				'adult_number'          => 0,
				'child_number'          => 0,
				'infant_number'         => 0,
				'customer_id'           => 0,
				'deposit'               => '',
				'deposit_amount'        => '',
				'created_at'            => FALSE
			));
			$insert = array(
				'order_id'              => $order_id,
				'post_id'               => $cart_item['post_id'],
				'base_price'            => $cart_item['base_price'],
				'sub_total'             => $cart_item['sub_total'],
				'service_type'          => $cart_item['service_type'],
				'raw_data'              => serialize($cart_item),
				'currency'              => $cart_item['currency'],
				'order_form'            => serialize($cart_item['order_form']),
				'check_in_timestamp'    => $cart_item['check_in_timestamp'],
				'check_out_timestamp'   => $cart_item['check_out_timestamp'],
				'adult_number'          => $cart_item['adult_number'],
				'child_number'          => $cart_item['child_number'],
				'infant_number'         => $cart_item['infant_number'],
				'customer_id'           => $customer_id,
				'deposit'               => $cart_item['deposit'],
				'deposit_amount'        => $cart_item['deposit_amount'],
				'partner_id'            => get_post_field('post_author', $cart_item['post_id']),
				'payment_status'        => 0,
				'status'                => 'on-hold',
				'created_at'            => $cart_item['created_at']
			);

			return $this->insert($insert);
		}

		/**
		 * Generate Confirmation Code
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $order_item_id int
		 */
		function generate_order_item_confirm_code($order_item_id)
		{
			if ($order_item_id) {
				$item = $this->find($order_item_id);
				if (!empty($item)) {
					$update = array();

					// Customer Confirm
					if ($item['need_customer_confirm'] and !$item['customer_confirm_code']) {
						$update['customer_confirm_code'] = $this->generate_random_code();
					}
					// Partner Confirm
					if ($item['need_partner_confirm'] and !$item['partner_confirm_code']) {
						$update['partner_confirm_code'] = $this->generate_random_code();
					}

					if (!empty($update)) {
						$this->where('id', $order_item_id)->update($update);
					}
				}
			}
		}

		/**
		 * Generate Random MD5 string
		 * @param $string
		 * @return string
		 */
		function generate_random_code($string = FALSE)
		{
			if (!$string) $string = rand(0, 99999);

			return md5($string . time());
		}

		/**
		 * Get all items of an Order
		 * @param $order_id
		 * @return $this
		 */
		function get_order_items($order_id)
		{
			$a = $this->where('order_id', $order_id)->get();

			return $a->result();
		}


		/**
		 * Update Payment Status of Items by Order ID
		 *
		 * @param $order_id
		 * @since 1.0
		 */
		function complete_purchase($order_id)
		{
			$this->where('order_id', $order_id)->update(array('payment_status' => 'completed', 'status' => 'completed'));
		}

		/**
		 * Update Order Status to On-Hold for Offline Payment
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $payment_id
		 */
		function onhold_purchase($payment_id)
		{
			$this->where('payment_id', $payment_id)->update(array('status' => 'completed'));
		}

		/**
		 * Update Status of Order Item to Cancelled by Admin or Customer
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $order_id
		 */
		function cancel_purchase($order_id){
			$this->where('order_id', $order_id)->update(array('payment_status' => 'completed', 'status' => 'cancelled'));
		}

		function payment_failed()
		{

		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}


	WPBooking_Order_Model::inst();
}
