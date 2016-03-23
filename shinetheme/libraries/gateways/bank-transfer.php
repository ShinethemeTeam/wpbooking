<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:37 PM
 */
use Omnipay\Omnipay;
if(!class_exists('Traveler_BankTransfer_Gateway') and class_exists('Traveler_Abstract_Payment_Gateway'))
{
	class Traveler_BankTransfer_Gateway extends Traveler_Abstract_Payment_Gateway
	{
		static $_inst=FALSE;

		protected $gateway_id='bank_transfer';

		//settings fields
		protected $settings=array();

		function __construct()
		{
			$this->gateway_info=array(
				'label'=>__("Bank Transfer",'traveler-booking')
			);
			$this->settings=array(
				array(
					'id'    => 'bank_transfer_enable_div',
					'label' => __('Enable', 'traveler-booking'),
					'type'  => 'muti-checkbox',
					'std'   => '',
					'value' => array(
						array(
							'id'    => 'bank_transfer_enable',
							'label' =>__('Yes, I want to enable Bank Transfer','travel-booking'),
						),
					),
				),
				array(
					'id'    => 'bank_transfer_display_title',
					'label' => __('Title', 'traveler-booking'),
					'type'  => 'text',
					'std'   => 'Bank Transfer',
				),

				array(
					'id'    => 'bank_transfer_display_desc',
					'label' => __('Descriptions', 'traveler-booking'),
					'type'  => 'textarea',
				),
				array(
					'id'    => 'bank_transfer_bank_account',
					'label' => __('Bank Account', 'traveler-booking'),
					'type'  => 'textarea',
					'description'=>__("Write down your back account here",'traveler-booking')
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

	Traveler_BankTransfer_Gateway::inst();
}

