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

            /**
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_before_order_information_table',array($this,'_show_paynow_button'));
		}

		function _show_paynow_button($order_object)
        {
            if($order_object->get_payment_gateway('id')==$this->gateway_id){

                // Check Payment is not complete
                if(in_array($order_object->get_status(),array('payment-failed'))){
                    // Now show the Paynow Button
                    $gateway=$this->gatewayObject;

                    if ($this->is_test_mode()) {
                        $gateway->setTestMode(true);
                        $gateway->setUsername($this->get_option('test_api_username'));
                        $gateway->setPassword($this->get_option('test_api_password'));
                        $gateway->setSignature($this->get_option('test_api_signature'));
                    }else{

                        $gateway->setUsername($this->get_option('api_username'));
                        $gateway->setPassword($this->get_option('api_password'));
                        $gateway->setSignature($this->get_option('api_signature'));
                    }

                    $total=$order_object->get_total();

                    $purchase = array(
                        'amount'      => (float)$total,
                        'currency'    => WPBooking_Currency::get_current_currency('currency'),
                        'description' => __('WPBooking','wpbooking'),
                        'returnUrl'   => $this->get_return_url($order_object->get_order_id()),
                        'cancelUrl'   => $this->get_cancel_url($order_object->get_order_id()),
                    );

                    $response = $gateway->purchase(
                        $purchase
                    )->send();

                    if (!$response->isSuccessful() and $response->isRedirect()) {

                        ?>
                        <a class="wb-btn wb-btn-blue wb-btn-md" href="<?php echo esc_url($response->getRedirectUrl())   ?>"><?php esc_html_e('Pay Now','wpbooking') ?></a>
                        <p class=><?php esc_html_e('You will be redirect to PayPal.com to complete the payment','wpbooking') ?></p>
                        <?php
                    }else{
                        wpbooking_set_message(esc_html__('Paypal Error:','wpbooking').' '.$response->getMessage(),'error');
                        echo wpbooking_get_message();
                    }
                }

            }
        }

		function validate()
		{

			if ($this->is_test_mode()) {
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
		function do_checkout($order_id)
		{

			if(!$this->validate())
			{
				return array(
					'status'=>0
				);
			}
			$order=new WB_Order($order_id);

			$gateway=$this->gatewayObject;
            if ($this->is_test_mode()) {
				$gateway->setTestMode(true);
				$gateway->setUsername($this->get_option('test_api_username'));
				$gateway->setPassword($this->get_option('test_api_password'));
				$gateway->setSignature($this->get_option('test_api_signature'));
			}else{

				$gateway->setUsername($this->get_option('api_username'));
				$gateway->setPassword($this->get_option('api_password'));
				$gateway->setSignature($this->get_option('api_signature'));
			}

			$total=$order->get_total();

			$purchase = array(
				'amount'      => (float)$total,
				'currency'    => WPBooking_Currency::get_current_currency('currency'),
				'description' => __('WPBooking','wpbooking'),
				'returnUrl'   => $this->get_return_url($order_id),
				'cancelUrl'   => $this->get_cancel_url($order_id),
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

		/**
		 * Validate Return Data from PayPal
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $order_id
		 * @return bool
		 */
		function complete_purchase($order_id)
		{
			if(!$this->validate())
			{
				return FALSE;
			}
			$order=new WB_Order($order_id);

			$gateway=$this->gatewayObject;
            if ($this->is_test_mode()) {
				$gateway->setTestMode(true);
				$gateway->setUsername($this->get_option('test_api_username'));
				$gateway->setPassword($this->get_option('test_api_password'));
				$gateway->setSignature($this->get_option('test_api_signature'));
			}else{

				$gateway->setUsername($this->get_option('api_username'));
				$gateway->setPassword($this->get_option('api_password'));
				$gateway->setSignature($this->get_option('api_signature'));
			}

			$total=$order->get_total();

			$purchase = array(
				'amount'      => (float)$total,
				'currency'    => WPBooking_Currency::get_current_currency('currency'),
				'description' => __('WPBooking','wpbooking'),
				'returnUrl'   => $this->get_return_url($order_id),
				'cancelUrl'   => $this->get_cancel_url($order_id),
			);

			$response = $gateway->completePurchase(
				$purchase
			)->send();

			if ($response->isSuccessful()) {
				return true;

			} elseif ($response->isRedirect()) {
				return FALSE;
			} else {
			    var_dump($response->getMessage());
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

