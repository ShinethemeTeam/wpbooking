<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/9/2016
 * Time: 12:07 PM
 */
if (!class_exists('WB_Order')) {
    class WB_Order
    {

        private $order_id = FALSE;
        private $user_id = FALSE;

        private $data=array();

        function __construct($order_id)
        {

            $this->init($order_id);
        }

        private function init($order_id)
        {
            if (!$order_id) return;

            $this->order_id = $order_id;
            $this->user_id = get_post_meta($this->order_id, 'user_id', true);
            $this->data=WPBooking_Order_Model::inst()->where('order_id',$this->order_id)->get()->row();
        }

        function get_order_id()
        {
            return $this->order_id;
        }

        function get_order_data()
        {
            return $this->data;
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
            if ($this->user_id) {
                $udata = get_userdata($this->user_id);
                $customer_info = array(
                    'id'          => $this->user_id,
                    'name'        => $udata->display_name,
                    'avatar'      => get_avatar($this->user_id),
                    'description' => $udata->user_description,
                    'email'       => $udata->user_email
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
        function get_customer_email()
        {
            if ($this->order_id) {
                if ($this->user_id) return $this->get_customer('email');

                // Try to get user email field
                return get_post_meta($this->order_id, 'wpbooking_form_user_email', true);
            }
        }


        /**
         * Get Order Total Money
         *
         * @since 1.0
         * @author quandq
         *
         * @param array $args
         * @return mixed|void
         */
        function get_total($args = array())
        {
            if ($this->order_id) {
                $order_data = $this->get_order_data();
                $total = $order_data['price'];
                if(!empty($order_data['deposit_price'])){
                    $total = $order_data['deposit_price'];
                }
                if(!empty($args)){
                    $total = $order_data['price'];
                    if(!empty($args['without_deposit'])){
                        $total = $order_data['deposit_price'];
                    }
                }
                $total = apply_filters('wpbooking_get_order_total', $total);
                return $total;
            }
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
        function create($cart, $form_billing_data = array(), $selected_gateway = FALSE, $customer_id = FALSE)
        {
            $created = time();
            $order_data = array(
                'post_title'  => sprintf(__('New Order In %s', 'wpbooking'), date(get_option('date_format') . ' @' . get_option('time_format'))),
                'post_type'   => 'wpbooking_order',
                'post_status' => 'on_hold'
            );
            $order_id = wp_insert_post($order_data);

            // Save Current Data
            $this->init($order_id);

            if ($order_id) {

                $booking = WPBooking_Checkout_Controller::inst();

                $price = $booking->get_cart_total(array( 'without_deposit' => false, 'without_tax' => true ),$cart);
                $deposit_price = $booking->get_cart_total(array( 'without_deposit' => true, 'without_tax' => true ),$cart);
                $tax = $booking->get_cart_tax_price();
                $post_author = get_post_field( 'post_author', $cart['post_id'] );

                update_post_meta($order_id, 'post_id', $cart['post_id']);
                update_post_meta($order_id, 'service_type', $cart['service_type']);
                update_post_meta($order_id, 'price', $price);
                update_post_meta($order_id, 'discount', $cart['discount']);
                update_post_meta($order_id, 'extra_fees', array());
                update_post_meta($order_id, 'tax',$tax);
                update_post_meta($order_id, 'currency', WPBooking_Currency::get_current_currency('currency'));
                update_post_meta($order_id, 'raw_data', array());
                update_post_meta($order_id, 'check_in_timestamp', $cart['check_in_timestamp']);
                update_post_meta($order_id, 'check_out_timestamp', $cart['check_out_timestamp']);
                update_post_meta($order_id, 'user_id', $customer_id);
                update_post_meta($order_id, 'author_id', $post_author);
                update_post_meta($order_id, 'deposit_price', $deposit_price);
                update_post_meta($order_id, 'deposit', $cart['deposit']);
                update_post_meta($order_id, 'created_at', $created);
                update_post_meta($order_id, 'payment_method', $selected_gateway);

                if (!empty($form_billing_data)) {
                    foreach ($form_billing_data as $key => $value) {
                        update_post_meta($order_id, 'wpbooking_' . $key, $value['value']);
                    }
                }
            }

            WPBooking_Order_Model::inst()->save_order($cart, $order_id, $customer_id);
            do_action('wpbooking_save_order_'.$cart['service_type'],$cart,$order_id);

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
            if ($this->order_id) {

                // Update Current Order
                wp_update_post(array(
                    'ID'=>$this->order_id,
                    'post_status'=>'cancelled'
                ));

                // Update Status of Order Item in database
                $order_model = WPBooking_Order_Model::inst();
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
            if ($this->order_id) {
                // Update Current Order
                wp_update_post(array(
                    'ID'=>$this->order_id,
                    'post_status'=>'completed'
                ));

                // Update Status of Order Item in database
                $order_model = WPBooking_Order_Model::inst();
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
            if ($this->order_id) {

                // Update Status
                wp_update_post(array(
                    'ID'=>$this->order_id,
                    'post_status'=>'payment_failed'
                ));

                // Update Status of Order Item in database
                $order_model = WPBooking_Order_Model::inst();
                $order_model->where('order_id', $this->order_id)->update(array(
                    'status' => 'payment_failed'
                ));
            }
        }

        /**
         * Get Tax Total
         *
         * @since 1.0
         * @author dungdt
         *
         * @return int|mixed|void
         */
        function get_tax_price()
        {
            $price = 0;

            return $price;
        }




        /**
         * Get Order PayNow Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @return float
         */
        function get_paynow_price()
        {
            $price = $this->get_total();

            $price = apply_filters('wpbooking_get_order_paynow_price', $price);

            return $price;
        }

        /**
         * Get Status of Current Order
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool|false|string
         */
        function get_status()
        {
            if($this->order_id){
                return get_post_status($this->order_id);
            }

            return false;
        }

        /**
         * Get HTML of Order Status
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string
         */
        function get_status_html()
        {
            $status=$this->get_status();

            if($status){
                $all_status=WPBooking_Config::inst()->item('order_status');
                if(array_key_exists($status,$all_status)){
                    switch($status){
                        case "on_hold":
                        case "payment_failed":
                            return sprintf('<label class="bold text_up">%s</label>',$all_status[$status]['label']);
                            break;
                        case "completed":
                            return sprintf('<label class="bold text_up">%s</label>',$all_status[$status]['label']);
                            break;
                        case "cancelled":
                        case "refunded":
                            return sprintf('<label class="bold text_up">%s</label>',$all_status[$status]['label']);
                            break;

                        default:
                            return sprintf('<label class="bold text_up">%s</label>',$all_status[$status]['label']);
                            break;
                    }
                }else{
                    return sprintf('<label class="bold text_up">%s</label>',esc_html__('Unknown','wpbooking'));
                }
            }
        }

        /**
         * Get Booking Date
         *
         * @since 1.0
         * @author dungdt
         *
         * @param null $format
         * @return false|string
         */
        function get_booking_date($format=NULL)
        {
            if($this->order_id){
                if(!$format) $format=get_option('date_format');

                return get_the_time($format);
            }
        }

        /**
         * Gate Gateway Info or Gateway Object
         *
         * @since 1.0
         * @author dungdt
         *
         * @param string $need
         * @return bool|mixed|object|string
         */
        function get_payment_gateway($need='label'){

            if($this->order_id){
                $gateway=get_post_meta($this->order_id,'payment_method',true);

                if($gateway){
                    $gateway_object=WPBooking_Payment_Gateways::inst()->get_gateway($gateway);
                    if($gateway_object){
                        if($need) return $gateway_object->get_info($need); else return $gateway_object;
                    }else{
                        return $gateway;
                    }
                }else{
                    return esc_html__('Unknow','wpbooking');
                }

            }
        }

    }
}