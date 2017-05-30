<?php
/**
 * @since 1.0.0
 **/

if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}
if(!class_exists( 'WPBooking_Order_Hotel_Order_Model' )) {
    class WPBooking_Order_Hotel_Order_Model extends WPBooking_Model
    {
        static $_inst = false;
        public function __construct()
        {
            $this->table_version = '1.0.2';
            $this->table_name    = 'wpbooking_order_hotel_room';
            $this->columns = array(
                'id'                  => array(
                    'type'           => 'int' ,
                    'AUTO_INCREMENT' => true
                ) ,
                'order_id'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'hotel_id'            => array( 'type' => 'int' , 'length' => 11 ) ,
                'room_id'             => array( 'type' => 'int' , 'length' => 11 ) ,
                'price'               => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'price_total'         => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'number'              => array( 'type' => 'int' , 'length' => 11 ) ,
                'extra_fees'          => array( 'type' => 'text' ) ,
                'check_in_timestamp'  => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'check_out_timestamp' => array( 'type' => 'varchar' , 'length' => 255 ) ,
                'raw_data'            => array( 'type' => 'text' ) ,
            );
            parent::__construct();

        }

        /**
         * Save Order Hotel Room
         *
         * @since 1.0
         * @author quandq
         *
         * @param $data
         * @param $room_id
         * @param $order_id
         */
        function save_order_hotel_room($data, $room_id , $order_id)
        {
            $data_room = array();
            $columns = $this->get_columns();
            if (empty($columns)) return;
            foreach ($columns as $k => $v) {
                if (in_array($k, array('id'))) continue;
                if (!empty($data[$k])){
                    $data_room[$k] = $data[$k];
                }

            }
            if (!$check_exists = $this->find_by(array('room_id'=>$room_id,'order_id'=>$order_id),false)) {
                $this->insert($data_room);
            } else {
                $this->where('room_id', $room_id)
                    ->where('order_id', $order_id)
                    ->update($data_room);
            }
        }

        /**
         * Get Data Order
         *
         * @since 1.0
         * @author quandq
         *
         * @param $order_id
         * @return array
         */
        function get_order($order_id){
            return $this->where('order_id',$order_id)->get()->result();
        }

        static function inst()
        {
            if(!self::$_inst) {
                self::$_inst = new self();
            }

            return self::$_inst;
        }

    }

    WPBooking_Order_Hotel_Order_Model::inst();
}