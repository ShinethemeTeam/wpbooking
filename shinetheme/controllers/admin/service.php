<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( !class_exists( 'WPBooking_Admin_Service' ) ) {
        class WPBooking_Admin_Service extends WPBooking_Controller
        {
            private static $_inst;

            function __construct()
            {
                add_action( 'init', [ $this, '_add_taxonomy' ] );
                add_action( 'init', [ $this, '_add_post_type' ], 5 );
                add_action( 'init', [ $this, '_add_metabox' ] );
                add_action( 'save_post', [ $this, '_save_extra_field' ], 10, 2 );
                add_filter( 'wpbooking_settings', [ $this, '_add_settings' ] );

                // Merge Data
                add_action( 'admin_init', [ $this, '_merge_data' ] );

                add_action( 'wp_ajax_wpbooking_autocomplete_post', [ $this, '_autocomplete_post' ] );

                /**
                 * Get header email template
                 *
                 * @author: tienhd
                 * @since : 1.0
                 */
                add_filter( 'wpbooking_header_email_template_html', [ $this, '_get_header_email_template' ] );

                /**
                 * Get header email template
                 *
                 * @author: tienhd
                 * @since : 1.0
                 */
                add_filter( 'wpbooking_footer_email_template_html', [ $this, '_get_footer_email_template' ] );

                /**
                 * Add field filter in list service
                 *
                 * @author: tienhd
                 * @since : 1.0
                 */
                add_action( 'restrict_manage_posts', [ $this, '_service_filter_field' ], 15 );
                add_filter( 'parse_query', [ $this, '_service_filter_meta' ] );

                /**
                 * Add More Columns Head to Manage Service Screen
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_filter( 'manage_posts_columns', [ $this, '_add_service_columns' ] );

                /**
                 * Add Columns Content to Manage Service Screen
                 *
                 * @since  1.0
                 * @author dungdt
                 */
                add_filter( 'manage_posts_custom_column', [ $this, '_add_service_columns_content' ], 10, 2 );


            }


            /**
             * Callback to Add More Columns Head to Manage Service Screen
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $columns
             *
             * @return array
             */
            public function _add_service_columns( $columns )
            {

                if ( $this->get( 'post_type' ) == 'wpbooking_service' ) {
                    $new                             = [];
                    $new[ 'wpbooking_service_type' ] = esc_html__( 'Type', 'wp-booking-management-system' );

                    $columns = array_slice( $columns, 0, 1, true ) +
                        $new +
                        array_slice( $columns, 1, count( $columns ) - 1, true );
                }

                return $columns;
            }


            /**
             * Callback Add Columns Content to Manage Service Screen
             *
             * @since  1.0
             * @author dungdt
             *
             * @param $column_name
             * @param $post_ID
             */

            public function _add_service_columns_content( $column_name, $post_ID )
            {
                switch ( $column_name ) {
                    case "wpbooking_service_type":
                        $service = new WB_Service( $post_ID );
                        echo esc_html( $service->get_type_name() );

                        break;
                }

            }

            function _autocomplete_post()
            {
                $res = [];

                $type                     = $this->post( 'type' );
                $args[ 'post_type' ]      = $type;
                $args[ 'post_status' ]    = 'publish';
                $args[ 's' ]              = $this->post( 'q' );
                $args[ 'posts_per_page' ] = 10;
                $args[ 'post__not_in' ]   = $this->post( 'post__not_in' );

                $query = new WP_Query( $args );

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $res[] = [
                        'id'      => get_the_ID(),
                        'text'    => get_the_title(),
                        'thumb'   => get_the_post_thumbnail(),
                        'address' => get_post_meta( get_the_ID(), 'address', true )
                    ];
                }

                wp_reset_postdata();

                echo json_encode( $res );
                die;
            }

            function _save_extra_field( $post_id, $post_object )
            {
                if ( get_post_type( $post_id ) != 'wpbooking_service' ) return false;

                WPBooking_Service_Model::inst()->save_extra( $post_id );
                do_action( 'wpbooking_saved_service', $post_id, $post_object );
            }

            function _add_settings( $settings )
            {
                $settings[ 'services' ] = [
                    'name'     => esc_html__( "Services", 'wp-booking-management-system' ),
                    'sections' => apply_filters( 'wpbooking_service_setting_sections', [] )
                ];

                return $settings;
            }

            function _add_taxonomy()
            {

            }

            function _add_post_type()
            {
                $labels = [
                    'name'               => esc_html__( 'Service', 'wp-booking-management-system' ),
                    'singular_name'      => esc_html__( 'Service', 'wp-booking-management-system' ),
                    'menu_name'          => esc_html__( 'Services', 'wp-booking-management-system' ),
                    'name_admin_bar'     => esc_html__( 'Service', 'wp-booking-management-system' ),
                    'add_new'            => esc_html__( 'Add New', 'wp-booking-management-system' ),
                    'add_new_item'       => esc_html__( 'Add New Service', 'wp-booking-management-system' ),
                    'new_item'           => esc_html__( 'New Service', 'wp-booking-management-system' ),
                    'edit_item'          => esc_html__( 'Edit Service', 'wp-booking-management-system' ),
                    'view_item'          => esc_html__( 'View Service', 'wp-booking-management-system' ),
                    'all_items'          => esc_html__( 'All Services', 'wp-booking-management-system' ),
                    'search_items'       => esc_html__( 'Search for Services', 'wp-booking-management-system' ),
                    'parent_item_colon'  => esc_html__( 'Parent Services:', 'wp-booking-management-system' ),
                    'not_found'          => esc_html__( 'Not found services.', 'wp-booking-management-system' ),
                    'not_found_in_trash' => esc_html__( 'Not found services in Trash.', 'wp-booking-management-system' )
                ];

                $args = [
                    'labels'             => $labels,
                    'description'        => esc_html__( 'Description.', 'wp-booking-management-system' ),
                    'public'             => true,
                    'publicly_queryable' => true,
                    'show_ui'            => true,
                    'show_in_menu'       => true,
                    'query_var'          => true,
                    'rewrite'            => [ 'slug' => apply_filters( 'wpbooking_service_slug', 'service' ) ],
                    'capability_type'    => 'post',
                    'has_archive'        => ( $page_id = wpbooking_get_option( 'archive-page' ) ) && get_post( $page_id ) ? get_page_uri( $page_id ) : 'all-services',
                    'hierarchical'       => false,
                    'menu_icon'          => 'dashicons-tickets-alt',
                    'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ]
                ];

                register_post_type( 'wpbooking_service', $args );


                // Default Taxonomy
                $labels = [
                    'name'              => esc_html__( 'Amenities', 'wp-booking-management-system' ),
                    'singular_name'     => esc_html__( 'Amenity', 'wp-booking-management-system' ),
                    'search_items'      => esc_html__( 'Search for Amenity', 'wp-booking-management-system' ),
                    'all_items'         => esc_html__( 'All Amenities', 'wp-booking-management-system' ),
                    'parent_item'       => esc_html__( 'Parent Amenity', 'wp-booking-management-system' ),
                    'parent_item_colon' => esc_html__( 'Parent Amenity:', 'wp-booking-management-system' ),
                    'edit_item'         => esc_html__( 'Edit Amenity', 'wp-booking-management-system' ),
                    'update_item'       => esc_html__( 'Update Amenity', 'wp-booking-management-system' ),
                    'add_new_item'      => esc_html__( 'Add New Amenity', 'wp-booking-management-system' ),
                    'new_item_name'     => esc_html__( 'New Amenity Name', 'wp-booking-management-system' ),
                    'menu_name'         => esc_html__( 'Amenity', 'wp-booking-management-system' ),
                ];

                $args = [
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => false,
                    'query_var'         => true,
                    'rewrite'           => [ 'slug' => 'amenities' ],
                    'meta_box_cb'       => false
                ];
                $args = apply_filters( 'wpbooking_register_amenity_taxonomy', $args );

                register_taxonomy( 'wpbooking_amenity', [ 'wpbooking_service' ], $args );

                WPBooking_Assets::add_css( "#wpbooking_amenitydiv{display:none!important}" );


                // Extra Services
                $labels = [
                    'name'              => esc_html__( 'Extra Services', 'wp-booking-management-system' ),
                    'singular_name'     => esc_html__( 'Extra Service', 'wp-booking-management-system' ),
                    'search_items'      => esc_html__( 'Search Extra Services', 'wp-booking-management-system' ),
                    'all_items'         => esc_html__( 'All Extra Services', 'wp-booking-management-system' ),
                    'parent_item'       => esc_html__( 'Parent Extra Service', 'wp-booking-management-system' ),
                    'parent_item_colon' => esc_html__( 'Parent Extra Service:', 'wp-booking-management-system' ),
                    'edit_item'         => esc_html__( 'Edit Extra Service', 'wp-booking-management-system' ),
                    'update_item'       => esc_html__( 'Update Extra Service', 'wp-booking-management-system' ),
                    'add_new_item'      => esc_html__( 'Add New Extra Service', 'wp-booking-management-system' ),
                    'new_item_name'     => esc_html__( 'New Extra Service Name', 'wp-booking-management-system' ),
                    'menu_name'         => esc_html__( 'Extra Service', 'wp-booking-management-system' ),
                ];

                $args = [
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => false,
                    'query_var'         => true,
                ];
                $args = apply_filters( 'wpbooking_register_extra_services_taxonomy', $args );

                register_taxonomy( 'wpbooking_extra_service', [ 'wpbooking_service' ], $args );

                WPBooking_Assets::add_css( "#wpbooking_extra_servicediv{display:none!important}" );

                WPBooking_Taxonomy_Metabox::inst()->add_metabox( [
                    'id'       => 'extra_services_info',
                    'taxonomy' => [ 'wpbooking_extra_service' ],
                    'fields'   => [
                        [
                            'type'     => 'service-type-checkbox',
                            'id'       => 'service_type',
                            'label'    => esc_html__( 'Extra Service', 'wp-booking-management-system' ),
                            'add_meta' => true // ,
                        ]
                    ]
                ] );

            }

            function _add_metabox()
            {
                $metabox = WPBooking_Metabox::inst();

                $settings = [
                    'id'       => 'st_post_metabox',
                    'title'    => esc_html__( 'Information', 'wp-booking-management-system' ),
                    'desc'     => '',
                    'pages'    => [ 'wpbooking_service' ],
                    'context'  => 'normal',
                    'priority' => 'high',

                ];

                $metabox->register_meta_box( $settings );
            }

            function _merge_data()
            {
                if ( $this->get( 'wb_merge_data' ) ) {
                    $query = new WP_Query( [
                        'post_type'      => 'wpbooking_service',
                        'posts_per_page' => 1000
                    ] );

                    while ( $query->have_posts() ) {
                        $query->the_post();
                        WPBooking_Service_Model::inst()->save_extra( get_the_ID() );
                    }
                    wp_reset_postdata();
                    echo 'done';
                    die;
                }

                if ( $this->get( 'wb_setup_term' ) ) {
                    do_action( 'wpbooking_do_setup' );
                }
            }

            /**
             * Get header email html
             *
             * @since: 1.0
             *
             * @return bool|mixed|void
             */
            public function _get_header_email_template()
            {
                return wpbooking_get_option( 'email_header', '' );
            }

            /**
             * Get header email html
             *
             * @since: 1.0
             *
             * @return bool|mixed|void
             */
            public function _get_footer_email_template()
            {
                return wpbooking_get_option( 'email_footer', '' );
            }

            /**
             * Add filter field service type
             *
             * @param $post_type
             */
            function _service_filter_field( $post_type )
            {
                if ( $post_type == 'wpbooking_service' ) {
                    $service_types = WPBooking_Service_Controller::inst()->get_service_types();

                    echo '<select name="service_type">';
                    echo '<option value="0">' . esc_html__( 'All services', 'wp-booking-management-system' ) . '</option>';
                    foreach ( $service_types as $key => $val ) {
                        echo '<option ' . selected( WPBooking_Input::get( 'service_type' ), $key, false ) . ' value="' . esc_attr( $key ) . '">' . esc_html( $val->get_info( 'label' ) ) . '</option>';
                    }

                    echo '</select>';

                }
            }

            /**
             * Add meta query for filter service type
             *
             * @param $query
             */
            function _service_filter_meta( $query )
            {
                if ( is_admin() AND $query->query[ 'post_type' ] == 'wpbooking_service' ) {
                    $query_vars                 = &$query->query_vars;
                    $query_vars[ 'meta_query' ] = [];
                    if ( WPBooking_Input::get( 'service_type' ) ) {
                        $query_vars[ 'meta_query' ][] = [
                            'field'   => 'service_type',
                            'value'   => WPBooking_Input::get( 'service_type' ),
                            'type'    => 'char',
                            'compare' => '='
                        ];
                    }
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

        WPBooking_Admin_Service::inst();
    }