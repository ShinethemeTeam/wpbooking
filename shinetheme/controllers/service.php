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

		function __construct()
		{
			// Load Abstract Service Type class and Default Service Types

			$loader=Traveler_Loader::inst();
			$loader->load_library(array(
				'service-types/abstract-service-type',
				'service-types/room',
			));
		}

		function get_service_types()
		{
			$default= array(
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