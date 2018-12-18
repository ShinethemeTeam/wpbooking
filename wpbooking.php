<?php
    /**
     * Plugin Name: WPBooking
     * Plugin URI: https://wpbooking.org
     * Description: WP Booking helps you to setup an hotel booking, tour booking , marketplace booking system like booking.com, viator.com ... quickly, friendly, pleasantly and easily.
     * Version: 2.0.3
     * Author: wpbooking
     * Author URI: https://wpbooking.org
     * Requires at least: 4.9.8
     * Tested up to: 4.9.8
     * License URI: https://www.gnu.org/licenses/gpl-2.0.html
     * Text Domain: wp-booking-management-system
     * Domain Path: /languages/
     *
     * @package wpbooking
     * @author  shinetheme
     * @since   1.0
     */

    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    if ( !class_exists( 'WPBooking_System' ) and !function_exists( 'WPBooking' ) ) {
        class WPBooking_System
        {
            static $_inst = false;
            private $_version = '2.0.3';

            /**
             * Get and Access Global Variable
             * @var array
             */
            protected $global_values = [];
            protected $_dir_path = false;
            protected $_dir_url = false;
            public $API_URL = 'https://wpbooking.org/wp-admin/admin-ajax.php';

            /**
             * @since 1.0
             */
            function __construct()
            {
                do_action( 'wpbooking_before_plugin_init' );

                $this->_dir_path = plugin_dir_path( __FILE__ );
                $this->_dir_url  = plugin_dir_url( __FILE__ );

                add_action( 'plugins_loaded', [ $this, '_init' ] );
                add_action( 'admin_menu', [ $this, '_admin_init_menu_page' ] );
                add_action( 'after_setup_theme', [ $this, '_load_cores' ] );

                add_action( 'admin_enqueue_scripts', [ $this, '_admin_default_scripts' ] );
                add_action( 'wp_enqueue_scripts', [ $this, '_frontend_scripts' ] );

                do_action( 'wpbooking_after_plugin_init' );
            }

            function _frontend_scripts()
            {
                /**
                 * Css
                 */
                wp_enqueue_style( 'wpbooking-font-awesome', wpbooking_assets_url( 'fa4.5/css/font-awesome.min.css' ), false, '4.5.0' );
                wp_enqueue_style( 'wpbooking-fotorama', wpbooking_assets_url( 'fotorama4.6.4/fotorama.css' ) );
                /**
                 * WPBooking Icon
                 */
                wp_enqueue_style( 'wpbooking-icon', wpbooking_assets_url( 'my-icons-collection/font/flaticon.css' ) );
                /**
                 * Magnific
                 */
                wp_register_script( 'wpbooking-magnific', wpbooking_assets_url( 'magnific/jquery.magnific-popup.min.js' ), [ 'jquery' ], null, true );
                wp_register_style( 'wpbooking-magnific', wpbooking_assets_url( 'magnific/magnific-popup.css' ) );

                /**
                 * Fotorama
                 */
                wp_register_script( 'wpbooking-fotorama', wpbooking_assets_url( 'fotorama4.6.4/fotorama.js' ), [ 'jquery' ], null, true );
                wp_register_style( 'wpbooking-fotorama', wpbooking_assets_url( 'fotorama4.6.4/fotorama.css' ) );

                /**
                 * OwlCarousel
                 */
                wp_register_script( 'wpbooking-owlcarousel', wpbooking_assets_url( 'owl.carousel/owl.carousel.min.js' ), [ 'jquery' ], null, true );
                wp_register_style( 'wpbooking-owlcarousel', wpbooking_assets_url( 'owl.carousel/assets/owl.carousel.css' ) );

                /**
                 * Bootstrap
                 */
                wp_register_script( 'wpbooking-bootstrap', wpbooking_assets_url( 'bootstrap/js/bootstrap.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Select2 CSS
                 */
                wp_enqueue_style( 'wpbooking-select2', wpbooking_assets_url( 'select2/css/select2.min.css' ) );
                wp_enqueue_style( 'wpbooking-jquery-ui-datepicker', wpbooking_assets_url( 'css/datepicker.css' ) );
                if ( is_singular( 'wpbooking_service' ) ) {
                    wp_enqueue_style( 'wpbooking-magnific' );
                    wp_enqueue_script( 'wpbooking-magnific' );
                    wp_enqueue_style( 'wpbooking-fororama' );
                    wp_enqueue_script( 'wpbooking-fotorama' );
                }
                wp_enqueue_style( 'wpbooking', wpbooking_assets_url( 'css/wpbooking-booking.css' ), [ 'wpbooking-owlcarousel', 'wpbooking-icon' ] );
                if ( is_rtl() ) {
                    wp_enqueue_style( 'wpbooking-rtl', wpbooking_assets_url( 'css/rtl.css' ), [ 'wpbooking' ] );
                }
                /**
                 * Ion RangeSlider for Price Search Field
                 * @author dungdt
                 * @since  1.0
                 */
                wp_register_script( 'wpbooking-ion-range-slider', wpbooking_assets_url( 'ion-range-slider/js/ion.rangeSlider.min.js' ), [ 'jquery' ], null, true );
                wp_register_style( 'wpbooking-ion-range-slider', wpbooking_assets_url( 'ion-range-slider/css/ion.rangeSlider.css' ) );
                wp_register_style( 'wpbooking-ion-range-slider-flatui', wpbooking_assets_url( 'ion-range-slider/css/ion.rangeSlider.skinFlat.css' ) );
                wp_register_style( 'wpbooking-ion-range-slider-html5', wpbooking_assets_url( 'ion-range-slider/css/ion.rangeSlider.skinHTML5.css' ) );

                /**
                 * Javascripts
                 */
                wp_enqueue_script( 'wpbooking-fotorama-js', wpbooking_assets_url( 'fotorama4.6.4/fotorama.js' ), [ 'jquery' ], null, true );
                $google_api_key = wpbooking_get_option( 'google_api_key', 'AIzaSyAwXoW3vyBK0C5k2G-0l1D3n10UJ3LwZ3k' );
                wp_enqueue_script( 'wpbooking-google-map-js', '//maps.googleapis.com/maps/api/js?libraries=places&key=' . $google_api_key, [ 'jquery' ], null, true );
                wp_enqueue_script( 'wpbooking-gmap3.min-js', wpbooking_assets_url( 'js/gmap3.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Moment Js
                 */
                wp_register_script( 'wpbooking-moment', wpbooking_admin_assets_url( 'js/moment.min.js' ), [], null, true );
                wp_register_script( 'wpbooking-base64', wpbooking_assets_url( 'js/base64.min.js' ), [ 'jquery' ], null, true );
                wp_register_script( 'wpbooking-simpleWeather', wpbooking_assets_url( 'js/jquery.simpleWeather.min.js' ), [ 'jquery' ], null, true );

                wp_enqueue_script( 'wpbooking-calendar-room', wpbooking_admin_assets_url( 'js/wpbooking-calendar-room.js' ), [ 'jquery' ], null, true );

                wp_register_script( 'jquery.lang.gantt', wpbooking_admin_assets_url( '/js/gantt/lang.js' ), [ 'jquery', 'prettify' ], null, true );
                wp_register_script( 'gantt-js', wpbooking_admin_assets_url( 'js/gantt/jquery.fn.gantt.js' ), [ 'wpbooking-moment' ], null, true );
                wp_register_script( 'inventory-js', wpbooking_admin_assets_url( 'js/gantt/inventory.js' ), [ 'gantt-js' ], null, true );
                wp_register_style( 'gantt-css', wpbooking_admin_assets_url( 'js/gantt/css/style.css' ) );

                /*
                 * Daterangepicker
                 * */
                $locale = get_locale();
                wp_register_script( 'locale-daterangepicker-js', wpbooking_assets_url( '/js/daterangepicker/languages/' . $locale . '.js' ), [], null, true );
                wp_register_script( 'wpbooking-daterangepicker-js', wpbooking_assets_url( 'js/daterangepicker/daterangepicker.js' ), [ 'jquery', 'wpbooking-moment', 'locale-daterangepicker-js' ], null, true );
                wp_register_style( 'wpbooking-daterangepicker', wpbooking_assets_url( 'js/daterangepicker/daterangepicker.css' ) );

                /**
                 * Select2 Jquery
                 */
                wp_enqueue_script( 'wpbooking-select2', wpbooking_assets_url( 'select2/js/select2.full.min.js' ), [ 'jquery' ], null, true );
                wp_enqueue_script( 'wpbooking-booking', wpbooking_assets_url( 'js/wpbooking-booking.js' ), [ 'jquery', 'jquery-ui-datepicker', 'wpbooking-owlcarousel', 'wpbooking-moment' ], null, true );

                $ajax_url        = admin_url( 'admin-ajax.php' );
                $my_current_lang = wpbooking_current_lang();
                if ( $my_current_lang ) {
                    $ajax_url = add_query_arg( 'wpml_lang', $my_current_lang, $ajax_url );
                }
                wp_localize_script( 'jquery', 'wpbooking_params', [
                    'ajax_url'              => $ajax_url,
                    'wpbooking_security'    => wp_create_nonce( 'wpbooking-nonce-field' ),
                    'select_comment_review' => esc_html__( 'Please rate the criteria of this accommodation.', 'wp-booking-management-system' ),
                    'currency_symbol'       => WPBooking_Currency::get_current_currency( 'symbol' ),
                    'currency_position'     => WPBooking_Currency::get_current_currency( 'position' ),
                    'thousand_separator'    => WPBooking_Currency::get_current_currency( 'thousand_sep' ),
                    'decimal_separator'     => WPBooking_Currency::get_current_currency( 'decimal_sep' ),
                    'currency_precision'    => WPBooking_Currency::get_current_currency( 'decimal' ),
                    'dateformat'            => wpbooking_get_date_format_js(),
                    'locale'                => get_locale()
                ] );

                wp_localize_script( 'jquery', 'wpbooking_hotel_localize', [
                    'booking_required_adult'          => esc_html__( 'Please select the number of adults ', 'wp-booking-management-system' ),
                    'booking_required_children'       => esc_html__( 'Please select the number of children ', 'wp-booking-management-system' ),
                    'booking_required_adult_children' => esc_html__( 'Please select the number of adults and children', 'wp-booking-management-system' ),
                    'is_not_select_date'              => esc_html__( 'To see price details, please select check-in and check-out date.', 'wp-booking-management-system' ),
                    'is_not_select_check_in_date'     => esc_html__( 'Please select check-in date.', 'wp-booking-management-system' ),
                    'is_not_select_check_out_date'    => esc_html__( 'Please select check-out date.', 'wp-booking-management-system' ),
                    'loading_url'                     => admin_url( '/images/wpspin_light.gif' ),
                ] );
            }

            /**
             * Load default CSS and Javascript for admin
             * @since 1.0
             */
            function _admin_default_scripts()
            {
                /**
                 * WPBooking Icon
                 */
                wp_enqueue_style( 'wpbooking-icon', wpbooking_assets_url( 'my-icons-collection/font/flaticon.css' ) );

                /**
                 * JQuery Sticky
                 *
                 */
                wp_register_script( 'wpbooking-sticky', wpbooking_assets_url( 'admin/js/jquery.sticky.js' ), [ 'jquery' ], null, true );
                /**
                 * Code Flask
                 */
                wp_register_style( 'wpbooking-prismjs', wpbooking_assets_url( 'codeflask/themes/prism.css' ) );
                wp_register_style( 'wpbooking-codeflask', wpbooking_assets_url( 'codeflask/codeflask.css' ), [ 'wpbooking-prismjs' ] );
                wp_register_script( 'wpbooking-prismjs', wpbooking_assets_url( 'codeflask/prism.js' ), [], null, true );
                wp_register_script( 'wpbooking-codeflask', wpbooking_assets_url( 'codeflask/codeflask.js' ), [ 'wpbooking-prismjs' ], null, true );
                wp_register_script( 'wpbooking-bootstrap', wpbooking_assets_url( 'bootstrap/js/bootstrap.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Icon Picker
                 */
                wp_register_script( 'wpbooking-iconpicker', wpbooking_assets_url( 'iconpicker/js/fontawesome-iconpicker.min.js' ), [ 'jquery' ], null, true );

                wp_register_script( 'wpbooking-base64', wpbooking_assets_url( 'js/base64.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Select2 Jquery
                 */
                wp_enqueue_script( 'wpbooking-select2', wpbooking_assets_url( 'select2/js/select2.full.min.js' ), [ 'jquery' ], null, true );
                wp_enqueue_style( 'wpbooking-select2', wpbooking_assets_url( 'select2/css/select2.min.css' ) );

                /**
                 * wbCalendar
                 */
                wp_enqueue_script( 'wpbooking-Calendar', wpbooking_assets_url( 'js/wb-calendar.js' ), [ 'jquery' ], null, true );
                wp_enqueue_style( 'wpbooking-Calendar', wpbooking_assets_url( 'css/wb-calendar.css' ) );

                /**
                 * Chart Report
                 */
                wp_register_script( 'wpbooking-chart', wpbooking_assets_url( 'js/Chart.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Js Color
                 */
                wp_enqueue_script( 'wpbooking-colorpicker-master', wpbooking_assets_url( 'colorpicker-master/colors.js' ), [ 'jquery' ], null, true );
                wp_enqueue_style( 'wpbooking-colorpicker-master', wpbooking_assets_url( 'colorpicker-master/mod.css' ) );

                /**
                 * Flag icon
                 * @since  1.0
                 * @author dungdt
                 *
                 */

                wp_enqueue_script( 'wpbooking-admin', wpbooking_admin_assets_url( 'js/wpbooking-admin.js' ), [ 'jquery', 'wpbooking-bootstrap', 'jquery-ui-core', 'wpbooking-iconpicker', 'jquery-ui-datepicker', 'jquery-ui-accordion', 'wpbooking-calendar-room', 'wpbooking-sticky' ], null, true );
                wp_enqueue_script( 'wpbooking-admin-form-build', wpbooking_admin_assets_url( 'js/wpbooking-admin-form-build.js' ), [ 'jquery' ], null, true );

                wp_enqueue_script( 'wpbooking-moment-js', wpbooking_admin_assets_url( 'js/moment.min.js' ), [ 'jquery' ], null, true );

                /**
                 * Gantt
                 */

                wp_register_script( 'prettify', wpbooking_admin_assets_url( '/js/gantt/prettify.js' ), [ 'wpbooking-moment-js' ], null, true );
                wp_register_script( 'jquery.lang.gantt', wpbooking_admin_assets_url( '/js/gantt/lang.js' ), [ 'jquery', 'prettify' ], null, true );
                wp_register_script( 'gantt-js', wpbooking_admin_assets_url( 'js/gantt/jquery.fn.gantt.js' ), [ 'wpbooking-moment-js' ], null, true );
                wp_register_script( 'inventory-js', wpbooking_admin_assets_url( 'js/gantt/inventory.js' ), [ 'gantt-js' ], null, true );
                wp_register_style( 'gantt-css', wpbooking_admin_assets_url( 'js/gantt/css/style.css' ) );

                wp_enqueue_script( 'wpbooking-full-calendar', wpbooking_admin_assets_url( 'js/fullcalendar.min.js' ), [ 'jquery', 'wpbooking-moment-js' ], null, true );

                wp_enqueue_script( 'wpbooking-fullcalendar-lang', wpbooking_admin_assets_url( '/js/lang-all.js' ), [ 'jquery' ], null, true );

                wp_enqueue_script( 'wpbooking-calendar-room', wpbooking_admin_assets_url( 'js/wpbooking-calendar-room.js' ), [ 'jquery', 'jquery-ui-datepicker' ], null, true );

                //Popover
                wp_register_style( 'wpbooking-popover', wpbooking_assets_url( 'bootstrap/less/popovers.css' ) );

                // Admin Fonts
                $fonts = add_query_arg( [
                    'family' => 'Open+Sans:700,800',
                    'subset' => 'vietnamese',
                ], 'https://fonts.googleapis.com/css' );

                wp_enqueue_style( 'wpbooking-open-sans-bold', $fonts );
                wp_enqueue_style( 'wpbooking-iconpicker', wpbooking_assets_url( 'iconpicker/css/fontawesome-iconpicker.min.css' ) );
                wp_enqueue_style( 'wpbooking-full-calendar', wpbooking_admin_assets_url( '/css/fullcalendar.min.css' ), false, '1.1.6' );

                wp_enqueue_style( 'wpbooking-font-awesome', wpbooking_assets_url( 'fa4.5/css/font-awesome.min.css' ), false, '4.5.0' );
                wp_enqueue_style( 'wpbooking-admin', wpbooking_admin_assets_url( 'css/admin.css' ), [ 'wpbooking-icon' ] );
                wp_enqueue_style( 'wpbooking-admin-form-build', wpbooking_admin_assets_url( 'css/wpbooking-admin-form-build.css' ) );
                if ( is_rtl() ) {
                    wp_enqueue_style( 'wpbooking-admin-rtl', wpbooking_admin_assets_url( 'css/rtl.css' ), [ 'wpbooking-admin' ] );
                }
                $ajax_url        = admin_url( 'admin-ajax.php' );
                $my_current_lang = wpbooking_current_lang();
                if ( $my_current_lang ) {
                    $ajax_url = add_query_arg( 'wpml_lang', $my_current_lang, $ajax_url );
                }
                wp_localize_script( 'jquery', 'wpbooking_params', [
                    'ajax_url'                 => $ajax_url,
                    'api_url'                  => $this->API_URL,
                    'wpbooking_security'       => wp_create_nonce( 'wpbooking-nonce-field' ),
                    'delete_confirm'           => esc_html__( 'Do you want to delete?', 'wp-booking-management-system' ),
                    'delete_string'            => esc_html__( 'delete', 'wp-booking-management-system' ),
                    'delete_gallery'           => esc_html__( 'Do you want to delete all image?', 'wp-booking-management-system' ),
                    'room'                     => esc_html__( 'room', 'wp-booking-management-system' ),
                    'rooms'                    => esc_html__( 'rooms', 'wp-booking-management-system' ),
                    'delete_permanently_image' => esc_html__( 'You want to delete this image permanently?', 'wp-booking-management-system' ),
                    'next'                     => esc_html__( 'Next', 'wp-booking-management-system' ),
                    'prev'                     => esc_html__( 'Prev', 'wp-booking-management-system' ),
                    'read_more'                => esc_html__( 'Read More', 'wp-booking-management-system' ),
                    'room_name'                => esc_html__( 'Room Name:', 'wp-booking-management-system' )
                ] );

            }

            function _load_cores()
            {
                $files = [
                    'cores/config',
                    'cores/model',
                    'cores/controllers',
                    'cores/loader',
                    'cores/updater',
                ];
                $this->load( $files );
            }

            /**
             * @since 1.0
             */

            function _init()
            {
                load_plugin_textdomain( 'wp-booking-management-system', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
            }

            /**
             * @since 1.0
             */
            function _admin_init()
            {
                $plugin         = get_plugin_data( __FILE__ );
                $this->_version = $plugin[ 'Version' ];

            }

            /**
             * Get Version Plugin
             *
             * @since  1.2
             * @author quandq
             *
             * @return mixed
             */
            function get_version_plugin()
            {
                $plugin = get_plugin_data( __FILE__ );

                return $plugin[ 'Version' ];
            }

            function _admin_init_menu_page()
            {

                $menu_page = $this->get_menu_page();
                add_menu_page(
                    $menu_page[ 'page_title' ],
                    $menu_page[ 'menu_title' ],
                    $menu_page[ 'capability' ],
                    $menu_page[ 'menu_slug' ],
                    $menu_page[ 'function' ],
                    $menu_page[ 'icon_url' ],
                    $menu_page[ 'position' ]
                );
            }

            /**
             * @since 1.0
             *
             * @param            $file
             * @param bool|FALSE $include_once
             */
            function load( $file, $include_once = false )
            {
                if ( is_array( $file ) ) {
                    if ( !empty( $file ) ) {
                        foreach ( $file as $value ) {
                            $this->load( $value, $include_once );
                        }
                    }
                } else {
                    $file = $this->get_dir( 'shinetheme/' . $file . '.php' );
                    if ( !$file ) {

                    }
                    if ( file_exists( $file ) ) {
                        if ( $include_once ) include_once( $file );
                        include( $file );
                    }
                }

            }

            /**
             * @since 1.0
             *
             * @param bool|FALSE $file
             *
             * @return string
             */
            function get_dir( $file = false )
            {
                return $this->_dir_path . $file;
            }

            /**
             * @since 1.0
             *
             * @param bool|FALSE $file
             *
             * @return string
             */
            function get_url( $file = false )
            {
                return $this->_dir_url . $file;
            }

            function get_menu_page()
            {
                $page = apply_filters( 'wpbooking_menu_page_args', [
                    'page_title' => esc_html__( "WPBooking", 'wp-booking-management-system' ),
                    'menu_title' => esc_html__( "WPBooking", 'wp-booking-management-system' ),
                    'capability' => 'manage_options',
                    'menu_slug'  => 'wpbooking',
                    'function'   => [ $this, '_show_default_page' ],
                    'icon_url'   => 'dashicons-analytics',
                    'position'   => 55
                ] );

                return $page;

            }

            function _show_default_page()
            {
                do_action( 'wpbooking_default_menu_page' );
            }

            function set_admin_message( $message, $type = 'information' )
            {
                $_SESSION[ 'message' ][ 'admin' ] = [
                    'content' => $message,
                    'type'    => $type
                ];
            }

            function set_message( $message, $type = 'information' )
            {
                $_SESSION[ 'message' ][ 'frontend' ] = [
                    'content' => $message,
                    'type'    => $type
                ];
            }

            function get_message( $clear_message = true )
            {
                $message = isset( $_SESSION[ 'message' ][ 'frontend' ] ) ? $_SESSION[ 'message' ][ 'frontend' ] : false;
                if ( $clear_message ) $_SESSION[ 'message' ][ 'frontend' ] = [];

                return $message;
            }

            function get_admin_message( $clear_message = true )
            {
                $message = isset( $_SESSION[ 'message' ][ 'admin' ] ) ? $_SESSION[ 'message' ][ 'admin' ] : false;
                if ( $clear_message ) $_SESSION[ 'message' ][ 'admin' ] = [];

                return $message;
            }

            /**
             * Set Global Variable
             *
             * @since 1.0
             *
             * @param $name
             * @param $value
             */
            function set( $name, $value )
            {
                $this->global_values[ $name ] = $value;
            }

            /**
             * Get Global Variable
             *
             * @since 1.0
             *
             * @param            $name
             * @param bool|FALSE $default
             *
             * @return bool
             */
            function get( $name, $default = false )
            {
                return isset( $this->global_values[ $name ] ) ? $this->global_values[ $name ] : $default;
            }

            /**
             * @return WPBooking_System
             */
            static function inst()
            {

                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        /**
         * @since 1.0
         */
        function WPBooking()
        {
            return WPBooking_System::inst();
        }

        WPBooking();
    }