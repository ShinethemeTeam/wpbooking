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
        private $customer_id = FALSE;

        function __construct($order_id)
        {

            $this->init($order_id);
        }

        private function init($order_id)
        {
            if (!$order_id) return;

            $this->order_id = $order_id;
            $this->customer_id = get_post_meta($this->order_id, 'customer_id', true);
        }

        function get_order_id()
        {
            return $this->order_id;
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
                    'id'          => $this->customer_id,
                    'name'        => $udata->display_name,
                    'avatar'      => get_avatar($this->customer_id),
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
                if ($this->customer_id) return $this->get_customer('email');

                // Try to get user email field
                return get_post_meta($this->order_id, 'wpbooking_form_user_email', true);
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
            if ($this->order_id) {
                $booking = WPBooking_Order::inst();

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
            if ($this->order_id) {
                return get_post_meta($this->order_id, 'checkout_form_data', TRUE);
            }

        }

        /**
         * Get Order Total Money
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $args array Options to change filter
         * @return int|mixed|void
         */
        function get_total($args = array())
        {
            $total = 0;
            if ($this->order_id) {
                $order_items = $this->get_items();

                foreach ($order_items as $key => $value) {
                    $total += $this->get_item_total($value, TRUE, $args);
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
         * @param $args array
         * @return float|mixed|void
         */
        function get_item_total($item, $need_convert = FALSE,$args=array())
        {
            $item_price = $item['sub_total'];
            $item_price = apply_filters('wpbooking_order_item_total', $item_price, $item, $item['service_type'],$args,$this);
            $item_price = apply_filters('wpbooking_order_item_total_' . $item['service_type'], $item_price, $item,$args,$this);

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
        function get_item_total_html($item)
        {

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
                'post_status' => 'on-hold'
            );
            $order_id = wp_insert_post($order_data);

            // Save Current Data
            $this->init($order_id);

            if ($order_id) {
                update_post_meta($order_id, 'checkout_form_data', $checkout_form_data);
                update_post_meta($order_id, 'wpbooking_selected_gateway', $selected_gateway);
                update_post_meta($order_id, 'customer_id', $customer_id);

                /**
                 * Save Coupon Code Data
                 *
                 * @since 1.0
                 * @author dungdt
                 *
                 */
                $coupon = new WB_Coupon(WPBooking_Order::inst()->get_cart_coupon());
                update_post_meta($order_id, 'coupon_code', WPBooking_Order::inst()->get_cart_coupon());
                update_post_meta($order_id, 'coupon_data', $coupon->get_full_data());

                //User Fields in case of customer dont want to create new account
                $f = array('user_email', 'user_first_name', 'user_last_name');
                foreach ($f as $v) {
                    if (array_key_exists($v, $checkout_form_data))
                        update_post_meta($order_id, $v, $checkout_form_data[$v]['value']);
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
                    'post_status'=>'payment-failed'
                ));

                // Update Status of Order Item in database
                $order_model = WPBooking_Order_Model::inst();
                $order_model->where('order_id', $this->order_id)->update(array(
                    'payment_status' => 'failed'
                ));
            }
        }

        /**
         * Get Used Coupon Code
         *
         * @since 1.0
         * @author dungdt
         *
         * @return int
         */
        function get_coupon_code()
        {
            if ($this->order_id) {
                return get_post_meta($this->order_id, 'coupon_code', true);
            }
        }

        /**
         * Get Used Coupon Full Data
         *
         * @since 1.0
         * @author dungdt
         *
         * @return array
         */
        function get_coupon_data()
        {
            $data = array();

            if ($this->order_id) {
                $data = get_post_meta($this->order_id, 'coupon_data', true);
            }

            return wp_parse_args($data, array(
                'coupon_type'          => false,
                'services_ids'         => false,
                'coupon_value'         => false,
                'coupon_value_type'    => false,
                'start_date_timestamp' => false,
                'end_date_timestamp'   => false,
                'minimum_spend'        => false,
                'usage_limit'          => false
            ));
        }


        /**
         * Get Order Extra Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @return double
         */
        function get_extra_price()
        {
            $price = 0;
            $cart = $this->get_items();
            if (!empty($cart)) {
                foreach ($cart as $key => $value) {
                    $price += $this->get_item_extra_price($value);
                }
            }

            $price = apply_filters('wpbooking_get_order_extra_price', $price, $cart);

            return $price;
        }

        /**
         * Get Order Item Extra Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @return int
         */
        function get_item_extra_price($cart_item)
        {
            if ($cart_item['raw_data'] and $cart_item = unserialize($cart_item['raw_data'])) {

                $service = new WB_Service($cart_item['post_id']);
                $extra_services = $cart_item['extra_services'];
                $default = $service->get_extra_services();
                $extra_service_price = 0;
                if (!empty($default)) {
                    foreach ($default as $key => $value) {
                        if (!$value['money']) continue; // Ignore Money is empty
                        // Check is required?
                        if ($value['require'] == 'yes') {
                            // Default Number is 1
                            $number = !empty($extra_services[$key]['number']) ? $extra_services[$key]['number'] : 1;
                            $extra_service_price += ($number * $value['money']);

                        } elseif ($extra_services and array_key_exists($key, $extra_services) and $extra_services[$key]['number'] and !empty($extra_services[$key]['selected'])) {
                            // If not required, check if user select it
                            $number = $extra_services[$key]['number'];
                            $extra_service_price += ($number * $value['money']);
                        }

                    }
                }

                $extra_service_price = apply_filters('wpbooking_get_order_item_extra_price', $extra_service_price, $cart_item, $service);

                return $extra_service_price;

            }
        }

        /**
         * Get Addition People Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @return double
         */
        function get_addition_price()
        {
            $price = 0;
            $cart = $this->get_items();
            if (!empty($cart)) {
                foreach ($cart as $key => $value) {
                    if ($value['raw_data'] and $value = unserialize($value['raw_data'])) {
                        $price += $this->get_item_addition($value);
                    }
                }
            }

            $price = apply_filters('wpbooking_get_cart_addition_price', $price, $cart);

            return $price;
        }

        /**
         * Get Order Item Addition People Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @return double
         */
        function get_item_addition($cart_item)
        {

            $cart_item = wp_parse_args($cart_item, array(
                'check_in_timestamp'  => false,
                'check_out_timestamp' => false,
            ));
            $days = 0;
            if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {

                $days = wpbooking_timestamp_diff_day($cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
                if (!$days) $days = 1;

            }
            $price = 0;

            /**
             * Calculate Additional Guest
             */
            if ($cart_item['enable_additional_guest_tax'] == 'on') {

                //Additional Guest
                if ($cart_item['guest'] and $cart_item['rate_based_on'] and $addition_money = $cart_item['additional_guest_money'] and $days) {
                    $addition = ($cart_item['guest'] - $cart_item['rate_based_on']) * $addition_money * $days;

                    if ($addition > 0) $price += $addition;
                }

            }

            return $price;
        }

        /**
         * Get Discount Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @return double
         */
        function get_discount_price()
        {
            $cart = $this->get_items();

            $price = 0;
            // Check if Coupon is for all services, only discount for cart
            if ($coupon_code = $this->get_coupon_code()) {

                if (!empty($cart)) {
                    foreach ($cart as $key => $value) {
                        $price += $this->get_item_discount($value);
                    }
                }
            }


            $price = apply_filters('wpbooking_get_cart_discount_price', $price, $cart);

            return $price;
        }


        /**
         * Get Cart Item Discount Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @param $cart_item_price
         * @return float|int|mixed
         */
        function get_item_discount($cart_item, $cart_item_price = null)
        {
            $price = 0;
            if ($coupon_code = $this->get_coupon_code()) {
                $coupon_data = $this->get_coupon_data();
                $cart_item['coupon_code']=$coupon_code;
                $cart_item['coupon_data']=$coupon_data;

                $price=WB_Service_Helper::calculate_discount($cart_item,$cart_item_price);
            }

            return $price;
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
            $cart = $this->get_items();
            if (!empty($cart)) {
                foreach ($cart as $key => $value) {
                    $price += $this->get_item_tax($value);
                }
            }

            $price = apply_filters('wpbooking_get_order_tax_price', $price, $cart);

            return $price;
        }


        /**
         * Get Item Tax Price
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @return int|mixed|void
         */
        function get_item_tax($cart_item)
        {
            $cart_item = wp_parse_args($cart_item, array(
                'enable_additional_guest_tax' => false
            ));
            $price = 0;

            /**
             * Calculate Additional Guest and Tax
             */
            if ($cart_item['raw_data'] and $cart_data = unserialize($cart_item['raw_data'])) {
                if ($cart_data['enable_additional_guest_tax'] == 'on') {

                    $item_price = $this->get_item_total($cart_item, true, array('without_tax' => true, 'without_deposit' => true,'without_discount'=>true));
                    // Tax
                    if ($tax = $cart_data['tax'])
                        $price = $item_price * ($tax / 100);
                }
            }

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
                $all_status=WPBooking_Config::inst()->item('order_item_status');
                if(array_key_exists($status,$all_status)){
                    switch($status){
                        case "on-hold":
                        case "payment-failed":
                            return sprintf('<label class="label label-warning">%s</label>',$all_status[$status]['label']);
                            break;
                        case "completed":
                            return sprintf('<label class="label label-success">%s</label>',$all_status[$status]['label']);
                            break;
                        case "cancelled":
                        case "refunded":
                            return sprintf('<label class="label label-danger">%s</label>',$all_status[$status]['label']);
                            break;

                        default:
                            return sprintf('<label class="label label-default">%s</label>',$all_status[$status]['label']);
                            break;
                    }
                }else{
                    return sprintf('<label class="label label-default">%s</label>',esc_html__('Unknown','wpbooking'));
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
                $gateway=get_post_meta($this->order_id,'wpbooking_selected_gateway',true);

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