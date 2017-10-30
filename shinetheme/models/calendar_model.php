<?php
    /**
     * @since 1.0.0
     **/

    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    if ( !class_exists( 'WPBooking_Calendar_Model' ) ) {
        class WPBooking_Calendar_Model extends WPBooking_Model
        {

            static $_inst = false;


            public function __construct()
            {
                $this->table_version = '1.0.6';
                $this->table_name    = 'wpbooking_availability';
                $this->columns       = [
                    'id'               => [
                        'type'           => 'int',
                        'AUTO_INCREMENT' => true
                    ],
                    'post_id'          => [ 'type' => 'int', 'length' => 11 ],
                    'start'            => [ 'type' => 'int' ],
                    'end'              => [ 'type' => 'int' ],
                    'price'            => [ 'type' => 'varchar', 'length' => 255 ],
                    'calendar_minimum' => [ 'type' => 'int', 'length' => 4 ],
                    'calendar_maximum' => [ 'type' => 'int', 'length' => 4 ],
                    'calendar_price'   => [ 'type' => 'FLOAT', 'length' => 255 ],
                    'adult_minimum'    => [ 'type' => 'int', 'length' => 4 ],
                    'adult_price'      => [ 'type' => 'FLOAT', 'length' => 255 ],
                    'child_minimum'    => [ 'type' => 'int', 'length' => 4 ],
                    'child_price'      => [ 'type' => 'FLOAT', 'length' => 255 ],
                    'infant_minimum'   => [ 'type' => 'int', 'length' => 4 ],
                    'infant_price'     => [ 'type' => 'FLOAT', 'length' => 255 ],
                    'weekly'           => [ 'type' => 'FLOAT' ],
                    'monthly'          => [ 'type' => 'FLOAT' ],
                    'status'           => [ 'type' => 'varchar', 'length' => 255 ],
                    'base_id'          => [ 'type' => 'int', 'length' => 11 ],
                    'can_check_in'     => [ 'type' => 'int', 'length' => 4 ],
                    'can_check_out'    => [ 'type' => 'int', 'length' => 4 ],
                    'group_day'        => [ 'type' => 'varchar', 'length' => 255 ],

                ];
                parent::__construct();

            }

            function get_prices( $post_id, $from, $to )
            {
                return $this->where( 'post_id', $post_id )->where( 'start>=', $from )->where( 'start<=', $to )->get()->result();
            }

            /**
             * Get Calendar Data From Date To Date
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $post_id
             * @param $start_date string timepstamp
             * @param $end_date   string timepstamp
             *
             * @return array|bool
             */
            function calendar_months( $post_id, $start_date, $end_date )
            {
                global $wpdb;
                $today = strtotime( 'today' );
                if ( $start_date < $today ) $start_date = $today;
                $res = $this
                    ->select( [
                        $wpdb->prefix . 'wpbooking_availability.*',
                    ] )
                    ->join( 'posts', 'posts.ID=wpbooking_availability.post_id' )
                    ->where( [
                        $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                        'start>='                                        => $start_date,
                        'end<='                                          => $end_date
                    ] )
                    ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                    ->orderby( 'start', 'asc' )->get()->result();

                return $res;
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }

        }

        WPBooking_Calendar_Model::inst();
    }