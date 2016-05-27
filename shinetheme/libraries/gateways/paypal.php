<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/23/2016
 * Time: 2:37 PM
 */
use Omnipay\Omnipay;
if(!class_exists('WPBooking_Paypal_Gateway') and class_exists('WPBooking_Abstract_Payment_Gateway'))
{
	class WPBooking_Paypal_Gateway extends WPBooking_Abstract_Payment_Gateway
	{
		static $_inst=FALSE;

		protected $gateway_id='paypal';

		//settings fields
		protected $settings=array();

		protected $gatewayObject=FALSE;

		function __construct()
		{
			$this->gateway_info=array(
				'label'=>__("PayPal",'wpbooking')
			);
			$this->settings=array(
				array(
					'id'    => 'enable',
					'label' => __('Enable', 'wpbooking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable PayPal",'wpbooking')
				),
				array(
					'id'    => 'title',
					'label' => __('Title', 'wpbooking'),
					'type'  => 'text',
					'std'   => 'PayPal',
				),

				array(
					'id'    => 'desc',
					'label' => __('Descriptions', 'wpbooking'),
					'type'  => 'textarea',
					'std'   => 'You will be redirect to paypal website to finish the payment process',
				),
				array(
					'type'=>'hr'
				),
				array(
					'id'    => 'api_username',
					'label' => __('API Username', 'wpbooking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'api_password',
					'label' => __('API Password', 'wpbooking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'api_signature',
					'label' => __('API Signature', 'wpbooking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_mode',
					'label' => __('Test Mode', 'wpbooking'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>__("Yes, I want to enable PayPal Sandbox Mode",'wpbooking')
				),
				array(
					'id'    => 'test_api_username',
					'label' => __('Test API Username', 'wpbooking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_api_password',
					'label' => __('Test API Password', 'wpbooking'),
					'type'  => 'text',
					'std'   => '',
				),
				array(
					'id'    => 'test_api_signature',
					'label' => __('Test API Signature', 'wpbooking'),
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
					wpbooking_set_message(__('Test PayPal API is not correctly! Please check with the Admin','wpbooking'),'error');
					return FALSE;
				}
			}else{

				if(!$this->get_option('api_username') or !$this->get_option('api_password') or !$this->get_option('api_signature') )
				{
					wpbooking_set_message(__('PayPal API is not correctly! Please check with the Admin','wpbooking'),'error');
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
			$payment=WPBooking_Payment_Model::inst();

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
				'currency'    => WPBooking_Currency::get_current_currency('name'),
				'description' => __('Traveler Booking','wpbooking'),
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

				wpbooking_set_message($response->getMessage(),'error');
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
			$payment=WPBooking_Payment_Model::inst();

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
				'currency'    => WPBooking_Currency::get_current_currency('name'),
				'description' => __('Traveler Booking','wpbooking'),
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

	WPBooking_Paypal_Gateway::inst();
}

