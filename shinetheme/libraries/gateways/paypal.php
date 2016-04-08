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

		protected $gatewayObject=FALSE;

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
				array(
					'type'=>'hr'
				),
				array(
					'id'    => 'api_username',
					'label' => __('API Username', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'api_password',
					'label' => __('API Password', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'api_signature',
					'label' => __('API Signature', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_mode',
					'label' => __('Test Mode', 'traveler-booking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable PayPal Sandbox Mode",'traveler-booking')
				),
				array(
					'id'    => 'test_api_username',
					'label' => __('Test API Username', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_api_password',
					'label' => __('Test API Password', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_api_signature',
					'label' => __('Test API Signature', 'traveler-booking'),
					'type'  => 'text',
					'std'   => '',
				),
			);
			$this->gatewayObject=Omnipay::create('PayPal_Express');

			parent::__construct();
		}

		function validate()
		{

			if ($this->get_option('test_mode') == 'on') {
				if(!$this->get_option('test_api_username') or !$this->get_option('test_api_password') or !$this->get_option('test_api_signature') )
				{
					traveler_set_message(__('Test PayPal API is not correctly! Please check with the Admin','traveler-booking'),'error');
					return FALSE;
				}
			}else{

				if(!$this->get_option('api_username') or !$this->get_option('api_password') or !$this->get_option('api_signature') )
				{
					traveler_set_message(__('PayPal API is not correctly! Please check with the Admin','traveler-booking'),'error');
					return FALSE;
				}
			}

			return true;
		}
		function do_checkout($order_id,$payment_id)
		{

			if(!$this->validate())
			{
				return array(
					'status'=>0
				);
			}
			$payment=Traveler_Payment_Model::inst();

			$gateway=$this->gatewayObject;
			if ($this->get_option('test_mode') == 'on') {
				$gateway->setTestMode(true);
				$gateway->setUsername($this->get_option('test_api_username'));
				$gateway->setPassword($this->get_option('test_api_password'));
				$gateway->setSignature($this->get_option('test_api_signature'));
			}else{

				$gateway->setUsername($this->get_option('api_username'));
				$gateway->setPassword($this->get_option('api_password'));
				$gateway->setSignature($this->get_option('api_signature'));
			}

			$total=$payment->get_payment_amount($payment_id);

			$purchase = array(
				'amount'      => (float)$total,
				'currency'    => Traveler_Currency::get_current_currency('name'),
				'description' => __('Traveler Booking','traveler-booking'),
				'returnUrl'   => $this->get_return_url($order_id,$payment_id),
				'cancelUrl'   => $this->get_cancel_url($order_id,$payment_id),
			);

			$response = $gateway->purchase(
				$purchase
			)->send();

			if ($response->isSuccessful()) {

				return array('status' => 1);

			} elseif ($response->isRedirect()) {

				return array('status' => 1, 'redirect' => $response->getRedirectUrl());
			} else {

				traveler_set_message($response->getMessage(),'error');
				return array('status' => false, 'data' => $purchase);

			}
		}

		function complete_purchase($payment_id,$order_id)
		{
			if(!$this->validate())
			{
				return array(
					'status'=>0
				);
			}
			$payment=Traveler_Payment_Model::inst();

			$gateway=$this->gatewayObject;
			if ($this->get_option('test_mode') == 'on') {
				$gateway->setTestMode(true);
				$gateway->setUsername($this->get_option('test_api_username'));
				$gateway->setPassword($this->get_option('test_api_password'));
				$gateway->setSignature($this->get_option('test_api_signature'));
			}else{

				$gateway->setUsername($this->get_option('api_username'));
				$gateway->setPassword($this->get_option('api_password'));
				$gateway->setSignature($this->get_option('api_signature'));
			}

			$total=$payment->get_payment_amount($payment_id);

			$purchase = array(
				'amount'      => (float)$total,
				'currency'    => Traveler_Currency::get_current_currency('name'),
				'description' => __('Traveler Booking','traveler-booking'),
				'returnUrl'   => $this->get_return_url($order_id,$payment_id),
				'cancelUrl'   => $this->get_cancel_url($order_id,$payment_id),
			);

			$response = $gateway->completePurchase(
				$purchase
			)->send();


			if ($response->isSuccessful()) {
				return true;

			} elseif ($response->isRedirect()) {
				return FALSE;
			} else {
				return FALSE;

			}
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

