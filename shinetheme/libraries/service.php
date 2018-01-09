<?php
if (!class_exists('WB_Service_Helper')) {

    class WB_Service_Helper
    {
        /**
         * @param $cart_item
         * @param array $default
         * @return int|mixed|void
         */
        static function calculate_extra_price($cart_item, $default = array())
        {

            $extra_services = $cart_item['extra_services'];

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

            $extra_service_price = apply_filters('wpbooking_service_calculate_extra_price', $extra_service_price, $cart_item);

            return $extra_service_price;
        }

        /**
         * Calculate Discount of Order or Cart Item
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @param $total_price
         * @return float|int
         */
        static function calculate_discount($cart_item, $total_price)
        {

            $cart_item = wp_parse_args($cart_item, array(
                'coupon_data' => array(),
                'coupon_code' => false
            ));
            $coupon_data = wp_parse_args($cart_item['coupon_data'], array(
                'coupon_type'       => '',
                'service_ids'       => '',
                'coupon_value'      => '',
                'coupon_value_type' => '',
            ));
            $price = 0;
            if (!empty($cart_item['coupon_code'])) {

                $possible = false;

                if ($coupon_data['coupon_type'] == 'specific_services') {
                    $services = $coupon_data['service_ids'];
                    if (!empty($services) and in_array($cart_item['post_id'], $services)) {
                        $possible = true;
                    }
                } else {
                    $possible = true;
                }

                if ($possible and $coupon_value = $coupon_data['coupon_value']) {
                    switch ($coupon_data['coupon_value_type']) {
                        case "percentage":

                            if ($coupon_value > 100) $coupon_value = 100;
                            if ($coupon_value < 0) $coupon_value = 0;

                            $price = $total_price * $coupon_value / 100;
                            break;
                        case "fixed_amount":
                        default:
                            $price = $coupon_value;
                            break;
                    }
                }
            }

            return $price;
        }

        static function calculate_deposit($cart_item=array(),$price){

            /**
             * Calculate Deposit
             */
            if (!empty($cart_item['deposit_amount'])) {

                switch ($cart_item['deposit_type']) {
                    case "percent":
                        if ($cart_item['deposit_amount'] > 100) $args['deposit_amount'] = 100;
                        $price = $price * $cart_item['deposit_amount'] / 100;
                        break;
                    case "value":
                    default:
                        if ($cart_item['deposit_amount'] < $price)
                            $price = $cart_item['deposit_amount'];
                        break;

                }
            }

            return $price;
        }

    }

}