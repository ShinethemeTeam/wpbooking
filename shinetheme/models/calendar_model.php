<?php
/**
*@since 1.0.0
**/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Calendar_Model')) {
	class WPBooking_Calendar_Model extends WPBooking_Model {

		static $_inst = FALSE;

		public function __construct(){
			$this->table_name = 'wpbooking_availability';
			$this->columns = array(
				'id' => array(
					'type'           => 'int',
					'AUTO_INCREMENT' => TRUE
				),
				'post_id'     => array('type' => 'int', 'length' => 11),
				'start'    => array('type' => 'varchar', 'length' => 255),
				'end'   => array('type' => 'varchar', 'length' => 255),
				'price'       => array('type' => 'varchar', 'length' => 255),
				'status' => array('type' => 'varchar', 'length' => 255),
				'base_id'     => array('type' => 'int', 'length' => 11),
				'group_day'   => array('type' => 'varchar', 'length' => 255),

			);
			parent::__construct();
			
		}

		function get_prices($post_id,$from,$to){
			return $this->where('post_id',$post_id)->where('start>=',$from)->where('start<=',$to)->get()->result();
		}

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}

	}

	WPBooking_Calendar_Model::inst();
}