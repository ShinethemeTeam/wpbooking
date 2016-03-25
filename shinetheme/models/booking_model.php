<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/24/2016
 * Time: 10:06 AM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Traveler_Booking_Model')) {
	/**
	 * Class Traveler_Booking_Model
	 * Save Booking Item
	 * @since 1.0
	 */
	class Traveler_Booking_Model extends Traveler_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_name = 'traveler_booking_item';
			$this->table_key = 'id';
			$this->columns = array(
				'id'                   => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'booking_id'           => array(
					'type' => 'int'
				),
				'item_id'              => array(
					'type' => 'int'
				),
				'check_in_timestamp'   => array('type' => 'varchar','length'=>'255'),
				'check_out_timestamp'  => array('type' => 'varchar','length'=>'255'),
				'adult_number'         => array('type' => 'varchar','length'=>'255'),
				'child_number'         => array('type' => 'varchar','length'=>'255'),
				'infant_number'        => array('type' => 'varchar','length'=>'255'),
				'need_custom_confirm'  => array('type' => 'INT'),
				'need_partner_confirm' => array('type' => 'INT'),
				'status'               => array('type' => 'varchar','length'=>'255'),// complete, on-request, cancelled, wait-for-payment,
			);
			parent::__construct();
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}
		}
	}

	Traveler_Booking_Model::inst();
}