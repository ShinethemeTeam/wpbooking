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
if (!class_exists('Traveler_Payment_Item_Model')) {
	class Traveler_Payment_Item_Model extends Traveler_Model
	{
		static $_inst;

		function __construct()
		{
			$this->table_name = 'payment_item';
			$this->table_version = '1.0';
			$this->columns = array(
				'id'            => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'payment_id'    => array('type' => "INT"),
				'created_on'    => array('type' => "INT"),
				'order_item_id' => array('type' => 'INT'),
			);

			parent::__construct();
		}


		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	Traveler_Payment_Item_Model::inst();
}