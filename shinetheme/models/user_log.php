<?php
if (!class_exists('WPBooking_User_Log_Model')) {
	class WPBooking_User_Log_Model extends WPBooking_Model
	{
		static $inst = FALSE;

		function __construct()
		{
			$this->table_name = 'wpbooking_user_log';
			$this->columns = array(
				'id'          => array(
					'type'           => "INT",
					'AUTO_INCREMENT' => TRUE
				),
				'user_id'     => array('type' => "INT"),
				'type'        => array('type' => "varchar", 'length' => 50),
				'source_id'   => array('type' => "INT"),
				'parent_type' => array('type' => "INT"),
				'parent_id'   => array('type' => "INT"),
				'created_at'  => array('type' => "INT")
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

	WPBooking_User_Log_Model::inst();
}