<?php
    if ( !class_exists( 'WPBooking_Tour_Service_Type' ) and class_exists( 'WPBooking_Abstract_Service_Type' ) ) {
        class WPBooking_Tour_Service_Type extends WPBooking_Abstract_Service_Type
        {
            static $_inst = false;

            protected $type_id = 'tour';

            function __construct()
            {
                $this->type_info = [
                    'label'  => esc_html__( "Tour", 'wpbooking' ),
                    'labels' => esc_html__( "Tours", 'wpbooking' ),
                    'desc'   => esc_html__( 'You can post anything related to activities such as tourism, events, workshops, etc anything called tour', 'wpbooking' )
                ];

                $this->settings = [

                    [
                        'id'    => 'title',
                        'label' => esc_html__( 'Layout', 'wpbooking' ),
                        'type'  => 'title',
                    ],
                    [
                        'id'    => 'posts_per_page',
                        'label' => esc_html__( "Item per page", 'wpbooking' ),
                        'type'  => 'number',
                        'std'   => 10
                    ],
                    [
                        'id'    => "thumb_size",
                        'label' => esc_html__( "Thumb Size", 'wpbooking' ),
                        'type'  => 'image-size'
                    ],
                    [
                        'id'    => "gallery_size",
                        'label' => esc_html__( "Gallery Size", 'wpbooking' ),
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
                            CASE WHEN 
                                        (CAST(avail.adult_price AS DECIMAL)) <= ( CAST(avail.child_price AS DECIMAL) ) 
                                        AND (CAST(avail.adult_price AS DECIMAL)) <= ( CAST(avail.infant_price AS DECIMAL) ) 
                                        THEN
                                            ( CAST(avail.adult_price AS DECIMAL) )
                            
                                        WHEN 
                                                 ( CAST(avail.child_price AS DECIMAL) ) <= ( CAST(avail.adult_price AS DECIMAL) ) 
                                                AND ( CAST(avail.child_price AS DECIMAL) ) <= ( CAST(avail.infant_price AS DECIMAL) ) 
                                        THEN
                                            (CAST(avail.child_price AS DECIMAL))
                                        WHEN  ( CAST(avail.infant_price AS DECIMAL) ) <= ( CAST(avail.adult_price AS DECIMAL) ) 
                                                AND ( CAST(avail.infant_price AS DECIMAL) ) <= ( CAST(avail.child_price AS DECIMAL) ) 
                                            THEN
                                                    (CAST(avail.infant_price AS DECIMAL))
                                END
                ELSE
                            CAST(avail.calendar_price AS DECIMAL)
                END
            ) as base_price

            " )
                    ->join( 'posts', 'posts.ID=' . $service->get_table_name( false ) . '.post_id' )
                    ->join( 'postmeta as wpb_meta', $wpdb->prefix . 'posts.ID=wpb_meta.post_id and wpb_meta.meta_key = \'pricing_type\'' )
                    ->join( 'wpbooking_availability AS avail', $wpdb->prefix . 'posts.ID= avail.post_id ' );


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
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Adult', 'wpbooking' ) . ' x ' . esc_html( $raw_data->adult_number ) . '</strong></span>';
                            echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $calendar_price ) . '</span>';
                            echo '</li>';
                        }
                        if ( !empty( $raw_data->children_number ) ) {
                            $calendar_price = ( $raw_data->pricing_type == 'per_person' ) ? $raw_data->calendar->child_price : $raw_data->calendar->calendar_price;
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Children', 'wpbooking' ) . ' x ' . esc_html( $raw_data->children_number ) . '</strong></span>';
                            echo '<span class="wb-room-price">' . WPBooking_Currency::format_money( $calendar_price ) . '</span>';
                            echo '</li>';
                        }
                        if ( !empty( $raw_data->infant_number ) ) {
                            $calendar_price = ( $raw_data->pricing_type == 'per_person' ) ? $raw_data->calendar->infant_price : $raw_data->calendar->calendar_price;
                            echo '<li class="wb-room-item"><span class="wb-room-name"><strong>' . esc_html__( 'Infant', 'wpbooking' ) . ' x ' . esc_html( $raw_data->infant_number ) . '</strong></span>';
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
                    echo '<span class="total-title">' . esc_html__( 'Tour Price', 'wpbooking' ) . '</span>
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
                printf( '<div class="people-price-item bold"><span class="head-item">%s</span></div>', esc_html__( "Booking Info", 'wpbooking' ) );
                // From
                if ( !empty( $cart[ 'check_in_timestamp' ] ) ) {
                    $from_detail = date_i18n( get_option( 'date_format' ), $cart[ 'check_in_timestamp' ] );
                    if ( !empty( $cart[ 'duration' ] ) ) {
                        $from_detail .= ' (' . $cart[ 'duration' ] . ')';
                    }
                    printf( '<div class="from-detail"><span class="head-item">%s:</span> <span class="from-detail-duration">%s</span></div>', esc_html__( 'From', 'wpbooking' ), $from_detail );
                }
                switch ( $cart[ 'pricing_type' ] ) {
                    case "per_unit":
                        if ( !empty( $cart[ 'adult_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Adult(s)', 'wpbooking' ), $cart[ 'adult_number' ] );
                        }
                        if ( !empty( $cart[ 'children_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Children', 'wpbooking' ), $cart[ 'children_number' ] );
                        }
                        if ( !empty( $cart[ 'infant_number' ] ) ) {
                            printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d</span></div>', esc_html__( 'Infant(s)', 'wpbooking' ), $cart[ 'infant_number' ] );
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
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Adult(s)', 'wpbooking' ), $cart[ 'adult_number' ], WPBooking_Currency::format_money( $calendar[ 'adult_price' ] ), WPBooking_Currency::format_money( $calendar[ 'adult_price' ] * $cart[ 'adult_number' ] ) );
                            }
                            if ( !empty( $cart[ 'children_number' ] ) ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Children', 'wpbooking' ), $cart[ 'children_number' ], WPBooking_Currency::format_money( $calendar[ 'child_price' ] ), WPBooking_Currency::format_money( $calendar[ 'child_price' ] * $cart[ 'children_number' ] ) );
                            }
                            if ( !empty( $cart[ 'infant_number' ] ) ) {
                                printf( '<div class="people-price-item"><span class="head-item">%s:</span> <span class="price-item">%d x %s = %s</span></div>', esc_html__( 'Infant(s)', 'wpbooking' ), $cart[ 'infant_number' ], WPBooking_Currency::format_money( $calendar[ 'infant_price' ] ), WPBooking_Currency::format_money( $calendar[ 'infant_price' ] * $cart[ 'infant_number' ] ) );
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
                              class="change-date"><?php esc_html_e( "Change Date", "wpbooking" ) ?></a></small>
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

                $cart_params[ 'check_in_timestamp' ] = $this->post( 'wb-departure-date' );
                $cart_params[ 'adult_number' ]       = $this->post( 'adult_number' );
                $cart_params[ 'children_number' ]    = $this->post( 'children_number' );
                $cart_params[ 'infant_number' ]      = $this->post( 'infant_number' );
                $cart_params[ 'pricing_type' ]       = get_post_meta( $post_id, 'pricing_type', true );
                $cart_params[ 'duration' ]           = get_post_meta( $post_id, 'duration', true );

                $post_extras              = $this->post( 'wpbooking_extra_service' );
                $extra_service            = [];
                $extra_service[ 'title' ] = esc_html__( 'Extra Service', 'wpbooking' );
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

                $cart_params[ 'calendar' ] = $this->get_available_data( $post_id, $cart_params[ 'check_in_timestamp' ] );

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
                $service = wpbooking_get_service( $post_id );
                $start   = $cart_params[ 'check_in_timestamp' ];

                if ( $start < strtotime( 'today' ) ) {
                    $is_validated = false;
                    wpbooking_set_message( esc_html__( 'Your date is incorrect.', 'wpbooking' ), 'error' );
                }

                if ( $is_validated ) {
                    $calendar = WPBooking_Calendar_Model::inst();
                    global $wpdb;
                    switch ( $service->get_meta( 'pricing_type' ) ) {
                        case "per_unit":
                            $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
	' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_minimum,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                                ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                                ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id AND check_in_timestamp = `start` and wpbooking_order.STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                                ->where( [
                                    $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                    $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                    'start'                                          => $start,
                                ] )
                                ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                                ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                                ->get()->row();
                            if ( !$query ) {
                                $is_validated = false;
                                wpbooking_set_message( esc_html__( 'Sorry! This tour is not available at your selected time', 'wpbooking' ), 'error' );
                            } else {
                                $total_people = $cart_params[ 'adult_number' ] + $cart_params[ 'children_number' ] + $cart_params[ 'infant_number' ];


                                if ( empty( $total_people ) ) {
                                    $is_validated = false;
                                    wpbooking_set_message( esc_html__( 'This tour requires 1 person at least', 'wpbooking' ), 'error' );
                                } else {
                                    // Check Slot(s) Remain
                                    // Check Slot(s) Remain
                                    if ( $total_people + $query[ 'total_people_booked' ] > $query[ 'max_guests' ] ) {
                                        $is_validated = false;
                                        wpbooking_set_message( sprintf( esc_html__( 'This tour only remains availability for %d people', 'wpbooking' ), $query[ 'max_guests' ] - $query[ 'total_people_booked' ] ), 'error' );
                                    } else {
                                        // Check Max, Min
                                        $min = (int)$query[ 'calendar_minimum' ];
                                        $max = (int)$query[ 'calendar_maximum' ];
                                        if ( $min <= $max ) {
                                            if ( $min ) {
                                                if ( $total_people < $min ) {
                                                    $is_validated = false;
                                                    wpbooking_set_message( sprintf( esc_html__( 'Minimum Travelers must be %d', 'wpbooking' ), $min ), 'error' );
                                                }
                                            }
                                            if ( $max ) {
                                                if ( $total_people > $max ) {
                                                    $is_validated = false;
                                                    wpbooking_set_message( sprintf( esc_html__( 'Maximum Travelers must be %d', 'wpbooking' ), $max ), 'error' );
                                                }
                                            }
                                        }
                                    }
                                }


                            }
                            break;

                        case "per_person":
                        default:
                            $query = $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
                                                ' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                                ' . $wpdb->prefix . 'wpbooking_availability.adult_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability.child_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability.infant_price,
                                                adult_minimum,
                                                child_minimum,
                                                infant_minimum
' )
                                ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                                ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id AND check_in_timestamp = `start` and wpbooking_order.STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                                ->where( [
                                    $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                    $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                    'start'                                          => $start,
                                ] )
                                ->where( "({$wpdb->prefix}wpbooking_availability.adult_price > 0 or {$wpdb->prefix}wpbooking_availability.child_price>0 or {$wpdb->prefix}wpbooking_availability.infant_price>0)", false, true )
                                ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                                ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                                ->get()->row();
                            if ( !$query ) {
                                $is_validated = false;
                                wpbooking_set_message( esc_html__( 'Sorry! This tour is not available at your selected time', 'wpbooking' ), 'error' );
                            } else {
                                $total_people = $cart_params[ 'adult_number' ] + $cart_params[ 'children_number' ] + $cart_params[ 'infant_number' ];

                                // Check Slot(s) Remain
                                if ( $total_people + $query[ 'total_people_booked' ] > $query[ 'max_guests' ] ) {
                                    $is_validated = false;
                                    wpbooking_set_message( sprintf( esc_html__( 'This tour only remains availability for %d people', 'wpbooking' ), $query[ 'max_guests' ] - $query[ 'total_people_booked' ] ), 'error' );
                                } else {

                                    $error_message = [];

                                    if ( ( !empty( $query[ 'adult_minimum' ] ) and $cart_params[ 'adult_number' ] < $query[ 'adult_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d adult(s)', 'wpbooking' ), $query[ 'adult_minimum' ] );
                                    }
                                    if ( ( !empty( $query[ 'child_minimum' ] ) and $cart_params[ 'children_number' ] < $query[ 'child_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d children', 'wpbooking' ), $query[ 'child_minimum' ] );
                                    }
                                    if ( ( !empty( $query[ 'infant_minimum' ] ) and $cart_params[ 'infant_number' ] < $query[ 'infant_minimum' ] ) ) {
                                        $error_message[] = sprintf( esc_html__( '%d infant(s)', 'wpbooking' ), $query[ 'infant_minimum' ] );
                                    }

                                    if ( !empty( $error_message ) ) {
                                        $is_validated = false;
                                        wpbooking_set_message( sprintf( esc_html__( 'This tour requires %s people at least', 'wpbooking' ), implode( ', ', $error_message ) ), 'error' );
                                    } elseif ( !$total_people ) {
                                        $is_validated = false;
                                        wpbooking_set_message( esc_html__( 'This tour requires 1 person at least', 'wpbooking' ), 'error' );
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

                $service  = wpbooking_get_service( $post_id );
                $calendar = WPBooking_Calendar_Model::inst();
                global $wpdb;

                switch ( $service->get_meta( 'pricing_type' ) ) {
                    case "per_unit":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
	' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order.STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'start'                                          => $start,
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->row();

                        return $query;
                        break;

                    case "per_person":
                    default:
                        $query = $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
                                                ' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                                ' . $wpdb->prefix . 'wpbooking_availability.adult_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability.child_price,
                                                ' . $wpdb->prefix . 'wpbooking_availability.infant_price,
                                                adult_minimum,
                                                child_minimum,
                                                infant_minimum
                                ' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order.STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'start'                                          => $start,
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability.adult_price > 0 or {$wpdb->prefix}wpbooking_availability.child_price>0 or {$wpdb->prefix}wpbooking_availability.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
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
                    'name'              => esc_html__( 'Tour Type', 'wpbooking' ),
                    'singular_name'     => esc_html__( 'Tour Type', 'wpbooking' ),
                    'search_items'      => esc_html__( 'Search for Tour Type', 'wpbooking' ),
                    'all_items'         => esc_html__( 'All Tour Types', 'wpbooking' ),
                    'parent_item'       => esc_html__( 'Parent Tour Type', 'wpbooking' ),
                    'parent_item_colon' => esc_html__( 'Parent Tour Type:', 'wpbooking' ),
                    'edit_item'         => esc_html__( 'Edit Tour Type', 'wpbooking' ),
                    'update_item'       => esc_html__( 'Update Tour Type', 'wpbooking' ),
                    'add_new_item'      => esc_html__( 'Add New Tour Type', 'wpbooking' ),
                    'new_item_name'     => esc_html__( 'New Tour Type Name', 'wpbooking' ),
                    'menu_name'         => esc_html__( 'Tour Type', 'wpbooking' ),
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
                global $wpdb;
                $calendar = WPBooking_Calendar_Model::inst();

                $pricing_type = get_post_meta( $post_id, 'pricing_type', true );

                if ( $pricing_type == 'per_person' ) {
                    $query = $calendar->select( '
                CASE
                WHEN MIN(adult_price) <= MIN(child_price)
                AND MIN(adult_price) <= MIN(infant_price) THEN
                    MIN(adult_price)
                WHEN MIN(child_price) <= MIN(adult_price)
                AND MIN(child_price) <= MIN(infant_price) THEN
                    MIN(child_price)
                WHEN MIN(infant_price) <= MIN(adult_price)
                AND MIN(infant_price) <= MIN(child_price) THEN
                    MIN(infant_price)
                END AS min_price
                ' )->where( [
                        'post_id'  => $post_id,
                        'status'   => 'available',
                        'start >=' => strtotime( date( 'd-m-Y' ) )

                    ] )->where( '(child_price > 0 or adult_price > 0)', false, true )->get( 1 )->row();
                } else {
                    $query = $calendar->select( 'MIN(calendar_price) as min_price' )->where( [
                        'post_id'          => $post_id,
                        'status'           => 'available',
                        'calendar_price >' => 0,
                        'start >='         => strtotime( date( 'd-m-Y' ) )

                    ] )->get( 1 )->row();
                }

                if ( $query ) {
                    $price = $query[ 'min_price' ];
                }

                $price_html = WPBooking_Currency::format_money( $price );

                $price_html = sprintf( esc_html__( 'from %s', 'wpbooking' ), '<br><span class="price" itemprop="price" >' . $price_html . '</span>' );

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
                global $wpdb;
                $calendar = WPBooking_Calendar_Model::inst();

                $pricing_type = get_post_meta( $post_id, 'pricing_type', true );

                if ( $pricing_type == 'per_person' ) {
                    $query = $calendar->select( '
                CASE
                WHEN MIN(adult_price) <= MIN(child_price)
                AND MIN(adult_price) <= MIN(infant_price) THEN
                    MIN(adult_price)
                WHEN MIN(child_price) <= MIN(adult_price)
                AND MIN(child_price) <= MIN(infant_price) THEN
                    MIN(child_price)
                WHEN MIN(infant_price) <= MIN(adult_price)
                AND MIN(infant_price) <= MIN(child_price) THEN
                    MIN(infant_price)
                END AS min_price
                ' )->where( [
                        'post_id'  => $post_id,
                        'status'   => 'available',
                        'start >=' => strtotime( date( 'd-m-Y' ) )

                    ] )->where( '(child_price > 0 or adult_price > 0)', false, true )->get( 1 )->row();
                } else {
                    $query = $calendar->select( 'MIN(calendar_price) as min_price' )->where( [
                        'post_id'          => $post_id,
                        'status'           => 'available',
                        'calendar_price >' => 0,
                        'start >='         => strtotime( date( 'd-m-Y' ) )

                    ] )->get( 1 )->row();
                }

                if ( $query ) {
                    $price = $query[ 'min_price' ];
                }

                return $price;
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
                        'label'  => esc_html__( '1. Basic Information', 'wpbooking' ),
                        'fields' => [
                            [
                                'type' => 'open_section',
                            ],
                            [
                                'label' => esc_html__( "About Your Tour", 'wpbooking' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( 'Basic information', 'wpbooking' ),
                            ],
                            [
                                'id'    => 'enable_property',
                                'label' => esc_html__( "Enable Tour", 'wpbooking' ),
                                'type'  => 'on-off',
                                'std'   => 'on',
                                'desc'  => esc_html__( 'Listing will appear in search results.', 'wpbooking' ),
                            ],
                            [
                                'id'       => 'tour_type',
                                'label'    => esc_html__( "Tour Type", 'wpbooking' ),
                                'type'     => 'dropdown',
                                'taxonomy' => 'wb_tour_type',
                                'class'    => 'small'
                            ],
                            [
                                'id'    => 'star_rating',
                                'label' => esc_html__( "Star Rating", 'wpbooking' ),
                                'type'  => 'star-select',
                                'desc'  => esc_html__( 'Standard of tour from 1 to 5 stars.', 'wpbooking' ),
                                'class' => 'small'
                            ],
                            [
                                'id'          => 'duration',
                                'label'       => esc_html__( "Duration", 'wpbooking' ),
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Example: 10 days', 'wpbooking' ),
                                'class'       => 'small',
                                'rules'       => 'required'
                            ],
                            [
                                'label'       => esc_html__( 'Contact Number', 'wpbooking' ),
                                'id'          => 'contact_number',
                                'desc'        => esc_html__( 'The contact phone', 'wpbooking' ),
                                'type'        => 'text',
                                'class'       => 'small',
                                'rules'       => 'required',
                                'placeholder' => esc_html__( 'Phone number', 'wpbooking' )
                            ],
                            [
                                'label'       => esc_html__( 'Contact Email', 'wpbooking' ),
                                'id'          => 'contact_email',
                                'type'        => 'text',
                                'placeholder' => esc_html__( 'Example@domain.com', 'wpbooking' ),
                                'class'       => 'small',
                                'rules'       => 'required|valid_email'
                            ],
                            [
                                'label'       => esc_html__( 'Website', 'wpbooking' ),
                                'id'          => 'website',
                                'type'        => 'text',
                                'desc'        => esc_html__( 'Property website (optional)', 'wpbooking' ),
                                'placeholder' => esc_html__( 'http://exampledomain.com', 'wpbooking' ),
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
                                'label' => esc_html__( "Tour Destination", 'wpbooking' ),
                                'type'  => 'title',
                            ],
                            [
                                'label'           => esc_html__( 'Address', 'wpbooking' ),
                                'id'              => 'address',
                                'type'            => 'address',
                                'container_class' => 'mb35',
                                'exclude'         => [ 'apt_unit' ],
                                'rules'           => 'required'
                            ],
                            [
                                'label' => esc_html__( 'Map\'s Latitude & Longitude', 'wpbooking' ),
                                'id'    => 'gmap',
                                'type'  => 'gmap',
                                'desc'  => esc_html__( 'This is the location we will provide for guests. Click to move the marker if you need to move it', 'wpbooking' )
                            ],
                            [
                                'type'    => 'desc_section',
                                'title'   => esc_html__( 'Your address matters! ', 'wpbooking' ),
                                'content' => esc_html__( 'Please make sure to enter your full address ', 'wpbooking' )
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
                        'label'  => esc_html__( '2. Booking Details', 'wpbooking' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Pricing type", 'wpbooking' ),
                                'type'  => 'title',
                            ],
                            [
                                'label' => esc_html__( 'Pricing Type', 'wpbooking' ),
                                'type'  => 'dropdown',
                                'id'    => 'pricing_type',
                                'value' => [
                                    'per_person' => esc_html__( 'Per person', 'wpbooking' ),
                                    'per_unit'   => esc_html__( 'Per unit', 'wpbooking' ),
                                ],
                                'class' => 'small'
                            ],
                            [
                                'label' => esc_html__( 'Maximum people', 'wpbooking' ),
                                'id'    => 'max_guests',
                                'type'  => 'number',
                                'std'   => 1,
                                'class' => 'small',
                                'min'   => 1
                            ],
                            [
                                'label'     => esc_html__( 'Age Options', 'wpbooking' ),
                                'desc'      => esc_html__( 'Provide your requirements for kinds of age defined as a child or adult.', 'wpbooking' ),
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
                                'label' => esc_html__( 'Extra Services', 'wpbooking' ),
                                'desc'  => esc_html__( 'Set the extended services for your property', 'wpbooking' )
                            ],
                            [
                                'type'           => 'extra_services',
                                'label'          => esc_html__( 'Choose extra services', 'wpbooking' ),
                                'id'             => 'extra_services',
                                'extra_services' => $this->get_extra_services(),
                                'service_type'   => $this->type_id
                            ],
                            [
                                'type' => 'close_section'
                            ],
                            [ 'type' => 'open_section' ],

                            [
                                'label' => esc_html__( "Availability", 'wpbooking' ),
                                'type'  => 'title',
                            ],
                            [
                                'type'         => 'calendar',
                                'id'           => 'calendar',
                                'service_type' => 'tour'
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type'  => 'section_navigation',
                                'class' => 'reload_calender'
                            ],
                        ]
                    ],
                    'policies_tab' => [
                        'label'  => esc_html__( '3. Policies & Checkout', 'wpbooking' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Pre-payment and cancellation policies", 'wpbooking' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Pre-payment and cancellation policies", "wpbooking" )
                            ],
                            [
                                'label' => esc_html__( 'Select optional deposit ', 'wpbooking' ),
                                'id'    => 'deposit_payment_status',
                                'type'  => 'dropdown',
                                'value' => [
                                    ''        => esc_html__( 'Disallow Deposit', 'wpbooking' ),
                                    'percent' => esc_html__( 'Deposit by percent', 'wpbooking' ),
                                    'amount'  => esc_html__( 'Deposit by amount', 'wpbooking' ),
                                ],
                                'desc'  => esc_html__( "You can select Disallow Deposit, Deposit by percent, Deposit by amount", "wpbooking" ),
                                'class' => 'small'
                            ],
                            [
                                'label' => esc_html__( 'Deposit payment amount', 'wpbooking' ),
                                'id'    => 'deposit_payment_amount',
                                'type'  => 'number',
                                'desc'  => esc_html__( "Leave empty for disallow deposit payment", "wpbooking" ),
                                'class' => 'small',
                                'min'   => 1
                            ],
                            [
                                'label' => esc_html__( 'How many days in advance can guests cancel free of  charge?', 'wpbooking' ),
                                'id'    => 'cancel_free_days_prior',
                                'type'  => 'dropdown',
                                'value' => [
                                    'day_of_arrival' => esc_html__( 'Day of arrival (6 pm)', 'wpbooking' ),
                                    '1'              => esc_html__( '1 day', 'wpbooking' ),
                                    '2'              => esc_html__( '2 days', 'wpbooking' ),
                                    '3'              => esc_html__( '3 days', 'wpbooking' ),
                                    '7'              => esc_html__( '7 days', 'wpbooking' ),
                                    '14'             => esc_html__( '14 days', 'wpbooking' ),
                                ],
                                'desc'  => esc_html__( "Day of arrival ( 18: 00 ) , 1 day , 2 days, 3 days, 7 days, 14 days", "wpbooking" ),
                                'class' => 'small'
                            ],
                            [ 'type' => 'close_section' ],
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Tax", 'wpbooking' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Set your local VAT, so guests know what is included in the price of their stay.", "wpbooking" )
                            ],
                            [
                                'label'  => esc_html__( 'VAT', 'wpbooking' ),
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
                                'label' => esc_html__( "Term & condition", 'wpbooking' ),
                                'type'  => 'title',
                                'desc'  => esc_html__( "Set terms and conditions for your property", "wpbooking" )
                            ],
                            [
                                'label' => esc_html__( 'Terms & Conditions', 'wpbooking' ),
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
                        'label'  => esc_html__( '4. Photos', 'wpbooking' ),
                        'fields' => [
                            [ 'type' => 'open_section' ],
                            [
                                'label' => esc_html__( "Pictures", 'wpbooking' ),
                                'type'  => 'title',
                            ],
                            [
                                'label'         => esc_html__( "Gallery", 'wpbooking' ),
                                'id'            => 'tour_gallery',
                                'type'          => 'gallery',
                                'rules'         => 'required',
                                'desc'          => esc_html__( 'Great photos invite guests to get the full experience of your property. Be sure to include high-resolution photos of the building, facilities, and amenities. We will display these photos on your property\'s page', 'wpbooking' ),
                                'error_message' => esc_html__( 'You must upload one minimum photo for your tour', 'wpbooking' ),
                                'service_type'  => esc_html__( 'tour', 'wpbooking' )
                            ],
                            [ 'type' => 'close_section' ],
                            [
                                'type'       => 'section_navigation',
                                'next_label' => esc_html__( 'Save', 'wpbooking' ),
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

                $list_taxonomy = [ 'wb_tour_type' => esc_html__( 'Tour type', 'wpbooking' ) ];
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
                        'label'   => esc_html__( 'Field Type', "wpbooking" ),
                        'type'    => "dropdown",
                        'options' => [
                            ""            => esc_html__( "-- Select --", "wpbooking" ),
                            "location_id" => esc_html__( "Destination", "wpbooking" ),
                            "check_in"    => esc_html__( "From date", "wpbooking" ),
                            "check_out"   => esc_html__( "To date", "wpbooking" ),
                            "taxonomy"    => esc_html__( "Taxonomy", "wpbooking" ),
                            "star_rating" => esc_html__( "Star Of Tour", "wpbooking" ),
                            "price"       => esc_html__( "Price", "wpbooking" ),
                        ]
                    ],
                    [
                        'name'  => 'title',
                        'label' => esc_html__( 'Title', "wpbooking" ),
                        'type'  => "text",
                        'value' => ""
                    ],
                    [
                        'name'  => 'placeholder',
                        'label' => esc_html__( 'Placeholder', "wpbooking" ),
                        'desc'  => esc_html__( 'Placeholder', "wpbooking" ),
                        'type'  => 'text',
                    ],
                    [
                        'name'    => 'taxonomy',
                        'label'   => esc_html__( '- Taxonomy', "wpbooking" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => $list_taxonomy
                    ],
                    [
                        'name'    => 'taxonomy_show',
                        'label'   => esc_html__( '- Display Style', "wpbooking" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => [
                            "dropdown"  => esc_html__( "Dropdown", "wpbooking" ),
                            "check_box" => esc_html__( "Check Box", "wpbooking" ),
                        ]
                    ],
                    [
                        'name'    => 'taxonomy_operator',
                        'label'   => esc_html__( '- Operator', "wpbooking" ),
                        'type'    => "dropdown",
                        'class'   => "hide",
                        'options' => [
                            "AND" => esc_html__( "And", "wpbooking" ),
                            "OR"  => esc_html__( "Or", "wpbooking" ),
                        ]
                    ],
                    [
                        'name'    => 'required',
                        'label'   => esc_html__( 'Required', "wpbooking" ),
                        'type'    => "dropdown",
                        'options' => [
                            "no"  => esc_html__( "No", "wpbooking" ),
                            "yes" => esc_html__( "Yes", "wpbooking" ),
                        ]
                    ],
                    [
                        'name'  => 'in_more_filter',
                        'label' => esc_html__( 'In Advance Search?', "wpbooking" ),
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

                $calendar = WPBooking_Calendar_Model::inst();

                $start = strtotime( date( '1-' . $month . '-' . $year ) );
                if ( $start < strtotime( date( 'd-m-Y' ) ) ) $start = strtotime( date( 'd-m-Y' ) );
                $end = strtotime( date( 't-' . $month . '-' . $year ) );
                global $wpdb;

                switch ( get_post_meta( $post_id, 'pricing_type', true ) ) {
                    case "per_unit":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
	' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'calendar_price >'                               => 0,
                                'start >='                                       => $start,
                                'end <='                                         => $end,
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                            ->orderby( $wpdb->prefix . 'wpbooking_availability.start' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->get()->result();
                        $calendar->_clear_query();

                        break;
                    case "per_person":
                        $query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
                                    ' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
                                    ' . $wpdb->prefix . 'wpbooking_availability.adult_price,
                                    ' . $wpdb->prefix . 'wpbooking_availability.child_price,
                                    ' . $wpdb->prefix . 'wpbooking_availability.infant_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'start >='                                       => $start,
                                'end <='                                         => $end,
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability.adult_price > 0 or {$wpdb->prefix}wpbooking_availability.child_price>0 or {$wpdb->prefix}wpbooking_availability.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                            ->orderby( $wpdb->prefix . 'wpbooking_availability.start' )
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
             * @since  1.0
             * @author dungdt
             *
             * @param bool $post_id
             *
             * @return array
             */
            public function getNext10MonthAvailable( $post_id = false )
            {
                if ( !$post_id ) $post_id = get_the_ID();

                $calendar = WPBooking_Calendar_Model::inst();

                global $wpdb;
                switch ( get_post_meta( $post_id, 'pricing_type', true ) ) {
                    case "per_unit":
                        $from_query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,calendar_price' )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'calendar_price >'                               => 0,
                                'start >='                                       => strtotime( date( 'd-m-Y' ) ),
                            ] )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < calendar_maximum' )
                            ->_get_query();
                        $calendar->_clear_query();

                        $query = $wpdb->get_results( "
                            SELECT
                                calendar_maximum,
                                start,
                                CONCAT(
                                    MONTH (FROM_UNIXTIME(START)),
                                    '_',
                                    YEAR (FROM_UNIXTIME(START))
                                ) AS month_year
                                FROM ($from_query) as available_table
                                GROUP BY month_year
                                ORDER BY
                                    START ASC
                                LIMIT 0,
                                 10
                    
                    ", ARRAY_A );
                        break;
                    case "per_person":
                        $from_query = $calendar->select( $wpdb->prefix . 'wpbooking_availability.id,
	' . $wpdb->prefix . 'wpbooking_service.max_guests,calendar_maximum,SUM(adult_number + children_number + infant_number) AS total_people_booked,start,
' . $wpdb->prefix . 'wpbooking_availability.adult_price,
' . $wpdb->prefix . 'wpbooking_availability.child_price,
' . $wpdb->prefix . 'wpbooking_availability.infant_price' )
                            ->join( 'wpbooking_service', "wpbooking_service.post_id = wpbooking_availability.post_id" )
                            ->join( 'wpbooking_order', "wpbooking_order.post_id = wpbooking_availability.post_id and check_in_timestamp=`start` and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')", 'left' )
                            ->where( [
                                $wpdb->prefix . 'wpbooking_availability.post_id' => $post_id,
                                $wpdb->prefix . 'wpbooking_availability.status'  => 'available',
                                'start >='                                       => strtotime( date( 'd-m-Y' ) ),
                            ] )
                            ->where( "({$wpdb->prefix}wpbooking_availability.adult_price > 0 or {$wpdb->prefix}wpbooking_availability.child_price>0 or {$wpdb->prefix}wpbooking_availability.infant_price>0)", false, true )
                            ->groupby( $wpdb->prefix . 'wpbooking_availability.id' )
                            ->having( ' total_people_booked IS NULL OR total_people_booked < max_guests' )
                            ->_get_query();
                        $calendar->_clear_query();

                        $query = $wpdb->get_results( "
                            SELECT
                                calendar_maximum,
                                start,
                                CONCAT(
                                    MONTH (FROM_UNIXTIME(START)),
                                    '_',
                                    YEAR (FROM_UNIXTIME(START))
                                ) AS month_year
                                FROM ($from_query) as available_table
                                GROUP BY month_year
                                ORDER BY
                                    START ASC
                                LIMIT 0,
                                 10
                    ", ARRAY_A );
                    default:
                        break;
                }
                $res = [];

                if ( !empty( $query ) ) {
                    foreach ( $query as $item ) {
                        $res[ $item[ 'month_year' ] ] = [
                            'days'  => $this->get_available_days( $post_id, date( 'm', $item[ 'start' ] ), date( 'Y', $item[ 'start' ] ) ),
                            'label' => date_i18n( 'M Y', $item[ 'start' ] )
                        ];
                    }
                }

                return $res;
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

                //Check in
                if ( $this->request( 'checkout_d' ) ) {
                    $end_date = strtotime( $this->request( 'checkout_d' ) . '-' . $this->request( 'checkout_m' ) . '-' . $this->request( 'checkout_y' ) );
                    if ( $this->request( 'checkin_d' ) && $this->request( 'checkin_m' ) && $this->request( 'checkin_y' ) ) {
                        $from_date = strtotime( $this->request( 'checkin_d' ) . '-' . $this->request( 'checkin_m' ) . '-' . $this->request( 'checkin_y' ) );

                        $injection->join( 'wpbooking_availability as avail', "avail.post_id={$wpdb->posts}.ID" );
                        $injection->where( "(avail.`start` >= {$from_date} AND avail.`start` <= {$end_date})", false, true );
                        $injection->where( 'avail.status', 'available' );
                        $injection->groupby( 'avail.post_id' );
                    }
                } else {
                    if ( $this->request( 'checkin_d' ) && $this->request( 'checkin_m' ) && $this->request( 'checkin_y' ) ) {
                        $from_date = strtotime( $this->request( 'checkin_d' ) . '-' . $this->request( 'checkin_m' ) . '-' . $this->request( 'checkin_y' ) );

                        $injection->join( 'wpbooking_availability as avail', "avail.post_id={$wpdb->posts}.ID" );
                        $injection->where( 'avail.`start`', $from_date );
                        $injection->where( 'avail.`status`', 'available' );
                        $injection->groupby( 'avail.post_id' );
                    }
                }


                if ( !empty( $tax_query ) )
                    $injection->add_arg( 'tax_query', $tax_query );

                if ( !empty( $meta_query ) )
                    $injection->add_arg( 'meta_query', $meta_query );

                // Review

                $injection->add_arg( 'post_status', 'publish' );

                // Price
                if ( $price = WPBooking_Input::get( 'price' ) ) {
                    $array = explode( ';', $price );

                    $injection->select( "
                                        MIN(
                                            CASE
                                                WHEN wpb_meta.meta_value = 'per_person'
                                                THEN
                                                        CASE WHEN 
                                                                    (CAST(avail.adult_price AS DECIMAL)) <= ( CAST(avail.child_price AS DECIMAL) ) 
                                                                    AND (CAST(avail.adult_price AS DECIMAL)) <= ( CAST(avail.infant_price AS DECIMAL) ) 
                                                                    THEN
                                                                        ( CAST(avail.adult_price AS DECIMAL) )
                                                        
                                                                    WHEN 
                                                                             ( CAST(avail.child_price AS DECIMAL) ) <= ( CAST(avail.adult_price AS DECIMAL) ) 
                                                                            AND ( CAST(avail.child_price AS DECIMAL) ) <= ( CAST(avail.infant_price AS DECIMAL) ) 
                                                                    THEN
                                                                        (CAST(avail.child_price AS DECIMAL))
                                                                    WHEN  ( CAST(avail.infant_price AS DECIMAL) ) <= ( CAST(avail.adult_price AS DECIMAL) ) 
                                                                            AND ( CAST(avail.infant_price AS DECIMAL) ) <= ( CAST(avail.child_price AS DECIMAL) ) 
                                                                        THEN
                                                                                (CAST(avail.infant_price AS DECIMAL))
                                                            END
                                            ELSE
                                                        CAST(avail.calendar_price AS DECIMAL)
                                            END
                                        ) as wpb_base_price" )
                        ->join( 'postmeta as wpb_meta', $wpdb->prefix . 'posts.ID=wpb_meta.post_id and wpb_meta.meta_key = \'pricing_type\'' )
                        ->join( 'wpbooking_availability as avail', $wpdb->prefix . 'posts.ID= avail.post_id ' );

                    $injection->where( 'avail.start>=', strtotime( 'today' ) );
                    if ( !empty( $array[ 0 ] ) ) {
                        $injection->having( ' CAST(wpb_base_price AS DECIMAL) >= ' . $array[ 0 ] );
                    }
                    if ( !empty( $array[ 1 ] ) ) {
                        $injection->having( ' CAST(wpb_base_price AS DECIMAL) <= ' . $array[ 1 ] );
                    }
                }

                // Order By
                if ( $sortby = $this->request( 'wb_sort_by' ) ) {
                    switch ( $sortby ) {
                        case "price_asc":
                            $injection->select( "CASE
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.adult_price AS DECIMAL) ) <= MIN( CAST(avail.child_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.adult_price AS DECIMAL) ) <= MIN( CAST(avail.infant_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.adult_price AS DECIMAL)
                                                )
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.child_price AS DECIMAL) ) <= MIN(	CAST(avail.adult_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.child_price AS DECIMAL) ) <= MIN(	CAST(avail.infant_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.child_price AS DECIMAL)
                                                )
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.infant_price AS DECIMAL) ) <= MIN(	CAST(avail.adult_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.infant_price AS DECIMAL) ) <= MIN(	CAST(avail.child_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.infant_price AS DECIMAL)
                                                )
                                            ELSE
                                                MIN(
                                                    CAST(
                                                        avail.calendar_price AS DECIMAL
                                                    )
                                                )
                                            END AS min_price" );
                            $injection->join( 'postmeta as meta', "meta.post_id={$wpdb->posts}.ID AND meta.meta_key='pricing_type'", 'left' );
                            $injection->join( 'wpbooking_availability as avail', "avail.post_id = {$wpdb->posts}.ID" );
                            $injection->where( 'avail.`status`', 'available' );
                            $injection->where( "((
                                            (meta.meta_value = 'per_person' and
                                        CAST(avail.adult_price AS DECIMAL) > 0)
                                        or (
                                        meta.meta_value = 'per_person' and
                                        CAST(avail.child_price AS DECIMAL) > 0
                                        )
                                        )
                                        or (meta.meta_value = 'per_unit' AND CAST(avail.calendar_price AS DECIMAL) > 0))", false, true );
                            $injection->orderby( 'min_price', 'asc' );
                            break;
                        case "price_desc":
                            $injection->select( "CASE
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.adult_price AS DECIMAL) ) <= MIN( CAST(avail.child_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.adult_price AS DECIMAL) ) <= MIN( CAST(avail.infant_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.adult_price AS DECIMAL)
                                                )
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.child_price AS DECIMAL) ) <= MIN(	CAST(avail.adult_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.child_price AS DECIMAL) ) <= MIN(	CAST(avail.infant_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.child_price AS DECIMAL)
                                                )
                                            WHEN meta.meta_value = 'per_person' 
                                            AND MIN( CAST(avail.infant_price AS DECIMAL) ) <= MIN(	CAST(avail.adult_price AS DECIMAL) ) 
                                            AND MIN( CAST(avail.infant_price AS DECIMAL) ) <= MIN(	CAST(avail.child_price AS DECIMAL) ) 
                                            THEN
                                                MIN(
                                                    CAST(avail.infant_price AS DECIMAL)
                                                )
                                            ELSE
                                                MIN(
                                                    CAST(
                                                        avail.calendar_price AS DECIMAL
                                                    )
                                                )
                                            END AS min_price" );
                            $injection->join( 'postmeta as meta', "meta.post_id={$wpdb->posts}.ID AND meta.meta_key='pricing_type'", 'left' );
                            $injection->join( 'wpbooking_availability as avail', "avail.post_id = {$wpdb->posts}.ID" );
                            $injection->where( 'avail.`status`', 'available' );
                            $injection->where( "((
                                            (meta.meta_value = 'per_person' and
                                        CAST(avail.adult_price AS DECIMAL) > 0)
                                        or (
                                        meta.meta_value = 'per_person' and
                                        CAST(avail.child_price AS DECIMAL) > 0
                                        )
                                        )
                                        or (meta.meta_value = 'per_unit' AND CAST(avail.calendar_price AS DECIMAL) > 0))", false, true );
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