<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:37 PM
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_BankTransfer_Gateway') and class_exists('WPBooking_Abstract_Payment_Gateway'))
{
	class WPBooking_BankTransfer_Gateway extends WPBooking_Abstract_Payment_Gateway
	{
		static $_inst=FALSE;

		protected $gateway_id='bank_transfer';

		//settings fields
		protected $settings=array();

		function __construct()
		{
			$this->gateway_info=array(
				'label'=>__("Bank Transfer",'wpbooking')
			);
			$this->settings=array(
				array(
					'id'    => 'enable',
					'label' => __('Enable', 'wpbooking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable Bank Transfer",'wpbooking')
				),
				array(
					'id'    => 'title',
					'label' => __('Title', 'wpbooking'),
					'type'  => 'text',
					'std'   => 'Bank Transfer',
				),

				array(
					'id'    => 'desc',
					'label' => __('Descriptions', 'wpbooking'),
					'type'  => 'textarea',
				),
				array(
					'id'    => 'bank_account',
					'label' => __('Bank Account', 'wpbooking'),
					'type'  => 'textarea',
					'description'=>__("Write down your back account here",'wpbooking')
				),

			);

			add_action('wpbooking_gateway_desc_'.$this->gateway_id,array($this,'_show_bank_account'));

			parent::__construct();
		}
		function _show_bank_account()
		{
			$bank_account=$this->get_option('bank_account');
			echo "<br>";
			echo nl2br($bank_account);
		}

		function do_checkout($order_id){
			return array(
				'status'=>1
			);
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}

	//WPBooking_BankTransfer_Gateway::inst();
}

