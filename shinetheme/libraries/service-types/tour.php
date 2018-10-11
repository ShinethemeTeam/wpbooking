<?php
    if ( !class_exists( 'WPBooking_Tour_Service_Type' ) and class_exists( 'WPBooking_Abstract_Service_Type' ) ) {
        class WPBooking_Tour_Service_Type extends WPBooking_Abstract_Service_Type
        {
            static $_inst = false;

            protected $type_id = 'tour';
            protected $table_availability = 'wpbooking_availability_tour';

            function __construct()
            {
                $this->type_info = [
                    'label'  => esc_html__( "Tour", 'wp-booking-management-system' ),
                    'labels' => esc_html__( "Tours", 'wp-booking-management-system' ),
                    'desc'   => esc_html__( 'You can post anything related to activities such as tourism, events, workshops, etc anything called tour', 'wp-booking-management-system' )
                ];

                $this->settings = [

                    [
                        'id'    => 'title',
                        'label' => esc_html__( 'Layout', 'wp-booking-management-system' ),
                        'type'  => 'title',
                    ],
                    [
                        'id'    => 'posts_per_page',
                        'label' => esc_html__( "Item per page", 'wp-booking-management-system' ),
                        'type'  => 'number',
                        'std'   => 10
                    ],
                    [
                        'id'    => "thumb_size",
                        'label' => esc_html__( "Thumb Size", 'wp-booking-management-system' ),
                        'type'  => 'image-size'
                    ],
                    [
                        'id'    => "gallery_size",
                        'label' => esc_html__( "Gallery Size", 'wp-booking-management-system' ),
                        'type'  => 'image-size'
                    ],
                ];

                parent::__construct();


                add_filter( 'wpbooking_archive_loop_image_size', [ $this, '_apply_thumb_size' ], 10, 3 );


                /**
                 * Register metabox fields
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'init', [ $this, '_register_meta_fields' ] );


                /**
                 * Register Tour Type
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'init', [ $this, '_register_tour_type' ] );

                /**
                 * Change Base Price Format
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_service_base_price_html_' . $this->type_id, [ $this, '_edit_price' ], 10, 4 );
                add_action( 'wpbooking_service_base_price_' . $this->type_id, [ $this, '_edit_base_price' ], 10, 3 );

                /**
                 * Filter to Validate Add To Cart
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_filter( 'wpbooking_add_to_cart_validate_' . $this->type_id, [ $this, '_add_to_cart_validate' ], 10, 4 );

                /**
                 * Filter to Change Cart Params
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_filter( 'wpbooking_cart_item_params_' . $this->type_id, [ $this, '_change_cart_params' ], 10, 2 );


                /**
                 * Show More info in Cart Total Box
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_check_out_total_item_information_' . $this->type_id, [ $this, '_add_total_box_info' ] );


                /**
                 * Show More info in Order Total Box
                 *
                 * @since  1.0
                 * @author dungdt
                 *
                 */
                add_action( 'wpbooking_order_detail_total_item_information_' . $this->type_id, [ $this, '_add_order_total_box_info' ] );


                /**
                 * Change Cart Total Price
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_get_cart_total_' . $this->type_id, [ $this, '_change_cart_total' ], 10, 4 );

                /**
                 * Show Tour Info
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_review_after_address_' . $this->type_id, [ $this, '_show_review_tour_info' ] );

                /**
                 * Show Order Info after Address
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_order_detail_after_address_' . $this->type_id, [ $this, '_show_order_info_after_address' ] );


                /**
                 * Show More Order Info for Email
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_email_order_after_address_' . $this->type_id, [ $this, '_show_email_order_info_after_address' ] );

                /**
                 * Show More Order Info for Booking Admin
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_action( 'wpbooking_admin_after_order_detail_other_info_' . $this->type_id, [ $this, '_show_order_info_after_order_detail_in_booking_admin' ], 10, 2 );


                /**
                 * Get Min and Max Price
                 *
                 * @since  1.0
                 * @author quandq
                 */
                add_filter( 'wpbooking_min_max_price_' . $this->type_id, [ $this, '_change_min_max_price' ], 10, 1 );

                /**
                 * Update Metabox min_price Tour
                 *
                 * @since  1.3
                 * @author quandq
                 */
                add_action( 'save_post', [ $this, '_update_min_price_tour' ] );
                add_action( 'wpbooking_save_metabox_section', [ $this, '_update_min_price_tour' ] );
                add_action( 'wpbooking_after_add_availability', [ $this, '_update_min_price_tour' ] );

                /**
                 * @since   1.5
                 * @updated 1.5
                 * @author  haint
                 */
                add_action( 'wpbooking_review_before_address', [ $this, 'before_address_checkout' ] );

                /**
                 * @since   1.6
                 * @updated 1.6
                 * @author  haint
                 */
                add_action( 'wp_ajax_wpbooking_get_availability_tour', [ $this, 'wpbooking_get_availability_tour' ] );
                add_action( 'wp_ajax_nopriv_wpbooking_get_availability_tour', [ $this, 'wpbooking_get_availability_tour' ] );

                add_filter( 'wpbooking_table_availability', [ $this, '__set_availability_table' ] );
            }

            /**
             * @since   1.7
             * @updated 1.7
             *
             * @param $table
             *
             * @return string
             */
            public function __set_availability_table( $table )
            {
                if ( $this->type_id == 'tour' ) {
                    $table = $this->table_availability;
                }

                return $table;
            }

            /**
             * @since   1.6
             * @updated 1.6
             * @author  haint
             */
            public function wpbooking_get_availability_tour()
            {
                check_ajax_referer( 'wpbooking-nonce-field', 'security' );
                $post_id            = (int)WPBooking_Input::post( 'post_id', '' );
                $start              = strtotime( WPBooking_Input::post( 'start', '' ) );
                $end                = strtotime( WPBooking_Input::post( 'end', '' ) );
                $events[ 'events' ] = [];
                $list_date          = $list_price = [];
                $post_origin        = wpbooking_origin_id( $post_id, 'wpbooking_service' );
                $availability       = $this->get_availability( $start, $end, $post_origin );
                $order              = $this->get_order( $start, $end, $post_origin );

                $tour_unit    = get_post_meta( $post_id, 'pricing_type', true );
                $max_people   = (int)get_post_meta( $post_id, 'max_guests', true );
                $adult_price  = $child_price = $infant_price = $price = 0;
                $onoff_people = (array)get_post_meta( $post_id, 'onoff_people', true );
                if ( !empty( $availability ) ) {
                    foreach ( $availability as $key => $item ) {
                        $status = $item->status;
                        if ( $status == 'available' ) {
                            $_event = esc_html__( 'Available', 'wp-booking-management-system' );
                        } else {
                            $_event = esc_html__( 'Unavailable', 'wp-booking-management-system' );
                        }
                        $events[ 'events' ][] = [
                            'start'  => date( 'Y-m-d', $item->start ),
                            'end'    => date( 'Y-m-d', $item->end ),
                            'event'  => $_event,
                            'status' => $item->status
                        ];

                        if ( $item->start == $item->end ) {
                            $list_date[] = $item->start;
                            if ( $tour_unit == 'per_person' ) {
                                $list_price[ $item->start ] = '';
                                if ( !in_array( 'adult', $onoff_people ) ) {
                                    $list_price[ $item->start ] .= esc_html__( 'Adult', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->adult_price ) . '<br/>';
                                }
                                if ( !in_array( 'child', $onoff_people ) ) {
                                    $list_price[ $item->start ] .= esc_html__( 'Children', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->child_price ) . '<br/>';
                                }
                                if ( !in_array( 'infant', $onoff_people ) ) {
                                    $list_price[ $item->start ] .= esc_html__( 'Infant', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->infant_price ) . '<br/>';
                                }
                            } else {
                                $list_price[ $item->start ] = WPBooking_Currency::format_money( $item->calendar_price );
                            }
                        } else {
                            for ( $i = $item->start; $i <= $item->end; $i = strtotime( '+1 day', $i ) ) {
                                $list_date[] = $i;
                                if ( $tour_unit == 'per_person' ) {
                                    $list_price[ $item->start ] = '';
                                    if ( !in_array( 'adult', $onoff_people ) ) {
                                        $list_price[ $item->start ] .= esc_html__( 'Adult', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->adult_price ) . '<br/>';
                                    }
                                    if ( !in_array( 'child', $onoff_people ) ) {
                                        $list_price[ $item->start ] .= esc_html__( 'Children', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->child_price ) . '<br/>';
                                    }
                                    if ( !in_array( 'infant', $onoff_people ) ) {
                                        $list_price[ $item->start ] .= esc_html__( 'Infant', 'wp-booking-management-system' ) . ': ' . WPBooking_Currency::format_money( $item->infant_price ) . '<br/>';
                                    }
                                } else {
                                    $list_price[ $i ] = WPBooking_Currency::format_money( $item->calendar_price );
                                }
                            }
                        }
                    }
                }
                for ( $i = $start; $i <= $end; $i = strtotime( '+1 day', $i ) ) {
                    if ( !in_array( $i, $list_date ) ) {
                        $events[ 'events' ][] = [
                            'start'  => date( 'Y-m-d', $i ),
                            'end'    => date( 'Y-m-d', $i ),
                            'event'  => esc_html__( 'Unavailable', 'wp-booking-management-system' ),
                            'status' => 'not_available'
                        ];
                    } else {
                        if ( !empty( $order ) ) {
                            $total_booked = 0;
                            foreach ( $order as $key => $item ) {
                                if ( $i >= (int)$item->check_in_timestamp && $i <= (int)$item->check_out_timestamp && !in_array( $item->status, [ 'cancelled', 'payment_failed', 'refunded' ] ) ) {
                                    $total_booked += (int)$item->adult_number + (int)$item->children_number + (int)$item->infant_number;
                                }
                            }
                            if ( $total_booked < $max_people ) {
                                $events[ 'events' ][] = [
                                    'start'  => date( 'Y-m-d', $i ),
                                    'end'    => date( 'Y-m-d', $i ),
                                    'event'  => ( isset( $list_price[ $i ] ) ) ? $list_price[ $i ] : esc_html__( 'Available', 'wp-booking-management-system' ),
                                    'status' => 'available'
                                ];
                            } else {
                                $events[ 'events' ][] = [
                                    'start'  => date( 'Y-m-d', $i ),
                                    'end'    => date( 'Y-m-d', $i ),
                                    'event'  => esc_html__( 'Unavailable', 'wp-booking-management-system' ),
                                    'status' => 'not_available'
                                ];
                            }
                        } else {
                            $events[ 'events' ][] = [
                                'start'  => date( 'Y-m-d', $i ),
                                'end'    => date( 'Y-m-d', $i ),
                                'event'  => ( isset( $list_price[ $i ] ) ) ? $list_price[ $i ] : esc_html__( 'Available', 'wp-booking-management-system' ),
                                'status' => 'available'
                            ];
                        }
                    }
                }
                echo json_encode( $events );
                die;
            }

            public function get_availability( $check_in, $check_out, $base_id )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'wpbooking_availability_tour';
                $sql   = "SELECT * FROM {$table} WHERE base_id = {$base_id} AND ( ( CAST( `start` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( `start` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( `end` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( `end` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) ) ";

                $result = $wpdb->get_results( $sql );

                return $result;
            }

            public function get_order( $check_in, $check_out, $base_id )
            {
                global $wpdb;
                $table = $wpdb->prefix . 'wpbooking_order';
                $sql   = "SELECT * FROM {$table} WHERE post_id = {$base_id} AND ( ( CAST( check_in_timestamp AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( check_in_timestamp AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( check_out_timestamp AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( check_out_timestamp AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) ) ";

                $result = $wpdb->get_results( $sql );

                return $result;
            }

            /**
             * @since   1.5
             * @updated 1.5
             * @author  haint
             *
             * @param $cart
             */
            public function before_address_checkout( $cart )
            {
                if ( $cart[ 'service_type' ] == 'tour' ) {
                    ?>
                    <div class="wb-hotel-star">
                        <?php
                            $service = wpbooking_get_service( $cart[ 'post_id' ] );
                            $service->get_star_rating_html();
                        ?>
                    </div>
                    <?php
                }
            }

            /**
             * Get Min and Max Price
             *
             * @since  1.0
             * @author quandq
             *
             * @param array $args
             *
             * @return array
             */
            function _change_min_max_price( $args = [] )
            {

                $service = WPBooking_Service_Model::inst();

                global $wpdb;

                $service->select( "
            (
                CASE
                    WHEN wpb_meta.meta_value = 'per_person'
                    THEN
                          (CAST(avail.adult_price AS DECIMAL))
                ELSE
                            CAST(avail.calendar_price AS DECIMAL)
                END
            ) as base_price

            " )
                    ->join( 'posts', 'posts.ID=' . $service->get_table_name( false ) . '.post_id' )
                    ->join( 'postmeta as wpb_meta', $wpdb->prefix . 'posts.ID=wpb_meta.post_id and wpb_meta.meta_key = \'pricing_type\'' )
                    ->join( 'wpbooking_availability_tour AS avail', $wpdb->prefix . 'posts.ID= avail.post_id ' );


                $service->where( 'avail.start >', strtotime( 'today' ) );
                $service->where( 'service_type', $this->type_id );
                $service->where( 'enable_property', 'on' );
                $service->where( $wpdb->prefix . 'posts.post_status', 'publish' );
                $service->where( $wpdb->prefix . 'posts.post_type', 'wpbooking_service' );

                $sql = 'SELECT
                        MIN(base_price) as min,
                        MAX(base_price) as max
                    FROM
                        (' . $service->_get_query() . ') as wpb_table';
                $service->_clear_query();

                $res = $wpdb->get_row( $sql, 'ARRAY_A' );
                if ( !is_wp_error( $res ) ) {
                    if ( $res[ 'min' ] == $res[ 'max' ] ) {
                        $res[ 'min' ] = 0;
                    }
                    $args = $res;
                }

                return $args;
            }

            /**
             * Show Other Info Order In Booking Admin
             *
             * @since  1.0
             * @author quandq
             *
             * @param $service_id
             * @param $order_data
             */
            function _show_order_info_after_order_detail_in_booking_admin( $order_id, $order_data )
            {
                if ( !empty( $order_data[ 'raw_data' ] ) ) {
                    $raw_data = json_decode( $order_data[ 'raw_data' ] );
                    if ( !empty( $raw_data->pricing_type ) ) {
                        if ( !empty( $raw_data->adult_number ) ) {
                            $calendar_price = ( $raw_data->pricing_type == 'per_person' ) ? $raw_data->calendar->adult_price : $raw_data->calendar->calendar_price;
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Adult', 'wp-booking-management-system' ) . ' x ' . esc_html( $raw_data->adult_number ) . '</strong></span>';
                            echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $calendar_price ) . '</span>';
                            echo '</li>';
                        }
                        if ( !empty( $raw_data->children_number ) ) {
                            $calendar_price = ( $raw_data->pricing_type == 'per_person' ) ? $raw_data->calendar->child_price : $raw_data->calendar->calendar_price;
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Children', 'wp-booking-management-system' ) . ' x ' . esc_html( $raw_data->children_number ) . '</strong></span>';
                            echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $calendar_price ) . '</span>';
                            echo '</li>';
                        }
                        if ( !empty( $raw_data->infant_number ) ) {
                            $calendar_price = ( $raw_data->pricing_type == 'per_person' ) ? $raw_data->calendar->infant_price : $raw_data->calendar->calendar_price;
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Infant', 'wp-booking-management-system' ) . ' x ' . esc_html( $raw_data->infant_number ) . '</strong></span>';
                            echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $calendar_price ) . '</span>';
                            echo '</li>';
                        }
                    }

                    $extra_fees = unserialize( $order_data[ 'extra_fees' ] );
                    if ( !empty( $extra_fees ) ) {
                        foreach ( $extra_fees as $k => $v ) {
                            if ( !empty( $v[ 'data' ] ) ) {
                                echo '<li class=""><span class="wb-room-name"><strong>' . $v[ 'title' ] . '</strong></span>';
                                echo '</li>';
                                foreach ( $v[ 'data' ] as $key => $value ) {
                                    echo '<li class="wb-room-item"><span class="wb-room-name"><strong>&nbsp&nbsp&nbsp&nbsp' . $value[ 'title' ] . ' x ' . $value[ 'quantity' ] . '</strong></span>';
                                    echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $value[ 'quantity' ] * $value[ 'price' ] ) . '</span>';
                                    echo '</li>';
                                }
                            }
                        }
                    }
                }
            }

            /**
             * Show More info in Cart Total Box
             *
             * @since  1.0
             * @author dungdt
             *
             * @param array $cart
             */

            public function _add_total_box_info( $cart )
            {
                if ( $cart[ 'price' ] ) {
                    echo '<span class="total-title">' . esc_html__( 'Tour Price', 'wp-booking-management-system' ) . '</span>
                      <span class="total-amount">' . WPBooking_Currency::format_money( $cart[ 'price' ] ) . '</span>';

                }
            }

            /**
             * Show More info in Order Total Box
             *
             * @since  1.0
             * @author dungdt
             *
             * @param array $order_data
             */
            public function _add_order_total_box_info( $order_data )
            {
                if ( !empty( $order_data[ 'raw_data' ] ) ) {
                    $raw_data = json_decode( $order_data[ 'raw_data' ], true );
                    if ( $raw_data ) {
                        $raw_data[ 'price' ] = $order_data[ 'price' ];
                        $tax_data            = unserialize( $order_data[ 'tax' ] );

                        if ( !empty( $tax_data ) ) {
                            foreach ( $tax_data as $tax ) {
                                if ( !empty( $tax[ 'excluded' ] ) and $tax[ 'excluded' ] == 'yes_not_included' ) {
                                    $raw_data[ 'price' ] = $order_data[ 'price' ] - $order_data[ 'tax_total' ];
                                }
                            }
                        }
                        $this->_add_total_box_info( $raw_data );
                    }
                }
            }

            public function get_discount_by_people( $tour_id, $price, $number_people = 1 )
            {
                $ranges = get_post_meta( $tour_id, 'discount_by_no_people', true );
                if ( !empty( $ranges ) ) {
                    $ranges = $this->_sort_range_list_item( $ranges, 'no_people' );
                    foreach ( $ranges as $key => $range ) {
                        if ( $number_people >= (float)$range[ 'no_people' ] ) {
                            $price = $price - ( $price * $range[ 'price' ] / 100 );
                            break;
                        }
                    }
                }

                return $price;
            }

            public function _sort_range_list_item( $list, $key = 'price' )
            {
                $size = count( $list );
                for ( $i = 0; $i <= $size - 1; $i++ ) {
                    for ( $j = $i + 1; $j < $size; $j++ ) {
                        if ( (float)$list[ $i ][ $key ] < (float)$list[ $j ][ $key ] ) {
                            $tmp        = $list[ $i ];
                            $list[ $i ] = $list[ $j ];
                            $list[ $j ] = $tmp;
                        }
                    }
                }

                return $list;
            }

            /**
             * Change Cart Total Price
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $price
             * @param $cart
             *
             * @return float
             */
            public function _change_cart_total( $price, $cart )
            {
                $cart = wp_parse_args( $cart, [
                    'pricing_type'    => '',
                    'adult_number'    => '',
                    'children_number' => '',
                    'infant_number'   => '',
                    'calendar'        => []
                ] );
                switch ( $cart[ 'pricing_type' ] ) {
                    case "per_unit":
                        if ( !empty( $cart[ 'calendar' ][ 'calendar_price' ] ) ) {
                            $price = $cart[ 'calendar' ][ 'calendar_price' ];
                        }
                        break;
                    case "fixed_people":
                        if ( !empty( $cart[ 'calendar' ][ 'calendar_price' ] ) ) {
                            $price        = $cart[ 'calendar' ][ 'calendar_price' ];
                            $total_people = (int)$cart[ 'adult_number' ] + (int)$cart[ 'children_number' ] + (int)$cart[ 'infant_number' ];
                            $price        *= $total_people;
                        }
                        break;
                    case "per_person":
                    default:
                        if ( !empty( $cart[ 'calendar' ] ) and is_array( $cart[ 'calendar' ] ) ) {
                            $calendar = wp_parse_args( $cart[ 'calendar' ], [
                                'adult_price'  => '',
                                'child_price'  => '',
                                'infant_price' => ''
                            ] );
                            $price    = 0;
                            if ( !empty( $cart[ 'adult_number' ] ) ) {
                                $price += $calendar[ 'adult_price' ] * $cart[ 'adult_number' ];
                            }
                            if ( !empty( $cart[ 'children_number' ] ) ) {
                                $price += $calendar[ 'child_price' ] * $cart[ 'children_number' ];
                            }
                            if ( !empty( $cart[ 'infant_number' ] ) ) {
                                $price += $calendar[ 'infant_price' ] * $cart[ 'infant_number' ];
                            }
                        }
                        break;
                }
                $people = (int)$cart[ 'adult_number' ] + (int)$cart[ 'children_number' ] + (int)$cart[ 'infant_number' ];
                $price  = $this->get_discount_by_people( $cart[ 'post_id' ], $price, $people );
                if ( !empty( $cart[ 'extra_fees' ] ) ) {
                    foreach ( $cart[ 'extra_fees' ] as $k => $v ) {
                        if ( !empty( $v[ 'data' ] ) ) {
                            foreach ( $v[ 'data' ] as $extra ) {
                                $price += $extra[ 'quantity' ] * $extra[ 'price' ];
                            }
                        }
                    }
                }

                return $price;
            }

            /**
             * Show Order Info after Address
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $order_data
             */
            public function _show_order_info_after_address( $order_data )
            {
                if ( !empty( $order_data[ 'raw_data' ] ) ) {
                    $raw_data = json_decode( $order_data[ 'raw_data' ], true );
                    if ( $raw_data ) {
                        $raw_data[ 'price' ] = 0;
                        $this->show_review_tour_info( $raw_data, false );
                    }
                }
            }

            /**
             * Show More Order Info for Email
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $order_data
             */
            public function _show_email_order_info_after_address( $order_data )
            {
                if ( !empty( $order_data[ 'raw_data' ] ) ) {
                    $raw_data = json_decode( $order_data[ 'raw_data' ], true );
                    if ( $raw_data ) {
                        $raw_data[ 'price' ] = 0;
                        $this->show_review_tour_info( $raw_data, false );
                    }
                }
            }

            /**
             * To show Tour More information
             *
             * @param $cart
             */
            protected function show_review_tour_info( $cart, $is_checkout = true )
            {


                // Price
                if ( !empty( $cart[ 'price' ] ) ) {
                    printf( '<span class="review-order-item-price tour-price">%s</span>', WPBooking_Currency::format_money( $cart[ 'price' ] ) );
                }


                $contact_meta = [
                    'contact_number' => 'fa-phone',
                    'contact_email'  => 'fa-envelope',
                    'website'        => 'fa-home',
                ];
                $html         = '';
                foreach ( $contact_meta as $key => $val ) {
                    if ( $value = get_post_meta( $cart[ 'post_id' ], $key, true ) ) {
                        switch ( $key ) {
                            case 'contact_number':
                                $value = sprintf( '<a href="tel:%s">%s</a>', esc_html( $value ), esc_html( $value ) );
                                break;

                            case 'contact_email':
                                $value = sprintf( '<a href="mailto:%s">%s</a>', esc_html( $value ), esc_html( $value ) );
                                break;
                            case 'website';
                                $value = '<a target=_blank href="' . $value . '">' . $value . '</a>';
                                break;
                        }
                        $html .= '<li class="wb-meta-contact">
                                    <i class="fa ' . $val . ' wb-icon-contact"></i>
                                    <span>' . $value . '</span>
                                </li>';
                    }
                }
                if ( !empty( $html ) ) {
                    echo '<ul class="wb-contact-list">' . $html . '</ul>';
                }
                printf( '<div class="people-price-item bold"><span class="head-item">%s</span></div>', esc_html__( "Booking Info", 'wp-booking-management-system' ) );
                // From
                if ( !empty( $cart[ 'check_in_timestamp' ] ) ) {
                    $from_detail = date_i18n( get_option( 'date_format' ), $cart[ 'check_in_timestamp' ] );
                    if ( !empty( $cart[ 'duration' ] ) ) {
                        $from_detail .= ' (' . $cart[ 'duration' ] . ')';
                    }
                    printf( '<div class="from-detail"><span class="head-item">%s:</span> <span class="from-detail-duration">%s</span></div>', esc_html__( 'From', 'wp-booking-management-system' ), $from_detail );
                }
                switch ( $cart[ 'pricing_type' ] ) {
                    case "per_unit":
                    case "fixed_people":
                        if ( !empty( $cart[ 'adult_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Adult(s)', 'wp-booking-management-system' ), $cart[ 'adult_number' ] );
                        }
                        if ( !empty( $cart[ 'children_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Children', 'wp-booking-management-system' ), $cart[ 'children_number' ] );
                        }
                        if ( !empty( $cart[ 'infant_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Infant(s)', 'wp-booking-management-system' ), $cart[ 'infant_number' ] );
                        }
                        break;
                    case "per_person":
                    default:
                        if ( !empty( $cart[ 'calendar' ] ) ) {
                            $calendar = wp_parse_args( $cart[ 'calendar' ], [
                                'adult_price'  => '',
                                'child_price'  => '',
                                'infant_price' => ''
                            ] );
                            if ( !empty( $cart[ 'adult_number' ] ) ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Adult(s)', 'wp-booking-management-system' ), $cart[ 'adult_number' ], WPBooking_Currency::format_money( $calendar[ 'adult_price' ] ), WPBooking_Currency::format_money( $calendar[ 'adult_price' ] * $cart[ 'adult_number' ] ) );
                            }
                            if ( !empty( $cart[ 'children_number' ] ) ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Children', 'wp-booking-management-system' ), $cart[ 'children_number' ], WPBooking_Currency::format_money( $calendar[ 'child_price' ] ), WPBooking_Currency::format_money( $calendar[ 'child_price' ] * $cart[ 'children_number' ] ) );
                            }
                            if ( !empty( $cart[ 'infant_number' ] ) ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Infant(s)', 'wp-booking-management-system' ), $cart[ 'infant_number' ], WPBooking_Currency::format_money( $calendar[ 'infant_price' ] ), WPBooking_Currency::format_money( $calendar[ 'infant_price' ] * $cart[ 'infant_number' ] ) );
                            }
                        }
                        break;
                }


                if ( !empty( $cart[ 'extra_fees' ] ) ) {
                    $extra_fees = $cart[ 'extra_fees' ];
                    foreach ( $extra_fees as $k => $v ) {
                        if ( !empty( $v[ 'data' ] ) ) {
                            printf( '<div class="people-price-item bold"><span class="head-item">%s</span></div>', $v[ 'title' ] );
                            foreach ( $v[ 'data' ] as $key => $value ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', $value[ 'title' ], $value[ 'quantity' ], WPBooking_Currency::format_money( $value[ 'price' ] ), WPBooking_Currency::format_money( $value[ 'price' ] * $value[ 'quantity' ] ) );
                            }
                        }
                    }
                }

                if ( $is_checkout ) {
                    $url_change_date = add_query_arg( [
                        'start_date' => $cart[ 'check_in_timestamp' ],
                    ], get_permalink( $cart[ 'post_id' ] ) );
                    ?>
                    <small><a href="<?php echo esc_url( $url_change_date ) ?>"
                              class="change-date"><?php echo esc_html__( "Change Date", "wp-booking-management-system" ) ?></a>
                    </small>
                    <?php
                }
            }

            /**
             * Callback to show Tour Info
             *
             * @since  1.0
             * @author dungdt
             */
            public function _show_review_tour_info( $cart )
            {
                $cart[ 'price' ] = WPBooking_Checkout_Controller::inst()->get_cart_total();

                $this->show_review_tour_info( $cart );
            }

            /**
             * Callback to Change Cart Params
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $cart_params
             * @param $post_id
             *
             * @return mixed
             */
            public function _change_cart_params( $cart_params, $post_id )
            {

                $day         = $this->post( 'checkin_d' );
                $month       = $this->post( 'checkin_m' );
                $year        = $this->post( 'checkin_y' );
                $post_origin = wpbooking_origin_id( $post_id, 'wpbooking_service' );

                $cart_params[ 'check_in_timestamp' ]  = strtotime( $year . '-' . $month . '-' . $day );
                $cart_params[ 'check_out_timestamp' ] = strtotime( $year . '-' . $month . '-' . $day );
                $cart_params[ 'adult_number' ]        = $this->post( 'adult_number' );
                $cart_params[ 'children_number' ]     = $this->post( 'children_number' );
                $cart_params[ 'infant_number' ]       = $this->post( 'infant_number' );
                $cart_params[ 'pricing_type' ]        = get_post_meta( $post_origin, 'pricing_type', true );
                $cart_params[ 'duration' ]            = get_post_meta( $post_id, 'duration', true );

                $post_extras              = $this->post( 'wpbooking_extra_service' );
                $extra_service            = [];
                $extra_service[ 'title' ] = esc_html__( 'Extra Service', 'wp-booking-management-system' );
                $my_extra_services        = get_post_meta( $post_id, 'extra_services', true );

                if ( !empty( $post_extras ) ) {
                    foreach ( $post_extras as $key => $value ) {
                        $price = 0;
                        $title = '';
                        foreach ( $my_extra_services as $key1 => $value2 ) {
                            if ( sanitize_title( $value2[ 'is_selected' ] ) == $key ) {
                                $price = $value2[ 'money' ];
                                $title = $value2[ 'is_selected' ];
                            }
                        }
                        if ( $value[ 'quantity' ] and $value[ 'quantity' ] > 0 ) {
                            $extra_service[ 'data' ][ $key ] = [
                                'title'    => $title,
                                'quantity' => $value[ 'quantity' ],
                                'price'    => $price
                            ];
                        }
                    }
                }
                // Check require
                if ( !empty( $my_extra_services ) ) {
                    foreach ( $my_extra_services as $key => $value ) {
                        if ( $value[ 'require' ] == 'yes' and empty( $extra_service[ 'data' ][ sanitize_title( $value[ 'is_selected' ] ) ] ) ) {
                            $extra_service[ 'data' ][ sanitize_title( $value[ 'is_selected' ] ) ] = [
                                'title'    => $value[ 'is_selected' ],
                                'quantity' => 1,
                                'price'    => $value[ 'money' ],
                            ];
                        }
                    }
                }
                $cart_params[ 'extra_fees' ] = [
                    'extra_service' => $extra_service
                ];

                $cart_params[ 'calendar' ] = $this->get_available_data( $post_origin, $cart_params[ 'check_in_timestamp' ] );

                return $cart_params;
            }

            /**
             * Filter to Validate Add To Cart
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $is_validated
             * @param $service_type
             * @param $post_id
             * @param $cart_params
             *
             * @return mixed
             */
            public function _add_to_cart_validate( $is_validated, $service_type, $post_id, $cart_params )
            {
                $service        = wpbooking_get_service( $post_id );
                $start          = $cart_params[ 'check_in_timestamp' ];
                $post_id_origin = wpbooking_origin_id( $post_id, 'wpbooking_service' );
                if ( $start < strtotime( 'today' ) ) {
                    $is_validated = false;
                    wpbooking_set_message( esc_html__( 'Your date is incorrect.', 'wp-booking-management-system' ), 'error' );
                }
                $booking_before = absint( get_post_meta( $post_id, 'booking_before', true ) );
                $diff           = wpbooking_date_diff( strtotime( 'today' ), $start );
                if ( $diff < $booking_before ) {
                    $is_validated = false;
                    wpbooking_set_message( sprintf( esc_html__( 'This tour is only accepted %s day(s) before departure', 'wp-booking-management-system' ), $booking_before ), 'error' );
                }
                if ( $is_validated ) {
                    $calendar = new WPBooking_Model();
                    $calendar->table( $this->table_availability );
                    global $wpdb;
                    switch ( $service->get_meta( 'pricing_type' ) ) {
                        case "per_unit":
                        case "fixed_people":
                            $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
	' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_minimum,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                                ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                                ->join( 'wpbooking_order', "wpbooking_order.post_origin = wpbooking_availability_tour.post_id AND check_in_timestamp = `start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','payment_failed', 'cancel')", 'left' )
                                ->where( [
                                    $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id_origin,
                                    $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                    'start'                                               => $start,
                                ] )
                                ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                                ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                                ->get()->row();
                            if ( !$query ) {
                                $is_validated = false;
                                wpbooking_set_message( esc_html__( 'Sorry! This tour is not available at your selected time', 'wp-booking-management-system' ), 'error' );
                            } else {
                                $total_people = $cart_params[ 'adult_number' ] + $cart_params[ 'children_number' ] + $cart_params[ 'infant_number' ];
                                if ( empty( $total_people ) ) {
                                    $is_validated = false;
                                    wpbooking_set_message( esc_html__( 'This tour requires 1 person at least', 'wp-booking-management-system' ), 'error' );
                                } else {
                                    // Check Slot(s) Remain
                                    // Check Slot(s) Remain
                                    if ( $total_people + $query[ 'total_people_booked' ] > $query[ 'max_guests' ] ) {
                                        $is_validated = false;
                                        wpbooking_set_message( sprintf( esc_html__( 'This tour only remains availability for %d people', 'wp-booking-management-system' ), $query[ 'max_guests' ] - $query[ 'total_people_booked' ] ), 'error' );
                                    } else {
                                        // Check Max, Min
                                        $min = (int)$query[ 'calendar_minimum' ];
                                        $max = (int)$query[ 'calendar_maximum' ];
                                        if ( $min <= $max ) {
                                            if ( $min ) {
                                                if ( $total_people < $min ) {
                                                    $is_validated = false;
                                                    wpbooking_set_message( sprintf( esc_html__( 'Minimum Travelers must be %d', 'wp-booking-management-system' ), $min ), 'error' );
                                                }
                                            }
                                            if ( $max ) {
                                                if ( $total_people > $max ) {
                                                    $is_validated = false;
                                                    wpbooking_set_message( sprintf( esc_html__( 'Maximum Travelers must be %d', 'wp-booking-management-system' ), $max ), 'error' );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            break;

                        case "per_person":
                        default:
                            $query = $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.adult_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.child_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.infant_price,
                                                adult_minimum,
                                                child_minimum,
                                                infant_minimum
' )
                                ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                                ->join( 'wpbooking_order', "wpbooking_order.post_origin = wpbooking_availability_tour.post_id AND check_in_timestamp = `start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                                ->where( [
                                    $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id_origin,
                                    $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                    'start'                                               => $start,
                                ] )
                                ->where( "({$wpdb->prefix}wpbooking_availability_tour.adult_price > 0 or {$wpdb->prefix}wpbooking_availability_tour.child_price>0 or {$wpdb->prefix}wpbooking_availability_tour.infant_price>0)", false, true )
                                ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                                ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                                ->get()->row();
                            if ( !$query ) {
                                $is_validated = false;
                                wpbooking_set_message( esc_html__( 'Sorry! This tour is not available at your selected time', 'wp-booking-management-system' ), 'error' );
                            } else {
                                $total_people = $cart_params[ 'adult_number' ] + $cart_params[ 'children_number' ] + $cart_params[ 'infant_number' ];

                                // Check Slot(s) Remain
                                if ( $total_people + $query[ 'total_people_booked' ] > $query[ 'max_guests' ] ) {
                                    $is_validated = false;
                                    wpbooking_set_message( sprintf( esc_html__( 'This tour only remains availability for %d people', 'wp-booking-management-system' ), $query[ 'max_guests' ] - $query[ 'total_people_booked' ] ), 'error' );
                                } else {

                                    $error_message = [];

                                    if ( ( !empty( $query[ 'adult_minimum' ] ) and $cart_params[ 'adult_number' ] < $query[ 'adult_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d adult(s)', 'wp-booking-management-system' ), $query[ 'adult_minimum' ] );
                                    }
                                    if ( ( !empty( $query[ 'child_minimum' ] ) and $cart_params[ 'children_number' ] < $query[ 'child_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d children', 'wp-booking-management-system' ), $query[ 'child_minimum' ] );
                                    }
                                    if ( ( !empty( $query[ 'infant_minimum' ] ) and $cart_params[ 'infant_number' ] < $query[ 'infant_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d infant(s)', 'wp-booking-management-system' ), $query[ 'infant_minimum' ] );
                                    }

                                    if ( !empty( $error_message ) ) {
                                        $is_validated = false;
                                        wpbooking_set_message( sprintf( esc_html__( 'This tour requires %s people at least', 'wp-booking-management-system' ), implode( ', ', $error_message ) ), 'error' );
                                    } elseif ( !$total_people ) {
                                        $is_validated = false;
                                        wpbooking_set_message( esc_html__( 'This tour requires 1 person at least', 'wp-booking-management-system' ), 'error' );
                                    }
                                }
                            }
                            break;
                    }
                }

                return $is_validated;
            }

            /**
             * Get Available Data for Specific
             *
             * @param     $post_id
             * @param int $start (timestamp)
             *
             * @return mixed
             */
            public function get_available_data( $post_id, $start )
            {
                $post_origin = wpbooking_origin_id( $post_id, 'wpbooking_service' );
                $service     = wpbooking_get_service( $post_id );
                $calendar    = new WPBooking_Model();
                $calendar->table( $this->table_availability );
                global $wpdb;

                switch ( $service->get_meta( 'pricing_type' ) ) {
                    case "per_unit":
                    case "fixed_people":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
	' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_origin = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_origin,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'start'                                               => $start,
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->row();

                        return $query;
                        break;

                    case "per_person":
                    default:
                        $query = $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.adult_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.child_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability_tour.infant_price,
                                                adult_minimum,
                                                child_minimum,
                                                infant_minimum
                                ' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'start'                                               => $start,
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability_tour.adult_price > 0 or {$wpdb->prefix}wpbooking_availability_tour.child_price>0 or {$wpdb->prefix}wpbooking_availability_tour.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->row();

                        return $query;
                        break;
                }
            }

            /**
             * Register Tour Type
             *
             * @since  1.0
             * @author dungdt
             */
            public function _register_tour_type()
            {
                // Register Taxonomy
                $labels = [
                    'name'              => esc_html__( 'Tour Type', 'wp-booking-management-system' ),
                    'singular_name'     => esc_html__( 'Tour Type', 'wp-booking-management-system' ),
                    'search_items'      => esc_html__( 'Search for Tour Type', 'wp-booking-management-system' ),
                    'all_items'         => esc_html__( 'All Tour Types', 'wp-booking-management-system' ),
                    'parent_item'       => esc_html__( 'Parent Tour Type', 'wp-booking-management-system' ),
                    'parent_item_colon' => esc_html__( 'Parent Tour Type:', 'wp-booking-management-system' ),
                    'edit_item'         => esc_html__( 'Edit Tour Type', 'wp-booking-management-system' ),
                    'update_item'       => esc_html__( 'Update Tour Type', 'wp-booking-management-system' ),
                    'add_new_item'      => esc_html__( 'Add New Tour Type', 'wp-booking-management-system' ),
                    'new_item_name'     => esc_html__( 'New Tour Type Name', 'wp-booking-management-system' ),
                    'menu_name'         => esc_html__( 'Tour Type', 'wp-booking-management-system' ),
                ];
                $args   = [
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => false,
                    'query_var'         => true,
                    'meta_box_cb'       => false,
                    'rewrite'           => [ 'slug' => 'tour-type' ],
                ];
                register_taxonomy( 'wb_tour_type', [ 'wpbooking_service' ], $args );
            }


            /**
             * Query Minimum Price for Tour
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $price
             * @param $post_id
             *
             * @return string
             */
            public function _edit_price( $price_html, $price, $post_id, $service_type )
            {

                $price_html = WPBooking_Currency::format_money( $price );

                $price_html = sprintf( esc_html__( 'from %s', 'wp-booking-management-system' ), '<br><span class="price" itemprop="price" >' . $price_html . '</span>' );

                return $price_html;
            }

            /**
             * Query Minimum Price for Tour
             *
             * @since  1.0
             * @author quandq
             *
             * @param $price
             * @param $post_id
             *
             * @return string
             */
            public function _edit_base_price( $price, $post_id, $service_type )
            {
                $base_price = $price;

                return $base_price;
            }

            /**
             * Register metabox fields
             *
             * @since  1.0
             * @author dungdt
             */
            public function _register_meta_fields()
            {
                // Metabox
                $this->set_metabox( [
                    'general_tab'  => [
                        'label'  => esc_html__( '1. Basic Information', 'wp-booking-management-system' ),
                        'fields' => [
                            [
                                'type' => 'open_section',
                            ],
                            [
                                'label' => esc_html__( "About Your Tour", 'wp-booking-management-system' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( 'Basic information', 'wp-booking-management-system' ),
                            ],
                            [
                                'id'    => 'enable_property',
                                'label' => esc_html__( "Enable Tour", 'wp-booking-management-system' ),
                                'type'  => 'on-off',
                                'std'   => 'on',
                                'desc'  => esc_html__( 'Listing will appear in search results.', 'wp-booking-management-system' ),
                            ],
                            [
                                'id'       => 'tour_type',
                                'label'    => esc_html__( "Tour Type", 'wp-booking-management-system' ),
                                'type'     => 'dropdown',
                                'taxonomy' => 'wb_tour_type',
                                'class'    => 'small'
                            ],
                            [
                                'id'    => 'star_rating',
                                'label' => esc_html__( "Star Rating", 'wp-booking-management-system' ),
                                'type'  => 'star-select',
                                'desc'  => esc_html__( 'Standard of tour from 1 to 5 stars.', 'wp-booking-management-system' ),
                                'class' => 'small'
                            ],
                            [
                                'id'          => 'duration',
                                'label'       => esc_html__( "Duration", 'wp-booking-management-system' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Example: 10 days', 'wp-booking-management-system' ),
                                'class'       => 'small',
                                'rules'       => 'required'
                            ],
                            [
                                'label'       => esc_html__( 'Contact Number', 'wp-booking-management-system' ),
                                'id'          => 'contact_number',
                                'desc'        => esc_html__( 'The contact phone', 'wp-booking-management-system' ),
                                'type'        => 'text',
                                'class'       => 'small',
                                'rules'       => 'required',
                                'placeholder' => esc_html__( 'Phone number', 'wp-booking-management-system' )
                            ],
                            [
                                'label'       => esc_html__( 'Contact Email', 'wp-booking-management-system' ),
                                'id'          => 'contact_email',
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Example@domain.com', 'wp-booking-management-system' ),
                                'class'       => 'small',
                                'rules'       => 'required|valid_email'
                            ],
                            [
                                'label'       => esc_html__( 'Website', 'wp-booking-management-system' ),
                                'id'          => 'website',
                                'type'        => 'text',
                                'desc'        => esc_html__( 'Property website (optional)', 'wp-booking-management-system' ),
                                'placeholder' => esc_html__( 'http://exampledomain.com', 'wp-booking-management-system' ),
                                'class'       => 'small',
                                'rules'       => 'valid_url'
                            ],
                            [
                                'id'           => 'taxonomy_custom',
                                'type'         => 'taxonomy_custom',
                                'service_type' => $this->type_id
                            ],
                            [ 'type' => 'close_section' ],
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Tour Destination", 'wp-booking-management-system' ),
                                'type'  => 'title',
                            ],
                            [
                                'label'           => esc_html__( 'Address', 'wp-booking-management-system' ),
                                'id'              => 'address',
                                'type'            => 'address',
                                'container_class' => 'mb35',
                                'exclude'         => [ 'apt_unit' ],
                                'rules'           => 'required'
                            ],
                            [
                                'label' => esc_html__( 'Map\'s Latitude & Longitude', 'wp-booking-management-system' ),
                                'id'    => 'gmap',
                                'type'  => 'gmap',
                                'desc'  => esc_html__( 'This is the location we will provide for guests. Click to move the marker if you need to move it', 'wp-booking-management-system' )
                            ],
                            [
                                'type'    => 'desc_section',
                                'title'   => esc_html__( 'Your address matters! ', 'wp-booking-management-system' ),
                                'content' => esc_html__( 'Please make sure to enter your full address ', 'wp-booking-management-system' )
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type' => 'section_navigation',
                                'prev' => false,
                                'step' => 'first'
                            ],
                        ]
                    ],
                    'detail_tab'   => [
                        'label'  => esc_html__( '2. Booking Details', 'wp-booking-management-system' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Pricing type", 'wp-booking-management-system' ),
                                'type'  => 'title',
                            ],
                            [
                                'label' => esc_html__( 'Pricing Type', 'wp-booking-management-system' ),
                                'type'  => 'dropdown',
                                'id'    => 'pricing_type',
                                'value' => [
                                    'per_person'   => esc_html__( 'Per person', 'wp-booking-management-system' ),
                                    'per_unit'     => esc_html__( 'Per unit', 'wp-booking-management-system' ),
                                    'fixed_people' => esc_html__( 'Fixed by People', 'wp-booking-management-system' ),
                                ],
                                'class' => 'small'
                            ],
                            [
                                'label' => esc_html__( 'Maximum people', 'wp-booking-management-system' ),
                                'id'    => 'max_guests',
                                'type'  => 'number',
                                'std'   => 1,
                                'class' => 'small',
                                'min'   => 1
                            ],
                            [
                                'label'   => esc_html__( 'Disable type of Passenger', 'wp-booking-management-system' ),
                                'id'      => 'onoff_people',
                                'type'    => 'checkbox',
                                'choices' => [
                                    'child'  => esc_html__( 'Child', 'wp-booking-management-system' ),
                                    'infant' => esc_html__( 'Infant', 'wp-booking-management-system' )
                                ]
                            ],
                            [
                                'label'     => esc_html__( 'Age Options', 'wp-booking-management-system' ),
                                'desc'      => esc_html__( 'Provide your requirements for kinds of age defined as a child or adult.', 'wp-booking-management-system' ),
                                'id'        => 'age_options',
                                'type'      => 'age_options',
                                'condition' => 'pricing_type:is(per_person)',
                                'rules'     => 'required'
                            ],
                            [
                                'type' => 'close_section'
                            ],
                            [ 'type' => 'open_section' ],
                            [
                                'type'  => 'title',
                                'label' => esc_html__( 'Extra Services', 'wp-booking-management-system' ),
                                'desc'  => esc_html__( 'Set the extended services for your property', 'wp-booking-management-system' )
                            ],
                            [
                                'type'           => 'extra_services',
                                'label'          => esc_html__( 'Choose extra services', 'wp-booking-management-system' ),
                                'id'             => 'extra_services',
                                'extra_services' => $this->get_extra_services(),
                                'service_type'   => $this->type_id
                            ],
                            [
                                'type' => 'close_section'
                            ],
                            [ 'type' => 'open_section' ],

                            [
                                'label' => esc_html__( "Availability", 'wp-booking-management-system' ),
                                'type'  => 'title',
                            ],
                            [
                                'type'         => 'calendar',
                                'id'           => 'calendar',
                                'service_type' => 'tour'
                            ],
                            [
                                'id'    => 'discount_by_no_people',
                                'label' => esc_html__( 'Discounted by number of people', 'wp-booking-management-system' ),
                                'type'  => 'list-item',
                                'value' => [
                                    [
                                        'id'    => 'no_people',
                                        'label' => esc_html__( 'No. People', 'wp-booking-management-system' ),
                                        'type'  => 'text',
                                    ],
                                    [
                                        'id'    => 'price',
                                        'label' => esc_html__( 'Discount(%)', 'wp-booking-management-system' ),
                                        'type'  => 'text'
                                    ]
                                ]
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type'  => 'section_navigation',
                                'class' => 'reload_calender'
                            ],
                        ]
                    ],
                    'policies_tab' => [
                        'label'  => esc_html__( '3. Policies & Checkout', 'wp-booking-management-system' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( 'External Link', 'wp-booking-management-system' ),
                                'id'    => 'external_link',
                                'type'  => 'text',
                                'desc'  => esc_html__( 'Enter an external link to use this feature.', 'wp-booking-management-system' )
                            ],
                            [
                                'label' => esc_html__( "Pre-payment and cancellation policies", 'wp-booking-management-system' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Pre-payment and cancellation policies", "wp-booking-management-system" )
                            ],
                            [
                                'label' => esc_html__( 'Select optional deposit ', 'wp-booking-management-system' ),
                                'id'    => 'deposit_payment_status',
                                'type'  => 'dropdown',
                                'value' => [
                                    ''        => esc_html__( 'Disallow Deposit', 'wp-booking-management-system' ),
                                    'percent' => esc_html__( 'Deposit by percent', 'wp-booking-management-system' ),
                                    'amount'  => esc_html__( 'Deposit by amount', 'wp-booking-management-system' ),
                                ],
                                'desc'  => esc_html__( "You can select Disallow Deposit, Deposit by percent, Deposit by amount", "wp-booking-management-system" ),
                                'class' => 'small'
                            ],
                            [
                                'label' => esc_html__( 'Deposit payment amount', 'wp-booking-management-system' ),
                                'id'    => 'deposit_payment_amount',
                                'type'  => 'number',
                                'desc'  => esc_html__( "Leave empty for disallow deposit payment", "wp-booking-management-system" ),
                                'class' => 'small',
                                'min'   => 1
                            ],
                            [
                                'label' => esc_html__( 'How many days in advance can guests cancel free of  charge?', 'wp-booking-management-system' ),
                                'id'    => 'cancel_free_days_prior',
                                'type'  => 'dropdown',
                                'value' => [
                                    'day_of_arrival' => esc_html__( 'Day of arrival (6 pm)', 'wp-booking-management-system' ),
                                    '1'              => esc_html__( '1 day', 'wp-booking-management-system' ),
                                    '2'              => esc_html__( '2 days', 'wp-booking-management-system' ),
                                    '3'              => esc_html__( '3 days', 'wp-booking-management-system' ),
                                    '7'              => esc_html__( '7 days', 'wp-booking-management-system' ),
                                    '14'             => esc_html__( '14 days', 'wp-booking-management-system' ),
                                ],
                                'desc'  => esc_html__( "Day of arrival ( 18: 00 ) , 1 day , 2 days, 3 days, 7 days, 14 days", "wp-booking-management-system" ),
                                'class' => 'small'
                            ],
                            [ 'type' => 'close_section' ],
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Tax", 'wp-booking-management-system' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Set your local VAT, so guests know what is included in the price of their stay.", "wp-booking-management-system" )
                            ],
                            [
                                'label'  => esc_html__( 'VAT', 'wp-booking-management-system' ),
                                'id'     => 'vat_different',
                                'type'   => 'vat_different',
                                'fields' => [
                                    'vat_excluded',
                                    'vat_amount',
                                    'vat_unit',
                                ]
                            ],
                            [ 'type' => 'close_section' ],

                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Term & condition", 'wp-booking-management-system' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Set terms and conditions for your property", "wp-booking-management-system" )
                            ],
                            [
                                'label' => esc_html__( 'Book tour before', 'wp-booking-management-system' ),
                                'type'  => 'number',
                                'id'    => 'booking_before',
                                'min'   => 0,
                                'desc'  => esc_html__( 'The number of days allowed before departure (unit: day)', 'wp-booking-management-system' )
                            ],
                            [
                                'label' => esc_html__( 'Terms & Conditions', 'wp-booking-management-system' ),
                                'id'    => 'terms_conditions',
                                'type'  => 'textarea',
                                'rows'  => '5',
                                'rules' => 'required'
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type' => 'section_navigation',
                            ],
                        ],
                    ],
                    'photo_tab'    => [
                        'label'  => esc_html__( '4. Photos', 'wp-booking-management-system' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Pictures", 'wp-booking-management-system' ),
                                'type'  => 'title',
                            ],
                            [
                                'label'         => esc_html__( "Gallery", 'wp-booking-management-system' ),
                                'id'            => 'tour_gallery',
                                'type'          => 'gallery',
                                'rules'         => 'required',
                                'desc'          => esc_html__( 'Great photos invite guests to get the full experience of your property. Be sure to include high-resolution photos of the building, facilities, and amenities. We will display these photos on your property\'s page', 'wp-booking-management-system' ),
                                'error_message' => esc_html__( 'You must upload one minimum photo for your tour', 'wp-booking-management-system' ),
                                'service_type'  => esc_html__( 'tour', 'wp-booking-management-system' )
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type'       => 'section_navigation',
                                'next_label' => esc_html__( 'Save', 'wp-booking-management-system' ),
                                'step'       => 'finish'
                            ],
                        ]
                    ],

                ] );
            }

            /**
             * Change Thumb Size of Gallery
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $size
             * @param $service_type
             * @param $post_id
             *
             * @return array
             */
            function _apply_thumb_size( $size, $service_type, $post_id )
            {
                if ( $service_type == $this->type_id ) {
                    $thumb = $this->thumb_size( '300,300,on' );
                    $thumb = explode( ',', $thumb );
                    if ( count( $thumb ) == 3 ) {
                        if ( $thumb[ 2 ] == 'off' ) $thumb[ 2 ] = false;

                        $size = [ $thumb[ 0 ], $thumb[ 1 ] ];
                    }

                }

                return $size;
            }

            /**
             * @param bool $default
             *
             * @return bool|mixed|void
             */
            function thumb_size( $default = false )
            {
                return $this->get_option( 'thumb_size', $default );
            }

            /**
             * Get Search Fields
             *
             * @since  1.0
             * @author dungdt
             *
             * @return mixed|void
             */
            public function get_search_fields()
            {
                $wpbooking_taxonomy = get_option( 'wpbooking_taxonomies' );

                $list_taxonomy = [ 'wb_tour_type' => esc_html__( 'Tour type', 'wp-booking-management-system' ) ];
                if ( !empty( $wpbooking_taxonomy ) && is_array( $wpbooking_taxonomy ) ) {
                    foreach ( $wpbooking_taxonomy as $key => $val ) {
                        if ( !empty( $val[ 'service_type' ] ) && in_array( 'tour', $val[ 'service_type' ] ) ) {
                            $list_taxonomy[ $key ] = $val[ 'label' ];
                        }
                    }
                }


                $search_fields = apply_filters( 'wpbooking_search_field_' . $this->type_id, [
                    [
                        'name'    => 'field_type',
                        'label'   => esc_html__( 'Field Type', "wp-booking-management-system" ),
                        'type'    => "dropdown",
                        'options' => [
                            ""            => esc_html__( "-- Select --", "wp-booking-management-system" ),
                            "location_id" => esc_html__( "Destination", "wp-booking-management-system" ),
                            "check_in"    => esc_html__( "From date", "wp-booking-management-system" ),
                            "check_out"   => esc_html__( "To date", "wp-booking-management-system" ),
                            "taxonomy"    => esc_html__( "Taxonomy", "wp-booking-management-system" ),
                            "star_rating" => esc_html__( "Star Of Tour", "wp-booking-management-system" ),
                            "adult_child" => esc_html__( "Adult & Children", "wp-booking-management-system" ),
                            "price"       => esc_html__( "Price", "wp-booking-management-system" ),
                        ]
                    ],
                    [
                        'name'  => 'title',
                        'label' => esc_html__( 'Title', "wp-booking-management-system" ),
                        'type'  => "text",
                        'value' => ""
                    ],
                    [
                        'name'  => 'placeholder',
                        'label' => esc_html__( 'Placeholder', "wp-booking-management-system" ),
                        'desc'  => esc_html__( 'Placeholder', "wp-booking-management-system" ),
                        'type'  => 'text',
                    ],
                    [
                        'name'    => 'taxonomy',
                        'label'   => esc_html__( '- Taxonomy', "wp-booking-management-system" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => $list_taxonomy
                    ],
                    [
                        'name'    => 'taxonomy_show',
                        'label'   => esc_html__( '- Display Style', "wp-booking-management-system" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => [
                            "dropdown"  => esc_html__( "Dropdown", "wp-booking-management-system" ),
                            "check_box" => esc_html__( "Check Box", "wp-booking-management-system" ),
                        ]
                    ],
                    [
                        'name'    => 'taxonomy_operator',
                        'label'   => esc_html__( '- Operator', "wp-booking-management-system" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => [
                            "AND" => esc_html__( "And", "wp-booking-management-system" ),
                            "OR"  => esc_html__( "Or", "wp-booking-management-system" ),
                        ]
                    ],
                    [
                        'name'    => 'required',
                        'label'   => esc_html__( 'Required', "wp-booking-management-system" ),
                        'type'    => "dropdown",
                        'options' => [
                            "no"  => esc_html__( "No", "wp-booking-management-system" ),
                            "yes" => esc_html__( "Yes", "wp-booking-management-system" ),
                        ]
                    ],
                    [
                        'name'  => 'in_more_filter',
                        'label' => esc_html__( 'In Advance Search?', "wp-booking-management-system" ),
                        'type'  => "checkbox",
                    ],

                ] );

                return $search_fields;
                // TODO: Implement get_search_fields() method.
            }


            /**
             * Get Available Days by Month, Years
             *
             * @since  1.0
             * @author dungdt
             *
             *
             * @param $month
             * @param $year
             *
             * @return array
             */
            public function get_available_days( $post_id, $month, $year )
            {
                $calendar = new WPBooking_Model();
                $calendar->table( $this->table_availability );
                $start = strtotime( date( '1-' . $month . '-' . $year ) );
                if ( $start < strtotime( date( 'd-m-Y' ) ) ) $start = strtotime( date( 'd-m-Y' ) );
                $end = strtotime( date( 't-m-Y', $start ) );
                global $wpdb;

                switch ( get_post_meta( $post_id, 'pricing_type', true ) ) {
                    case "per_unit":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
	                    ' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'calendar_price >='                                   => 0,
                                'CAST(`start` as UNSIGNED) >='                        => (int)$start,
                                'CAST(`end` as UNSIGNED) <='                          => (int)$end,
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->orderby( $wpdb->prefix . 'wpbooking_availability_tour.start' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->result();
                        $calendar->_clear_query();
                        break;
                    case "per_person":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
                                    ' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                    ' . $wpdb->prefix . 'wpbooking_availability_tour.adult_price,
                                    ' . $wpdb->prefix . 'wpbooking_availability_tour.child_price,
                                    ' . $wpdb->prefix . 'wpbooking_availability_tour.infant_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order.`status` NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'start >='                                            => $start,
                                'end <='                                              => $end,
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability_tour.adult_price > 0 or {$wpdb->prefix}wpbooking_availability_tour.child_price>0 or {$wpdb->prefix}wpbooking_availability_tour.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->orderby( $wpdb->prefix . 'wpbooking_availability_tour.start' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->result();
                    default:
                        break;
                }

                return $query;
            }

            /**
             * Get Next Available 10 Month
             *
             * @since   1.0
             * @author  dungdt
             *
             * @param bool $post_id
             *
             * @return integer|mixed
             * @updated 1.6
             */
            public function get_first_month_has_tour( $post_id = false )
            {
                if ( !$post_id ) $post_id = get_the_ID();

                $calendar = new WPBooking_Model();
                $calendar->table( $this->table_availability );
                $query = '';

                global $wpdb;
                switch ( get_post_meta( $post_id, 'pricing_type', true ) ) {
                    case "per_unit":
                        $from_query = $calendar->select( $wpdb->prefix . "wpbooking_availability_tour.max_people as max_guests, {$wpdb->prefix}wpbooking_availability_tour.id,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'calendar_price >'                                    => 0,
                                'start >='                                            => strtotime( date( 'd-m-Y' ) ),
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->_get_query();
                        $calendar->_clear_query();

                        $query = $wpdb->get_var( "
                            SELECT
                                    MONTH (FROM_UNIXTIME(START)) AS month_year
                                FROM ($from_query) as available_table
                                GROUP BY month_year
                                ORDER BY
                                    START ASC
                                LIMIT 1
                    " );
                        break;
                    case "per_person":
                        $from_query = $calendar->select( $wpdb->prefix . 'wpbooking_availability_tour.id,
                        ' . $wpdb->prefix . 'wpbooking_availability_tour.max_people as max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                    ' . $wpdb->prefix . 'wpbooking_availability_tour.adult_price,
                    ' . $wpdb->prefix . 'wpbooking_availability_tour.child_price,
                    ' . $wpdb->prefix . 'wpbooking_availability_tour.infant_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability_tour.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability_tour.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','cancel','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability_tour.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability_tour.status'  => 'available',
                                'start >='                                            => strtotime( date( 'd-m-Y' ) ),
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability_tour.adult_price > 0 or {$wpdb->prefix}wpbooking_availability_tour.child_price>0 or {$wpdb->prefix}wpbooking_availability_tour.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability_tour.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->_get_query();
                        $calendar->_clear_query();
                        $query = $wpdb->get_var( "
                            SELECT MONTH (FROM_UNIXTIME(START)) AS month_year
                                FROM ($from_query) as available_table
                                GROUP BY month_year
                                ORDER BY
                                    START ASC
                                LIMIT 1
                    " );
                    default:
                        break;
                }

                return $query;
            }


            /**
             * Update min_price
             *
             * @since  1.3
             * @author quandq
             *
             * @param $tour_id
             *
             * @return bool
             */
            function _update_min_price_tour( $tour_id )
            {
                if ( get_post_type( $tour_id ) != 'wpbooking_service' ) return false;
                $service_type = get_post_meta( $tour_id, 'service_type', true );
                if ( $service_type != $this->type_id ) return false;
                $min_price = $this->_edit_base_price( 0, $tour_id, $service_type );
                update_post_meta( $tour_id, 'price', $min_price );
                WPBooking_Service_Model::inst()->save_extra( $tour_id );
            }

            function _add_default_query_hook()
            {
                global $wpdb;
                $table_prefix = WPBooking_Service_Model::inst()->get_table_name();
                $injection    = WPBooking_Query_Inject::inst();
                $tax_query    = $injection->get_arg( 'tax_query' );

                $posts_per_page = $this->get_option( 'posts_per_page', 10 );

                $injection->add_arg( 'posts_per_page', $posts_per_page );

                // Taxonomy
                $tax = $this->request( 'taxonomy' );
                if ( !empty( $tax ) and is_array( $tax ) ) {
                    $taxonomy_operator = $this->request( 'taxonomy_operator' );
                    $tax_query_child   = [];
                    foreach ( $tax as $key => $value ) {
                        if ( $value ) {
                            if ( !empty( $taxonomy_operator[ $key ] ) ) {
                                $operator = $taxonomy_operator[ $key ];
                            } else {
                                $operator = "OR";
                            }
                            if ( $operator == 'OR' ) $operator = 'IN';
                            $value = explode( ',', $value );
                            if ( !empty( $value ) and is_array( $value ) ) {
                                foreach ( $value as $k => $v ) {
                                    if ( !empty( $v ) ) {
                                        $ids[] = $v;
                                    }
                                }
                            }
                            if ( !empty( $ids ) ) {
                                $tax_query[] = [
                                    'taxonomy' => $key,
                                    'terms'    => $ids,
                                    'operator' => $operator,
                                ];
                            }
                            $ids = [];
                        }
                    }


                    if ( !empty( $tax_query_child ) )
                        $tax_query[] = $tax_query_child;
                }

                // Star Rating
                if ( $star_rating = $this->get( 'star_rating' ) and is_array( explode( ',', $star_rating ) ) ) {

                    $star_rating_arr = explode( ',', $star_rating );
                    $meta_query[]    = [
                        'relation' => 'AND',
                        [
                            'key'     => 'star_rating',
                            'type'    => 'CHAR',
                            'value'   => $star_rating_arr,
                            'compare' => 'IN'
                        ]
                    ];
                }

                if ( !empty( $tax_query ) )
                    $injection->add_arg( 'tax_query', $tax_query );

                if ( !empty( $meta_query ) )
                    $injection->add_arg( 'meta_query', $meta_query );

                // Review

                $injection->add_arg( 'post_status', 'publish' );

                if ( WPBooking_Input::request( 'service_type' ) == 'tour' ) {
                    //Check in
                    $from_date = strtotime( $this->request( 'checkin_d' ) . '-' . $this->request( 'checkin_m' ) . '-' . $this->request( 'checkin_y' ) );
                    $end_date  = strtotime( $this->request( 'checkout_d' ) . '-' . $this->request( 'checkout_m' ) . '-' . $this->request( 'checkout_y' ) );
                    if ( !$end_date ) {
                        $end_date = $from_date;
                    }
                    if ( $from_date ) {
                        $injection->join( 'wpbooking_availability_tour as avail', "avail.post_id={$wpdb->posts}.ID" );
                        $injection->where( "(avail.`start` >= {$from_date} AND avail.`start` <= {$end_date})", false, true );
                        $injection->where( 'avail.status', 'available' );
                        $injection->groupby( 'avail.post_id' );
                        $total_guest = (int)WPBooking_Input::get( 'adult_s', 1 ) + (int)WPBooking_Input::get( 'child_s' );
                        $sql         = "{$wpdb->posts}.ID IN( SELECT
                            post_id
                        FROM
                            (
                                SELECT
                                    sv.post_id,
                                    sv.max_guests,
                                
                                IF (
                                    sum(_od.adult_number) IS NULL,
                                    0,
                                    sum(_od.adult_number)
                                ) AS adult_number,
                                
                                IF (
                                    sum(_od.children_number) IS NULL,
                                    0,
                                    sum(_od.children_number)
                                ) AS children_number,
                                
                                IF (
                                    sum(_od.infant_number) IS NULL,
                                    0,
                                    sum(_od.infant_number)
                                ) infant_number
                                FROM
                                    {$wpdb->prefix}wpbooking_service AS sv
                                LEFT JOIN {$wpdb->prefix}wpbooking_order AS _od ON (
                                    _od.post_id = sv.post_id
                                    AND _od.check_in_timestamp = {$from_date}
                                    AND _od.`status` NOT IN (
                                        'cancel',
                                        'cancelled',
                                        'payment_failed',
                                        'refunded'
                                    )
                                )
                                WHERE
                                    1 = 1
                                AND sv.service_type = 'tour'
                                
                                GROUP BY
                                    sv.post_id
                                having max_guests - (adult_number + children_number + infant_number) >= {$total_guest}
                            ) AS _tour)";
                        $injection->where( $sql, false, true );
                        // Order By
                        if ( $sortby = $this->request( 'wb_sort_by' ) ) {
                            switch ( $sortby ) {
                                case "price_asc":
                                    $injection->select( "CASE
                                            WHEN meta.meta_value = 'per_person' 
                                            THEN
                                                MIN(
                                                    CAST(avail.adult_price AS DECIMAL)
                                                )
                                            ELSE
                                                MIN(
                                                    CAST(
                                                        avail.calendar_price AS DECIMAL
                                                    )
                                                )
                                            END AS min_price" );
                                    $injection->join( 'postmeta as meta', "meta.post_id={$wpdb->posts}.ID AND meta.meta_key='pricing_type'", 'left' );
                                    $injection->where( "((
                                            (meta.meta_value = 'per_person' and CAST(avail.adult_price AS DECIMAL) > 0) )
                                        or (meta.meta_value = 'per_unit' AND CAST(avail.calendar_price AS DECIMAL) > 0))", false, true );
                                    $injection->orderby( 'min_price', 'asc' );
                                    break;
                                case "price_desc":
                                    $injection->select( "CASE
                                            WHEN meta.meta_value = 'per_person'
                                            THEN
                                                MIN(
                                                    CAST(avail.adult_price AS DECIMAL)
                                                )
                                            ELSE
                                                MIN(
                                                    CAST(
                                                        avail.calendar_price AS DECIMAL
                                                    )
                                                )
                                            END AS min_price" );
                                    $injection->join( 'postmeta as meta', "meta.post_id={$wpdb->posts}.ID AND meta.meta_key='pricing_type'", 'left' );
                                    $injection->where( "((
                                            (meta.meta_value = 'per_person' and CAST(avail.adult_price AS DECIMAL) > 0) )  or (meta.meta_value = 'per_unit' AND CAST(avail.calendar_price AS DECIMAL) > 0))", false, true );
                                    $injection->orderby( 'min_price', 'desc' );
                                    break;
                                case "date_asc":
                                    $injection->add_arg( 'orderby', 'date' );
                                    $injection->add_arg( 'order', 'ASC' );
                                    break;
                                case "date_desc":
                                    $injection->add_arg( 'orderby', 'date' );
                                    $injection->add_arg( 'order', 'DESC' );
                                    break;
                                case "name_a_z":
                                    $injection->add_arg( 'orderby', 'post_title' );
                                    $injection->add_arg( 'order', 'asc' );
                                    break;
                                case "name_z_a":
                                    $injection->add_arg( 'orderby', 'post_title' );
                                    $injection->add_arg( 'order', 'desc' );
                                    break;
                            }
                        }

                        // Price
                        if ( $price = WPBooking_Input::get( 'price' ) ) {
                            $array = explode( ';', $price );
                            $injection->select( "
                                        MIN(
                                            CASE WHEN wpb_meta.meta_value = 'per_person'
                                            THEN
                                                CAST(avail.adult_price AS DECIMAL) 
                                            ELSE
                                                CAST(avail.calendar_price AS DECIMAL)
                                            END
                                        ) as wpb_base_price" )
                                ->join( 'postmeta as wpb_meta', "{$wpdb->posts}.ID=wpb_meta.post_id and wpb_meta.meta_key = 'pricing_type'" );

                            if ( !empty( $array[ 0 ] ) ) {
                                $injection->having( ' CAST(wpb_base_price AS DECIMAL) >= ' . $array[ 0 ] );
                            }
                            if ( !empty( $array[ 1 ] ) ) {
                                $injection->having( ' CAST(wpb_base_price AS DECIMAL) <= ' . $array[ 1 ] );
                            }
                        }
                    }
                }

                parent::_add_default_query_hook();
            }

            static function inst()
            {
                if ( !self::$_inst )
                    self::$_inst = new self();

                return self::$_inst;
            }
        }

        WPBooking_Tour_Service_Type::inst();
    }