<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:37 PM
 */
use Omnipay\Omnipay;
if(!class_exists('Traveler_Paypal_Gateway') and class_exists('Traveler_Abstract_Payment_Gateway'))
{
	class Traveler_Paypal_Gateway extends Traveler_Abstract_Payment_Gateway
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
					'id'    => 'enable',
					'label' => __('Enable', 'traveler-booking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable PayPal",'traveler-booking')
				),
				array(
					'id'    => 'title',
					'label' => __('Title', 'traveler-booking'),
					'type'  => 'text',
					'std'   => 'PayPal',
				),

				array(
					'id'    => 'desc',
					'label' => __('Descriptions', 'traveler-booking'),
					'type'  => 'textarea',
					'std'   => 'You will be redirect to paypal website to finish the payment process',
				),

			);

			parent::__construct();
		}

		function do_checkout()
		{

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

