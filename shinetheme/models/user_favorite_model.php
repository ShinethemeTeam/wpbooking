<?php
if (!class_exists('WPBooking_User_Favorite_Model')) {
	class WPBooking_User_Favorite_Model extends WPBooking_Model
	{
		static $inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_favorite';
			$this->columns = array(
				'id'             => array(
					'type'           => "int",
					'AUTO_INCREMENT' => TRUE
				),
				'post_id'        => array('type' => "INT"),
				'user_id'        => array('type' => "INT"),
				'created_at'     => array('type' => "INT"),
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

	WPBooking_User_Favorite_Model::inst();
}