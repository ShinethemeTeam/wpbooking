<?php
    if ( !class_exists( 'wpbooking_shortcode_controller' ) ) {
        class wpbooking_shortcode_controller
        {

            static $_inst;

            function __construct()
            {
                add_action( 'init', [ $this, 'register_shortcode_list_service' ] );
                add_action( 'init', [ $this, 'register_shortcode_form_search' ] );
                add_action( 'init', [ $this, 'register_shortcode_list_room' ] );
            }

            function register_shortcode_list_service()
            {
                add_shortcode( 'wpbooking_list_services', [ $this, '_render_list_service_shortcode' ] );
            }

            function _render_list_service_shortcode( $atts )
            {
                $atts = shortcode_atts(
                    [
                        'service_type'    => '',
                        'number_per_page' => '6',
                        'order'           => 'DESC',
                        'orderby'         => 'date',
                        'layout'          => 'grid',
                        'post_per_row'    => '2',
                        'location_id'     => '',
                        'service_id'      => '',
                    ], $atts
                );

                return wpbooking_load_view( 'shortcode/services/services', [ 'atts' => $atts ] );
            }

            function register_shortcode_form_search()
            {
                add_shortcode( 'wpbooking_search_form', [ $this, '_render_search_form_shortcode' ] );
            }

            function _render_search_form_shortcode( $atts )
            {
                $atts = shortcode_atts( [
                    'id' => '',
                ], $atts );

                return wpbooking_load_view( 'shortcode/form-search/form-search', [
                    'atts' => $atts,
                ] );
            }

            function register_shortcode_list_room()
            {
                add_shortcode( 'wpbooking_list_rooms', [ $this, '_render_list_room_shortcode' ] );
            }

            function _render_list_room_shortcode( $atts )
            {
                $atts = shortcode_atts( [
                    'layout'          => 'grid',
                    'orderby'         => 'date',
                    'order'           => 'desc',
                    'number_per_page' => '6',
                    'post_per_row'    => '2',
                    'hotel_id'        => '',
                ], $atts );

                return wpbooking_load_view( 'shortcode/rooms/room', [
                    'atts' => $atts
                ] );
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        wpbooking_shortcode_controller::inst();
    }