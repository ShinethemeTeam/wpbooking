<?php
    /**
     * @since 1.0.0
     **/
    if ( !class_exists( 'WPBooking_Calendar_Metabox' ) ) {
        class WPBooking_Calendar_Metabox extends WPBooking_Controller
        {
            static $_inst;

            public $table = 'wpbooking_availability';

            public function __construct()
            {
                parent::__construct();

                add_action( 'wp_ajax_wpbooking_load_availability', [ $this, '_load_availability' ] );

                add_action( 'wp_ajax_wpbooking_add_availability', [ $this, '_add_availability' ] );

                add_action( 'wp_ajax_wpbooking_calendar_bulk_edit', [ $this, '_calendar_bulk_edit' ] );

                // Ajax Save Property For
                add_action( 'wp_ajax_wpbooking_save_property_available_for', [ $this, '_save_property_available_for' ] );
            }

            public function set_table( $table = '' )
            {
                if ( !empty( $table ) ) {
                    $this->table = $table;
                }
            }

            /**
             * @since  1.0
             * @author haint
             *
             *
             */
            public function _load_availability()
            {
                $post_id = (int)WPBooking_Input::post( 'post_id', '' );

                // Validate Permission
                $post_encrypt = (int)WPBooking_Input::post( 'post_encrypt', '' );
                if ( $post_id > 0 || wpbooking_encrypt_compare( $post_id, $post_encrypt ) ) {
                    $this->set_table( WPBooking_Input::post( 'table' ) );
                    $base_id = (int)wpbooking_origin_id( $post_id, 'wpbooking_service' );

                    $check_in  = WPBooking_Input::post( 'start', '' );
                    $check_out = WPBooking_Input::post( 'end', '' );
                    $check_in  = strtotime( $check_in );
                    $check_out = strtotime( $check_out );

                    if ( $check_in < strtotime( 'today' ) ) {
                        $check_in = strtotime( 'today' );
                    }

                    $return = $this->get_availability( $base_id, $check_in, $check_out );



                    // Other day, in case Specific Periods Available

                    $all_days = [];

                    $begin = new DateTime();
                    $begin->setTimestamp( $check_in );
                    $end = new DateTime();
                    $end->setTimestamp( $check_out );

                    $interval = DateInterval::createFromDateString( '1 day' );
                    $period   = new DatePeriod( $begin, $interval, $end );

                    foreach ( $period as $dt ) {
                        $all_days[ $dt->format( 'Y-m-d' ) ] = [
                            'start'         => $dt->format( 'Y-m-d' ),
                            'end'           => $dt->format( 'Y-m-d' ),
                            'status'        => 'available',
                            'can_check_in'  => 1,
                            'can_check_out' => 1,

                        ];
                        if ( get_post_meta( $post_id, 'property_available_for', true ) == 'specific_periods' ) {
                            $all_days[ $dt->format( 'Y-m-d' ) ][ 'status' ] = 'wb-disable';
                        } else {
                            $all_days[ $dt->format( 'Y-m-d' ) ][ 'price_text' ] = WPBooking_Currency::format_money( get_post_meta( $post_id, 'base_price', true ) );
                        }

                    }
                    // Foreach Data
                    if ( !empty( $return ) ) {
                        foreach ( $return as $day ) {
                            if ( array_key_exists( $day[ 'start' ], $all_days ) ) {
                                unset( $all_days[ $day[ 'start' ] ] );
                            }
                        }
                    }
                    // Now append the exsits
                    if ( !empty( $all_days ) ) {
                        foreach ( $all_days as $day ) {
                            $return[] = $day;
                        }
                    }
                    $data = [
                        'data' => $return
                    ];
                    echo json_encode( $data );
                    die;
                }
            }

            public function _add_availability()
            {
                $post_id = (int)WPBooking_Input::post( 'post-id', 0 );

                // Validate Permission
                $post = get_post( $post_id );
                if ( !$post or ( $post->post_author != get_current_user_id() and !current_user_can( 'manage_options' ) ) ) {
                    echo json_encode( [
                        'status'  => 0,
                        'message' => esc_html__( 'You do not have permission to do it', 'wp-booking-management-system' )
                    ] );
                    die;
                }

                if ( wp_verify_nonce( $_POST[ 'security' ], 'wpbooking-nonce-field' ) ) {
                    $post_encrypt = (int)WPBooking_Input::post( 'post-encrypt', '' );

                    if ( $post_id > 0 || wpbooking_encrypt_compare( $post_id, $post_encrypt ) ) {

                        $check_in  = strtotime( WPBooking_Input::post( 'check_in', '' ) );
                        $check_out = strtotime( WPBooking_Input::post( 'check_out', '' ) );
                        if ( !$check_in || !$check_out ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of start date or end date is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }

                        $price = WPBooking_Input::post( 'price', '' );
                        if ( $price and $price < 0 ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of price is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }

                        $status = WPBooking_Input::post( 'status', '' );

                        $group_day     = WPBooking_Input::post( 'group_day', '' );
                        $can_check_in  = WPBooking_Input::post( 'can_check_in' );
                        $can_check_out = WPBooking_Input::post( 'can_check_out' );
                        $weekly        = WPBooking_Input::post( 'weekly' );
                        $monthly       = WPBooking_Input::post( 'monthly' );

                        $calendar_minimum = WPBooking_Input::post( 'calendar_minimum' );
                        if ( $calendar_minimum < 0 ) $calendar_minimum = 0;

                        $calendar_maximum = WPBooking_Input::post( 'calendar_maximum' );
                        if ( $calendar_maximum < 0 ) $calendar_maximum = 1;

                        $calendar_price = WPBooking_Input::post( 'calendar_price' );
                        if ( $calendar_price and $calendar_price < 0 ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of price is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                        $calendar_adult_minimum = WPBooking_Input::post( 'calendar_adult_minimum' );
                        if ( $calendar_adult_minimum < 0 ) $calendar_adult_minimum = 0;

                        $calendar_adult_price = WPBooking_Input::post( 'calendar_adult_price' );
                        if ( $calendar_adult_price and $calendar_adult_price < 0 ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of adult price is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                        $calendar_child_minimum = WPBooking_Input::post( 'calendar_child_minimum' );
                        if ( $calendar_child_minimum < 0 ) $calendar_child_minimum = 0;

                        $calendar_child_price = WPBooking_Input::post( 'calendar_child_price' );
                        if ( $calendar_child_price and $calendar_child_price < 0 ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of child price is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                        $calendar_infant_minimum = WPBooking_Input::post( 'calendar_infant_minimum' );
                        if ( $calendar_infant_minimum < 0 ) $calendar_infant_minimum = 0;

                        $calendar_infant_price = WPBooking_Input::post( 'calendar_infant_price' );
                        if ( $calendar_infant_price and $calendar_infant_price < 0 ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The field of infant price is invalid.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                        $this->set_table( WPBooking_Input::post( 'table' ) );
                        $base_id = (int)wpbooking_origin_id( $post_id, get_post_type($post_id) );
                        $max_people = WPBooking_Input::post( 'calendar_max_people', '' );

                        $new_item = $this->_calendar_save_data( $post_id, $base_id, $check_in, $check_out, $price, $status, $group_day, $weekly, $monthly, $can_check_in, $can_check_out, $calendar_minimum, $calendar_maximum, $calendar_price, $calendar_adult_minimum, $calendar_adult_price, $calendar_child_minimum, $calendar_child_price, $calendar_infant_minimum, $calendar_infant_price, $max_people );
                        do_action( 'wpbooking_after_add_availability', $post_id );
                        if ( $new_item > 0 ) {
                            echo json_encode( [
                                'status'  => 1,
                                'message' => esc_html__( 'Successffully added', 'wp-booking-management-system' )
                            ] );
                            die;
                        } else {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'Getting an error when adding new item.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                    }
                }
            }

            public function _calendar_save_data( $post_id, $base_id, $check_in, $check_out, $price, $status, $group_day, $weekly, $monthly, $can_check_in, $can_check_out, $calendar_minimum, $calendar_maximum, $calendar_price, $calendar_adult_minimum, $calendar_adult_price, $calendar_child_minimum, $calendar_child_price, $calendar_infant_minimum, $calendar_infant_price, $max_people )
            {
                /* Get all item between check in - out */

                $result = $this->get_availability( $base_id, $check_in, $check_out );

                $split = $this->split_availability( $result, $check_in, $check_out );

                if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                    foreach ( $split[ 'delete' ] as $item ) {
                        $this->wpbooking_delete_availability( $item[ 'id' ] );
                    }
                }

                if ( isset( $split[ 'insert' ] ) && !empty( $split[ 'insert' ] ) ) {
                    foreach ( $split[ 'insert' ] as $item ) {
                        $this->wpbooking_insert_availability( $item[ 'post_id' ], $item[ 'base_id' ], $item[ 'start' ], $item[ 'end' ], $item[ 'price' ], $item[ 'status' ], $item[ 'group_day' ], $weekly, $monthly, $can_check_in, $can_check_out, $item[ 'calendar_minimum' ], $item[ 'calendar_maximum' ], $item[ 'calendar_price' ], $item[ 'adult_minimum' ], $item[ 'adult_price' ], $item[ 'child_minimum' ], $item[ 'child_price' ], $item[ 'infant_minimum' ], $item[ 'infant_price' ] , $item['max_people']);
                    }
                }
                $new_item = $this->wpbooking_insert_availability( $post_id, $base_id, $check_in, $check_out, $price, $status, $group_day, $weekly, $monthly, $can_check_in, $can_check_out, $calendar_minimum, $calendar_maximum, $calendar_price, $calendar_adult_minimum, $calendar_adult_price, $calendar_child_minimum, $calendar_child_price, $calendar_infant_minimum, $calendar_infant_price, $max_people );

                return $new_item;
            }

            public function _calendar_bulk_edit()
            {

                $post_id = (int)WPBooking_Input::post( 'post_id', 0 );

                // Validate Permission
                $post = get_post( $post_id );
                if ( !$post ) {
                    echo json_encode( [
                        'status'  => 0,
                        'message' => esc_html__( 'Please select a service', 'wp-booking-management-system' )
                    ] );
                    die;
                }

                if ( wp_verify_nonce( $_POST[ 'security' ], 'wpbooking-nonce-field' ) ) {
                    $post_id = (int)WPBooking_Input::post( 'post_id', 0 );

                    if ( isset( $_POST[ 'all_days' ] ) && !empty( $_POST[ 'all_days' ] ) ) {
                        $data           = WPBooking_Input::post( 'data', '' );
                        $all_days       = WPBooking_Input::post( 'all_days', '' );
                        $posts_per_page = (int)WPBooking_Input::post( 'posts_per_page', '' );
                        $current_page   = (int)WPBooking_Input::post( 'current_page', '' );
                        $total          = (int)WPBooking_Input::post( 'total', '' );
                        if ( $current_page > ceil( $total / $posts_per_page ) ) {
                            echo json_encode( [
                                'status'  => 1,
                                'message' => esc_html__( 'Successffully added', 'wp-booking-management-system' )
                            ] );
                            die;
                        } else {
                            $return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );
                            echo json_encode( $return );
                            die;
                        }
                    }

                    $day_of_week  = WPBooking_Input::post( 'day-of-week', '' );
                    $day_of_month = WPBooking_Input::post( 'day-of-month', '' );

                    $array_month = [
                        'January'   => '1',
                        'February'  => '2',
                        'March'     => '3',
                        'April'     => '4',
                        'May'       => '5',
                        'June'      => '6',
                        'July'      => '7',
                        'August'    => '8',
                        'September' => '9',
                        'October'   => '10',
                        'November'  => '11',
                        'December'  => '12',
                    ];

                    $months = WPBooking_Input::post( 'months', '' );

                    $years = WPBooking_Input::post( 'years', '' );

                    $price = WPBooking_Input::post( 'price_bulk', '' );

                    $adult  = WPBooking_Input::post( 'adult_bulk', '' );
                    $child  = WPBooking_Input::post( 'child_bulk', '' );
                    $infant = WPBooking_Input::post( 'infant_bulk', '' );
                    $max_people = WPBooking_Input::post( 'max_people_bulk', '' );

                    $status     = WPBooking_Input::post( 'status_bulk', 'available' );
                    $price_type = WPBooking_Input::post( 'price_type', '' );

                    $post_type = WPBooking_Input::post( 'post_type', 'accommodation' );

                    if ( $post_type == 'accommodation' ) {

                        if ( $status == 'available' && ( !is_numeric( $price ) || (float)$price < 0 ) ) {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The price is not a number.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }
                    } elseif ( $post_type == 'tour' ) {
                        if ( $price_type == 'per_unit' ) {
                            if ( $status == 'available' ) {
                                if ( !is_numeric( $price ) || (float)$price < 0 ) {
                                    echo json_encode( [
                                        'status'  => 0,
                                        'message' => esc_html__( 'The price is not a number.', 'wp-booking-management-system' )
                                    ] );
                                    die;
                                }
                            }
                        } else {
                            if ( $status == 'available' ) {
                                if ( !is_numeric( $adult ) || (float)$adult < 0 ) {
                                    echo json_encode( [
                                        'status'  => 0,
                                        'message' => esc_html__( 'The adult is not a number.', 'wp-booking-management-system' )
                                    ] );
                                    die;
                                }
                                if ( !empty( $child ) && !is_numeric( $child ) ) {
                                    echo json_encode( [
                                        'status'  => 0,
                                        'message' => esc_html__( 'The child is not a number.', 'wp-booking-management-system' )
                                    ] );
                                    die;
                                }
                                if ( !empty( $infant ) && !is_numeric( $infant ) ) {
                                    echo json_encode( [
                                        'status'  => 0,
                                        'message' => esc_html__( 'The infant is not a number.', 'wp-booking-management-system' )
                                    ] );
                                    die;
                                }
                            }
                        }
                    }
                    $price = (float)$price;

                    $group_day = WPBooking_Input::post( 'group_bulk', '' );

                    $base_id = (int)wpbooking_origin_id( $post_id, 'wpbooking_service' );
                    /*	Start, End is a timestamp */
                    $all_years  = [];
                    $all_months = [];
                    $all_days   = [];
                    $link       = '';

                    if ( !empty( $years ) ) {

                        sort( $years, 1 );

                        foreach ( $years as $year ) {
                            $all_years[] = $year;
                        }

                        if ( !empty( $months ) ) {

                            foreach ( $months as $month ) {
                                foreach ( $all_years as $year ) {
                                    $all_months[] = $month . ' ' . $year;
                                }
                            }

                            if ( !empty( $day_of_week ) && !empty( $day_of_month ) ) {
                                // Each day in month
                                foreach ( $day_of_month as $day ) {
                                    // Each day in week
                                    foreach ( $day_of_week as $day_week ) {
                                        // Each month year
                                        foreach ( $all_months as $month ) {
                                            $time = strtotime( $day . ' ' . $month );

                                            if ( date( 'l', $time ) == $day_of_week ) {
                                                $all_days[] = $time;
                                            }

                                        }
                                    }
                                }
                                foreach ( $day_of_month as $day ) {
                                    foreach ( $all_months as $month ) {
                                        $day        = str_pad( $day, 2, '0', STR_PAD_LEFT );
                                        $all_days[] = strtotime( $day . ' ' . $month );
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $all_months as $month ) {
                                    for ( $i = strtotime( 'first day of ' . $month ); $i <= strtotime( 'last day of ' . $month ); $i = strtotime( '+1 day', $i ) ) {
                                        $all_days[] = $i;
                                    }
                                }
                            } elseif ( empty( $day_of_week ) && !empty( $day_of_month ) ) {

                                foreach ( $day_of_month as $day ) {
                                    foreach ( $all_months as $month ) {
                                        $month_tmp = trim( $month );
                                        $month_tmp = explode( ' ', $month );

                                        $num_day = cal_days_in_month( CAL_GREGORIAN, $array_month[ $month_tmp[ 0 ] ], $month_tmp[ 1 ] );

                                        if ( $day <= $num_day ) {
                                            $all_days[] = strtotime( $day . ' ' . $month );
                                        }
                                    }
                                }
                            } elseif ( !empty( $day_of_week ) && empty( $day_of_month ) ) {
                                foreach ( $day_of_week as $day ) {
                                    foreach ( $all_months as $month ) {
                                        for ( $i = strtotime( 'first ' . $day . ' of ' . $month ); $i <= strtotime( 'last ' . $day . ' of ' . $month ); $i = strtotime( '+1 week', $i ) ) {
                                            $all_days[] = $i;
                                        }
                                    }
                                }
                            }

                            if ( !empty( $all_days ) ) {
                                $posts_per_page = 10;
                                $total          = count( $all_days );

                                $current_page = 1;

                                $data = [
                                    'post_id'        => $post_id,
                                    'base_id'        => $base_id,
                                    'status'         => $status,
                                    'group_day'      => $group_day,
                                    'price'          => $price,
                                    'calendar_price' => $price,
                                    'adult_price'    => $adult,
                                    'child_price'    => $child,
                                    'infant_price'   => $infant,
                                    'max_people'   => $max_people,
                                    'table' => WPBooking_Input::post('table')
                                ];

                                $return = $this->insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id );

                                echo json_encode( $return );
                                die;
                            }
                        } else {
                            echo json_encode( [
                                'status'  => 0,
                                'message' => esc_html__( 'The months field is required.', 'wp-booking-management-system' )
                            ] );
                            die;
                        }

                    } else {
                        echo json_encode( [
                            'status'  => 0,
                            'message' => esc_html__( 'The years field is required.', 'wp-booking-management-system' )
                        ] );
                        die;
                    }
                }
            }

            public function insert_calendar_bulk( $data, $posts_per_page, $total, $current_page, $all_days, $post_id )
            {
                $this->set_table($data['table']);
                global $wpdb;

                $start = ( $current_page - 1 ) * $posts_per_page;

                $end = ( $current_page - 1 ) * $posts_per_page + $posts_per_page - 1;

                if ( $end > $total - 1 ) $end = $total - 1;

                for ( $i = $start; $i <= $end; $i++ ) {

                    $data[ 'start' ] = $all_days[ $i ];
                    $data[ 'end' ]   = $all_days[ $i ];

                    /*	Delete old item */
                    $result = $this->get_availability( $data[ 'base_id' ], $all_days[ $i ], $all_days[ $i ] );

                    $split = $this->split_availability( $result, $all_days[ $i ], $all_days[ $i ] );

                    if ( isset( $split[ 'delete' ] ) && !empty( $split[ 'delete' ] ) ) {
                        foreach ( $split[ 'delete' ] as $item ) {
                            $this->wpbooking_delete_availability( $item[ 'id' ] );
                        }
                    }
                    /*	.End */
                    $this->wpbooking_insert_availability( $data[ 'post_id' ], $data[ 'base_id' ], $data[ 'start' ], $data[ 'end' ], $data[ 'price' ], $data[ 'status' ], $data[ 'group_day' ], false, false, false, false, false, false, $data[ 'price' ], false, $data[ 'adult_price' ], false, $data[ 'child_price' ], false, $data[ 'child_price' ], $data['max_people'] );
                }

                $next_page = (int)$current_page + 1;

                $progress        = ( $current_page / $total ) * 100;
                $return          = [
                    'all_days'       => $all_days,
                    'current_page'   => $next_page,
                    'posts_per_page' => $posts_per_page,
                    'total'          => $total,
                    'status'         => 2,
                    'data'           => $data,
                    'progress'       => $progress,
                    'post_id'        => $post_id
                ];

                return $return;
            }

            public function wpbooking_delete_availability( $id = '' )
            {

                global $wpdb;

                $table = $wpdb->prefix . $this->table;

                $wpdb->delete(
                    $table,
                    [
                        'id' => $id
                    ]
                );

            }

            public function wpbooking_insert_availability( $post_id = '', $base_id = '', $check_in = '', $check_out = '', $price = '', $status = '', $group_day = '', $weekly = false, $monthly = false, $can_check_in = 1, $can_check_out = 1, $calendar_minimum = '', $calendar_maximum = '', $calendar_price = '', $adult_minimum = '', $adult_price = '', $child_minimum = '', $child_price = '', $infant_minimum = '', $infant_price = '', $max_people = '' )
            {
                global $wpdb;
                $table = $wpdb->prefix . $this->table;
                if ( $group_day === 'group' ) {
                    $wpdb->insert(
                        $table,
                        [
                            'post_id'          => $post_id,
                            'base_id'          => $base_id,
                            'start'            => $check_in,
                            'end'              => $check_out,
                            'price'            => $price,
                            'status'           => $status,
                            'group_day'        => $group_day,
                            'monthly'          => $monthly,
                            'weekly'           => $weekly,
                            'can_check_in'     => $can_check_in,
                            'can_check_out'    => $can_check_out,
                            'calendar_minimum' => $calendar_minimum,
                            'calendar_maximum' => $calendar_maximum,
                            'calendar_price'   => $calendar_price,
                            'adult_minimum'    => $adult_minimum,
                            'adult_price'      => $adult_price,
                            'child_minimum'    => $child_minimum,
                            'child_price'      => $child_price,
                            'infant_minimum'   => $infant_minimum,
                            'infant_price'     => $infant_price,
                            'max_people'     => $max_people,
                        ]
                    );
                } else {
                    for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {

                        $wpdb->insert(
                            $table,
                            [
                                'post_id'          => $post_id,
                                'base_id'          => $base_id,
                                'start'            => $i,
                                'end'              => $i,
                                'price'            => $price,
                                'status'           => $status,
                                'group_day'        => $group_day,
                                'monthly'          => $monthly,
                                'weekly'           => $weekly,
                                'can_check_in'     => $can_check_in,
                                'can_check_out'    => $can_check_out,
                                'calendar_minimum' => $calendar_minimum,
                                'calendar_maximum' => $calendar_maximum,
                                'calendar_price'   => $calendar_price,
                                'adult_minimum'    => $adult_minimum,
                                'adult_price'      => $adult_price,
                                'child_minimum'    => $child_minimum,
                                'child_price'      => $child_price,
                                'infant_minimum'   => $infant_minimum,
                                'infant_price'     => $infant_price,
                                'max_people'     => $max_people,
                            ]
                        );
                    }
                }
                return (int)$wpdb->insert_id;
            }

            public function get_availability( $base_id = '', $check_in = '', $check_out = '' )
            {
                global $wpdb;

                $table = $wpdb->prefix . $this->table;

                $where = apply_filters( 'wpbooking_get_availability_where_clause', false, $base_id );

                $sql = "SELECT * FROM {$table} WHERE base_id = {$base_id} AND ( ( CAST( `start` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED) AND CAST( `start` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) OR ( CAST( `end` AS UNSIGNED ) >= CAST( {$check_in} AS UNSIGNED ) AND ( CAST( `end` AS UNSIGNED ) <= CAST( {$check_out} AS UNSIGNED ) ) ) ) " . $where;
                $result = $wpdb->get_results( $sql, ARRAY_A );

                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $item_array = [
                            'id'               => $item[ 'id' ],
                            'post_id'          => $item[ 'post_id' ],
                            'base_id'          => $item[ 'base_id' ],
                            'start'            => date( 'Y-m-d', $item[ 'start' ] ),
                            'end'              => date( 'Y-m-d', strtotime( '+1 day', $item[ 'end' ] ) ),
                            'price'            => (float)$item[ 'price' ],
                            'price_text'       => WPBooking_Currency::format_money( $item[ 'price' ] ),
                            'weekly'           => (float)$item[ 'weekly' ],
                            'monthly'          => (float)$item[ 'monthly' ],
                            'status'           => $item[ 'status' ],
                            'group_day'        => $item[ 'group_day' ],
                            'can_check_in'     => $item[ 'can_check_in' ],
                            'can_check_out'    => $item[ 'can_check_out' ],
                            'calendar_minimum' => $item[ 'calendar_minimum' ],
                            'calendar_maximum' => $item[ 'calendar_maximum' ],
                            'calendar_price'   => $item[ 'calendar_price' ],
                            'adult_minimum'    => $item[ 'adult_minimum' ],
                            'adult_price'      => $item[ 'adult_price' ],
                            'child_minimum'    => $item[ 'child_minimum' ],
                            'child_price'      => $item[ 'child_price' ],
                            'infant_minimum'   => $item[ 'infant_minimum' ],
                            'infant_price'     => $item[ 'infant_price' ],
                            'pricing_type'     => get_post_meta( $item[ 'post_id' ], 'pricing_type', true ),
                            'max_people' => $item['max_people'],
                        ];

                        $return[] = $item_array;
                    }
                }

                return $return;
            }

            public function split_availability( $result = [], $check_in = '', $check_out = '' )
            {
                $return = [];

                if ( !empty( $result ) ) {
                    foreach ( $result as $item ) {
                        $check_in  = (int)$check_in;
                        $check_out = (int)$check_out;

                        $start = strtotime( $item[ 'start' ] );
                        $end   = strtotime( '-1 day', strtotime( $item[ 'end' ] ) );

                        if ( $start < $check_in && $end >= $check_in ) {
                            $return[ 'insert' ][] = [
                                'post_id'      => $item[ 'post_id' ],
                                'base_id'      => $item[ 'base_id' ],
                                'start'        => strtotime( $item[ 'start' ] ),
                                'end'          => strtotime( '-1 day', $check_in ),
                                'price'        => (float)$item[ 'price' ],
                                'adult_price'  => (float)$item[ 'adult_price' ],
                                'child_price'  => (float)$item[ 'child_price' ],
                                'infant_price' => (float)$item[ 'infant_price' ],
                                'price_init'   => (float)$item[ 'price_init' ],
                                'price_person' => (float)$item[ 'price_person' ],
                                'status'       => $item[ 'status' ],
                                'group_day'    => $item[ 'group_day' ],
                                'start2'       => date( 'Y-m-d H:i:s', strtotime( $item[ 'start' ] ) )
                            ];
                        }

                        if ( $start <= $check_out && $end > $check_out ) {
                            $return[ 'insert' ][] = [
                                'post_id'      => $item[ 'post_id' ],
                                'base_id'      => $item[ 'base_id' ],
                                'start'        => strtotime( '+1 day', $check_out ),
                                'end'          => strtotime( '-1 day', strtotime( $item[ 'end' ] ) ),
                                'price'        => (float)$item[ 'price' ],
                                'adult_price'  => (float)$item[ 'adult_price' ],
                                'child_price'  => (float)$item[ 'child_price' ],
                                'infant_price' => (float)$item[ 'infant_price' ],
                                'price_init'   => (float)$item[ 'price_init' ],
                                'price_person' => (float)$item[ 'price_person' ],
                                'status'       => $item[ 'status' ],
                                'group_day'    => $item[ 'group_day' ],
                                'start2'       => date( 'Y-m-d H:i:s', strtotime( $item[ 'start' ] ) )
                            ];
                        }

                        $return[ 'delete' ][] = [
                            'id' => $item[ 'id' ]
                        ];
                    }
                }

                return $return;
            }

            /**
             * Ajax Handler for save property available for
             *
             * @since  1.0
             * @author dungdt
             *
             */
            function _save_property_available_for()
            {
                $post_id = WPBooking_Input::post( 'post_id' );
                if ( !$post_id ) return false;

                $property_available_for = WPBooking_Input::post( 'property_available_for' );
                update_post_meta( $post_id, 'property_available_for', $property_available_for );
                WPBooking_Service_Model::inst()->where( 'post_id', $post_id )->update( [
                    'property_available_for' => $property_available_for
                ] );

                echo json_encode( [ 'status' => 1 ] );
                die;
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        WPBooking_Calendar_Metabox::inst();
    }