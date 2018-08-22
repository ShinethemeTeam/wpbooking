<?php
    if ( !function_exists( 'wpbooking_service_price' ) ) {
        function wpbooking_service_price( $post_id = false )
        {
            if ( !$post_id ) $post_id = get_the_ID();

            $service_type = get_post_meta( $post_id, 'service_type', true );

            if($service_type!='tour'){
                $base_price   = get_post_meta( $post_id, 'price', true );

            }else{
                $base_price   = get_post_meta( $post_id, 'base_price', true );
            }

            $base_price = apply_filters( 'wpbooking_service_base_price', $base_price, $post_id, $service_type );
            $base_price = apply_filters( 'wpbooking_service_base_price_' . $service_type, $base_price, $post_id, $service_type );

            return $base_price;
        }
    }
    if ( !function_exists( 'wpbooking_service_price_html' ) ) {
        function wpbooking_service_price_html( $post_id = false )
        {
            if ( !$post_id ) $post_id = get_the_ID();
            $post_id = wpbooking_origin_id( $post_id, get_post_type( $post_id ) );
            $price   = wpbooking_service_price( $post_id );

            $service_type = get_post_meta( $post_id, 'service_type', true );

            $price_html = WPBooking_Currency::format_money( $price );
            $price_html = sprintf( esc_html__( 'from %s/night', 'wp-booking-management-system' ), '<br><span class="price" itemprop="price">' . $price_html . '</span>' );

            $price_html = apply_filters( 'wpbooking_service_base_price_html', $price_html, $price, $post_id, $service_type );
            $price_html = apply_filters( 'wpbooking_service_base_price_html_' . $service_type, $price_html, $price, $post_id, $service_type );


            return $price_html;
        }
    }

    /**
     * @return string
     */
    if ( !function_exists( 'wpbooking_service_star_rating' ) ) {
        function wpbooking_service_star_rating( $post_id )
        {
            if ( empty( $post_id ) ) $post_id = get_the_ID();

            $hotel_star = get_post_meta( $post_id, 'star_rating', true );
            for ( $i = 1; $i <= 5; $i++ ) {
                $active = false;
                if ( $hotel_star >= $i ) $active = 'active';
                echo sprintf( '<span class="%s"><i class="fa fa-star-o wpbooking-icon-star"></i></span>', $active );
            }
            $star_rating = '<span>' . $hotel_star . ' ' . _n( 'star', 'stars', (int)$hotel_star, 'wp-booking-management-system' ) . '</span>';

            return $star_rating;
        }
    }

    if ( !function_exists( 'wpbooking_order_item_status_html' ) ) {
        function wpbooking_order_item_status_html( $status )
        {
            $all_status = WPBooking_Config::inst()->item( 'order_status' );
            if ( array_key_exists( $status, $all_status ) ) {
                switch ( $status ) {
                    case "on_hold":
                        return sprintf( '<label class="label label-warning">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                    case "completed":
                        return sprintf( '<label class="label label-success">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                    case "cancelled":
                    case "refunded":
                        return sprintf( '<label class="label label-danger">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;

                    default:
                        return sprintf( '<label class="label label-default">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                }
            } else {
                return sprintf( '<label class="label label-default">%s</label>', esc_html__( 'Unknown', 'wp-booking-management-system' ) );
            }
        }
    }
    if ( !function_exists( 'wpbooking_order_item_status_color' ) ) {
        function wpbooking_order_item_status_color( $status )
        {
            $all_status = WPBooking_Config::inst()->item( 'order_status' );
            if ( array_key_exists( $status, $all_status ) ) {
                switch ( $status ) {
                    case "on_hold":
                        return '#f0ad4e';
                        break;
                    case "completed":
                        return '#5cb85c';
                        break;
                    case "cancelled":
                    case "refunded":
                        return '#d9534f';
                        break;

                    default:
                        return '#5e5e5e';
                        break;
                }
            } else {
                return '#5e5e5e';
            }
        }
    }
    if ( !function_exists( 'wpbooking_payment_status_html' ) ) {
        function wpbooking_payment_status_html( $status )
        {

            // Pre-handle for old
            if ( $status == 'on-paying' ) $status = 'processing';

            $all_status = WPBooking_Config::inst()->item( 'order_status' );
            if ( array_key_exists( $status, $all_status ) ) {
                switch ( $status ) {
                    case "processing":
                        return sprintf( '<label class="label label-info">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                    case "completed":
                        return sprintf( '<label class="label label-success">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                    case "failed":
                        return sprintf( '<label class="label label-danger">%s</label>', $all_status[ $status ][ 'label' ] );
                        break;
                }
            } else {
                return sprintf( '<label class="label label-default">%s</label>', esc_html__( 'Unknown', 'wp-booking-management-system' ) );
            }
        }
    }
    if ( !function_exists( 'wpbooking_get_order_item_used_gateway' ) ) {
        function wpbooking_get_order_item_used_gateway( $payment_id = false )
        {

            $payment = WPBooking_Payment_Model::inst()->find( $payment_id );
            if ( $payment and !empty( $payment[ 'gateway' ] ) ) {
                $gateway = WPBooking_Payment_Gateways::inst()->get_gateway( $payment[ 'gateway' ] );
                if ( $gateway ) {
                    return $gateway->get_info( 'label' );
                } else {
                    return esc_html__( 'Unknown Gateway', 'wp-booking-management-system' );
                }

            }
        }
    }

    if ( !function_exists( 'wpbooking_post_query_desc' ) ) {
        function wpbooking_post_query_desc( $input = false )
        {
            if ( !$input ) $input = WPBooking_Input::get();

            $q = [];
            if ( !empty( $input[ 'location_id' ] ) and $location_id = $input[ 'location_id' ] ) {
                $location = get_term( $location_id, 'wpbooking_location' );
                if ( !is_wp_error( $location ) and $location )
                    $q[] = sprintf( esc_html__( 'in %s', 'wp-booking-management-system' ), '<span>' . $location->name . '</span>' );
            }
            if ( !empty( $input[ 'checkin_d' ] ) and $checkin_d = $input[ 'checkin_d' ] and !empty( $input[ 'checkin_m' ] ) and $checkin_m = $input[ 'checkin_m' ] and !empty( $input[ 'checkin_y' ] ) and $checkin_y = $input[ 'checkin_y' ] ) {
                $from_date = date( get_option( 'date_format' ), strtotime( $checkin_d . '-' . $checkin_m . '-' . $checkin_y ) );
                $q[]       = sprintf( esc_html__( 'from %s', 'wp-booking-management-system' ), '<span>' . $from_date . '</span>' );

                if ( !empty( $input[ 'checkout_d' ] ) and $checkout_d = $input[ 'checkout_d' ] and !empty( $input[ 'checkout_m' ] ) and $checkout_m = $input[ 'checkout_m' ] and !empty( $input[ 'checkout_y' ] ) and $checkout_y = $input[ 'checkout_y' ] ) {
                    $to_date = date( get_option( 'date_format' ), strtotime( $checkout_d . '-' . $checkout_m . '-' . $checkout_y ) );
                    $q[]     = sprintf( esc_html__( 'to %s', 'wp-booking-management-system' ), '<span>' . $to_date . '</span>' );
                }
            }

            $query_desc = false;
            if ( !empty( $q ) ) {
                foreach ( $q as $key => $val ) {
                    if ( $key == count( $q ) - 1 && count( $q ) > 1 ) {
                        $query_desc .= ' ';
                    }
                    $query_desc .= $val . ' ';
                }
            }

            return apply_filters( 'wpbooking_service_post_query_desc', $query_desc, $q, $input );
        }
    }
    if ( !function_exists( 'wpbooking_get_service' ) ) {
        function wpbooking_get_service( $post_id = false )
        {
            return WPBooking_Service_Controller::inst()->get_service_instance( $post_id );
        }
    }

    if ( !function_exists( 'wpbooking_pagination_room' ) ) {
        function wpbooking_pagination_room( $c_wp_query = false )
        {
            global $wp_query;
            if ( !empty( $c_wp_query ) ) {
                $wp_query = $c_wp_query;
            }
            if ( $wp_query->max_num_pages < 2 ) {
                return;
            }
            $paged          = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
            $max            = $wp_query->found_posts;
            $posts_per_page = $wp_query->query[ 'posts_per_page' ];
            $number         = ceil( $max / $posts_per_page );
            $html           = ' <div class="wpbooking-pagination paged_room">';
            if ( $paged > 1 ) {
                $html .= ' <a class="page-numbers" data-page="' . ( $paged - 1 ) . '">' . __( 'Previous', "wp-booking-management-system" ) . '</a> ';
            }
            for ( $i = 1; $i <= $number; $i++ ) {
                if ( $i == $paged ) {
                    $html .= ' <span class="page-numbers current" data-page="' . $i . '">' . $i . '</span> ';
                } else {
                    $html .= ' <a class="page-numbers" data-page="' . $i . '">' . $i . '</a> ';
                }
            }
            if ( $paged < $i - 1 ) {
                $html .= ' <a class="page-numbers" data-page="' . ( $paged + 1 ) . '">' . __( 'Next', "wp-booking-management-system" ) . '</a>';
            }
            $html .= '</div>';

            return $html;
        }
    }

