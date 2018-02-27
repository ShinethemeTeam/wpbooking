<?php
    /**
     * @package    WordPress
     * @subpackage WPBooking
     * @since      1.0
     *
     * Class WPBooking_Input
     *
     * Created by WpBooking Team
     * @author     quandq
     */
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( !class_exists( 'WPBooking_Input' ) ) {
        class WPBooking_Input
        {
            static function ip_address()
            {

                if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
                    $ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
                } elseif ( !empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
                    $ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
                } else {
                    $ip = $_SERVER[ 'REMOTE_ADDR' ];
                }

                return apply_filters( 'stinput_ip_address', $ip );

            }

            static function post( $index = null, $default = false )
            {
                // Check if a field has been provided
                if ( $index === null AND !empty( $_POST ) ) {
                    return $_POST;
                }

                if ( isset( $_POST[ $index ] ) ) return $_POST[ $index ];

                return $default;

            }

            static function get( $index = null, $default = false )
            {
                // Check if a field has been provided
                if ( $index === null AND !empty( $_GET ) ) {
                    return $_GET;
                }

                if ( isset( $_GET[ $index ] ) ) return $_GET[ $index ];

                return $default;
            }

            static function request( $index = null, $default = false )
            {
                // Check if a field has been provided
                if ( $index === null AND !empty( $_REQUEST ) ) {
                    return $_REQUEST;
                }

                if ( isset( $_REQUEST[ $index ] ) ) return $_REQUEST[ $index ];

                return $default;
            }

        }
    }
