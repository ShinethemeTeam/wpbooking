<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    use Omnipay\Omnipay;

    if ( !class_exists( 'WPBooking_Paypal_Gateway' ) and class_exists( 'WPBooking_Abstract_Payment_Gateway' ) ) {
        class WPBooking_Paypal_Gateway extends WPBooking_Abstract_Payment_Gateway
        {
            static $_inst = false;

            protected $gateway_id = 'paypal';

            //settings fields
            protected $settings = [];

            protected $gatewayObject = false;

            function __construct()
            {
                $this->gateway_info  = [
                    'label' => esc_html__( "PayPal", 'wp-booking-management-system' )
                ];
                $this->settings      = [
                    [
                        'id'             => 'enable',
                        'label'          => esc_html__( 'Enable', 'wp-booking-management-system' ),
                        'type'           => 'checkbox',
                        'std'            => '',
                        'checkbox_label' => esc_html__( "Yes, I want to enable PayPal", 'wp-booking-management-system' )
                    ],
                    [
                        'id'    => 'title',
                        'label' => esc_html__( 'Title', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => 'PayPal',
                    ],

                    [
                        'id'    => 'desc',
                        'label' => esc_html__( 'Descriptions', 'wp-booking-management-system' ),
                        'type'  => 'textarea',
                        'std'   => 'You will be redirect to paypal website to finish the payment process',
                    ],
                    [
                        'type' => 'hr'
                    ],
                    [
                        'id'    => 'api_username',
                        'label' => esc_html__( 'API Username', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                    [
                        'id'    => 'api_password',
                        'label' => esc_html__( 'API Password', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                    [
                        'id'    => 'api_signature',
                        'label' => esc_html__( 'API Signature', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                    [
                        'id'             => 'test_mode',
                        'label'          => esc_html__( 'Test Mode', 'wp-booking-management-system' ),
                        'type'           => 'checkbox',
                        'std'            => '',
                        'checkbox_label' => esc_html__( "Yes, I want to enable PayPal Sandbox Mode", 'wp-booking-management-system' )
                    ],
                    [
                        'id'    => 'test_api_username',
                        'label' => esc_html__( 'Test API Username', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                    [
                        'id'    => 'test_api_password',
                        'label' => esc_html__( 'Test API Password', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                    [
                        'id'    => 'test_api_signature',
                        'label' => esc_html__( 'Test API Signature', 'wp-booking-management-system' ),
                        'type'  => 'text',
                        'std'   => '',
                    ],
                ];
                $this->gatewayObject = Omnipay::create( 'PayPal_Express' );

                parent::__construct();

                /**
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_before_order_information_table', [ $this, '_show_paynow_button' ] );
            }

            function _show_paynow_button( $order_object )
            {
                if ( $order_object->get_payment_gateway( 'id' ) == $this->gateway_id ) {

                    // Check Payment is not complete
                    if ( in_array( $order_object->get_status(), [ 'payment_failed', 'on_hold' ] ) ) {
                        // Now show the Paynow Button
                        $gateway = $this->gatewayObject;

                        if ( $this->is_test_mode() ) {
                            $gateway->setTestMode( true );
                            $gateway->setUsername( $this->get_option( 'test_api_username' ) );
                            $gateway->setPassword( $this->get_option( 'test_api_password' ) );
                            $gateway->setSignature( $this->get_option( 'test_api_signature' ) );
                        } else {

                            $gateway->setUsername( $this->get_option( 'api_username' ) );
                            $gateway->setPassword( $this->get_option( 'api_password' ) );
                            $gateway->setSignature( $this->get_option( 'api_signature' ) );
                        }

                        $total = $order_object->get_total();

                        $purchase = [
                            'amount'      => (float)$total,
                            'currency'    => WPBooking_Currency::get_current_currency( 'currency' ),
                            'description' => esc_html__( 'WPBooking', 'wp-booking-management-system' ),
                            'returnUrl'   => $this->get_return_url( $order_object->get_order_id() ),
                            'cancelUrl'   => $this->get_cancel_url( $order_object->get_order_id() ),
                        ];

                        $response = $gateway->purchase(
                            $purchase
                        )->send();

                        if ( !$response->isSuccessful() and $response->isRedirect() ) {

                            ?>
                            <a class="wb-btn wb-btn-blue wb-btn-md"
                               href="<?php echo esc_url( $response->getRedirectUrl() ) ?>"><?php echo esc_html__( 'Pay Now', 'wp-booking-management-system' ) ?></a>
                            <p class="mgt-10"><?php echo esc_html__( 'You will be redirected to PayPal.com to complete the payment', 'wp-booking-management-system' ) ?></p>
                            <?php
                        } else {
                            wpbooking_set_message( esc_html__( 'Paypal Error:', 'wp-booking-management-system' ) . ' ' . $response->getMessage(), 'error' );
                            echo wpbooking_get_message();
                        }
                    }

                }
            }

            function validate()
            {

                if ( $this->is_test_mode() ) {
                    if ( !$this->get_option( 'test_api_username' ) or !$this->get_option( 'test_api_password' ) or !$this->get_option( 'test_api_signature' ) ) {
                        wpbooking_set_message( esc_html__( 'Test PayPal API is incorrect! Please check with the Admin', 'wp-booking-management-system' ), 'error' );

                        return false;
                    }
                } else {

                    if ( !$this->get_option( 'api_username' ) or !$this->get_option( 'api_password' ) or !$this->get_option( 'api_signature' ) ) {
                        wpbooking_set_message( esc_html__( 'PayPal API is incorrect! Please check with the Admin', 'wp-booking-management-system' ), 'error' );

                        return false;
                    }
                }

                return true;
            }

            function do_checkout( $order_id )
            {

                if ( !$this->validate() ) {
                    return [
                        'status' => 0
                    ];
                }
                $order = new WB_Order( $order_id );

                $gateway = $this->gatewayObject;
                if ( $this->is_test_mode() ) {
                    $gateway->setTestMode( true );
                    $gateway->setUsername( $this->get_option( 'test_api_username' ) );
                    $gateway->setPassword( $this->get_option( 'test_api_password' ) );
                    $gateway->setSignature( $this->get_option( 'test_api_signature' ) );
                } else {

                    $gateway->setUsername( $this->get_option( 'api_username' ) );
                    $gateway->setPassword( $this->get_option( 'api_password' ) );
                    $gateway->setSignature( $this->get_option( 'api_signature' ) );
                }

                $total = WPBooking_Currency::convert_money( $order->get_total() );

                $purchase = [
                    'amount'      => (float)$total,
                    'currency'    => WPBooking_Currency::get_current_currency( 'currency' ),
                    'description' => esc_html__( 'WPBooking', 'wp-booking-management-system' ),
                    'returnUrl'   => $this->get_return_url( $order_id ),
                    'cancelUrl'   => $this->get_cancel_url( $order_id ),
                ];

                $response = $gateway->purchase(
                    $purchase
                )->send();

                if ( $response->isSuccessful() ) {
                    return [ 'status' => 1 ];
                } elseif ( $response->isRedirect() ) {
                    return [ 'status' => 1, 'redirect' => $response->getRedirectUrl() ];
                } else {
                    wpbooking_set_message( $response->getMessage(), 'error' );

                    return [ 'status' => false, 'data' => $purchase ];
                }
            }

            /**
             * Validate Return Data from PayPal
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $order_id
             *
             * @return bool
             */
            function complete_purchase( $order_id )
            {
                if ( !$this->validate() ) {
                    return false;
                }
                $order = new WB_Order( $order_id );

                $gateway = $this->gatewayObject;
                if ( $this->is_test_mode() ) {
                    $gateway->setTestMode( true );
                    $gateway->setUsername( $this->get_option( 'test_api_username' ) );
                    $gateway->setPassword( $this->get_option( 'test_api_password' ) );
                    $gateway->setSignature( $this->get_option( 'test_api_signature' ) );
                } else {

                    $gateway->setUsername( $this->get_option( 'api_username' ) );
                    $gateway->setPassword( $this->get_option( 'api_password' ) );
                    $gateway->setSignature( $this->get_option( 'api_signature' ) );
                }

                $total = WPBooking_Currency::convert_money( $order->get_total() );

                $purchase = [
                    'amount'      => (float)$total,
                    'currency'    => WPBooking_Currency::get_current_currency( 'currency' ),
                    'description' => esc_html__( 'WPBooking', 'wp-booking-management-system' ),
                    'returnUrl'   => $this->get_return_url( $order_id ),
                    'cancelUrl'   => $this->get_cancel_url( $order_id ),
                ];

                $response = $gateway->completePurchase(
                    $purchase
                )->send();

                if ( $response->isSuccessful() ) {
                    return true;

                } elseif ( $response->isRedirect() ) {
                    return false;
                } else {
                    return false;

                }
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        WPBooking_Paypal_Gateway::inst();
    }

