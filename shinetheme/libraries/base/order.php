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

			$this->init($order_id);
		}

		private function init($order_id)
		{
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

		/**
		 * Get Order Total Money
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return int|mixed|void
		 */
		function get_total()
		{
			$total=0;
			if($this->order_id){
				$order_items = $this->get_items();

				foreach ($order_items as $key => $value) {
					$total += $this->get_item_total($value, TRUE);
				}

				$total = apply_filters('wpbooking_get_order_total', $total);

				return $total;
			}
		}

		/**
		 * Get Order Item Total
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $item
		 * @param bool|FALSE $need_convert
		 * @return float|mixed|void
		 */
		function get_item_total($item,$need_convert=FALSE)
		{
			$item_price = $item['sub_total'];
			$item_price = apply_filters('wpbooking_order_item_total', $item_price, $item, $item['service_type']);
			$item_price = apply_filters('wpbooking_order_item_total_' . $item['service_type'], $item_price, $item);

			// Convert to current currency
			if ($need_convert) {
				$item_price = WPBooking_Currency::convert_money($item_price, array(
					'currency' => $item['currency']
				));
			}

			return $item_price;
		}

		/**
		 * Get Price HTML of Order Item
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $item array
		 * @return string
		 */
		function get_item_total_html($item){

			$item_price = $this->get_item_total($item, TRUE);

			return WPBooking_Currency::format_money($item_price);
		}

		/**
		 * Do Create New Order
		 *
		 * @param $cart
		 * @param array $checkout_form_data
		 * @param bool|FALSE $selected_gateway
		 * @param bool|FALSE $customer_id
		 * @return int|WP_Error
		 */
		function create($cart, $checkout_form_data = array(), $selected_gateway = FALSE, $customer_id = FALSE)
		{
			$created = time();
			$order_data = array(
				'post_title'  => sprintf(__('New Order In %s', 'wpbooking'), date(get_option('date_format') . ' @' . get_option('time_format'))),
				'post_type'   => 'wpbooking_order',
				'post_status' => 'publish'
			);
			$order_id = wp_insert_post($order_data);

			// Save Current Data
			$this->init($order_id);

			if ($order_id) {
				update_post_meta($order_id, 'checkout_form_data', $checkout_form_data);
				update_post_meta($order_id, 'wpbooking_selected_gateway', $selected_gateway);
				update_post_meta($order_id, 'customer_id', $customer_id);

				//User Fields in case of customer dont want to create new account
				$f = array('user_email', 'user_first_name', 'user_last_name');
				foreach ($f as $v) {
					if (array_key_exists($v, $checkout_form_data))
						update_post_meta($order_id, $v, $checkout_form_data[$v]);
				}


				if (!empty($checkout_form_data)) {
					foreach ($checkout_form_data as $key => $value) {
						update_post_meta($order_id, 'wpbooking_form_' . $key, $value['value']);
					}
				}
			}

			if (!empty($cart) and is_array($cart)) {
				foreach ($cart as $key => $value) {
					$value['created_at'] = $created;
					WPBooking_Order_Model::inst()->save_order_item($value, $order_id, $customer_id);
				}
			}


			return $order_id;
		}

		/**
		 * Cancel All Order Items by Admin or Customer
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function cancel_purchase()
		{
			if($this->order_id){

				// Update Status of Order Item in database
				$order_model=WPBooking_Order_Model::inst();
				$order_model->cancel_purchase($this->order_id);
			}
		}

		/**
		 * Complete all Order Items after validate by payment gateways
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function complete_purchase()
		{
			if($this->order_id){

				// Update Status of Order Item in database
				$order_model=WPBooking_Order_Model::inst();
				$order_model->complete_purchase($this->order_id);
			}
		}

		/**
		 * Can not validate data from Gateway or Data is not valid
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function payment_failed()
		{
			if($this->order_id){
				// Update Status of Order Item in database
				$order_model=WPBooking_Order_Model::inst();
				$order_model->where('order_id',$this->order_id)->update(array(
					'payment_status'=>'failed'
				));
			}
		}
	}
}