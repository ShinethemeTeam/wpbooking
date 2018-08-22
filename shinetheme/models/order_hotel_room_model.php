<?php
    /**
     * @since 1.0.0
     **/

    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    if ( !class_exists( 'WPBooking_Order_Hotel_Order_Model' ) ) {
        class WPBooking_Order_Hotel_Order_Model extends WPBooking_Model
        {
            static $_inst = false;

            public function __construct()
            {

                add_action('wpbooking_order_item_changed', array($this, 'add_status_order_hotel_room'), 10, 2);

                $this->table_version = '1.3';
                $this->table_name    = 'wpbooking_order_hotel_room';
                $this->columns       = [
                    'id'                  => [
                        'type'           => 'int',
                        'AUTO_INCREMENT' => true
                    ],
                    'order_id'            => [ 'type' => 'int', 'length' => 11 ],
                    'hotel_id'            => [ 'type' => 'int', 'length' => 11 ],
                    'hotel_id_origin'     => [ 'type' => 'int', 'length' => 11 ],
                    'room_id'             => [ 'type' => 'int', 'length' => 11 ],
                    'room_id_origin'      => [ 'type' => 'int', 'length' => 11 ],
                    'price'               => [ 'type' => 'varchar', 'length' => 255 ],
                    'price_total'         => [ 'type' => 'varchar', 'length' => 255 ],
                    'number'              => [ 'type' => 'int', 'length' => 11 ],
                    'extra_fees'          => [ 'type' => 'text' ],
                    'check_in_timestamp'  => [ 'type' => 'varchar', 'length' => 255 ],
                    'check_out_timestamp' => [ 'type' => 'varchar', 'length' => 255 ],
                    'raw_data'            => [ 'type' => 'text' ],
                    'status'              => [ 'type' => 'varchar', 'length' => 255 ],
                    'num_room'            => [ 'type' => 'int', 'length' => 11 ],
                    'total_room'          => [ 'type' => 'int', 'length' => 11 ],
                ];
                parent::__construct();

                $updated_1_7 = get_option( 'wpbooking_update_1_0_7_hotel_room_order', '' );
                if ( !$updated_1_7 ) {
                    $this->updated_post_origin_order();
                }


            }

            private function updated_post_origin_order()
            {
                global $wpdb;
                $table = $wpdb->prefix . $this->table_name;
                $sql   = "UPDATE {$table} SET hotel_id_origin = hotel_id, room_id_origin = room_id";
                $wpdb->query( $sql );

                update_option( 'wpbooking_update_1_0_7_hotel_room_order', 'updated' );
            }

            /**
             * Save Order Hotel Room
             *
             * @since  1.0
             * @author quandq
             *
             * @param $data
             * @param $room_id
             * @param $order_id
             */
            function save_order_hotel_room( $data, $room_id, $order_id )
            {
                $data_room = [];
                $columns   = $this->get_columns();
                if ( empty( $columns ) ) return;
                foreach ( $columns as $k => $v ) {
                    if ( in_array( $k, [ 'id' ] ) ) continue;
                    if ( !empty( $data[ $k ] ) ) {
                        $data_room[ $k ] = $data[ $k ];
                    }

                }
                $hotel_id                       = $data[ 'hotel_id' ];
                $hotel_id_origin                = wpbooking_origin_id( $hotel_id, get_post_type( $hotel_id ) );
                $data_room[ 'hotel_id_origin' ] = $hotel_id_origin;

                $room_id                       = $data[ 'room_id' ];
                $room_id_origin                = wpbooking_origin_id( $room_id, get_post_type( $room_id ) );
                $data_room[ 'room_id_origin' ] = $room_id_origin;
                $data_room[ 'status' ]      = get_post_field( 'post_status', $order_id );

                if ( !$check_exists = $this->find_by( [ 'room_id' => $room_id, 'order_id' => $order_id ], false ) ) {
                    $this->insert( $data_room );
                } else {
                    $this->where( 'room_id', $room_id )
                        ->where( 'order_id', $order_id )
                        ->update( $data_room );
                }

                $this->update_room_total_num($hotel_id,$room_id);
            }

            function update_room_total_num($hotel_id,$room_id=false){

                global $wpdb;
                $query = new WP_Query( [
                    'post_parent'    => $hotel_id,
                    'posts_per_page' => -1,
                    'post_type'      => 'wpbooking_hotel_room',
                    'post_status'    => [ 'pending', 'future', 'publish' ],
                ] );
                $total_room = 0;
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $room_id = get_the_ID();
                    $num_room = (int)get_post_meta( get_the_ID(), 'room_number', true );
                    $sql = "UPDATE {$wpdb->prefix}$this->table_name SET num_room = '".$num_room."' WHERE room_id_origin = $room_id";
                    $wpdb->query($sql);
                    $total_room += 1;
                }
                $sql = "UPDATE {$wpdb->prefix}$this->table_name SET total_room = '".$total_room."' WHERE hotel_id_origin = $hotel_id";
                $wpdb->query($sql);


            }


            function complete_purchase( $order_id, $status = 'completed' )
            {
                $this->where( 'order_id', $order_id )->update( [
                    'status' => $status
                ] );
            }

            /**
             * Get Data Order
             *
             * @since  1.0
             * @author quandq
             *
             * @param $order_id
             *
             * @return array
             */
            function get_order( $order_id )
            {
                return $this->where( 'order_id', $order_id )->get()->result();
            }

            function add_status_order_hotel_room($order_id,$status){
                switch($status){
                    case 'onhold_booking':
                        $status = 'on_hold';
                        break;
                    case 'complete_booking':
                        $status = 'completed';
                        break;
                    case 'cancel_booking':
                        $status = 'cancelled';
                        break;
                    case 'refunded_booking':
                        $status = 'refunded';
                        break;
                    case 'cancel':
                        $status = 'cancel';
                        break;
                    case 'permanently_delete':
                        $status = 'cancel';
                        break;
                }
                global $wpdb;
                $sql = "UPDATE {$wpdb->prefix}wpbooking_order_hotel_room SET status = '".$status."' WHERE order_id = $order_id ";
                $wpdb->query($sql);
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }

        }

        WPBooking_Order_Hotel_Order_Model::inst();
    }