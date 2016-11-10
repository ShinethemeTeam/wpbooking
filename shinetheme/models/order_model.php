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

        /**
         * Save Order
         *
         * @since 1.0
         * @author quandq
         *
         * @param $cart
         * @param $order_id
         * @param $customer_id
         */
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
        function complete_purchase($order_id,$status='completed')
        {
            $this->where( 'order_id' , $order_id )->update( array(
                'status' => $status
            ) );
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

        /**
         * Update status for order
         *
         * @since 1.0
         * @author tienhd
         *
         * @param $order_id
         * @param $status
         */
        function update_status($order_id, $status){
            $this->where('order_id', $order_id)->update(array('status' => $status));
            wp_update_post(array('ID' => $order_id, 'post_status' => $status));
        }

        /**
         * Delete permanently order
         *
         * @since 1.0
         * @author tienhd
         *
         * @param $order_id
         */
        function delete_order($order_id){
            $this->where('order_id', $order_id)->delete();
            $order_room = WPBooking_Order_Hotel_Order_Model::inst();
            $order_room->where('order_id', $order_id)->delete();
            wp_delete_post($order_id);
        }

        /**
         * Get Table Name with Prefix
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $prefix
         * @return string
         */
        function get_table_name($prefix=true)
        {
            global $wpdb;
            if($prefix)
                return $table_name = $wpdb->prefix . $this->table_name;
            else
                return $this->table_name;
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