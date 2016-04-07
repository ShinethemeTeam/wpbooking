<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/6/2016
 * Time: 10:40 AM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Traveler_Payment_Model')) {
	class Traveler_Payment_Model extends Traveler_Model
	{
		static $_inst;

		function __construct()
		{
			$this->table_name = 'payment';
			$this->table_version = '1.0';
			$this->columns = array(
				'id'             => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'order_id'       => array('type' => "INT"),
				'created_on'     => array('type' => "INT"),
				'amount'         => array('type' => 'float'),
				'currency'       => array('type' => 'varchar', 'length' => 255),
				'gateway'        => array('type' => 'varchar', 'length' => 255),
				'payment_status' => array('type' => "varchar", 'length' => 255)
			);

			parent::__construct();
		}


		function create_payment($order_id, $gateway)
		{
			$booking = Traveler_Booking::inst();
			$data = array(
				'order_id'   => $order_id,
				'created_on' => time(),
				'amount'     => $booking->get_order_pay_amount($order_id),
				'currency'   => Traveler_Currency::get_current_currency('currency'),
				'gateway'    => $gateway
			);

			return $this->insert($data);
		}

		function get_payment_amount($payment_id)
		{
			return $this->find($payment_id)->amount;
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	Traveler_Payment_Model::inst();
}