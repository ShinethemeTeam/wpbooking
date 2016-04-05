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
					'id'    => 'enable',
					'label' => __('Enable', 'traveler-booking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable Bank Transfer",'traveler-booking')
				),
				array(
					'id'    => 'title',
					'label' => __('Title', 'traveler-booking'),
					'type'  => 'text',
					'std'   => 'Bank Transfer',
				),

				array(
					'id'    => 'desc',
					'label' => __('Descriptions', 'traveler-booking'),
					'type'  => 'textarea',
				),
				array(
					'id'    => 'bank_account',
					'label' => __('Bank Account', 'traveler-booking'),
					'type'  => 'textarea',
					'description'=>__("Write down your back account here",'traveler-booking')
				),

			);

			add_action('traveler_gateway_desc_'.$this->gateway_id,array($this,'_show_bank_account'));

			parent::__construct();
		}
		function _show_bank_account()
		{
			$bank_account=$this->get_option('bank_account');
			echo "<br>";
			echo nl2br($bank_account);
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

