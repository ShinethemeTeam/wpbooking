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
if (!class_exists('Traveler_Order_Model')) {
	class Traveler_Order_Model extends Traveler_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_name = 'traveler_order_item';
			$this->columns = array(
				'id'                    => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'order_id'              => array('type' => "INT"),
				'post_id'               => array('type' => "INT"),
				'base_price'            => array('type' => "FLOAT"),
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
				'need_customer_confirm' => array('type' => 'INT'),
				'customer_confirm_code' => array('type' => "varchar", 'length' => 255),
				'partner_confirm_code'  => array('type' => "varchar", 'length' => 255),
				'need_partner_confirm'  => array('type' => 'INT'),
				'payment_status'        => array('type' => "varchar", 'length' => 50),
				'payment_id'            => array('type' => "varchar", 'length' => 50),
				'status'                => array('type' => "varchar", 'length' => 50),
			);
			parent::__construct();
		}

		function create($cart)
		{

			$order_data = array(
				'post_title'  => sprintf(__('New Order In %s', 'traveler-booking'), date(get_option('date_format') . ' @' . get_option('time_format'))),
				'post_type'   => 'traveler_order',
				'post_status' => 'publish'
			);
			$order_id = wp_insert_post($order_data);

			if (!empty($cart) and is_array($cart)) {
				foreach ($cart as $key => $value) {
					$this->save_order_item($value, $order_id);
				}
			}

			return $order_id;
		}

		function save_order_item($cart_item, $order_id)
		{
			$cart_item = wp_parse_args($cart_item, array(
				'post_id'             => '',
				'base_price'          => 0,
				'service_type'        => '',
				'order_form'          => array(),
				'check_in_timestamp'  => '',
				'check_out_timestamp' => '',
				'adult_number'        => 0,
				'child_number'        => 0,
				'infant_number'       => 0,
				'customer_id'         => 0
			));
			$insert = array(
				'order_id'              => $order_id,
				'post_id'               => $cart_item['post_id'],
				'base_price'            => $cart_item['base_price'],
				'service_type'          => $cart_item['service_type'],
				'raw_data'              => serialize($cart_item),
				'currency'              => Traveler_Currency::get_current_currency('currency'),
				'order_form'            => serialize($cart_item['order_form']),
				'check_in_timestamp'    => $cart_item['check_in_timestamp'],
				'check_out_timestamp'   => $cart_item['check_out_timestamp'],
				'adult_number'          => $cart_item['adult_number'],
				'child_number'          => $cart_item['child_number'],
				'infant_number'         => $cart_item['infant_number'],
				'customer_id'           => $cart_item['customer_id'],
				'partner_id'            => get_post_field('post_author', $cart_item['post_id']),
				'need_customer_confirm' => array('type' => 'INT'),
				'need_partner_confirm'  => array('type' => 'INT'),
				'payment_status'        => 0,
				'status'                => 'on-hold'
			);

			$this->insert($insert);
		}

		/**
		 * Get all items of an Order
		 * @param $order_id
		 * @return $this
		 */
		function get_order_items($order_id)
		{
			return $this->where('order_id', $order_id)->get();
		}


		/**
		 * Get Payable order items at current time
		 * @param $order_id
		 * @return bool|array
		 */
		function prepare_paying($order_id,$payment_id)
		{
			$items = $this->get_order_items($order_id);
			if (!empty($items)) {
				$on_paying = array();
				foreach ($items as $key => $value) {
					// Payment Completed -> Ignore
					if ($value['payment_status'] == 'completed') continue;

					// Payment On-Paying -> Ignore
					if ($value['payment_status'] == 'on-paying') continue;

					// Customer does not confirm the booking -> Ignore
					if ($value['need_customer_confirm'] === 1) continue;

					// Partner does not confirm the booking -> Ignore
					if ($value['need_partner_confirm'] === 1) continue;

					$on_paying[] = $value['id'];
					$this->where('id', $value['id'])->update(array('payment_status' => 'on-paying','payment_id'=>$payment_id));
				}

				return $on_paying;
			}
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}


	Traveler_Order_Model::inst();
}
