<?php
    if ( !class_exists( 'WPBooking_Checkout_Controller' ) ) {
        class WPBooking_Checkout_Controller extends WPBooking_Controller
        {
            static $_inst;

            function __construct()
            {
                if ( !session_id() ) {
                    session_start();
                }
                /**
                 * Ajax Do Check Out
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'wp_ajax_wpbooking_do_checkout', [ $this, 'do_checkout' ] );
                add_action( 'wp_ajax_nopriv_wpbooking_do_checkout', [ $this, 'do_checkout' ] );

                /**
                 * Ajax Add To Cart
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'wp_ajax_wpbooking_add_to_cart', [ $this, '_add_to_cart' ] );
                add_action( 'wp_ajax_nopriv_wpbooking_add_to_cart', [ $this, '_add_to_cart' ] );

                /**
                 * Ajax Check Empty Cart
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'wp_ajax_wpbooking_check_empty_cart', [ $this, '_check_empty_cart' ] );
                add_action( 'wp_ajax_nopriv_wpbooking_check_empty_cart', [ $this, '_check_empty_cart' ] );

                /**
                 * Register Page CheckOut
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'init', [ $this, '_register_shortcode' ] );


                /**
                 * Register Order Status
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'init', [ $this, '_register_order_status' ] );

                /**
                 * Get form billing html
                 *
                 * @since 1.0
                 */
                add_action( 'wpbooking_billing_information_form', [ $this, 'form_billing_html' ] );

                parent::__construct();
            }

            /**
             * Register Order Status
             *
             * @since  1.0
             * @author quandq
             */
            function _register_order_status()
            {
                $order_status = WPBookingConfig()->item( 'order_status' );
                if ( !empty( $order_status ) ) {
                    foreach ( $order_status as $k => $v ) {
                        register_post_status( $k, [
                            'label'                     => $v[ 'label' ],
                            'public'                    => true,
                            'exclude_from_search'       => false,
                            'show_in_admin_all_list'    => true,
                            'show_in_admin_status_list' => true,
                            'publicly_queryable'        => false,
                            'label_count'               => $v[ 'label' ] . ' <span class="count">(%s)</span>'
                        ] );
                    }
                }

            }

            /**
             * Ajax Checkout Handler
             *
             * @since  1.0
             * @author quandq
             */
            function do_checkout()
            {

                $cart         = WPBooking_Session::get( 'wpbooking_cart' );
                $service_type = $cart[ 'service_type' ];
                $res          = [];
                $is_validate  = true;

                if ( empty( WPBooking_Input::request( 'term_condition' ) ) ) {
                    $is_validate = false;
                    wpbooking_set_message( esc_html__( "You do not accept our terms!", 'wp-booking-management-system' ), 'error' );
                }

                if ( empty( $cart ) ) {
                    $is_validate = false;
                    wpbooking_set_message( esc_html__( "Sorry! Your cart is currently empty", 'wp-booking-management-system' ), 'error' );
                }


                $fields = $this->get_billing_form_fields();
                // Validate Form Billing
                $validator  = new WPBooking_Form_Validator();

                if ( !empty( $fields ) and $is_validate ) {
                    foreach ( $fields as $key => $value ) {
                        if ( $value[ 'name' ] != 'passengers' ) {
                            $validator->set_rules( $value[ 'name' ], strtolower( $value[ 'title' ] ), $value[ 'rule' ] );
                        }
                    }
                    if ( $is_validate and !$validator->run() ) {
                        $is_validate = false;
                        wpbooking_set_message( $validator->error_string(), 'error' );
                        $res[ 'error_type' ]   = 'form_validate';
                        $res[ 'error_fields' ] = $validator->get_error_fields();
                    }
                }

                $pay_amount = $this->get_cart_total();
                if ( $is_validate and empty( $pay_amount ) ) {
                    $is_validate = false;
                    wpbooking_set_message( esc_html__( "Price of basket is 0. You cannot make this payment!", 'wp-booking-management-system' ), 'error' );
                }

                // Require Payment Gateways
                $gateway_manage   = WPBooking_Payment_Gateways::inst();
                $selected_gateway = WPBooking_Input::post( 'payment_gateway' );
                $gateway          = $gateway_manage->get_gateway( $selected_gateway );

                if ( $is_validate and $pay_amount ) {
                    if ( empty( $selected_gateway ) ) {
                        $is_validate = false;
                        wpbooking_set_message( esc_html__( "Please select a method of payment.", 'wp-booking-management-system' ), 'error' );
                    } elseif ( !$gateway and !$gateway->is_available() ) {
                        $is_validate = false;
                        wpbooking_set_message( sprintf( esc_html__( "Gateway: %s is not ready to use, please choose another gateway", 'wp-booking-management-system' ), $selected_gateway ), 'error' );
                    }
                }

                $is_validate = apply_filters( 'wpbooking_do_checkout_validate', $is_validate, $cart );
                $is_validate = apply_filters( 'wpbooking_do_checkout_validate_' . $service_type, $is_validate, $cart );

                if ( !$is_validate ) {
                    $res [ 'status' ] = 0;
                    $res[ 'message' ] = wpbooking_get_message( true );
                } else {
                    // Checkout form data
                    if ( !empty( $fields ) ) {
                        foreach ( $fields as $k => $v ) {
                            $fields[ $k ][ 'value' ] = WPBooking_Input::post( $k );
                        }
                    }
                    // Register User
                    $customer_id = false;
                    if ( is_user_logged_in() ) {
                        $customer_id = get_current_user_id();
                    } else {
                        // Default Fields
                        $post_data = wp_parse_args( WPBooking_Input::post(), [
                            'user_first_name' => false,
                            'user_last_name'  => false,
                            'user_email'      => false,
                        ] );
                        if ( $email = $post_data[ 'user_email' ] ) {
                            // Check User Exists
                            if ( $user_id = email_exists( $email ) ) $customer_id = $user_id;
                            if ( empty( $customer_id ) ) {
                                $customer_id = WPBooking_User::inst()->order_create_user( [
                                    'user_email' => $email,
                                    'first_name' => $post_data[ 'user_first_name' ],
                                    'last_name'  => $post_data[ 'user_last_name' ],
                                ], $fields );
                            }
                        }
                    }

                    /**
                     * Update User Billing Info if empty
                     *
                     * @since  1.0
                     * @author dungdt
                     */
                    if ( $customer_id ) WPBooking_User::inst()->order_update_user( $customer_id, $fields );

                    $order_id = WPBooking_Session::get( 'wpbooking_order_id' );
                    if ( !empty( $order_id ) ) {
                        $order = new WB_Order( $order_id );
                    } else {
                        $order    = new WB_Order( false );
                        $order_id = $order->create( $cart, $fields, $selected_gateway, $customer_id );
                        $order    = new WB_Order( $order_id );
                    }

                    if ( $order_id ) {
                        WPBooking_Session::set( 'wpbooking_order_id', $order_id );
                        $data            = [
                            'status' => 1
                        ];
                        $res[ 'status' ] = 1;
                        // Only work with Order Table bellow
                        try {
                            if ( $selected_gateway ) {
                                $data = WPBooking_Payment_Gateways::inst()->do_checkout( $selected_gateway, $order_id );
                                if ( !$data[ 'status' ] ) {
                                    $res = [
                                        'status'  => 0,
                                        'message' => wpbooking_get_message( true ),
                                        'data'    => $data
                                    ];
                                    // If Payment Fail update the status
                                    $order->payment_failed();
                                }
                                if ( $data[ 'status' ] and isset( $data[ 'complete_purchase' ] ) and $data[ 'complete_purchase' ] ) {
                                    $order->complete_purchase();
                                }
                            }
                            if ( $res[ 'status' ] ) {
                                //Clear the Order Id after create new order,
                                WPBooking_Session::set( 'wpbooking_order_id', '' );
                                //Clear the Cart after create new order,
                                WPBooking_Session::set( 'wpbooking_cart', [] );

                                wpbooking_set_message( esc_html__( 'Booking Successfully', 'wp-booking-management-system' ) );
                                //do checkout
                                $res[ 'data' ]    = $data;
                                $res[ 'message' ] = wpbooking_get_message( true );
                            }
                        } catch ( Exception $e ) {
                            wpbooking_set_message( $e->getMessage(), 'error' );
                            //do checkout
                            $res = [
                                'status'  => 0,
                                'message' => wpbooking_get_message( true ),
                            ];
                        }
                        if ( empty( $data[ 'redirect' ] ) ) {
                            $res[ 'redirect' ] = get_permalink( $order_id );
                        }

                        if ( !empty( $data[ 'redirect' ] ) ) {
                            $res[ 'redirect' ] = $data[ 'redirect' ];
                            WPBooking_Session::set( 'wpbooking_order_id', '' );
                        }
                        if ( !empty( $data[ 'redirect_form' ] ) ) {
                            echo balanceTags( $data[ 'redirect_form' ] );
                            exit();
                        }
                        if ( isset( $data[ 'complete_purchase' ] ) and !$data[ 'complete_purchase' ] ) {
                            $res[ 'redirect' ] = "";
                        }

                        do_action( 'wpbooking_after_checkout_success', $order_id );

                    } else {
                        $res = [
                            'status'  => 0,
                            'message' => esc_html__( 'The order cannot be created. Please contact Admin', 'wp-booking-management-system' )
                        ];
                    }

                }

                $res = apply_filters( 'wpbooking_ajax_do_checkout', $res, $cart );

                echo json_encode( $res );
                die;
            }

            /**
             * Ajax Add To Cart Handler
             *
             * @since  1.0
             * @author quandq
             * @return string
             */
            function _add_to_cart()
            {

                $res = [];

                $post_id = WPBooking_Input::post( 'post_id' );

                $service_type = get_post_meta( $post_id, 'service_type', true );

                // Validate Order Form
                $is_validate = true;

                // Validate Post and Post Type
                if ( !$post_id or get_post_type( $post_id ) != 'wpbooking_service' ) {
                    $is_validate = false;
                    wpbooking_set_message( esc_html__( "You do not select any service", 'wp-booking-management-system' ), 'error' );
                }

                $service = new WB_Service( $post_id );

                $cart_params = [
                    'post_id'      => $post_id,
                    'service_type' => $service_type,
                    'currency'     => WPBooking_Currency::get_current_currency( 'currency' ),
                    'price'        => 0,
                    'discount'     => [],
                ];

                $cart_params[ 'tax' ][ 'vat' ][ 'excluded' ] = $service->get_meta( 'vat_excluded' );
                if ( $service->get_meta( 'vat_excluded' ) != '' and $service->get_meta( 'vat_amount' ) != '' ) {
                    $cart_params[ 'tax' ][ 'vat' ][ 'amount' ] = $service->get_meta( 'vat_amount' );
                    $cart_params[ 'tax' ][ 'vat' ][ 'unit' ]   = $service->get_meta( 'vat_unit' );
                }
                $cart_params[ 'tax' ][ 'citytax' ][ 'excluded' ] = $service->get_meta( 'citytax_excluded' );
                if ( $service->get_meta( 'citytax_excluded' ) != '' and $service->get_meta( 'citytax_amount' ) != '' ) {
                    $cart_params[ 'tax' ][ 'citytax' ][ 'amount' ] = $service->get_meta( 'citytax_amount' );
                    $cart_params[ 'tax' ][ 'citytax' ][ 'unit' ]   = $service->get_meta( 'citytax_unit' );
                }
                $cart_params[ 'deposit' ][ 'status' ] = $service->get_meta( 'deposit_payment_status' );
                if ( $service->get_meta( 'deposit_payment_status' ) != '' ) {
                    $cart_params[ 'deposit' ][ 'amount' ] = $service->get_meta( 'deposit_payment_amount' );
                }

                // Convert Check In and Check Out to Timestamp if available
                $check_in     = WPBooking_Input::request( 'wpbooking_checkin_y' ) . "-" . WPBooking_Input::request( 'wpbooking_checkin_m' ) . "-" . WPBooking_Input::request( 'wpbooking_checkin_d' );
                $check_out    = WPBooking_Input::request( 'wpbooking_checkout_y' ) . "-" . WPBooking_Input::request( 'wpbooking_checkout_m' ) . "-" . WPBooking_Input::request( 'wpbooking_checkout_d' );
                $check_in_out = WPBooking_Input::request( 'wpbooking_check_in_out' );
                if ( $check_in == '--' ) $check_in = '';
                if ( $check_out == '--' ) $check_out = '';
                if ( $check_in ) {
                    $cart_params[ 'check_in_timestamp' ] = strtotime( $check_in );
                    if ( $check_out ) {
                        $cart_params[ 'check_out_timestamp' ] = strtotime( $check_out );
                    } else {
                        $cart_params[ 'check_out_timestamp' ] = $cart_params[ 'check_in_timestamp' ];
                    }
                }
                $cart_params[ 'check_in_out' ] = $check_in_out;

                $cart_params = apply_filters( 'wpbooking_cart_item_params', $cart_params, $post_id, $service_type );
                $cart_params = apply_filters( 'wpbooking_cart_item_params_' . $service_type, $cart_params, $post_id );

                $is_validate = apply_filters( 'wpbooking_add_to_cart_validate', $is_validate, $service_type, $post_id, $cart_params );
                $is_validate = apply_filters( 'wpbooking_add_to_cart_validate_' . $service_type, $is_validate, $service_type, $post_id, $cart_params );

                if ( !$is_validate ) {
                    $res[ 'status' ]  = false;
                    $res[ 'message' ] = wpbooking_get_message( true );
                } else {
                    WPBooking_Session::set( 'wpbooking_cart', $cart_params );
                    $res = [
                        'status'   => 1,
                        'message'  => '',
                        'redirect' => $this->get_checkout_url(),
                    ];
                    WPBooking_Session::set( 'wpbooking_order_id', false );
                }
                $res[ 'updated_content' ] = apply_filters( 'wpbooking_cart_updated_content', [], $is_validate );

                $res = apply_filters( 'wpbooking_ajax_add_to_cart', $res, $post_id, $is_validate );
                $res = apply_filters( 'wpbooking_ajax_add_to_cart_' . $service_type, $res, $post_id, $is_validate );

                do_action( 'wpbooking_ajax_after_add_to_cart' );

                echo json_encode( $res );

                die;
            }

            /**
             * Return permalink of the Cart Page
             *
             * @since  1.0
             * @author quandq
             * @return false|string
             */
            function get_checkout_url()
            {
                $checkout_url         = get_permalink( wpbooking_get_option( 'checkout_page' ) );
                $allow_guest_checkout = wpbooking_get_option( 'allow_guest_checkout' );
                if ( !$allow_guest_checkout and !is_user_logged_in() ) {
                    $checkout_url = wp_login_url( $checkout_url );
                }

                return $checkout_url;
            }

            /**
             * Register shortcode
             *
             * @since  1.0
             * @author quandq
             */
            function _register_shortcode()
            {
                add_shortcode( 'wpbooking_checkout_page', [ $this, '_render_checkout_shortcode' ] );
            }

            /**
             * CheckOut shortcode
             *
             * @since  1.0
             * @author quandq
             */
            function _render_checkout_shortcode( $attr = [], $content = false )
            {
                return wpbooking_load_view( 'checkout/index' );
            }

            /**
             * Get Total Cart
             * @author quandq
             * @since  1.0
             *
             * @return mixed|void
             */
            function get_cart_total( $args = [], $cart = false )
            {


                if ( empty( $cart ) ) {
                    $cart = $this->get_cart();
                }

                $total_price = $cart[ 'price' ];

                $service_type = $cart[ 'service_type' ];


                $total_price = apply_filters( 'wpbooking_get_cart_total_' . $service_type, $total_price, $cart, $args );

                $total_price = apply_filters( 'wpbooking_get_cart_total', $total_price, $cart, $args );

                return $total_price;
            }

            /**
             * Get Cart Deposit Amount
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $cart
             *
             * @return float|int|mixed|void
             */
            public function get_cart_deposit( $cart = false )
            {

                $total_price = $this->get_total_price_cart_with_tax();

                $price_deposit = 0;

                if ( empty( $cart ) ) {
                    $cart = $this->get_cart();
                }

                if ( !empty( $cart[ 'deposit' ][ 'status' ] ) ) {
                    switch ( $cart[ 'deposit' ][ 'status' ] ) {
                        case "percent":
                            if ( $cart[ 'deposit' ][ 'amount' ] > 100 ) $cart[ 'deposit' ][ 'amount' ] = 100;
                            $price_deposit = round( $total_price * $cart[ 'deposit' ][ 'amount' ] / 100, 2 );
                            break;
                        case "amount":
                            if ( $cart[ 'deposit' ][ 'amount' ] < $total_price )
                                $price_deposit = $cart[ 'deposit' ][ 'amount' ];
                            break;

                    }
                }

                return $price_deposit;
            }


            /**
             * Get cart
             *
             * @author quandq
             * @since  1.0
             *
             * @return array
             */
            function get_cart()
            {
                return WPBooking_Session::get( 'wpbooking_cart' );
            }

            /**
             * Set cart
             *
             * @author quandq
             * @since  1.0
             *
             * @param $cart
             */
            function set_cart( $cart )
            {
                return WPBooking_Session::set( 'wpbooking_cart', $cart );
            }

            /**
             * Billing Form Fields
             *
             * @author quandq
             * @since  1.0
             *
             * @return array|mixed|void
             */
            function get_billing_form_fields()
            {
                $field_form = [
                    'user_first_name'      => [
                        'title'       => esc_html__( "First Name", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "First name", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_first_name',
                        'size'        => '6',
                        'required'    => true,
                        'rule'        => 'required|max_length[100]',
                    ],
                    'user_last_name'       => [
                        'title'       => esc_html__( "Last Name", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Last name", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_last_name',
                        'size'        => '6',
                        'required'    => true,
                        'rule'        => 'required|max_length[100]',
                    ],
                    'user_email'           => [
                        'title'       => esc_html__( "Email", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Email", "wp-booking-management-system" ),
                        'desc'        => esc_html__( "Email for confirmation", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_email',
                        'size'        => '12',
                        'required'    => true,
                        'rule'        => 'required|max_length[100]|valid_email',
                    ],
                    'user_phone'           => [
                        'title'       => esc_html__( "Telephone", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Telephone", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_phone',
                        'size'        => '12',
                        'required'    => true,
                        'rule'        => 'required|numeric|max_length[100]',
                    ],
                    'user_address'         => [
                        'title'       => esc_html__( "Address", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Address", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_address',
                        'size'        => '12',
                        'required'    => true,
                        'rule'        => 'required|max_length[100]',
                    ],
                    'user_postcode'        => [
                        'title'       => esc_html__( "Postcode / ZIP", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Postcode / ZIP", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_postcode',
                        'size'        => '6',
                        'required'    => false,
                        'rule'        => '',
                    ],
                    'user_apt_unit'        => [
                        'title'       => esc_html__( "Apt/ Unit", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Apt/ Unit", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'user_apt_unit',
                        'size'        => '6',
                        'required'    => false,
                        'rule'        => '',
                    ],
                    'passengers'           => [
                        'title'       => esc_html__( "Passengers", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Passengers", "wp-booking-management-system" ),
                        'type'        => 'text',
                        'name'        => 'passengers',
                        'size'        => '12',
                        'required'    => true,
                        'rule'        => 'required',
                    ],
                    'user_special_request' => [
                        'title'       => esc_html__( "Special Request", "wp-booking-management-system" ),
                        'placeholder' => esc_html__( "Notes about your order, e.g. special notes for  delivery.", "wp-booking-management-system" ),
                        'type'        => 'textarea',
                        'name'        => 'user_special_request',
                        'size'        => '12',
                        'required'    => false,
                        'rule'        => '',
                    ],
                ];

                $field_form = apply_filters( 'wpbooking_get_billing_form_fields', $field_form );

                return $field_form;
            }

            /**
             * Get Cart Tax Total
             *
             * @since  1.0
             * @author dungdt
             *
             * @return int|mixed|void
             */
            public function get_cart_tax_price( $total_with_tax = 0 )
            {
                $tax          = [];
                $cart         = $this->get_cart();
                $total_price  = $total_with_tax;
                $total_tax    = 0;
                $tax_total    = 0;
                $service_type = $cart[ 'service_type' ];
                if ( !empty( $cart[ 'tax' ] ) ) {
                    foreach ( $cart[ 'tax' ] as $key => $value ) {
                        if ( $value[ 'excluded' ] != '' and !empty( $value[ 'amount' ] ) ) {
                            $unit        = $value[ 'unit' ];
                            $tax[ $key ] = $value;
                            switch ( $unit ) {
                                case "percent":
                                    $price = round( $total_price * ( $value[ 'amount' ] / 100 ), 2 );
                                    break;
                                case "fixed":
                                default:
                                    $price = $value[ 'amount' ];
                                    break;
                            }
                            if ( $value[ 'excluded' ] == 'yes_not_included' ) {
                                $total_tax += $price;
                            }

                            $tax_total += (float)$price;

                            $tax[ $key ][ 'price' ] = floatval( $price );
                        }
                    }
                }
                $tax[ 'total_price' ] = $total_tax;
                $tax[ 'tax_total' ]   = $tax_total;
                $tax                  = apply_filters( 'wpbooking_get_cart_tax_price', $tax, $cart );
                $tax                  = apply_filters( 'wpbooking_get_cart_tax_price_' . $service_type, $tax, $cart );

                return $tax;
            }

            /**
             * Get Tax Price
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $total_without_tax
             *
             * @return float
             */
            public function get_cart_tax_amount( $total_without_tax )
            {

                $tax = $this->get_cart_tax_price( $total_without_tax );

                return !empty( $tax[ 'total_price' ] ) ? (float)$tax[ 'total_price' ] : 0;
            }

            /**
             * Get Tax Price Cart
             *
             * @since  1.0
             * @author quandq
             *
             * @return mixed|void
             */
            public function get_total_price_cart_with_tax()
            {

                $price_total = $this->get_cart_total();

                $price_total = apply_filters( 'wpbooking_get_total_price_cart_without_tax', $price_total );

                $tax = $this->get_cart_tax_price( $price_total );

                $tax_total = !empty( $tax[ 'total_price' ] ) ? $tax[ 'total_price' ] : 0;

                $total = $price_total + $tax_total;

                $total = apply_filters( 'wpbooking_get_total_price_cart_with_tax', $total );

                return $total;

            }

            /**
             * Ajax Check Empty Cart
             *
             * @since  1.0
             * @author quandq
             *
             * @return mixed
             */
            function _check_empty_cart()
            {
                $cart = $this->get_cart();
                if ( !empty( $cart ) ) {
                    echo json_encode( [ 'status' => 'true' ] );
                } else {
                    echo json_encode( [ 'status' => 'false' ] );
                }
                wp_die();
            }

            /**
             * Get form billing information
             *
             * @since 1.0
             *
             * @return WPBooking_Checkout_Controller
             */
            function form_billing_html()
            {
                ?>
                <div class="billing_information">
                    <div class="row">
                        <?php
                            $field_form_billing = $this->get_billing_form_fields();
                            if ( !empty( $field_form_billing ) ) {
                                ?>
                                <?php foreach ( $field_form_billing as $k => $v ) {
                                    $data  = wp_parse_args( $v, [
                                        'title'       => '',
                                        'desc'        => '',
                                        'placeholder' => '',
                                        'type'        => 'text',
                                        'name'        => '',
                                        'size'        => '12',
                                        'required'    => false,
                                    ] );
                                    $value = '';
                                    if ( is_user_logged_in() ) {
                                        $customer_id = get_current_user_id();
                                        $key         = str_ireplace( "user_", "", $v[ 'name' ] );
                                        if ( $key == 'email' ) {
                                            $value = get_the_author_meta( 'email', $customer_id );
                                        } else {
                                            $value = get_user_meta( $customer_id, $key, true );
                                        }
                                    }
                                    if ( $data[ 'name' ] == 'passengers' && wpbooking_get_option( 'allow_passenger_information_checkout' ) == 1 ) {
                                        $cart       = $this->get_cart();
                                        $passengers = 0;
                                        switch ( $cart[ 'service_type' ] ) {
                                            case 'accommodation':
                                                $passengers = (int)$cart[ 'person' ];
                                                break;
                                            case 'tour':
                                                $passengers = (int)$cart[ 'adult_number' ] + (int)$cart[ 'children_number' ] + (int)$cart[ 'infant_number' ];
                                                break;
                                            case 'car':
                                                $passengers = (int)$cart[ 'person' ];
                                                break;
                                        }
                                        for ( $i = 1; $i <= $passengers; $i++ ) {
                                            ?>
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="passenger-<?php echo esc_attr( $i ); ?>"><strong><?php echo sprintf( esc_html__( 'Passenger %s', 'wpbooking' ), $i ); ?></strong><?php if ( $data[ 'required' ] ) echo '<span class="required">*</span>'; ?>
                                                    </label>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-9">
                                                            <span><?php echo esc_html__( 'Name', 'wp-booking-management-system' ); ?><input
                                                                        type="text"
                                                                        class="form-control <?php if ( $data[ 'required' ] ) echo 'required'; ?>"
                                                                        name="passengers[name][]"
                                                                        value="" <?php if ( $data[ 'required' ] ) echo 'required'; ?>></span>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-3">
                                                            <span><?php echo esc_html__( 'Ages', 'wp-booking-management-system' ); ?><input
                                                                        type="number" min="0"
                                                                        class="form-control <?php if ( $data[ 'required' ] ) echo 'required'; ?>"
                                                                        name="passengers[age][]"
                                                                        value="0" <?php if ( $data[ 'required' ] ) echo 'required'; ?>></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } elseif ( $data[ 'name' ] != 'passengers' ) {
                                        ?>
                                        <div class="col-md-<?php echo esc_html( $data[ 'size' ] ) ?>">
                                            <div class="form-group">
                                                <label
                                                        for="<?php echo esc_html( $data[ 'name' ] ) ?>"><?php echo esc_html( $data[ 'title' ] ) ?><?php if ( $data[ 'required' ] ) echo '<span class="required">*</span>'; ?></label>
                                                <?php if ( $data[ 'type' ] != 'textarea' ) { ?>
                                                    <input type="<?php echo esc_attr( $data[ 'type' ] ) ?>"
                                                           class="form-control only_number"
                                                           id="<?php echo esc_html( $data[ 'name' ] ) ?>"
                                                           name="<?php echo esc_html( $data[ 'name' ] ) ?>"
                                                           placeholder="<?php echo esc_html( $data[ 'placeholder' ] ) ?>" <?php if ( $data[ 'required' ] ) echo 'required'; ?>
                                                           value="<?php echo esc_html( $value ) ?>">
                                                    <span class="desc"><?php echo esc_html( $data[ 'desc' ] ) ?></span>
                                                <?php } else { ?>
                                                    <textarea name="<?php echo esc_html( $data[ 'name' ] ) ?>"
                                                              class="form-control" rows="4"
                                                              placeholder="<?php echo esc_html( $data[ 'placeholder' ] ) ?>" <?php if ( $data[ 'title' ] ) echo 'required'; ?>><?php echo esc_html( $value ) ?></textarea>
                                                    <span class="desc"><?php echo esc_html( $data[ 'desc' ] ) ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php }
                                } ?>
                            <?php } ?>
                    </div>
                </div>
                <?php
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        WPBooking_Checkout_Controller::inst();
    }