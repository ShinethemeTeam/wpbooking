<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 2:08 PM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Service_Model')) {
	class WPBooking_Service_Model extends WPBooking_Model
	{
		static $_inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_service';
			$this->columns = array(
				'id'                => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'post_id'           => array('type' => "INT"),
				'price'             => array('type' => "FLOAT"),
				'children_price'    => array('type' => "FLOAT"),
				'infant_price'      => array('type' => "FLOAT"),
				'max_people'        => array('type' => "INT"),
				'next_days_blocked' => array('type' => "INT"),
				'avg_review_rate'   => array('type' => "INT"),
				'map_lat'           => array('type' => "FLOAT"),
				'map_lng'           => array('type' => "FLOAT"),
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
	WPBooking_Service_Model::inst();
}