<?php
    if ( !class_exists( 'WB_Order' ) ) {
        class WB_Order
        {

            private $order_id = false;
            private $user_id = false;

            private $data = [];

            function __construct( $order_id )
            {

                $this->init( $order_id );
            }

            private function init( $order_id )
            {
                if ( !$order_id ) return;

                $this->order_id = $order_id;
                $this->user_id  = get_post_meta( $this->order_id, 'user_id', true );
                $this->data     = WPBooking_Order_Model::inst()->where( 'order_id', $this->order_id )->get()->row();
            }

            function get_order_id()
            {
                return $this->order_id;
            }

            function get_order_data()
            {
                return $this->data;
            }

            function get_order_room_data()
            {
                if ( $this->order_id ) {
                    $data = WPBooking_Order_Hotel_Order_Model::inst()->get_order( $this->order_id );

                    return $data;
                } else {
                    return false;
                }
            }

            /**
             * IF $need is specific, return the single value of customer of the order. Otherwise, return the array
             *
             * @since  1.0
             * @author dungdt
             *
             * @param bool|FALSE $need
             *
             * @return array|bool|string
             */
            function get_customer( $need = false )
            {
                if ( $this->user_id ) {
                    $udata         = get_userdata( $this->user_id );
                    $customer_info = [
                        'id'          => $this->user_id,
                        'name'        => ( !empty( $udata ) ? $udata->display_name : '' ),
                        'avatar'      => get_avatar( $this->user_id ),
                        'description' => ( !empty( $udata ) ? $udata->user_description : '' ),
                        'email'       => ( !empty( $udata ) ? $udata->user_email : '' )
                    ];

                    if ( $need ) {
                        switch ( $need ) {
                            case 'full_name':
                                $first_name = get_post_meta( $this->order_id, 'wpbooking_user_first_name', true );
                                $last_name  = get_post_meta( $this->order_id, 'wpbooking_user_last_name', true );
                                $full_name  = '';
                                if ( !empty( $first_name ) && !empty( $last_name ) ) {
                                    $full_name = $first_name . ' ' . $last_name;
                                }

                                return $full_name;
                                break;
                            case 'phone':
                                $phone = get_post_meta( $this->order_id, 'wpbooking_user_phone', true );

                                return !empty( $phone ) ? $phone : false;
                            case 'address':
                                $address = get_post_meta( $this->order_id, 'wpbooking_user_address', true );

                                return !empty( $address ) ? $address : false;
                            case 'email':
                                $email = get_post_meta( $this->order_id, 'wpbooking_user_email', true );

                                return !empty( $email ) ? $email : false;
                            case 'apt':
                                $apt = get_post_meta( $this->order_id, 'wpbooking_user_apt_unit', true );

                                return !empty( $apt ) ? $apt : '';
                            default:
                                return !empty( $customer_info[ $need ] ) ? $customer_info[ $need ] : false;
                        }

                    }

                    return $customer_info;
                }
            }

            function get_column_row_data()
            {

            }

            /**
             * Get Customer Email that received the booking email
             *
             * @since  1.0
             * @author dungdt
             *
             * @return mixed
             */
            function get_customer_email()
            {
                if ( $this->order_id ) {
                    //if ($this->user_id) return $this->get_customer('email');
                    // Try to get user email field
                    return get_post_meta( $this->order_id, 'wpbooking_user_email', true );
                }
            }


            /**
             * Get Order Total Money
             *
             * @since  1.0
             * @author quandq
             *
             * @param array $args
             *
             * @return mixed|void
             */
            function get_total( $args = [] )
            {
                if ( $this->order_id ) {
                    $order_data = $this->get_order_data();
                    $total      = $order_data[ 'price' ];
                    if ( !empty( $order_data[ 'deposit_price' ] ) ) {
                        $total = $order_data[ 'deposit_price' ];
                    }
                    if ( !empty( $args ) ) {
                        $total = $order_data[ 'price' ];
                        if ( !empty( $args[ 'without_deposit' ] ) ) {
                            $total = $order_data[ 'deposit_price' ];
                        }
                    }
                    $total = apply_filters( 'wpbooking_get_order_total', $total, $args );

                    return $total;
                }
            }


            function get_deposit_and_remain_html()
            {
                if ( $this->order_id ) {
                    $order_data = $this->get_order_data();
                    $total      = $order_data[ 'price' ];
                    $deposit    = $order_data[ 'deposit_price' ];
                    $remain     = $total - $deposit;
                    if ( !empty( $deposit ) ) {
                        $full_html = WPBooking_Currency::format_money( $deposit ) . ' / <strong>' . WPBooking_Currency::format_money( $remain ) . '</strong>';

                        return $full_html;
                    } else {
                        return false;
                    }
                }
            }

            /**
             * Do Create New Order
             *
             * @param            $cart
             * @param array      $form_billing_data
             * @param bool|FALSE $selected_gateway
             * @param bool|FALSE $customer_id
             *
             * @return int|WP_Error
             */
            function create( $cart, $form_billing_data = [], $selected_gateway = false, $customer_id = false )
            {
                $created    = time();
                $order_data = [
                    'post_title'  => sprintf( esc_html__( 'New Order In %s', 'wp-booking-management-system' ), date( get_option( 'date_format' ) . ' @' . get_option( 'time_format' ) ) ),
                    'post_type'   => 'wpbooking_order',
                    'post_status' => 'on_hold'
                ];
                $order_id   = wp_insert_post( $order_data );

                // Save Current Data
                $this->init( $order_id );

                if ( $order_id ) {

                    $booking = WPBooking_Checkout_Controller::inst();

                    $price = $booking->get_cart_total( [ 'without_deposit' => false, 'without_tax' => true ], $cart );

                    $deposit_price = $booking->get_cart_deposit();

                    $tax = $booking->get_cart_tax_price( $price );

                    $post_author   = get_post_field( 'post_author', $cart[ 'post_id' ] );
                    $cart[ 'tax' ] = $tax;

                    $extra_fees = [];
                    if ( !empty( $cart[ 'extra_fees' ] ) ) {
                        $extra_fees = $cart[ 'extra_fees' ];
                    }

                    update_post_meta( $order_id, 'post_id', $cart[ 'post_id' ] );
                    update_post_meta( $order_id, 'service_type', $cart[ 'service_type' ] );
                    update_post_meta( $order_id, 'price', $booking->get_total_price_cart_with_tax() ); // With Tax (With only Not Excluded Tax)
                    update_post_meta( $order_id, 'discount', $cart[ 'discount' ] );
                    update_post_meta( $order_id, 'extra_fees', $extra_fees );
                    update_post_meta( $order_id, 'tax', $tax );
                    update_post_meta( $order_id, 'tax_total', $tax[ 'tax_total' ] );
                    update_post_meta( $order_id, 'currency', WPBooking_Currency::get_current_currency( 'currency' ) );
                    update_post_meta( $order_id, 'check_in_timestamp', $cart[ 'check_in_timestamp' ] );
                    update_post_meta( $order_id, 'check_out_timestamp', $cart[ 'check_out_timestamp' ] );
                    update_post_meta( $order_id, 'user_id', $customer_id );
                    update_post_meta( $order_id, 'author_id', $post_author );
                    update_post_meta( $order_id, 'deposit_price', $deposit_price );
                    update_post_meta( $order_id, 'deposit', $cart[ 'deposit' ] );
                    update_post_meta( $order_id, 'created_at', $created );
                    update_post_meta( $order_id, 'payment_method', $selected_gateway );

                    if ( !empty( $cart ) ) {
                        foreach ( $cart as $key => $value ) {
                            update_post_meta( $order_id, 'wb_cart_' . $key, $value );
                        }
                    }
                    if ( !empty( $form_billing_data ) ) {
                        foreach ( $form_billing_data as $key => $value ) {
                            update_post_meta( $order_id, 'wpbooking_' . $key, $value[ 'value' ] );
                        }
                    }
                }


                WPBooking_Order_Model::inst()->save_order( $cart, $order_id, $customer_id );
                do_action( 'wpbooking_save_order_' . $cart[ 'service_type' ], $cart, $order_id );
                do_action( 'wpbooking_save_order', $cart, $order_id );

                return $order_id;
            }

            /**
             * Cancel All Order Items by Admin or Customer
             *
             * @since  1.0
             * @author dungdt
             *
             */
            function cancel_purchase()
            {
                if ( $this->order_id ) {

                    // Update Current Order
                    wp_update_post( [
                        'ID'          => $this->order_id,
                        'post_status' => 'cancelled'
                    ] );

                    // Update Status of Order Item in database
                    $order_model = WPBooking_Order_Model::inst();
                    $order_model->cancel_purchase( $this->order_id );
                }
            }

            /**
             * Complete all Order Items after validate by payment gateways
             *
             * @since  1.0
             * @author dungdt
             */
            function complete_purchase()
            {
                if ( $this->order_id ) {
                    // Update Current Order

                    $data = $this->get_order_data();

                    $status = 'completed';

                    if ( !empty( $data[ 'deposit_price' ] ) and $data[ 'deposit_price' ] > 0 and round($data[ 'deposit_price' ], 2) != round($data[ 'price' ], 2) ) {
                        $status = 'completed_a_part';
                    }

                    wp_update_post( [
                        'ID'          => $this->order_id,
                        'post_status' => $status
                    ] );
                    
                    // Update Status of Order Item in database
                    $order_model = WPBooking_Order_Model::inst();
                    $order_model->complete_purchase( $this->order_id, $status );

                    // Send Email
                    $order = new WB_Order( $this->order_id );
                    $order->send_email_after_booking( $this->order_id );
                }
            }

            /**
             * Can not validate data from Gateway or Data is not valid
             *
             * @since  1.0
             * @author dungdt
             *
             */
            function payment_failed()
            {
                if ( $this->order_id ) {

                    // Update Status
                    wp_update_post( [
                        'ID'          => $this->order_id,
                        'post_status' => 'payment_failed'
                    ] );

                    // Update Status of Order Item in database
                    $order_model = WPBooking_Order_Model::inst();
                    $order_model->where( 'order_id', $this->order_id )->update( [
                        'status' => 'payment_failed'
                    ] );
                }
            }

            /**
             * Get Tax Total
             *
             * @since  1.0
             * @author dungdt
             *
             * @return int|mixed|void
             */
            function get_tax_price()
            {
                $price = 0;

                return $price;
            }


            /**
             * Get Order PayNow Price
             *
             * @since  1.0
             * @author dungdt
             *
             * @return float
             */
            function get_paynow_price()
            {
                $price = $this->get_total();

                $price = apply_filters( 'wpbooking_get_order_paynow_price', $price );

                return $price;
            }

            /**
             * Get Status of Current Order
             *
             * @since  1.0
             * @author dungdt
             *
             * @return bool|false|string
             */
            function get_status()
            {
                if ( $this->order_id ) {
                    return get_post_status( $this->order_id );
                }

                return false;
            }

            /**
             * Get HTML of Order Status
             *
             * @since  1.0
             * @author dungdt
             *
             * @return string
             */
            function get_status_html()
            {
                $status = $this->get_status();
                if ( $status ) {
                    $all_status = WPBooking_Config::inst()->item( 'order_status' );
                    if ( array_key_exists( $status, $all_status ) ) {
                        switch ( $status ) {
                            case "on_hold":
                            case "payment_failed":
                                return sprintf( '<label class="bold text_up %s">%s</label>', $status, $all_status[ $status ][ 'label' ] );
                                break;
                            case "completed_a_part":
                            case "completed":
                                return sprintf( '<label class="bold text_up %s">%s</label>', $status, $all_status[ $status ][ 'label' ] );
                                break;
                            case "cancelled":
                            case "refunded":
                                return sprintf( '<label class="bold text_up cancelled">%s</label>', $all_status[ $status ][ 'label' ] );
                                break;

                            default:
                                return sprintf( '<label class="bold text_up %s">%s</label>', $status, $all_status[ $status ][ 'label' ] );
                                break;
                        }
                    } else {
                        return sprintf( '<label class="bold text_up">%s</label>', esc_html__( 'Unknown', 'wp-booking-management-system' ) );
                    }
                }
            }

            /**
             * Get HTML of Order Status
             *
             * @since  1.0
             * @author dungdt
             *
             * @return string
             */
            function get_status_email_html()
            {
                $status = $this->get_status();
                if ( $status ) {
                    $all_status = WPBooking_Config::inst()->item( 'order_status' );
                    if ( array_key_exists( $status, $all_status ) ) {
                        switch ( $status ) {
                            case "payment_failed":
                                return sprintf( '<label class="failed">%s</label>', strtoupper( $all_status[ $status ][ 'label' ] ) );
                                break;
                            case "completed":
                                return sprintf( '<label class="completed">%s</label>', strtoupper( $all_status[ $status ][ 'label' ] ) );
                                break;
                            case "on_hold":
                            case "cancelled":
                            case "refunded":
                                return sprintf( '<label class="on_hold">%s</label>', strtoupper( $all_status[ $status ][ 'label' ] ) );
                                break;
                            default:
                                return sprintf( '<label class="on_hold">%s</label>', strtoupper( $all_status[ $status ][ 'label' ] ) );
                                break;
                        }
                    } else {
                        return sprintf( '<label class="on_hold">%s</label>', esc_html__( 'Unknown', 'wp-booking-management-system' ) );
                    }
                }
            }

            /**
             * Get Booking Date
             *
             * @since  1.0
             * @author dungdt
             *
             * @param null $format
             *
             * @return false|string
             *
             * @author lncj
             * @since 1.9.6
             * Hide number night and show duration of tour. check for tour and accommodation
             */
            function get_booking_date( $format = null )
            {
                $full_time = false;

                if ( $this->order_id ) {
                    if ( !$format ) $format = get_option( 'date_format' );
                    $check_in  = $this->data[ 'check_in_timestamp' ];
                    $check_out = $this->data[ 'check_out_timestamp' ];

                    $start     = new DateTime( date( 'Y-m-d', $check_in ) );
                    if ( $check_out ) {
                        $end = new DateTime( date( 'Y-m-d', $check_out ) );
                    }

                    $service_type = $this->data['service_type'];
                    $service_id = $this->data['post_id'];

                    if($service_type!='tour'){
                        if ( !empty( $start ) and !empty( $end ) ) {
                            $full_time = date_i18n( $format, $check_in ) . '&nbsp; &rarr; &nbsp;' . date_i18n( $format, $check_out ) . ' <br>(' . sprintf( _n( '%s night', '%s nights', $start->diff( $end )->days, 'wp-booking-management-system' ), $start->diff( $end )->days ) . ')';
                        }

                        if ( !empty( $start ) and empty( $end ) ) {

                            $full_time = date_i18n( $format, $check_in );
                            if ( $duration = $this->get_meta( 'wb_duration' ) ) {
                                $full_time .= '<br>(' . $duration . ')';
                            }
                        }
                    }else{
                        $duration = get_post_meta($service_id,'duration',true);

                        if ( !empty( $start ) and !empty( $end ) ) {
                            $full_time = date_i18n( $format, $check_in ) . '&nbsp; &rarr; &nbsp;' . $duration;
                        }

                        if ( !empty( $start ) and empty( $end ) ) {

                            $full_time = date_i18n( $format, $check_in );
                            if ( $duration = $this->get_meta( 'wb_duration' ) ) {
                                $full_time .= '<br>(' . $duration . ')';
                            }
                        }
                    }

                    return apply_filters( 'wpbooking_order_get_booking_date', $full_time, $this );
                }
            }

            /**
             * Gate Gateway Info or Gateway Object
             *
             * @since  1.0
             * @author dungdt
             *
             * @param string $need
             *
             * @return bool|mixed|object|string
             */
            function get_payment_gateway( $need = 'label' )
            {

                if ( $this->order_id ) {
                    $gateway = get_post_meta( $this->order_id, 'payment_method', true );

                    if ( $gateway ) {
                        $gateway_object = WPBooking_Payment_Gateways::inst()->get_gateway( $gateway );
                        if ( $gateway_object ) {
                            if ( $need ) return $gateway_object->get_info( $need ); else return $gateway_object;
                        } else {
                            return $gateway;
                        }
                    } else {
                        return esc_html__( 'Unknown', 'wp-booking-management-system' );
                    }

                }
            }

            /**
             * Get service type
             *
             * @since  1.0
             * @author tienhd
             *
             * @return bool/string
             */
            function get_service_type()
            {
                if ( !empty( $this->data ) and is_array( $this->data ) ) {
                    return $this->data[ 'service_type' ];
                } else {
                    return false;
                }
            }

            /**
             * Get Order MEta
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $key
             *
             * @return mixed
             */
            function get_meta( $key )
            {
                if ( $this->order_id ) {
                    return get_post_meta( $this->order_id, $key, true );
                }
            }

            /**
             * Send Email After Booking
             *
             * @since  1.0
             * @author tienhd
             *
             * @param $order_id
             */
            function send_email_after_booking( $order_id )
            {
                do_action( 'wpbooking_send_email_after_checkout', $order_id );
            }

        }
    }