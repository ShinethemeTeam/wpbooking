<?php
/**
 * @since 1.0.0
 **/

if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}
if(!class_exists( 'WPBooking_Order_Model' )) {
    class WPBooking_Order_Model extends WPBooking_Model
    {

        static $_inst = false;


        public function __construct()
        {
            $this->table_version = '1.0.0';
            $this->table_name    = 'wpbooking_order';
            $this->columns = array(
                'id'                  => array(
                    'type'           => 'int' ,
                    'AUTO_INCREMENT' => true
                ) ,
                'order_id'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'post_id'             => array( 'type' => 'int' , 'length' => 11 ) ,
                'service_type'        => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'price'               => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'discount'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'extra_fees'          => array( 'type' => 'text' ) ,
                'tax'                 => array( 'type' => 'text' ) ,
                'currency'            => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'raw_data'            => array( 'type' => 'text' ) ,
                'check_in_timestamp'  => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'check_out_timestamp' => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'user_id'             => array( 'type' => 'int' , 'length' => 11 ) ,
                'author_id'           => array( 'type' => 'int' , 'length' => 11 ) ,
                'deposit'             => array( 'type' => 'text' ) ,
                'deposit_price'             => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'created_at'          => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'payment_method'      => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'status'              => array( 'type' => 'varchar' , 'length' => 255 ) ,

            );
            parent::__construct();

        }

        function save_order($cart, $order_id ,  $customer_id)
        {
            $columns = $this->get_columns();
            if (empty($columns)) return;

            foreach ($columns as $k => $v) {
                if (in_array($k, array('id', 'post_id'))) continue;
                $value = get_post_meta($order_id, $k, TRUE);
                if(!empty($value) and is_array($value)){
                    $value = serialize($value);
                }
                $data[$k] = $value;

            }
            $post_id = $cart['post_id'];
            $data['order_id'] = $order_id;
            $data['status'] = get_post_field( 'post_status', $order_id );
            if (!$check_exists = $this->find_by('order_id', $order_id)) {
                $data['post_id'] = $post_id;
                $this->insert($data);
            } else {
                $this->where('order_id', $order_id)->update($data);
            }
        }

        /**
         * Update Payment Status of Items by Order ID
         *
         * @param $order_id
         * @since 1.0
         */
        function complete_purchase($order_id)
        {
            $this->where('order_id', $order_id)->update(array('status' => 'completed', 'status' => 'completed'));
        }


        /**
         * Update Status of Order Item to Cancelled by Admin or Customer
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_id
         */
        function cancel_purchase($order_id)
        {
            $this->where('order_id', $order_id)->update(array('status' => 'completed', 'status' => 'cancelled'));
        }


        function get_calendar_booked($service_id, $checkin_timestamp = FALSE, $checkout_timestamp = FALSE)
        {
            global $wpdb;
            $res = $this
                ->select(array(
                    $wpdb->prefix . 'wpbooking_order_item.*'
                ))
                ->join('wpbooking_service', 'wpbooking_service.post_id=wpbooking_order_item.post_id')
                ->where($wpdb->prefix . 'wpbooking_order_item.post_id', $service_id)
                ->where($wpdb->prefix . "wpbooking_order_item.status not in ('refunded','cancelled','trash')", FALSE, true)
                ->where(
                    $wpdb->prepare(
                        "
					(
						({$wpdb->prefix}wpbooking_order_item.check_in_timestamp<=%d and {$wpdb->prefix}wpbooking_order_item.check_out_timestamp>=%d)
						OR ({$wpdb->prefix}wpbooking_order_item.check_in_timestamp>=%d and {$wpdb->prefix}wpbooking_order_item.check_in_timestamp<=%d)
					)
				", $checkin_timestamp, $checkin_timestamp, $checkin_timestamp, $checkout_timestamp), FALSE, TRUE)
                ->get()->result();

            return $res;
        }


        static function inst()
        {
            if(!self::$_inst) {
                self::$_inst = new self();
            }

            return self::$_inst;
        }

    }

    WPBooking_Order_Model::inst();
}