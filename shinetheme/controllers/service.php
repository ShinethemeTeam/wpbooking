<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 9:32 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Service'))
{
	class Traveler_Service{

		private static $_inst;

		function get_service_types()
		{
			$default= array(
				'room'=>array(
					'label'=>__("Room",'traveler-booking')
				),
				'tour'=>array(
					'label'=>__("Tour",'traveler-booking')
				),

			);

			return apply_filters('traveler_service_types',$default);
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}


	}

	Traveler_Service::inst();
}