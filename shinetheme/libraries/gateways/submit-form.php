<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_SubmitForm_Gateway') and class_exists('WPBooking_Abstract_Payment_Gateway'))
{
	class WPBooking_SubmitForm_Gateway extends WPBooking_Abstract_Payment_Gateway
	{
		static $_inst=FALSE;

		protected $gateway_id='submit_form';

		//settings fields
		protected $settings=array();

		function __construct()
		{
			$this->gateway_info=array(
				'label'=>esc_html__("Submit Form",'wp-booking-management-system')
			);
			$this->settings=array(
				array(
					'id'    => 'enable',
					'label' => esc_html__('Enable', 'wp-booking-management-system'),
					'type'  => 'checkbox',
					'std'   => '',
					'checkbox_label'=>esc_html__("Yes, I want to enable Submit Form",'wp-booking-management-system')
				),
				array(
					'id'    => 'title',
					'label' => esc_html__('Title', 'wp-booking-management-system'),
					'type'  => 'text',
					'std'   => 'Submit Form',
				),

				array(
					'id'    => 'desc',
					'label' => esc_html__('Descriptions', 'wp-booking-management-system'),
					'type'  => 'textarea',
				),
				array(
					'id'    => 'bank_account',
					'label' => esc_html__('Bank Account', 'wp-booking-management-system'),
					'type'  => 'textarea',
					'description'=>esc_html__("Write down your back account here",'wp-booking-management-system')
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
            $order = new WB_Order($order_id);
            $order->send_email_after_booking($order_id);
			return array(
				'status'=>1
			);
		}

		function get_name_submit_form(){
            return $this->gateway_id;
        }

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}

    WPBooking_SubmitForm_Gateway::inst();
}

