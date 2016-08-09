<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/9/2016
 * Time: 12:07 PM
 */
if(!class_exists('WB_Order')){
	class WB_Order{

		private $order_id=FALSE;
		private $customer_id=FALSE;

		function __construct($order_id){
			if(!$order_id) return;

			$this->order_id=$order_id;
			$this->customer_id=get_post_meta($this->order_id,'customer_id',true);
		}

		/**
		 * IF $need is specific, return the single value of customer of the order. Otherwise, return the array
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $need
		 * @return array|bool|string
		 */
		function get_customer($need = FALSE)
		{
			if ($this->customer_id) {
				$udata = get_userdata($this->customer_id);
				$customer_info = array(
					'id'              => $this->customer_id,
					'name'            => $udata->display_name,
					'avatar'          => get_avatar($this->customer_id),
					'description'     => $udata->user_description,
					'email'           => $udata->user_email
				);

				if ($need) {
					switch ($need) {
						default:
							return !empty($customer_info[$need]) ? $customer_info[$need] : FALSE;
							break;
					}

				}

				return $customer_info;
			}
		}

		/**
		 * Get Customer Email that received the booking email
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_customer_email(){
			if($this->order_id){
				if($this->customer_id) return $this->get_customer('email');

				// Try to get user email field
				return get_post_meta($this->order_id,'wpbooking_form_user_email',true);
			}
		}

		/**
		 * Get All Order Items
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_items()
		{
			if($this->order_id){
				$booking=WPBooking_Order::inst();
				return $booking->get_order_items($this->order_id);
			}
		}

		/**
		 * Get Checkout Form Data
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed
		 */
		function get_checkout_form_data()
		{
			if($this->order_id){
				return get_post_meta($this->order_id, 'checkout_form_data', TRUE);
			}

		}
	}
}