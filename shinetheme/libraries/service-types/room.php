<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/24/2016
 * Time: 4:23 PM
 */
if(!class_exists('Traveler_Room_Service_Type') and class_exists('Traveler_Abstract_Service_Type'))
{
	class Traveler_Room_Service_Type extends Traveler_Abstract_Service_Type
	{
		static $_inst=FALSE;

		protected $type_id='room';

		function __construct()
		{
			$this->type_info=array(
				'label'=>__("Room",'traveler-booking')
			);
			$this->settings=array(

				array(
					'id'    => 'title',
					'label' => __('Title', 'traveler-booking'),
					'type'  => 'text',
					'std'   => 'PayPal',
				),



			);

			parent::__construct();
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}

	Traveler_Paypal_Gateway::inst();
}

