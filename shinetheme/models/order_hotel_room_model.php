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
                $this->table_version = '1.0.3';
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

                if ( !$check_exists = $this->find_by( [ 'room_id' => $room_id, 'order_id' => $order_id ], false ) ) {
                    $this->insert( $data_room );
                } else {
                    $this->where( 'room_id', $room_id )
                        ->where( 'order_id', $order_id )
                        ->update( $data_room );
                }
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