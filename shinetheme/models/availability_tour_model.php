<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 4/9/2018
     * Time: 11:29 AM
     * Since: 1.0.0
     * Updated: 1.0.0
     */
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    if ( !class_exists( 'WPBooking_Calendar_Tour_Model' ) ) {
        class WPBooking_Calendar_Tour_Model extends WPBooking_Model
        {
            public function __construct()
            {
                $this->table_version = '1.3';
                $this->table_name    = 'wpbooking_availability_tour';
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
                    'max_people'        => [ 'type' => 'int', 'length' => 5 ],
                ];
                parent::__construct();
            }

            public static function get_inst()
            {
                static $instance;
                if ( is_null( $instance ) ) {
                    $instance = new self();
                }

                return $instance;
            }
        }

        WPBooking_Calendar_Tour_Model::get_inst();
    }