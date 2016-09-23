<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 3:47 PM
 */
if (!class_exists('WPBooking_Car_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
	class WPBooking_Car_Service_Type extends WPBooking_Abstract_Service_Type
	{
		static $_inst = FALSE;

		protected $type_id = 'car';

		function __construct()
		{
			$this->type_info = array(
				'label' => __("Car", 'wpbooking'),
                'desc'  => esc_html__('Thuê Xe Hơi', 'wpbooking')
			);

			parent::__construct();
		}

		static function inst()
		{
			if(!self::$_inst) self::$_inst=new self();

			return self::$_inst;
		}
	}

	WPBooking_Car_Service_Type::inst();
}