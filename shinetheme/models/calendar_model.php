<?php
/**
*@since 1.0.0
**/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Traveler_Calendar_Model')) {
	class Traveler_Calendar_Model extends Traveler_Model {

		public function __construct(){
			$this->table_name = 'traveler_availability';
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

	}

	new Traveler_Calendar_Model();
}