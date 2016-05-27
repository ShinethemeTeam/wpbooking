<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 12:01 PM
 */
if(!class_exists('WPBooking_Email'))
{
	class WPBooking_Email
	{
		static $_inst;
		function __construct()
		{
			// Init Shortcodes

			add_action('init',array($this,'_load_email_shortcodes'));

			add_action('traveler_after_checkout_success',array($this,'_send_order_email_success'));

			/**
			 * Send Emails when new Order Item has been updated/changed, example: payment complete or cancelled
			 * @since 1.0
			 */
			add_action('traveler_order_item_changed',array($this,'_send_order_email_success'));


			//add_action('traveler_after_checkout_success',array($this,'_send_order_email_confirm'));


		}

		function _send_order_email_success($order_id)
		{
			$order_model=WPBooking_Order_Model::inst();

			$items=$order_model->get_order_items($order_id);

			// Send Booking Information
			// To all Partners
			$authors=array();
			if(!empty($items))
			{
				foreach($items as $key=>$value)
				{
					if(!empty($value['partner_id'])){
						$authors[$value['partner_id']][]=$value;
					}
				}
			}

			if(!empty($authors))
			{
				foreach($authors as $key=>$value)
				{
					$to=$user_email = get_the_author_meta( 'user_email' ,$key);
					$subject=sprintf(__("New Order from %s",'wpbooking'),get_bloginfo('title'));
					WPBooking()->set('items',$value);
					$message=wpbooking_load_view('emails/booking-information');
					$this->send($to,$subject,$message);
				}
			}

			// to Admin
			$to=get_option('admin_email');
			$subject=sprintf(__("New Order from %s",'wpbooking'),get_bloginfo('title'));
			$message=wpbooking_load_view('emails/booking-information',array('items'=>$items,'order_id'=>$order_id));
			$this->send($to,$subject,$message);

		}

		function _send_order_email_confirm()
		{

		}

		function send($to, $subject, $message, $attachment=false){

			if(!$message) return array(
				'status'  => 0,
				'message' => __("Email content is empty",'wpbooking')
			);
			$from = wpbooking_get_option('email_from');
			$from_address = wpbooking_get_option('email_from_address');
			$headers = array();

			if($from and $from_address){
				$headers[]='From:'. $from .' <'.$from_address.'>';
			}

			add_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );

			$check=wp_mail( $to, $subject, $message,$headers ,$attachment);

			remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			return array(
				'status'=>$check,
				'data'=>array(
					'to'=>$to,
					'subject'=>$subject,
					'message'=>$message,
					'headers'=>$headers
				)
			);
		}
		function set_html_content_type()
		{
			return 'text/html';
		}

		/**
		 * Load Default Email Shortcodes in folders libraries/shortcodes/email
		 *
		 * @since 1.0
		 */
		function _load_email_shortcodes()
		{
			WPBooking_Loader::inst()->load_library('shorcodes/email/order-table');
		}

		static function inst()
		{
			if(!self::$_inst)
			{
				self::$_inst=new self();
			}

			return self::$_inst;
		}
	}

	WPBooking_Email::inst();
}