<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( !class_exists( 'WPBooking_Admin_Setting' ) ) {
        class WPBooking_Admin_Setting extends WPBooking_Controller
        {
            private static $_inst;

            function __construct()
            {
                WPBookingConfig()->load( 'settings' );

                add_action( 'admin_menu', [ $this, "register_wpbooking_sub_menu_page" ] );

                add_action( 'admin_init', [ $this, "_save_settings" ] );
                add_action( 'admin_init', [ $this, "_sync_settings_wpml" ] );

                // add script and style
                add_action( 'admin_enqueue_scripts', [ $this, "_add_scripts" ] );

            }

            function _add_scripts()
            {
                wp_enqueue_script( 'jquery-ui-tabs' );
                wp_enqueue_media();
            }

            public function _sync_settings_wpml()
            {
                if ( wpbooking_is_wpml() && !get_option( 'wpbooking_sync_wettings_wpml', false ) ) {
                    global $wpdb;
                    $sql     = "select * from {$wpdb->options} where option_name LIKE 'wpbooking_%'";
                    $results = $wpdb->get_results( $sql );
                    if ( !empty( $results ) ) {
                        $langs = wpbooking_all_langs();
                        foreach ( $langs as $lang ) {
                            foreach ( $results as $row ) {
                                $name = str_replace( 'wpbooking_', 'wpbooking_' . $lang . '_', $row->option_name );
                                update_option( $name, $row->option_value, false );
                            }
                        }
                        update_option( 'wpbooking_sync_wettings_wpml', 'updated' );
                    }
                }
            }

            /**
             * Ajax create new extra service item for
             *
             * @since  1.0
             * @author dungdt
             */
            function _ajax_add_extra_service()
            {
                $res = [
                    'status' => 0
                ];
                if ( current_user_can( 'manage_options' ) ) {
                    $service_type = WPBooking_Input::post( 'service_type' );
                    $service_name = WPBooking_Input::post( 'service_name' );
                    if ( $service_type and $service_name ) {
                        $option_key = 'wpbooking_service_type_' . $service_type . '_extra_services';
                        $option     = get_option( $option_key );

                        // check service name exsits
                        $is_exists = false;
                        if ( is_array( $option ) and !empty( $option ) ) {
                            foreach ( $option as $value ) {
                                if ( $value[ 'title' ] == $service_name ) $is_exists = true;
                            }
                        }

                        if ( !$is_exists ) {
                            $option[] = [
                                'title' => $service_name
                            ];

                            update_option( $option_key, $option );
                            $res[ 'status' ] = 1;
                        } else {
                            $res[ 'message' ] = esc_html__( 'Service Exists. Please choose another name', 'wp-booking-management-system' );
                        }
                    }
                }
                echo json_encode( $res );
                die;
            }

            /*---------Begin Helper Functions----------------*/
            function get_option( $option_id, $default = false )
            {
                $current_lang = wpbooking_current_lang();
                if ( $current_lang ) {
                    $current_lang = $current_lang . '_';
                }
                /* get the saved options */
                $options = get_option( 'wpbooking_' . $current_lang . $option_id );
                $options = maybe_unserialize($options);
                /* look for the saved value */
                if ( isset( $options ) && '' != $options ) {
                    return $options;
                }

                return $default;
            }

            function register_wpbooking_sub_menu_page()
            {

                $menu_page = $this->get_menu_page();
                add_submenu_page(
                    $menu_page[ 'parent_slug' ],
                    $menu_page[ 'page_title' ],
                    $menu_page[ 'menu_title' ],
                    $menu_page[ 'capability' ],
                    $menu_page[ 'menu_slug' ],
                    $menu_page[ 'function' ]
                );
            }

            function get_menu_page()
            {

                $menu_page = WPBooking()->get_menu_page();
                $page      = [
                    'parent_slug' => $menu_page[ 'menu_slug' ],
                    'page_title'  => esc_html__( 'Settings', 'wp-booking-management-system' ),
                    'menu_title'  => esc_html__( 'Settings', 'wp-booking-management-system' ),
                    'capability'  => 'manage_options',
                    'menu_slug'   => 'wpbooking_page_settings',
                    'function'    => [ $this, 'callback_wpbooking_sub_menu' ]
                ];

                return apply_filters( 'wpbooking_setting_menu_args', $page );

            }

            function callback_wpbooking_sub_menu()
            {
                echo( $this->admin_load_view( 'settings' ) );
            }

            function _save_settings()
            {
                if ( !empty( $_POST[ 'wpbooking_save_settings' ] ) and wp_verify_nonce( $_REQUEST[ 'wpbooking_save_settings_field' ], "wpbooking_action" ) ) {
                    $full_settings = $this->_get_settings();

                    if ( !empty( $full_settings ) ) {
                        $is_tab     = WPBooking_Input::request( 'st_tab' );
                        $is_section = WPBooking_Input::request( 'st_section' );
                        if ( empty( $is_tab ) and !empty( $full_settings ) ) {
                            $tmp_tab = $full_settings;
                            $tmp_key = array_keys( $tmp_tab );
                            $is_tab  = array_shift( $tmp_key );
                        }
                        if ( empty( $is_section ) and !empty( $full_settings[ $is_tab ][ 'sections' ] ) ) {
                            $tmp_section = $full_settings[ $is_tab ][ 'sections' ];
                            $tmp_key     = array_keys( $tmp_section );
                            $is_section  = array_shift( $tmp_key );
                        }
                        $custom_settings = $full_settings[ $is_tab ][ 'sections' ][ $is_section ][ 'fields' ];

                        $current_lang = wpbooking_current_lang();
                        if ( $current_lang ) {
                            $current_lang = $current_lang . '_';
                        }
                        foreach ( $custom_settings as $key => $value ) {
                            switch ( $value[ 'type' ] ) {
                                case "multi-checkbox":
                                    $custom_multi_checkbox = $value[ 'value' ];
                                    foreach ( $custom_multi_checkbox as $key_multi => $value_multi ) {
                                        $key_request   = 'wpbooking_' . $value_multi[ 'id' ];
                                        $value_request = WPBooking_Input::request( $key_request );
                                        $key_request   = 'wpbooking_' . $current_lang . $value_multi[ 'id' ];
                                        update_option( $key_request, $value_request );
                                    }
                                    break;
                                case "list-item":
                                    $id_list_item  = $value[ 'id' ];
                                    $data          = [];
                                    $key_request   = 'wpbooking_list_item';
                                    $value_request = WPBooking_Input::request( $key_request );
                                    if ( !empty( $value_request[ $id_list_item ] ) ) {
                                        $data = $value_request[ $id_list_item ];
                                    }
                                    unset( $data[ '__number_list__' ] );
                                    $id_save = 'wpbooking_' . $current_lang . $value[ 'id' ];
                                    update_option( $id_save, $data );
                                    break;
                                case "checkbox":
                                    if ( isset( $value[ 'id' ] ) ) {
                                        $key_request   = 'wpbooking_' . $value[ 'id' ];
                                        $value_request = WPBooking_Input::post( $key_request );
                                        $key_request   = 'wpbooking_' . $current_lang . $value[ 'id' ];
                                        if ( !$value_request )
                                            delete_option( $key_request );
                                        else
                                            update_option( $key_request, $value_request );
                                    }
                                    break;
                                default:
                                    if ( isset( $value[ 'id' ] ) ) {
                                        $key_request   = 'wpbooking_' . $value[ 'id' ];
                                        $value_request = WPBooking_Input::post( $key_request );
                                        $key_request   = 'wpbooking_' . $current_lang . $value[ 'id' ];
                                        update_option( $key_request, $value_request );
                                    }
                                    break;
                            }

                        }
                    }

                    do_action( 'wpbooking_after_admin_settings_saved', $full_settings );
                }
            }

            function _get_settings()
            {
                $custom_settings = WPBooking_Config::inst()->item( 'settings' );
                $custom_settings = apply_filters( 'wpbooking_settings', $custom_settings );

                return $custom_settings;
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }


        }

        WPBooking_Admin_Setting::inst();
    }