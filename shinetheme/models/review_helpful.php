<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/28/2016
 * Time: 5:37 PM
 */
if (!class_exists('WPBooking_Review_Helpful_Model')) {
	class WPBooking_Review_Helpful_Model extends WPBooking_Model
	{
		static $inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_review_helpful';
			$this->table_version='1.0';
			$this->columns = array(
				'id'         => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'review_id' => array('type' => "bigint"),
				'user_id'    => array('type' => "bigint"),
				'created_at' => array('type' => "INT"),
			);

			parent::__construct();
		}

		static function inst()
		{
			if (!self::$inst) {
				self::$inst = new self();
			}

			return self::$inst;
		}
	}

	WPBooking_Review_Helpful_Model::inst();
}