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
            $this->table_version = '1.0.2';
            $this->table_name    = 'wpbooking_order';
            $this->columns = array(
                'id'                  => array(
                    'type'           => 'int' ,
                    'AUTO_INCREMENT' => true
                ) ,
                'order_id'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'post_id'             => array( 'type' => 'int' , 'length' => 11 ) ,// Service ID
                'service_type'        => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'price'               => array( 'type' => 'float' ) ,// Total Price after calculating, with tax also
                'discount'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'extra_fees'          => array( 'type' => 'text' ) ,
                'tax'                 => array( 'type' => 'text' ) ,
                'tax_total'           => array( 'type' => 'float' ) ,
                'currency'            => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'raw_data'            => array( 'type' => 'text' ) ,
                'check_in_timestamp'  => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'check_out_timestamp' => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'user_id'             => array( 'type' => 'int' , 'length' => 11 ) ,// Customer ID
                'author_id'           => array( 'type' => 'int' , 'length' => 11 ) ,// Service's Author ID
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

        /**
         * Get where of service service type
         *
         * @param $service_type
         * @return string
         */
        function service_where($service_type){
            $sv_where = '1 = 1';
            if(is_array($service_type) and count($service_type) >= 1){
                foreach($service_type as $k => $val) {
                    if($k == 0) {
                        $sv_where = '( service_type = \'' . $val.'\'';
                    }elseif($k == count($service_type - 1)){
                        $sv_where = ' OR service_type = \''.$val.'\' )';
                    }else{
                        $sv_where = ' OR service_type = \''. $val .'\'';
                    }
                    if(count($service_type) == 1){
                        $sv_where = '(service_type = \'' . $val . '\')';
                    }
                }
            }
            return $sv_where;
        }

        /**
         *Get total sale in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $start_day
         * @param $end_day
         * @return string
         */
        function get_rp_total_sale($service_type,$start_day, $end_day){

            $row = $this->select('SUM(price) as total_sale')
                ->where('created_at>=',$start_day)
                ->where('created_at<=',$end_day)
                ->where("(status='on_hold' OR status='completed')",false,true)
                ->where($this->service_where($service_type),false,true)
                ->get()->row();

            return (!empty($row['total_sale']))?$row['total_sale']:'0';
        }

        /**
         *Get total items in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $start_day
         * @param $end_day
         * @return string
         */
        function get_rp_total_items($service_type,$start_day, $end_day){
            $row = $this->select('COUNT(DISTINCT post_id) as items')
                ->where('created_at>=',$start_day)
                ->where('created_at<=',$end_day)
                ->where("(status='on_hold' OR status='completed')",false,true)
                ->where($this->service_where($service_type),false,true)
                ->get()->row();
            return (!empty($row['items']))?$row['items']:'0';
        }
        /**
         *Get total booking in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $start_day
         * @param $end_day
         * @return string
         */
        function get_rp_total_bookings($service_type,$start_day, $end_day){
            $row = $this->select('COUNT(*) as total_bookings')
                ->where('created_at>=',$start_day)
                ->where('created_at<=',$end_day)
                ->where("(status='on_hold' OR status='completed')",false,true)
                ->where($this->service_where($service_type),false,true)
                ->get()->row();
            return (!empty($row['total_bookings']))?$row['total_bookings']:'0';
        }
        /**
         *Get total net profit in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $start_day
         * @param $end_day
         * @return string
         */
        function get_rp_total_net_profit($service_type,$start_day, $end_day){
            $total_price = $this->get_rp_total_sale($service_type,$start_day, $end_day);

            $row = $this->select('SUM(tax_total) as tax_total')
                ->where('created_at>=',$start_day)
                ->where('created_at<=',$end_day)
                ->where("(status='on_hold' OR status='completed')",false,true)
                ->where($this->service_where($service_type),false,true)
                ->get()->row();

            $tax_total = (!empty($row['tax_total']))?$row['tax_total']:0;

            $net_profit = (float)$total_price - (float)$tax_total;

            return $net_profit;
        }

        /**
         *Get total items by status
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $start_day
         * @param $end_day
         * @param $status
         * @return string
         */
        function get_rp_items_by_status($service_type,$start_day, $end_day,$status){
            $row = $this->select('COUNT(*) as '.$status)
                ->where('created_at>=',$start_day)
                ->where('created_at<=',$end_day)
                ->where('status',$status)
                ->where($this->service_where($service_type),false,true)
                ->get()->row();
            return (!empty($row[$status]))?$row[$status]:'0';
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