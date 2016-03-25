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

		protected $gateway_id='paypal';

		//settings fields
		protected $settings=array();

		function __construct()
		{
			$this->gateway_info=array(
				'label'=>__("PayPal",'traveler-booking')
			);
			$this->settings=array(
				array(
					'id'    => 'paypal_enable_div',
					'label' => __('Enable', 'traveler-booking'),
					'type'  => 'muti-checkbox',
					'std'   => '',
					'value' => array(
						array(
							'id'    => 'paypal_enable',
							'label' =>__('Yes, I want to enable PayPal','travel-booking'),
						),
					),
				),
				array(
					'id'    => 'paypal_display_title',
					'label' => __('Title', 'traveler-booking'),
					'type'  => 'text',
					'std'   => 'PayPal',
				),

				array(
					'id'    => 'paypal_display_desc',
					'label' => __('Descriptions', 'traveler-booking'),
					'type'  => 'textarea',
					'std'   => 'You will be redirect to paypal website to finish the payment process',
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

