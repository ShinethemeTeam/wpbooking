<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Payment_Model')) {
	class WPBooking_Payment_Model extends WPBooking_Model
	{
		static $_inst;

		function __construct()
		{
			$this->table_name = 'wpbooking_payment';
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

		function get_payment_amount($payment_id)
		{
			$payment= $this->find($payment_id);
			if($payment) return $payment['amount'];
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Payment_Model::inst();
}