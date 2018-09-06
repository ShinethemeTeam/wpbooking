<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( !class_exists( 'WPBooking_Abstract_Service_Type' ) ) {
        abstract class WPBooking_Abstract_Service_Type
        {
            protected $type_id = false;
            protected $type_info = [];
            protected $settings = [];
            protected $metabox = [];

            function __construct()
            {
                if ( !$this->type_id ) return false;
                $this->type_info = wp_parse_args( $this->type_info, [
                    'label'       => '',
                    'labels'      => '',
                    'description' => ''
                ] );

                add_filter( 'init', [ $this, '_register_type' ] );
                add_filter( 'wpbooking_service_setting_sections', [ $this, '_add_setting_section' ], 20 );
                add_filter( 'wpbooking_get_order_form_' . $this->type_id, [ $this, '_get_order_form' ] );

                /*Change Search*/
                add_filter( 'wpbooking_add_page_archive_search', [ $this, '_add_page_archive_search' ] );


                add_filter( 'wpbooking_get_order_form_id_' . $this->type_id, [ $this, 'get_order_form_id' ] );

                /**
                 * Add to cart add Need Customer Confirm
                 * @see WPBooking_Order::add_to_cart();
                 */
                add_filter( 'wpbooking_service_need_customer_confirm', [ $this, '_get_customer_confirm' ], 10, 3 );
                add_filter( 'wpbooking_service_need_partner_confirm', [ $this, '_get_partner_confirm' ], 10, 3 );

                add_action( 'wpbooking_cart_item_information_' . $this->type_id, [ $this, '_show_cart_item_information' ], 10, 2 );
                add_action( 'wpbooking_order_item_information_' . $this->type_id, [ $this, '_show_order_item_information' ], 10, 2 );


                /**
                 * Change Related Query Search
                 * @since  1.0
                 * @author dungdt
                 */
                add_action( 'wpbooking_before_related_query_' . $this->type_id, [ $this, '_add_related_query_hook' ], 10, 2 );


                /**
                 * Change Default Query Search
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                // Check current service type
                if ( ( $service_type = WPBooking_Input::get( 'service_type' ) and $service_type == $this->type_id ) ) {
                    add_action( 'init', [ $this, '_add_default_query_hook' ] );
                }

                /**
                 * Change status query archive wpbooking service page
                 *
                 * @since  1.0
                 * @author quandq
                 *
                 */
                if ( !is_admin() )
                    add_action( 'pre_get_posts', [ $this, '_change_post_status_query' ] );


                add_filter( 'wpml_duplicate_generic_string', [ $this, 'set_location_wpml_duplicated' ], 10, 3 );
            }

            public function set_location_wpml_duplicated( $value_to_filter, $target_language, $meta_data )
            {
                $context = $meta_data[ 'context' ];
                if ( $value_to_filter !== '' && $context ) {
                    $meta      = [
                        'location_id' => 'wpbooking_location',
                        'tour_type'   => 'wb_tour_type',
                    ];
                    $attribute = $meta_data[ 'attribute' ];
                    if ( $context == 'custom_field' && $attribute == 'value' && in_array( $meta_data[ 'key' ], array_keys( $meta ) ) ) {
                        $value_to_filter = apply_filters( 'wpml_object_id', $value_to_filter, $meta[ $meta_data[ 'key' ] ], false, $target_language );
                    }
                }

                return $value_to_filter;

            }

            /**
             * Change status query archive wpbooking service page
             *
             * @since  1.0
             * @author quandq
             *
             * @param $q
             */
            function _change_post_status_query( $q )
            {
                if ( $q->is_main_query() ) {
                    // Only Modify Archive, Tax page
                    if ( !$q->is_post_type_archive( 'wpbooking_service' ) && !$q->is_tax( get_object_taxonomies( 'wpbooking_service' ) ) ) return;
                    $q->set( 'post_status', 'publish' );
                }
            }

            function set_metabox( $metabox )
            {
                $this->metabox = $metabox;
            }

            function get_metabox()
            {
                return apply_filters( 'wpbooking_metabox_service_' . $this->type_id, $this->metabox );
            }

            /**
             * Show Cart Item Information Based on Service Type ID
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $cart_item
             */
            function _show_cart_item_information( $cart_item )
            {
                $cart_item = wp_parse_args( $cart_item, [
                    'need_customer_confirm' => '',
                    'order_form'            => [],
                    'post_id'               => false
                ] );

                $terms = wp_get_post_terms( $cart_item[ 'post_id' ], 'wpbooking_room_type' );
                if ( !empty( $terms ) and !is_wp_error( $terms ) ) {
                    $output[] = '<div class="wpbooking-room-type">';
                    $key      = 0;
                    foreach ( $terms as $term ) {
                        $html = sprintf( '<a href="%s">%s</a>', get_term_link( $term, 'wpbooking_room_type' ), $term->name );
                        if ( $key < count( $term ) - 1 ) {
                            $html .= ',';
                        }
                        $output[] = $html;
                        $key++;
                    }

                    $output[] = '</div>';

                    $output = apply_filters( 'wpbooking_room_show_room_type', $output );
                    echo implode( ' ', $output );
                }

                // Extra price and taxs
                $extra_price_html = [];

                // Show Order Form Field
                $order_form = $cart_item[ 'order_form' ];
                if ( ( !empty( $order_form ) and is_array( $order_form ) ) or !empty( $extra_price_html ) ) {
                    echo '<div class="cart-item-order-form-fields-wrap">';
                    echo '<span class="booking-detail-label">' . esc_html__( 'Booking Details:', 'wp-booking-management-system' ) . '</span>';
                    echo "<ul class='cart-item-order-form-fields'>";
                    foreach ( $order_form as $key => $value ) {

                        $value = wp_parse_args( $value, [
                            'data'       => '',
                            'field_type' => ''
                        ] );

                        $value_html = '';

                        if ( $value_html ) {
                            printf( "<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>", $key, $value[ 'title' ], $value_html );
                        }

                        do_action( 'wpbooking_form_field_to_html', $value );
                        do_action( 'wpbooking_form_field_to_html_' . $value[ 'field_type' ], $value );
                    }

                    if ( $extra_price_html ) {
                        echo implode( "\r\n", $extra_price_html );
                    }
                    echo "</ul>";
                    echo '<span class="show-more-less"><span class="more">' . esc_html__( 'More', 'wp-booking-management-system' ) . ' <i class="fa fa-angle-double-down"></i></span><span class="less">' . esc_html__( 'Less', 'wp-booking-management-system' ) . ' <i class="fa fa-angle-double-up"></i></span></span>';
                    echo "</div>";
                }

            }

            /**
             * Show Order Item Information Based on Service Type ID
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $order_item
             */
            function _show_order_item_information( $order_item )
            {
                $order_item = wp_parse_args( $order_item, [
                    'need_customer_confirm' => '',
                    'order_form'            => '',
                    'payment_status'        => '',
                    'status'                => ''
                ] );

                // Show Order Form Field
                $order_form_string = $order_item[ 'order_form' ];

                if ( $order_form_string and $order_form = unserialize( $order_form_string ) and !empty( $order_form ) and is_array( $order_form ) ) {

                    echo '<div class="order-item-form-fields-wrap">';
                    echo '<span class="booking-detail-label">' . esc_html__( 'Booking Details:', 'wp-booking-management-system' ) . '</span>';
                    echo "<ul class='order-item-form-fields'>";
                    foreach ( $order_form as $key => $value ) {

                        $value = wp_parse_args( $value, [
                            'data'       => '',
                            'field_type' => ''
                        ] );

                        $value_html = '';

                        if ( $value_html ) {
                            printf( "<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>", $key, $value[ 'title' ], $value_html );
                        }

                        do_action( 'wpbooking_form_field_to_html', $value );
                        do_action( 'wpbooking_form_field_to_html_' . $value[ 'field_type' ], $value );
                    }
                    echo "</ul>";
                    echo '<span class="show-more-less"><span class="more">' . esc_html__( 'More', 'wp-booking-management-system' ) . ' <i class="fa fa-angle-double-down"></i></span><span class="less">' . esc_html__( 'Less', 'wp-booking-management-system' ) . ' <i class="fa fa-angle-double-up"></i></span></span>';
                    echo "</div>";
                }


            }

            /**
             * Filter the Order Form HTML
             */
            function _get_order_form()
            {
                $form_id = $this->get_option( 'order_form' );
                $post    = get_post( $form_id );
                if ( $post ) {
                    return apply_filters( 'the_content', $post->post_content );
                }
            }

            /**
             * Get the Order Form ID in the Settings
             * @return bool|mixed|void
             */
            function get_order_form_id()
            {
                return $form_id = $this->get_option( 'order_form' );
            }

            /**
             * Filter Function for Check Service Type is require Customer Confirm the Booking (Confirm by send the email)
             *
             * @param $need
             * @param $post_id
             * @param $service_type
             *
             * @return bool|mixed|void
             */
            function _get_customer_confirm( $need, $post_id, $service_type )
            {
                if ( $this->type_id == $service_type ) {
                    $need = $this->get_option( 'customer_confirm' );

                    if ( $meta = get_post_meta( $post_id, 'require_customer_confirm', true ) ) $need = $meta;
                }

                return $need;
            }

            /**
             * Filter Function for Check Service Type is require Partner Confirm the Booking (Confirm by send the email)
             *
             * @param $need
             * @param $post_id
             * @param $service_type
             *
             * @return bool|mixed|void
             */
            function _get_partner_confirm( $need, $post_id, $service_type )
            {
                if ( $this->type_id == $service_type ) {
                    $need = $this->get_option( 'partner_confirm' );

                    if ( $meta = get_post_meta( $post_id, 'require_partner_confirm', true ) ) $need = $meta;
                }

                return $need;
            }

            function _add_setting_section( $sections = [] )
            {
                $settings = $this->get_settings_fields();
                if ( !empty( $settings ) ) {
                    foreach ( $settings as $key => $value ) {
                        if ( !empty( $value[ 'id' ] ) )
                            $settings[ $key ][ 'id' ] = 'service_type_' . $this->type_id . '_' . $value[ 'id' ];
                    }
                }
                $sections[ 'service_type_' . $this->type_id ] = [
                    'id'     => 'service_type_' . $this->type_id,
                    'label'  => $this->get_info( 'label' ),
                    'fields' => $settings
                ];

                return $sections;
            }

            function get_settings_fields()
            {

                return apply_filters( 'wpbooking_service_type_' . $this->type_id . '_settings_fields', $this->settings, $this->type_id );
            }

            function get_info( $key = false )
            {
                $info = apply_filters( 'wpbooking_service_type_info', $this->type_info );
                $info = apply_filters( 'wpbooking_service_type_' . $this->type_id . '_info', $info );

                if ( $key ) {

                    $data = isset( $info[ $key ] ) ? $info[ $key ] : false;

                    $data = apply_filters( 'wpbooking_service_type_info_' . $key, $data );
                    $data = apply_filters( 'wpbooking_service_type_' . $this->type_id . '_info_' . $key, $data );

                    return $data;
                }

                return $info;
            }

            function get_option( $key, $default = false )
            {
                return wpbooking_get_option( 'service_type_' . $this->type_id . '_' . $key, $default );
            }

            /**
             * Get All Extra Services From Settings Page
             *
             * @since  1.0
             * @author dungdt
             *
             * @return mixed|void
             */
            function get_extra_services()
            {

                $terms          = get_terms( 'wpbooking_extra_service', [ 'hide_empty' => false ] );
                $extra_services = [];
                if ( !empty( $terms ) and !is_wp_error( $terms ) ) {
                    foreach ( $terms as $key => $value ) {

                        if ( get_term_meta( $value->term_id, 'service_type', true ) )
                            $extra_services[ $value->term_id ] = [
                                'title'       => $value->name,
                                'description' => $value->description
                            ];
                    }
                }

                return $extra_services;
            }

            /**
             * Hook Callback Init to register Type
             *
             * @since  1.0
             * @author dungdt
             */
            function _register_type()
            {
                WPBooking_Service_Controller::inst()->register_type( $this->type_id, $this );

            }

            function _add_page_archive_search( $args )
            {

                return $args;
            }

            function _service_query_args( $args )
            {
                return $args;
            }

            /**
             * Add Hook for Related Query
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $post_id
             * @param $service_type
             */
            function _add_related_query_hook( $post_id, $service_type )
            {
                global $wpdb;

                $table        = WPBooking_Service_Model::inst()->get_table_name( false );
                $table_prefix = WPBooking_Service_Model::inst()->get_table_name();

                $injection = WPBooking_Query_Inject::inst();

                $injection->join( $table, $table_prefix . '.post_id=' . $wpdb->posts . '.ID' );
                $injection->groupby( $wpdb->posts . '.ID' );
                $injection->where( $table_prefix . '.enable_property', 'on' );
                $tax_query = $injection->get_arg( 'tax_query' );

                // Locations
                if ( $location_id = get_post_meta( $post_id, 'location_id', true ) ) {
                    $tax_query[] = [
                        'relation' => 'AND',
                        [
                            'taxonomy'         => 'wpbooking_location',
                            'field'            => 'id',
                            'terms'            => $location_id,
                            'include_children' => true,
                            'operator'         => 'IN'
                        ]
                    ];
                    $injection->add_arg( 'tax_query', $tax_query );
                }

                //service type
                $meta_query = $injection->get_arg( 'meta_query' );
                if ( $this->type_id ) {
                    $meta_query[] = [
                        'relation' => 'AND',
                        [
                            'key'     => 'service_type', //(string) - Custom field key.
                            'value'   => $this->type_id,
                            'type'    => 'CHAR',
                            'compare' => '='
                        ]
                    ];
                    $injection->add_arg( 'meta_query', $meta_query );
                }

            }

            /**
             * Add Query Hook in Archive Page
             *
             * @since  1.0
             * @author dungdt
             *
             */
            function _add_default_query_hook()
            {

                global $wpdb;
                $table        = WPBooking_Service_Model::inst()->get_table_name( false );
                $table_prefix = WPBooking_Service_Model::inst()->get_table_name();

                $injection = WPBooking_Query_Inject::inst();

                $injection->join( $table, $table_prefix . '.post_id=' . $wpdb->posts . '.ID' );
                $injection->groupby( $wpdb->posts . '.ID' );

                // Status
                $injection->add_arg( 'post_status', 'publish' );

                // Service Type
                if ( $service_type = WPBooking_Input::get( 'service_type' ) ) {
                    $injection->where( $table_prefix . '.service_type', $service_type );
                }


                // Enable
                $injection->where( $table_prefix . '.enable_property', 'on' );

                // Location
                if ( $location_id = WPBooking_Input::get( 'location_id' ) ) {
                    $childs = get_term_children( $location_id, 'wpbooking_location' );

                    $ids = [ $location_id ];

                    if ( !empty( $childs ) and !is_wp_error( $childs ) ) {
                        foreach ( $childs as $key => $value ) {
                            $ids[] = $value;
                        }
                    }
                    if ( !empty( $ids ) ) {
                        $injection->where_in( $table_prefix . '.location_id', $ids );
                    }

                }

                do_action( 'wpbooking_add_default_query_hook' );

            }

            /**
             * Get Thumb-size from setting page
             *
             * @since  1.0
             * @author dungdt
             *
             * @param bool $default
             *
             * @return bool|mixed|void
             */
            function thumb_size( $default = false )
            {
                return $this->get_option( 'thumb_size', $default );
            }

            /**
             * Quick method for get $_POST data
             *
             * @since 1.0
             * @auhor dungdt
             *
             * @param      $key
             * @param null $default
             *
             * @return bool
             */
            function post( $key, $default = null )
            {
                return WPBooking_Input::post( $key, $default );
            }


            /**
             * Quick method for get $_GET data
             *
             * @since 1.0
             * @auhor dungdt
             *
             * @param      $key
             * @param null $default
             *
             * @return bool
             */
            function get( $key, $default = null )
            {
                return WPBooking_Input::get( $key, $default );
            }

            /**
             * Quick method for get $_REQUEST data
             *
             * @since 1.0
             * @auhor dungdt
             *
             * @param      $key
             * @param null $default
             *
             * @return bool
             */
            function request( $key, $default = null )
            {
                return WPBooking_Input::request( $key, $default );
            }

            abstract public function get_search_fields();

        }
    }