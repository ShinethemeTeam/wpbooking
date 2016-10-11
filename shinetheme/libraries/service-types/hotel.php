<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 3:47 PM
 */
if (!class_exists('WPBooking_Hotel_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
    class WPBooking_Hotel_Service_Type extends WPBooking_Abstract_Service_Type
    {
        static $_inst = false;

        protected $type_id = 'hotel';

        function __construct()
        {
            $this->type_info = array(
                'label' => __( "Hotel" , 'wpbooking' ) ,
                'desc'  => esc_html__( 'Chỗ nghỉ cho khách du lịch, thường có nhà hàng, phòng họp và các dịch vụ khác dành cho khách' , 'wpbooking' )
            );

            parent::__construct();

            add_action( 'init' , array( $this , '_add_init_action' ) );
            add_action( 'wpbooking_do_setup' , array( $this , '_add_default_term' ) );


            /**
             * Ajax Show Room Form
             *
             * @since 1.0
             * @author dungdt
             */
            add_action( 'wp_ajax_wpbooking_show_room_form' , array( $this , '_ajax_room_edit_template' ) );

            /**
             * Ajax Save Room Data
             *
             * @since 1.0
             * @author dungdt
             */
            add_action( 'wp_ajax_wpbooking_save_hotel_room' , array( $this , '_ajax_save_room' ) );

            /**
             * Ajax delete room item
             *
             * @since 1.0
             * @author Tien37
             */
            add_action( 'wp_ajax_wpbooking_del_room_item' , array( $this , '_ajax_del_room_item' ) );

            /**
             * Hide custom Taxonomy in admin menu
             *
             * @since 1.0
             * @author dungdt
             */
            add_action( 'admin_menu' , array( $this , '_hide_custom_taxonomy_admin_menu' ) );

        }


        /**
         * Hide custom Taxonomy in admin menu
         *
         * @since 1.0
         * @author quandq
         */
        function _hide_custom_taxonomy_admin_menu()
        {
            global $submenu;
            $menu_page           = 'edit.php?post_type=wpbooking_service';
            $taxonomy_admin_page = array(
                //Hotel Room
                'edit-tags.php?taxonomy=wpbooking_room_type&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_amenity&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_bathroom&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_media_technology&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_services_extra&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_outdoor_view&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_accessibility&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_room_entertainment&amp;post_type=wpbooking_service' ,

                //Hotel
                'edit-tags.php?taxonomy=wb_hotel_room_food_drink&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_activity&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_pool&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_food&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_recep_serv&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_common_area&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_family_services&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_transport&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_cleaning_service&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_business_facility&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_shop&amp;post_type=wpbooking_service' ,
                'edit-tags.php?taxonomy=wb_hotel_miscellaneous&amp;post_type=wpbooking_service' ,
            );

            foreach( $submenu[ $menu_page ] as $index => $submenu_item ) {
                if(in_array( $submenu_item[ 2 ] , $taxonomy_admin_page )) {
                    unset( $submenu[ $menu_page ][ $index ] );
                }
            }
        }


        public function _add_init_action()
        {
            $labels = array(
                'name'               => _x( 'Hotel Room' , 'post type general name' , 'wpbooking' ) ,
                'singular_name'      => _x( 'Hotel Room' , 'post type singular name' , 'wpbooking' ) ,
                'menu_name'          => _x( 'Hotel Room' , 'admin menu' , 'wpbooking' ) ,
                'name_admin_bar'     => _x( 'Hotel Room' , 'add new on admin bar' , 'wpbooking' ) ,
                'add_new'            => _x( 'Add New' , 'Hotel Room' , 'wpbooking' ) ,
                'add_new_item'       => __( 'Add New Hotel Room' , 'wpbooking' ) ,
                'new_item'           => __( 'New Hotel Room' , 'wpbooking' ) ,
                'edit_item'          => __( 'Edit Hotel Room' , 'wpbooking' ) ,
                'view_item'          => __( 'View Hotel Room' , 'wpbooking' ) ,
                'all_items'          => __( 'All Hotel Room' , 'wpbooking' ) ,
                'search_items'       => __( 'Search Hotel Room' , 'wpbooking' ) ,
                'parent_item_colon'  => __( 'Parent Hotel Room:' , 'wpbooking' ) ,
                'not_found'          => __( 'No Hotel Room found.' , 'wpbooking' ) ,
                'not_found_in_trash' => __( 'No Hotel Room found in Trash.' , 'wpbooking' )
            );

            $args = array(
                'labels'             => $labels ,
                'description'        => __( 'Description.' , 'wpbooking' ) ,
                'public'             => true ,
                'publicly_queryable' => true ,
                'show_ui'            => true ,
                'show_in_menu'       => false ,
                'query_var'          => true ,
                'capability_type'    => 'post' ,
                'hierarchical'       => false ,
                //'menu_position'      => '59.9',
                'supports'           => array( 'title' , 'editor' , 'author' , 'thumbnail' , 'excerpt' , 'comments' )
            );

            register_post_type( 'wpbooking_hotel_room' , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Type' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Type' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Type' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Type' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Type' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Type:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Type' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Type' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Type' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Type Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Type' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => false ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-type' ) ,
            );
            register_taxonomy( 'wb_hotel_room_type' , array( 'wpbooking_hotel_room' ) , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Amenities' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Amenities' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Amenities' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Amenities' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Amenities' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Amenities:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Amenities' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Amenities' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Amenity' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Amenity Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Amenities' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-amenity' ) ,
            );
            register_taxonomy( 'wb_hotel_room_amenity' , array( 'wpbooking_service' ) , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Bathroom' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Bathroom' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Bathroom' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Bathroom' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Bathroom' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Bathroom:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Bathroom' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Bathroom' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add Room New Bathroom' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Bathroom Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Bathroom' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-bathroom' ) ,
            );
            register_taxonomy( 'wb_hotel_room_bathroom' , array( 'wpbooking_service' ) , $args );
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Media & technology' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Media & technology' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Media & technology' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Media & technology' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Media & technology' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Media & technology:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Media & technology' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Media & technology' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Media & technology' , 'wpbooking' ) ,
                'new_item_name'     => __( 'NewRoom  Media & technology Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Media & technology' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-media-technology' ) ,
            );
            register_taxonomy( 'wb_hotel_room_media_technology' , array( 'wpbooking_service' ) , $args );
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Food and Drink' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Food and Drink' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Food and Drink' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Food and Drink' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Food and Drink' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Food and Drink:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Food and Drink' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Food and Drink' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Food and Drink' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Food and Drink Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Food and Drink' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-food-drink' ) ,
            );
            register_taxonomy( 'wb_hotel_room_food_drink' , array( 'wpbooking_service' ) , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Services & extras' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Services & extras' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Services & extras' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Services & extras' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Services & extras' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Services & extras:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Services & extras' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Services & extras' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Services & extras' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Services & extras Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Services & extras' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-services-extra' ) ,
            );
            register_taxonomy( 'wb_hotel_room_services_extra' , array( 'wpbooking_service' ) , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Outdoor & view' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Outdoor & view' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Outdoor & view' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Outdoor & view' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Outdoor & view' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Outdoor & view:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Outdoor & view' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Outdoor & view' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Outdoor & view' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Outdoor & view Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Outdoor & view' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-outdoor-view' ) ,
            );
            register_taxonomy( 'wb_hotel_room_outdoor_view' , array( 'wpbooking_service' ) , $args );
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Accessibility' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Accessibility' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Accessibility' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Accessibility' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Accessibility' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Accessibility:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Accessibility' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Accessibility' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Accessibility' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Accessibility Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Accessibility' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-accessibility' ) ,
            );
            register_taxonomy( 'wb_hotel_room_accessibility' , array( 'wpbooking_service' ) , $args );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Entertainment & Family Services' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Room Entertainment & Family Services' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Room Entertainment & Family Services' , 'wpbooking' ) ,
                'all_items'         => __( 'All Room Entertainment & Family Services' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Room Entertainment & Family Services' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Room Entertainment & Family Services:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Room Entertainment & Family Services' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Room Entertainment & Family Services' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Room Entertainment & Family Services' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Room Entertainment & Family Services Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Room Entertainment & Family Services' , 'wpbooking' ) ,
            );
            $args   = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-room-entertainment-services' ) ,
            );
            register_taxonomy( 'wb_hotel_room_entertainment' , array( 'wpbooking_service' ) , $args );


            // Metabox
            $this->set_metabox( array(
                'general_tab'     => array(
                    'label'  => esc_html__( '1. Property Information' , 'wpbooking' ) ,
                    'fields' => array(
                        array(
                            'type' => 'open_section' ,
                        ) ,
                        array(
                            'label' => __( "About Your Hotel" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Basic information' , 'wpbooking' ) ,
                        ) ,
                        array(
                            'id'    => 'enable_property' ,
                            'label' => __( "Enable Property" , 'wpbooking' ) ,
                            'type'  => 'on-off' ,
                            'std'   => 'on' ,
                            'desc'  => esc_html__( 'Listing will appear in search results.' , 'wpbooking' ) ,
                        ) ,
                        array(
                            'id'    => 'star_rating' ,
                            'label' => __( "Star Rating" , 'wpbooking' ) ,
                            'type'  => 'star-select' ,
                            'desc'  => esc_html__( 'Standard of hotel from 1 to 5 star.' , 'wpbooking' ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'Total Room' , 'wpbooking' ) ,
                            'id'    => 'total_room' ,
                            'desc'  => esc_html__( 'Number of rooms in your hotel.' , 'wpbooking' ) ,
                            'type'  => 'text' ,
                            'class' => 'small' ,
                            'std'   => 1
                        ) ,
                        array(
                            'label' => __( 'Website' , 'wpbooking' ) ,
                            'id'    => 'website' ,
                            'type'  => 'text' ,
                            'desc'  => esc_html__( 'Property website (optional)' , 'wpbooking' ) ,
                            'class' => 'small'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Hotel Location" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Hotel's address and your contact number" , 'wpbooking' ) ,
                        ) ,
                        array(
                            'label' => __( 'Contact Name' , 'wpbooking' ) ,
                            'id'    => 'contact_name' ,
                            'desc'  => esc_html__( 'Who will receive the letters' , 'wpbooking' ) ,
                            'type'  => 'text' ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'Contact Number' , 'wpbooking' ) ,
                            'id'    => 'contact_number' ,
                            'desc'  => esc_html__( 'The contact phone' , 'wpbooking' ) ,
                            'type'  => 'phone_number' ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label'           => __( 'Address' , 'wpbooking' ) ,
                            'id'              => 'address' ,
                            'type'            => 'address' ,
                            'container_class' => 'mb35' ,
                        ) ,
                        array(
                            'label' => __( 'Map Lat & Long' , 'wpbooking' ) ,
                            'id'    => 'gmap' ,
                            'type'  => 'gmap' ,
                            'desc'  => esc_html__( 'This is the location we will provide guests. Click and drag the marker if you need to move it' , 'wpbooking' )
                        ) ,
                        array(
                            'type'    => 'desc_section' ,
                            'title'   => esc_html__( 'Your address matters! ' , 'wpbooking' ) ,
                            'content' => esc_html__( 'Please make sure to enter your full address including building name, apartment number, etc.' , 'wpbooking' )
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type' => 'section_navigation' ,
                            'prev' => false
                        ) ,

                    )
                ) ,
                'detail_tab'      => array(
                    'label'  => __( '2. Property Details' , 'wpbooking' ) ,
                    'fields' => array(
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Check In & Check Out" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Time to check in, out in your property' , 'wpbooking' )
                        ) ,
                        array(
                            'label'  => esc_html__( 'Check In time' , 'wpbooking' ) ,
                            'desc'   => esc_html__( 'Check In time' , 'wpbooking' ) ,
                            'type'   => 'check_in' ,
                            'fields' => array( 'checkin_from' , 'checkin_to' ) ,// Fields to save
                        ) ,
                        array(
                            'label'  => esc_html__( 'Check Out time' , 'wpbooking' ) ,
                            'desc'   => esc_html__( 'Check Out time' , 'wpbooking' ) ,
                            'type'   => 'check_out' ,
                            'fields' => array( 'checkout_from' , 'checkout_to' ) ,// Fields to save
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Internet" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Internet access is important for many travellers. Free WiFi is a huge selling point, too!' , 'wpbooking' )
                        ) ,
                        array(
                            'label' => __( 'Is internet available to guests?' , 'wpbooking' ) ,
                            'id'    => 'internet_status' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                'no_internet'   => esc_html__( 'No' , 'wpbooking' ) ,
                                'free_internet' => esc_html__( 'Yes, Free' , 'wpbooking' ) ,
                                'paid_internet' => esc_html__( 'Yes, Paid' , 'wpbooking' ) ,
                            ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Connection type' , 'wpbooking' ) ,
                            'id'        => 'internet_connection_type' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'cable' => esc_html__( 'Cable' , 'wpbooking' ) ,
                                'wifi'  => esc_html__( 'Wifi' , 'wpbooking' ) ,
                            ) ,
                            'class'     => 'small' ,
                            'condition' => 'internet_status:not(no_internet)'
                        ) ,
                        array(
                            'label'     => __( 'Connection location' , 'wpbooking' ) ,
                            'id'        => 'internet_connection_location' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'public_area'     => esc_html__( 'Public areas' , 'wpbooking' ) ,
                                'some_rooms'      => esc_html__( 'Some rooms' , 'wpbooking' ) ,
                                'all_rooms'       => esc_html__( 'All rooms' , 'wpbooking' ) ,
                                'entire_property' => esc_html__( 'Entire Property' , 'wpbooking' ) ,
                            ) ,
                            'class'     => 'small' ,
                            'condition' => 'internet_status:not(no_internet)'
                        ) ,
                        array(
                            'label'     => __( 'Price for internet (per day)' , 'wpbooking' ) ,
                            'id'        => 'internet_price' ,
                            'type'      => 'money_input' ,
                            'class'     => 'small' ,
                            'condition' => 'internet_status:not(no_internet)'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Parking lot" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'This information is especially important for those travelling to your accommodation by car.' , 'wpbooking' )
                        ) ,
                        array(
                            'label' => __( 'Is parking available to guests?' , 'wpbooking' ) ,
                            'id'    => 'parking_status' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                'no_parking'   => esc_html__( 'No' , 'wpbooking' ) ,
                                'free_parking' => esc_html__( 'Yes, Free' , 'wpbooking' ) ,
                                'paid_parking' => esc_html__( 'Yes, Paid' , 'wpbooking' ) ,
                            ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Parking lot type' , 'wpbooking' ) ,
                            'id'        => 'parking_lot_type' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'private' => esc_html__( 'Private' , 'wpbooking' ) ,
                                'public'  => esc_html__( 'Public' , 'wpbooking' ) ,
                            ) ,
                            'condition' => 'parking_status:not(no_parking)' ,
                            'class'     => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Parking lot area' , 'wpbooking' ) ,
                            'id'        => 'parking_lot_area' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'onside'  => esc_html__( 'On Side' , 'wpbooking' ) ,
                                'outside' => esc_html__( 'Out Side' , 'wpbooking' ) ,
                            ) ,
                            'condition' => 'parking_status:not(no_parking)' ,
                            'class'     => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Do guests need to reserve a parking space?' , 'wpbooking' ) ,
                            'id'        => 'parking_need_reserve' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'need_reservation'    => esc_html__( 'Reservation Needed' , 'wpbooking' ) ,
                                'no_need_reservation' => esc_html__( 'No Reservation Needed' , 'wpbooking' ) ,
                            ) ,
                            'condition' => 'parking_status:not(no_parking)' ,
                            'class'     => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Price for parking (per day)' , 'wpbooking' ) ,
                            'id'        => 'parking_price' ,
                            'type'      => 'money_input' ,
                            'class'     => 'small' ,
                            'condition' => 'parking_status:not(no_internet)'
                        ) ,
                        array( 'type' => 'close_section' ) ,

                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Breakfast" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate if breakfast is included in the price, or if it\'s an optional add-on.' , 'wpbooking' )
                        ) ,
                        array(
                            'label' => __( 'Is breakfast available to guests?' , 'wpbooking' ) ,
                            'id'    => 'breakfast_status' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                'no_breakfast' => esc_html__( 'No' , 'wpbooking' ) ,
                                'yes_included' => esc_html__( "Yes, it's included in the price" , 'wpbooking' ) ,
                                'no_optional'  => esc_html__( "Yes, it's optional" , 'wpbooking' ) ,
                            ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Price for breakfast (per person, per day)' , 'wpbooking' ) ,
                            'id'        => 'breakfast_price' ,
                            'type'      => 'money_input' ,
                            'class'     => 'small' ,
                            'condition' => 'breakfast_status:not(no_breakfast)'
                        ) ,
                        array(
                            'label'         => __( 'What kind of breakfast is available?' , 'wpbooking' ) ,
                            'id'            => 'breakfast_types' ,
                            'type'          => 'repeat_dropdown' ,
                            'value'         => WPBooking_Config::inst()->item( 'breakfast_types' ) ,
                            'class'         => 'small' ,
                            'condition'     => 'breakfast_status:not(no_breakfast)' ,
                            'add_new_label' => esc_html__( 'Add another breakfast type' , 'wpbooking' )
                        ) ,
                        array( 'type' => 'close_section' ) ,

                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Children" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'id'    => 'children_allowed' ,
                            'label' => esc_html__( 'Can you accommodate children?' , 'wpbooking' ) ,
                            'type'  => 'on-off' ,
                        ) ,
                        array( 'type' => 'close_section' ) ,

                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Pet" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Some guests like to travel with their furry friends. Indicate if you allow pets and if any charges apply.' , 'wpbooking' )
                        ) ,

                        array(
                            'label' => __( 'Do you allow pets' , 'wpbooking' ) ,
                            'id'    => 'pets_allowed' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                'no'      => esc_html__( 'No' , 'wpbooking' ) ,
                                'yes'     => esc_html__( "Yes" , 'wpbooking' ) ,
                                'request' => esc_html__( "Upon request" , 'wpbooking' ) ,
                            ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label'     => __( 'Are there additional charges for pets?' , 'wpbooking' ) ,
                            'id'        => 'pets_fee' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                'free' => esc_html__( 'Pets can stay for free' , 'wpbooking' ) ,
                                'paid' => esc_html__( "Charges may apply" , 'wpbooking' ) ,
                            ) ,
                            'class'     => 'small' ,
                            'condition' => 'pets_allowed:not(no)'
                        ) ,

                        array( 'type' => 'close_section' ) ,

                        // Languages
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Languages" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Select the language(s) in which you or your staff can help guests.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'         => __( 'Languages' , 'wpbooking' ) ,
                            'id'            => 'lang_spoken_by_staff' ,
                            'type'          => 'repeat_dropdown' ,
                            'value'         => WPBooking_Config::inst()->item( 'lang_spoken_by_staff' ) ,
                            'class'         => 'small' ,
                            'add_new_label' => esc_html__( 'Add another language' , 'wpbooking' )
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Languages


                        // Activities
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Activities" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate activities which you offer on-site.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Activities" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_activity' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_activity'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Activities

                        // Food & drink
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Food & drink" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate which options are available on-site.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Food & drink" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_food' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_food'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // Food & drink

                        // Pool and wellness
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Pool and wellness" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate the seasonal and year-round facilities you provide on-site.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Pool and wellness" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_pool' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_pool'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // Food & drink

                        // Pool and wellness
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Pool and wellness" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate the seasonal and year-round facilities you provide on-site.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Pool and wellness" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_pool' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_pool'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // Pool and wellness


                        // Transport
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Transport" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate the transport your property can provide or arrange for guests.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Transport" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_transport' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_transport'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // Transport

                        // Reception services
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Reception services" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label'    => __( "Reception services" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_recep_serv' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_recep_serv'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Reception services

                        // Common areas
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Common areas" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label'    => __( "Common areas" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_common_area' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_common_area'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Common areas

                        // Entertainment and family services
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Entertainment and family services" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate if you provide entertainment for kids and adults onsite.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Entertainment and family services" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_family_services' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_family_services'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Entertainment and family services

                        // Cleaning services
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Cleaning services" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label'    => __( "Cleaning services" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_cleaning_service' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_cleaning_service'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Cleaning services

                        // Business facilities
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Business facilities" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label'    => __( "Business facilities" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_business_facility' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_business_facility'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Business facilities

                        // Shops
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Shops" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( 'Indicate any shops your property has onsite.' , 'wpbooking' )
                        ) ,
                        array(
                            'label'    => __( "Shops" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_shop' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_shop'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Shops

                        // Miscellaneous
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Miscellaneous" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label'    => __( "Miscellaneous" , 'wpbooking' ) ,
                            'id'       => 'wb_hotel_miscellaneous' ,
                            'type'     => 'taxonomy_fee_select' ,
                            'taxonomy' => 'wb_hotel_miscellaneous'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        // End Miscellaneous

                        array(
                            'type' => 'section_navigation' ,
                        ) ,
                    )
                ) ,
                'room_detail_tab' => array(
                    'label'  => esc_html__( '3. Room details' , 'wpbooking' ) ,
                    'fields' => array(
                        array(
                            'label' => esc_html__( 'Your Rooms' , 'wpbooking' ) ,
                            'type'  => 'hotel_room_list' ,
                            'desc'  => esc_html__( 'Here is an overview of your rooms' , 'wpbooking' )
                        ) ,
                        array(
                            'type'        => 'section_navigation' ,
                            'next_label'  => esc_html__( 'Next Step' , 'wpbooking' ) ,
                            'ajax_saving' => 0
                        ) ,

                    )
                ) ,
                'facilities_tab'  => array(
                    'label'  => __( '4. Facilities' , 'wpbooking' ) ,
                    'fields' => array(
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Extra bed optional" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "These are the bed options that can be added upon request." , "wpbooking" )
                        ) ,
                        array(
                            'label' => __( 'Can you provide extra beds?' , 'wpbooking' ) ,
                            'id'    => 'extra_bed' ,
                            'type'  => 'radio' ,
                            'value' => array(
                                "yes" => esc_html__( "Yes" , 'wpbooking' ) ,
                                "no"  => esc_html__( "No" , 'wpbooking' ) ,
                            ) ,
                            'std'   => 'yes' ,
                            'class' => 'radio_pro' ,
                            'desc'  => esc_html__( "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium" , "wpbooking" )
                        ) ,
                        array(
                            'label'     => __( 'Select the number of extra beds that can be added.' , 'wpbooking' ) ,
                            'id'        => 'double_bed' ,
                            'type'      => 'dropdown' ,
                            'value'     => array(
                                1 ,
                                2 ,
                                3 ,
                                4 ,
                                5 ,
                                6 ,
                                7 ,
                                8 ,
                                9 ,
                                10 ,
                                11 ,
                                12 ,
                                13 ,
                                14 ,
                                15 ,
                                16 ,
                                17 ,
                                18 ,
                                19 ,
                                20
                            ) ,
                            'class'     => 'small' ,
                            'condition' => 'extra_bed:is(yes)' ,
                            'desc'      => esc_html__( "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium" , "wpbooking" )
                        ) ,
                        array(
                            'label'     => __( "Price for each extra beds" , 'wpbooking' ) ,
                            'type'      => 'money_input' ,
                            'id'        => 'price_for_extra_bed' ,
                            'class'     => 'small' ,
                            'std'       => '0' ,
                            'condition' => 'extra_bed:is(yes)' ,
                            'desc'      => esc_html__( 'Example: 2 extra bed and price is 10.00, total cost is 20.00' , 'wpbooking' )
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type' => 'open_section' ,
                        ) ,
                        array(
                            'label' => __( "Space" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "We display your room size to guests on your Booking.com propert" , "wpbooking" )
                        ) ,
                        array(
                            'label' => __( 'What is your preferred  unit of measure?' , 'wpbooking' ) ,
                            'id'    => 'room_measunit' ,
                            'type'  => 'radio' ,
                            'value' => array(
                                "metres" => esc_html__( "Square metres" , 'wpbooking' ) ,
                                "feed"   => esc_html__( "Square feet" , 'wpbooking' ) ,
                            ) ,
                            'std'   => 'metres' ,
                            'class' => 'radio_pro' ,
                            'desc'  => esc_html__( "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium" , "wpbooking" )
                        ) ,
                        array(
                            'label'  => __( 'Room sizes' , 'wpbooking' ) ,
                            'id'     => 'room_size' ,
                            'type'   => 'room_size' ,
                            'fields' => array(
                                'deluxe_queen_studio' ,
                                'queen_room' ,
                                'double_room' ,
                                'single_room' ,
                            )
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Room amenities" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Room Amenities" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_amenity' ,
                            'label'    => __( "Select Amenities" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_amenity'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Bathroom" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Amenities in Bathroom" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_bathroom' ,
                            'label'    => __( "Select Bathroom" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_bathroom'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Media & technology" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Media & technology amenities" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_media_technology' ,
                            'label'    => __( "Select Media & technology" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_media_technology'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Food and Drink" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Food and Drink" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_food_drink' ,
                            'label'    => __( "Food and Drink" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_food_drink'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Services & extras" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Services & extras" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_services_extra' ,
                            'label'    => __( "Services & extras" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_services_extra'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Outdoor & view" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Outdoor & view" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_outdoor_view' ,
                            'label'    => __( "Outdoor & view" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_outdoor_view'
                        ) ,
                        array( 'type' => 'close_section' ) ,


                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Accessibility" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Accessibility" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_accessibility' ,
                            'label'    => __( "Accessibility" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_accessibility'
                        ) ,
                        array( 'type' => 'close_section' ) ,


                        array(
                            'type'         => 'open_section' ,
                            'control'      => true ,
                            'open_section' => false ,
                        ) ,
                        array(
                            'label' => __( "Entertainment & Family Services" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Entertainment & Family Services" , "wpbooking" )
                        ) ,
                        array(
                            'id'       => 'hotel_room_entertainment' ,
                            'label'    => __( "Entertainment & Family Services" , 'wpbooking' ) ,
                            'type'     => 'taxonomy_room_select' ,
                            'taxonomy' => 'wb_hotel_room_entertainment'
                        ) ,
                        array( 'type' => 'close_section' ) ,


                        //Room amenities
                        array(
                            'type' => 'section_navigation' ,
                        ) ,
                    )
                ) ,
                'policies_tab'    => array(
                    'label'  => __( '5. Policies & Checkout' , 'wpbooking' ) ,
                    'fields' => array(
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Payment infomation" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "We will show in website yourdomain.com" , "wpbooking" )
                        ) ,
                        array(
                            'label' => __( 'We are accepted:' , 'wpbooking' ) ,
                            'id'    => 'creditcard_accepted' ,
                            'type'  => 'creditcard' ,
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Pre-payment and cancellation policies" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Pre-payment and cancellation policies" , "wpbooking" )
                        ) ,
                        array(
                            'label' => __( 'Select deposit optional' , 'wpbooking' ) ,
                            'id'    => 'deposit_payment_status' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                ''        => __( 'Disallow Deposit' , 'wpbooking' ) ,
                                'percent' => __( 'Deposit by percent' , 'wpbooking' ) ,
                                'amount'  => __( 'Deposit by amount' , 'wpbooking' ) ,
                            ) ,
                            'desc'  => esc_html__( "You can select Disallow Deposit, Deposit by percent, Deposit by amount" , "wpbooking" ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'Select deposit optional' , 'wpbooking' ) ,
                            'id'    => 'deposit_payment_amount' ,
                            'type'  => 'number' ,
                            'desc'  => esc_html__( "Leave empty for disallow deposit payment" , "wpbooking" ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'How many days in advance can guests cancel free of  charge?' , 'wpbooking' ) ,
                            'id'    => 'cancel_free_days_prior' ,
                            'type'  => 'dropdown' ,
                            'value' => array(

                                '0'  => __( 'Day of arrival (6 pm)' , 'wpbooking' ) ,
                                '1'  => __( '1 day' , 'wpbooking' ) ,
                                '2'  => __( '2 days' , 'wpbooking' ) ,
                                '3'  => __( '3 days' , 'wpbooking' ) ,
                                '7'  => __( '7 days' , 'wpbooking' ) ,
                                '14' => __( '14 days' , 'wpbooking' ) ,
                            ) ,
                            'desc'  => esc_html__( "Day of arrival ( 18: 00 ) , 1 day , 2 days, 3 days, 7 days, 14 days" , "wpbooking" ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'Or guests will pay 100%' , 'wpbooking' ) ,
                            'id'    => 'cancel_guest_payment' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                'first_night' => __( 'of the first night' , 'wpbooking' ) ,
                                'full_stay'   => __( 'of the full stay' , 'wpbooking' ) ,
                            ) ,
                            'desc'  => esc_html__( "Of the first night, of the full stay" , "wpbooking" ) ,
                            'class' => 'small'
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Tax" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "Set your local VAT or city tax, so guests know what is included in the price of their stay." , "wpbooking" )
                        ) ,
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Tax", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Set your local VAT or city tax, so guests know what is included in the price of their stay.", "wpbooking")
                        ),
                        array(
                            'label'  => __('VAT', 'wpbooking'),
                            'id'     => 'vat_different',
                            'type'   => 'vat_different',
                            'fields' => array(
                                'vat_excluded' ,
                                'vat_amount' ,
                                'vat_unit' ,
                            )
                        ) ,
                        array(
                            'label'  => __( 'City Tax' , 'wpbooking' ) ,
                            'id'     => 'citytax_different' ,
                            'type'   => 'citytax_different' ,
                            'fields' => array(
                                'citytax_excluded' ,
                                'citytax_amount' ,
                                'citytax_unit' ,
                            )
                        ) ,

                        array( 'type' => 'close_section' ) ,

                        array( 'type' => 'open_section' ) ,
                        array(
                            'label' => __( "Term & condition" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                            'desc'  => esc_html__( "We will show these information in checkout step." , "wpbooking" )
                        ) ,
                        array(
                            'label' => __( 'Minimum Stay' , 'wpbooking' ) ,
                            'id'    => 'minimum_stay' ,
                            'type'  => 'dropdown' ,
                            'value' => array(
                                1 ,
                                2 ,
                                3 ,
                                4 ,
                                5 ,
                                6 ,
                                7 ,
                                8 ,
                                9 ,
                                10 ,
                                11 ,
                                12 ,
                                13 ,
                                14 ,
                                15 ,
                                16 ,
                                17 ,
                                18 ,
                                19 ,
                                20 ,
                                21 ,
                                22 ,
                                23 ,
                                24 ,
                                25 ,
                                26 ,
                                27 ,
                                28 ,
                                29 ,
                                30
                            ) ,
                            'class' => 'small'
                        ) ,
                        array(
                            'label' => __( 'Terms & Conditions' , 'wpbooking' ) ,
                            'id'    => 'terms_conditions' ,
                            'type'  => 'textarea' ,
                            'rows'  => '5' ,
                        ) ,
                        array( 'type' => 'close_section' ) ,
                        array(
                            'type' => 'section_navigation' ,
                        ) ,
                    ),
                ),
                'photo_tab'       => array(
                    'label'  => __('6. Photos', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => __( "Pictures" , 'wpbooking' ) ,
                            'type'  => 'title' ,
                        ) ,
                        array(
                            'label' => __( "Gallery" , 'wpbooking' ) ,
                            'id'    => 'gallery_hotel' ,
                            'type'  => 'gallery_hotel' ,
                            'desc'  => __( 'Picture recommendations

				We recommend having pictures in the following order (if available):

				Living area
				Bedroom(s)
				Kitchen
				View from the apartment/house
				Exterior of apartment/building
				Please no generic pictures of the city
				Pictures showing animals, people, watermarks, logos and images composed of multiple
				smaller images will be removed.' , 'wpbooking' )
                        ) ,

                        array(
                            'type'       => 'section_navigation',
                        ),
                    )
                ) ,
                /*'calendar_tab'    => array(
                    'label'  => __( '5. Calendar' , 'wpbooking' ) ,
                    'fields' => array(

                        array(
                            'type'  => 'title' ,
                            'label' => esc_html__( 'Availability Template' , 'wpbooking' )
                        ) ,
                        array(
                            'id'   => 'calendar' ,
                            'type' => 'calendar'
                        )
                    )
                )*/
            ) );

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Hotel Activities' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Activities' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Activities' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Activities' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Activities' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Activities:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Activities' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Activities' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Activity' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Activity Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Activities' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-activity' ) ,
            );
            register_taxonomy( 'wb_hotel_activity' , array( 'wpbooking_service' ) , $args );

            $labels = array(
                'name'              => _x( 'Hotel Food & Drinks' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Food & Drinks' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Food & Drinks' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Food & Drinks' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Food & Drinks' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Food & Drinks:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Food & Drinks' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Food & Drinks' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Food & Drinks' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Food & Drinks Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Food & Drinks' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-food-drinks' ) ,
            );
            register_taxonomy( 'wb_hotel_food' , array( 'wpbooking_service' ) , $args );

            // Pool and wellness
            $labels = array(
                'name'              => _x( 'Hotel Pool & wellness' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Pool & wellness' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Pool & wellness' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Pool & wellnesss' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Pool & wellness' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Pool & wellness:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Pool & wellness' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Pool & wellness' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Pool & wellness' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Pool & wellness Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Pool & wellness' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-pool-wellness' ) ,
            );
            register_taxonomy( 'wb_hotel_pool' , array( 'wpbooking_service' ) , $args );

            // Transport
            $labels = array(
                'name'              => _x( 'Hotel Transport' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Transport' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Transport' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Transport' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Transport' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Transport:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Transport' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Transport' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Transport' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Transport Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Transport' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-transport' ) ,
            );
            register_taxonomy( 'wb_hotel_transport' , array( 'wpbooking_service' ) , $args );


            // Reception services
            $labels = array(
                'name'              => _x( 'Hotel Reception services' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Reception services' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Reception services' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Reception services' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Reception services' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Reception services:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Reception services' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Reception services' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Reception services' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Reception services Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Reception services' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-reception-services' ) ,
            );
            register_taxonomy( 'wb_hotel_recep_serv' , array( 'wpbooking_service' ) , $args );


            // Common areas
            $labels = array(
                'name'              => _x( 'Hotel Common areas' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Common areas' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Common areas' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Common areas' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Common areas' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Common areas:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Common areas' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Common area' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Common area' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Common area Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Common areas' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-common-areas' ) ,
            );
            register_taxonomy( 'wb_hotel_common_area' , array( 'wpbooking_service' ) , $args );

            // Entertainment and family services
            $labels = array(
                'name'              => _x( 'Hotel Family services' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Family services' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Family services' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Family services' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Family services' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Family services:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Family services' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Family services' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Family services' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Family services Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Entertainment & Family services' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-family-services' ) ,
            );
            register_taxonomy( 'wb_hotel_family_services' , array( 'wpbooking_service' ) , $args );

            // Cleaning services
            $labels = array(
                'name'              => _x( 'Hotel Cleaning services' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Cleaning services' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Cleaning services' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Cleaning services' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Cleaning services' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Cleaning services:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Cleaning services' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Cleaning services' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Cleaning services' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Cleaning services Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Cleaning services' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-cleaning-service' ) ,
            );
            register_taxonomy( 'wb_hotel_cleaning_service' , array( 'wpbooking_service' ) , $args );

            // Business facilities
            $labels = array(
                'name'              => _x( 'Hotel Business facilities' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Business facilities' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Business facilities' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Business facilities' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Business facilities' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Business facilities:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Business facilities' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Business facilities' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Business facilities' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Business facilities Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Business facilities' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-business-facility' ) ,
            );
            register_taxonomy( 'wb_hotel_business_facility' , array( 'wpbooking_service' ) , $args );

            // Shops
            $labels = array(
                'name'              => _x( 'Hotel Shops' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Shops' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Shops' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Shops' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Shops' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Shops:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Shops' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Shops' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Shops' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Shops Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Shops' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-shops' ) ,
            );
            register_taxonomy( 'wb_hotel_shop' , array( 'wpbooking_service' ) , $args );


            // Miscellaneous
            $labels = array(
                'name'              => _x( 'Hotel Miscellaneous' , 'taxonomy general name' , 'wpbooking' ) ,
                'singular_name'     => _x( 'Hotel Miscellaneous' , 'taxonomy singular name' , 'wpbooking' ) ,
                'search_items'      => __( 'Search Hotel Miscellaneous' , 'wpbooking' ) ,
                'all_items'         => __( 'All Hotel Miscellaneous' , 'wpbooking' ) ,
                'parent_item'       => __( 'Parent Hotel Miscellaneous' , 'wpbooking' ) ,
                'parent_item_colon' => __( 'Parent Hotel Miscellaneous:' , 'wpbooking' ) ,
                'edit_item'         => __( 'Edit Hotel Miscellaneous' , 'wpbooking' ) ,
                'update_item'       => __( 'Update Hotel Miscellaneous' , 'wpbooking' ) ,
                'add_new_item'      => __( 'Add New Hotel Miscellaneous' , 'wpbooking' ) ,
                'new_item_name'     => __( 'New Hotel Miscellaneous Name' , 'wpbooking' ) ,
                'menu_name'         => __( 'Hotel Miscellaneous' , 'wpbooking' ) ,
            );

            $args = array(
                'hierarchical'      => true ,
                'labels'            => $labels ,
                'show_ui'           => true ,
                'show_admin_column' => false ,
                'query_var'         => true ,
                'rewrite'           => array( 'slug' => 'hotel-miscellaneous' ) ,
            );
            register_taxonomy( 'wb_hotel_miscellaneous' , array( 'wpbooking_service' ) , $args );


        }

        function _add_default_term()
        {
            $terms = array(
                'wb_hotel_activity'              => array(
                    array(
                        'term' => 'Tennis court' ,
                    ) ,
                    array( 'term' => 'Billiards' , ) ,
                    array( 'term' => 'Table tennis' , ) ,
                    array( 'term' => 'Darts' , ) ,
                    array( 'term' => 'Squash' , ) ,
                    array( 'term' => 'Bowling' , ) ,
                    array( 'term' => 'Mini golf' , ) ,
                    array( 'term' => 'Golf course (within 3 km)' , ) ,
                    array( 'term' => 'Water park' , ) ,
                    array( 'term' => 'Water sport facilities (on site)' , ) ,
                    array( 'term' => 'Windsurfing' , ) ,
                    array( 'term' => 'Diving' , ) ,
                    array( 'term' => 'Snorkelling' , ) ,
                    array( 'term' => 'Canoeing' , ) ,
                    array( 'term' => 'Fishing' , ) ,
                    array( 'term' => 'Horse riding' , ) ,
                    array( 'term' => 'Cycling' , ) ,
                    array( 'term' => 'Hiking' , ) ,
                    array( 'term' => 'Skiing' , ) ,
                    array( 'term' => 'Ski storage' , ) ,
                    array( 'term' => 'Ski equipment hire (on site)' , ) ,
                    array( 'term' => 'Ski pass vendor' , ) ,
                    array( 'term' => 'Ski-to-door access' , ) ,
                    array( 'term' => 'Ski school' , ) ,

                ) ,
                'wb_hotel_food'                  => array(
                    array( 'term' => 'Restaurant' , ) ,
                    array( 'term' => 'Restaurant (à la carte)' , ) ,
                    array( 'term' => 'Restaurant (buffet)' , ) ,
                    array( 'term' => 'Bar' , ) ,
                    array( 'term' => 'Snack bar' , ) ,
                    array( 'term' => 'Grocery deliveries' , ) ,
                    array( 'term' => 'Packed lunches' , ) ,
                    array( 'term' => 'BBQ facilities' , ) ,
                    array( 'term' => 'Vending machine (drinks)' , ) ,
                    array( 'term' => 'Vending machine (snacks)' , ) ,
                    array( 'term' => 'Special diet menus (on request)' , ) ,
                    array( 'term' => 'Room service' , ) ,
                    array( 'term' => 'Breakfast in the room' , ) ,
                ) ,
                'wb_hotel_pool'                  => array(
                    array( 'term' => 'Indoor pool' , ) ,
                    array( 'term' => 'Indoor pool (seasonal)' , ) ,
                    array( 'term' => 'Indoor pool (all year)' , ) ,
                    array( 'term' => 'Outdoor pool' , ) ,
                    array( 'term' => 'Outdoor pool (seasonal)' , ) ,
                    array( 'term' => 'Outdoor pool (all year)' , ) ,
                    array( 'term' => 'Private beach area' , ) ,
                    array( 'term' => 'Beachfront' , ) ,
                    array( 'term' => 'Spa and wellness centre' , ) ,
                    array( 'term' => 'Sauna' , ) ,
                    array( 'term' => 'Hammam' , ) ,
                    array( 'term' => 'Hot tub/jacuzzi' , ) ,
                    array( 'term' => 'Fitness centre' , ) ,
                    array( 'term' => 'Solarium' , ) ,
                    array( 'term' => 'Hot spring bath' , ) ,
                    array( 'term' => 'Massage' , ) ,
                ) ,
                'wb_hotel_transport'             => array(
                    array( 'term' => 'Bikes available (free)' , ) ,
                    array( 'term' => 'Bicycle rental' , ) ,
                    array( 'term' => 'Car hire' , ) ,
                    array( 'term' => 'Airport shuttle (surcharge)' , ) ,
                    array( 'term' => 'Airport shuttle (free)' , ) ,
                    array( 'term' => 'Shuttle service (free)' , ) ,
                    array( 'term' => 'Shuttle service (surcharge)' , ) ,
                ) ,
                'wb_hotel_recep_serv'            => array(
                    array( 'term' => '24-hour front desk' , ) ,
                    array( 'term' => 'Private check-in/check-out' , ) ,
                    array( 'term' => 'Private check-in/check-out' , ) ,
                    array( 'term' => 'Concierge service' , ) ,
                    array( 'term' => 'Ticket service' , ) ,
                    array( 'term' => 'Tour desk' , ) ,
                    array( 'term' => 'Currency exchange' , ) ,
                    array( 'term' => 'ATM/cash machine on site' , ) ,
                    array( 'term' => 'Valet parking' , ) ,
                    array( 'term' => 'Luggage storage' , ) ,
                    array( 'term' => 'Lockers' , ) ,
                    array( 'term' => 'Safety deposit box' , ) ,
                    array( 'term' => 'Newspapers' , ) ,
                ) ,
                'wb_hotel_common_area'           => array(
                    array( 'term' => 'Garden' , ) ,
                    array( 'term' => 'Terrace' , ) ,
                    array( 'term' => 'Sun terrace' , ) ,
                    array( 'term' => 'Shared kitchen' , ) ,
                    array( 'term' => 'Shared lounge/TV area' , ) ,
                    array( 'term' => 'Games room' , ) ,
                    array( 'term' => 'Library' , ) ,
                    array( 'term' => 'Chapel/shrine' , ) ,
                ) ,
                'wb_hotel_family_services'       => array(
                    array( 'term' => 'Evening entertainment' , ) ,
                    array( 'term' => 'Nightclub/DJ' , ) ,
                    array( 'term' => 'Casino' , ) ,
                    array( 'term' => 'Karaoke' , ) ,
                    array( 'term' => 'Entertainment staff' , ) ,
                    array( 'term' => "Kids' club" , ) ,
                    array( 'term' => "Children's playground" , ) ,
                    array( 'term' => "Babysitting/child services" , ) ,
                ) ,
                'wb_hotel_cleaning_service'      => array(
                    array( 'term' => "Dry cleaning" , ) ,
                    array( 'term' => "Ironing service" , ) ,
                    array( 'term' => "Laundry" , ) ,
                    array( 'term' => "Daily maid service" , ) ,
                    array( 'term' => "Shoeshine" , ) ,
                    array( 'term' => "Trouser press" , ) ,
                ) ,
                'wb_hotel_business_facility'     => array(
                    array( 'term' => 'Meeting/banquet facilities' ) ,
                    array( 'term' => 'Business centre' ) ,
                    array( 'term' => 'Fax/photocopying' ) ,
                ) ,
                'wb_hotel_shop'                  => array(
                    array( 'term' => 'Shops (on site)' ) ,
                    array( 'term' => 'Mini-market on site' ) ,
                    array( 'term' => 'Barber/beauty shop' ) ,
                    array( 'term' => 'Gift shop' ) ,
                ) ,
                'wb_hotel_miscellaneous'         => array(
                    array( 'term' => 'Adult only' ) ,
                    array( 'term' => 'Allergy-free room' ) ,
                    array( 'term' => 'Non-smoking throughout' ) ,
                    array( 'term' => 'Designated smoking area' ) ,
                    array( 'term' => 'Non-smoking rooms' ) ,
                    array( 'term' => 'Facilities for disabled guests' ) ,
                    array( 'term' => 'Lift' ) ,
                    array( 'term' => 'Soundproof rooms' ) ,
                    array( 'term' => 'Bridal suite' ) ,
                    array( 'term' => 'VIP room facilities' ) ,
                    array( 'term' => 'Air conditioning' ) ,
                    array( 'term' => 'Heating' ) ,
                ) ,
                'wb_hotel_room_amenity'          => array(
                    array(
                        'term' => 'Clothes rack' ,
                    ) ,
                    array( 'term' => 'Drying rack for clothing' ) ,
                    array( 'term' => 'Fold-up bed' ) ,
                    array( 'term' => 'Sofa bed' ) ,
                    array( 'term' => 'Air Conditioning' ) ,
                    array( 'term' => 'Wardrobe/Closet' ) ,
                    array( 'term' => 'Carpeted' ) ,
                    array( 'term' => 'Dressing Room' ) ,
                    array( 'term' => 'Extra Long Beds (> 2 metres)' ) ,
                    array( 'term' => 'Fan' ) ,
                    array( 'term' => 'Fireplace' ) ,
                    array( 'term' => 'Heating' ) ,
                    array( 'term' => 'Interconnected room(s)  available' ) ,
                    array( 'term' => 'Iron' ) ,
                    array( 'term' => 'Ironing Facilities' ) ,
                    array( 'term' => 'Mosquito net' ) ,
                    array( 'term' => 'Private entrance' ) ,
                    array( 'term' => 'Safety Deposit Box' ) ,
                    array( 'term' => 'Sofa' ) ,
                    array( 'term' => 'Soundproof' ) ,
                    array( 'term' => 'Sitting area' ) ,
                    array( 'term' => 'Tile/Marble floor' ) ,
                    array( 'term' => 'Suit press' ) ,
                    array( 'term' => 'Hardwood/Parquet floors' ) ,
                    array( 'term' => 'Desk' ) ,
                    array( 'term' => 'Hypoallergenic' ) ,
                    array( 'term' => 'Cleaning products' ) ,
                    array( 'term' => 'Electric blankets' ) ,
                    array( 'term' => 'Bathroom' ) ,
                    array( 'term' => 'Toilet paper' ) ,
                    array( 'term' => 'Toilet With Grab Rails' ) ,
                    array( 'term' => 'Bathtub' ) ,
                    array( 'term' => 'Bidet' ) ,
                    array( 'term' => 'Bathtub or shower' ) ,
                    array( 'term' => 'Bathrobe' ) ,
                    array( 'term' => 'Bathroom' ) ,
                    array( 'term' => 'Free toiletries' ) ,
                    array( 'term' => 'Hairdryer' ) ,
                    array( 'term' => 'Spa tub' ) ,
                    array( 'term' => 'Shared bathroom' ) ,
                    array( 'term' => 'Shower' ) ,
                    array( 'term' => 'Slippers' ) ,
                    array( 'term' => 'Toilet' ) ,
                ) ,
                'wb_hotel_room_bathroom'         => array(
                    array( 'term' => 'Toilet paper' ) ,
                    array( 'term' => 'Toilet With Grab Rails' ) ,
                    array( 'term' => 'Bathtub' ) ,
                    array( 'term' => 'Bidet' ) ,
                    array( 'term' => 'Bathtub or shower' ) ,
                    array( 'term' => 'Bathrobe' ) ,
                    array( 'term' => 'Bathroom' ) ,
                    array( 'term' => 'Free toiletries' ) ,
                    array( 'term' => 'Hairdryer' ) ,
                    array( 'term' => 'Spa tub' ) ,
                    array( 'term' => 'Shared bathroom' ) ,
                    array( 'term' => 'Shower' ) ,
                    array( 'term' => 'Slippers' ) ,
                    array( 'term' => 'Toilet' ) ,
                ) ,
                'wb_hotel_room_media_technology' => array(
                    array( 'term' => 'Computer' ) ,
                    array( 'term' => 'Game console' ) ,
                    array( 'term' => 'Game console - Nintendo Wii' ) ,
                    array( 'term' => 'Game console - PS2' ) ,
                    array( 'term' => 'Game console - PS3' ) ,
                    array( 'term' => 'Game console - Xbox 360' ) ,
                    array( 'term' => 'Laptop' ) ,
                    array( 'term' => 'iPad' ) ,
                    array( 'term' => 'Cable channels' ) ,
                    array( 'term' => 'CD Player' ) ,
                    array( 'term' => 'DVD Player' ) ,
                    array( 'term' => 'Fax' ) ,
                    array( 'term' => 'iPod dock' ) ,
                    array( 'term' => 'Laptop safe' ) ,
                    array( 'term' => 'Flat-screen TV' ) ,
                    array( 'term' => 'Pay-per-view channels' ) ,
                    array( 'term' => 'Radio' ) ,
                    array( 'term' => 'Satellite channels' ) ,
                    array( 'term' => 'Telephone' ) ,
                    array( 'term' => 'TV' ) ,
                    array( 'term' => 'Video' ) ,
                    array( 'term' => 'Video games' ) ,
                    array( 'term' => 'Blu-ray player' ) ,
                ) ,
                'wb_hotel_room_food_drink'       => array(
                    array( 'term' => 'Dining area' ) ,
                    array( 'term' => 'Dining table' ) ,
                    array( 'term' => 'Barbecue' ) ,
                    array( 'term' => 'Stovetop' ) ,
                    array( 'term' => 'Toaster' ) ,
                    array( 'term' => 'Electric kettle' ) ,
                    array( 'term' => 'Outdoor dining area' ) ,
                    array( 'term' => 'Outdoor furniture' ) ,
                    array( 'term' => 'Minibar' ) ,
                    array( 'term' => 'Kitchenette' ) ,
                    array( 'term' => 'Kitchenware' ) ,
                    array( 'term' => 'Microwave' ) ,
                    array( 'term' => 'Refrigerator' ) ,
                    array( 'term' => 'Tea/Coffee maker' ) ,
                    array( 'term' => 'Coffee machine' ) ,
                    array( 'term' => 'High chair' ) ,
                ) ,
                'wb_hotel_room_services_extra'   => array(
                    array( 'term' => 'Executive Lounge Access' ) ,
                    array( 'term' => 'Alarm clock' ) ,
                    array( 'term' => 'Wake-up service' ) ,
                    array( 'term' => 'Wake up service/Alarm clock' ) ,
                    array( 'term' => 'Linens' ) ,
                    array( 'term' => 'Towels' ) ,
                    array( 'term' => 'Towels/Sheets (extra fee)' ) ,
                ) ,
                'wb_hotel_room_outdoor_view'     => array(
                    array( 'term' => 'Balcony' ) ,
                    array( 'term' => 'Patio' ) ,
                    array( 'term' => 'View' ) ,
                    array( 'term' => 'Terrace' ) ,
                    array( 'term' => 'City view' ) ,
                    array( 'term' => 'Garden view' ) ,
                    array( 'term' => 'Lake view' ) ,
                    array( 'term' => 'Landmark view' ) ,
                    array( 'term' => 'Mountain view' ) ,
                    array( 'term' => 'Pool view' ) ,
                    array( 'term' => 'River view' ) ,
                    array( 'term' => 'Sea view' ) ,
                ) ,
                'wb_hotel_room_accessibility'    => array(
                    array( 'term' => 'Room is located on the ground floor' ) ,
                    array( 'term' => 'Room is completely wheelchair accessible' ) ,
                    array( 'term' => 'Upper floors accessible by elevator' ) ,
                    array( 'term' => 'Upper floors accessible by stairs only' ) ,
                ) ,
                'wb_hotel_room_entertainment'    => array(
                    array( 'term' => 'Baby safety gates' ) ,
                    array( 'term' => 'Board games/puzzles' ) ,
                    array( 'term' => 'Books, DVDs or music for children' ) ,
                    array( 'term' => 'Child safety socket covers' ) ,
                ) ,

                // Room Type
                'wb_hotel_room_type'             => array(
                    array(
                        "term"     => esc_html__( "Single" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Budget Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Single Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Single Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Economy Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Large Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "New Year's Eve Special - Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room - Disability Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Bath" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Garden View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Lake View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Mountain View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Park View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Pool View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Private Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Private External Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Shared Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Shared Shower and Toilet" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Shared Toilet" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Shower" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Single Room with Terrace" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Small Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room with Mountain View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room with Sauna" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room with Shared Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Single Room with Shower" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior Single Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior Single Room with Lake View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior Single Room with Sea View" , 'wpbooking' ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Double" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Budget Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Business Double Room with Gym Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room (1 adult + 1 child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room (1 adult + 2 children)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room (2 Adults + 1 Child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Balcony and Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Bath" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Castle View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Extra Bed" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Shower" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Side Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe King Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Room (1 adult + 1 child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Room (1 adult + 2 children)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Deluxe Room (2 Adults + 1 Child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room (1 Adult + 1 Child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room - Disability Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Balcony (2 Adults + 1 Child)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Balcony (3 Adults)" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Balcony and Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Extra Bed" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Garden View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Lake View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Mountain View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Patio" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Pool View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Private Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Private External Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Shared Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Shared Toilet" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Spa Bath" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Double Room with Terrace" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Economy Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room - Disability Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Garden View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Lake View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Mountain View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Pool View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Roll-In Shower - Disability Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "King Room with Spa Bath" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Large Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room - Disability Access" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Balcony" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Garden View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Pool View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Sea View" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Shared Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Queen Room with Spa Bath" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Small Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Double Room with Fan" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Double Room with Shared Bathroom" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard King Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Standard Queen Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior Double Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior King Room" , 'wpbooking' ) ) ,
                            array( "term" => esc_html__( "Superior Queen Room" , 'wpbooking' ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Twin" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Budget Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double Room with Two Double Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double Room with Two Double Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Room with Two King Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Large Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Room with Two Queen Beds - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Small Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double Room with Two Double Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Twin Room with Sofa" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double Room with Two Double Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior King or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Twin Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Twin Room with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Extra Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Private Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Private External Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Shared Toilet" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Twin Room with View" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Twin/Double" ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Budget Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Cabin on Boat" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Ocean View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Pool Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with River View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Double or Twin Room with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Canal View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Extra Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Harbour View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Partial Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Private Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Private External Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Side Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Spa Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Swimming Pool Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Double or Twin Room with View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Large Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Small Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Cabin on Boat" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double or Twin Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double or Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double or Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Cabin on Boat" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Deluxe Double or Twin Room " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Double or Twin Room with Terrace" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Triple" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Basic Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Basic Triple Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Budget Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Classic Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Comfort Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Comfort Triple Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Triple Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Triple Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Executive Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Luxury Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Triple Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Triple Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Private Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Private External Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Shared Toilet" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Triple Room with View" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Quadruple" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Classic Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Comfort Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Duplex Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Economy Quadruple Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Executive Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Japanese-Style Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Room with Two King Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Luxury Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Premium Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Private Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Private External Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Quadruple Room with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Room with Two Queen Beds - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Quadruple Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Queen Room with Two Queen Beds" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Family" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Deluxe Family Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Family Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Bungalow" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Cabin on Boat" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Double Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Junior Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Private Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Side Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Room with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Suite with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Family Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Family Room" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__("Suite", 'wpbooking'),
                        'term_meta'=>array(
                            'wpbooking_is_multi_bedroom'=>1,
                            'wpbooking_is_multi_livingroom'=>1,
                        ),
                        'children' => array(
                            array( "term" => esc_html__( "Deluxe Double Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Junior Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe King Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe King Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Studio " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Suite with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Duplex Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Executive Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Canal View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Ocean View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Private Pool" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Junior Suite with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Studio with Sofa Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite with Ocean View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Suite with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "One-Bedroom Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Presidential Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Studio - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Suite with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Suite with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Double Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Triple Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Ocean View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Sofa Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with City View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Hot Tub" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Private Pool" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with River View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Spa Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Suite with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior King Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Suite with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Three-Bedroom Suite" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Two-Bedroom Suite" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__( "Studio" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Deluxe Double Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe King Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Queen Studio " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Duplex Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Family Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "King Studio with Sofa Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Queen Studio - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Triple Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio - Disability Access" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio - Split Level" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Ocean View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Pool View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Sofa Bed" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Spa Bath" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Studio" , "wpbooking" ) ) ,
                        ) ,
                    ) ,
                    array(
                        "term"     => esc_html__("Apartment", 'wpbooking'),
                        'term_meta'=>array(
                            'wpbooking_is_multi_bedroom'=>1,
                            'wpbooking_is_multi_livingroom'=>1,
                        ),
                        'children' => array(
                            array( "term" => esc_html__( "Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment - Ground Floor" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment - Split Level" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment With Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Balcony" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Garden View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Lake View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Mountain View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Pool View " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Shower" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Apartment with Terrace" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Deluxe Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Duplex Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Loft" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Maisonette" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "One-Bedroom Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Penthouse Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Standard Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Studio Apartment with Sea View" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Superior Apartment with Sauna" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Three-Bedroom Apartment" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Two-Bedroom Apartment" , "wpbooking" ) ) ,
                        ) ,

                    ) ,
                    array(
                        "term"     => esc_html__( "Dormitory room" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Bed in 10-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 10-Bed Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 10-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 8-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 8-Bed Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 8-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Female Dormitory Room " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Male Dormitory Room " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Mixed Dormitory Room" , "wpbooking" ) ) ,
                        ) ,

                    ) ,
                    array(
                        "term"     => esc_html__( "Bed in Dormitory" , 'wpbooking' ) ,
                        'children' => array(
                            array( "term" => esc_html__( "Bed in 10-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 4-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 6-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 8-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in 8-Bed Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bed in Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Female Dormitory Room " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Male Dormitory Room " , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Bunk Bed in Mixed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in 10-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in 4-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in 6-Bed Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Female Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Male Dormitory Room" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Male Dormitory Room with Shared Bathroom" , "wpbooking" ) ) ,
                            array( "term" => esc_html__( "Single Bed in Mixed Dormitory Room" , "wpbooking" ) ) ,
                        )
                    )
                )

            );

            foreach( $terms as $tax => $term ) {
                foreach ($term as $item) {
                    $item = wp_parse_args($item, array('parent' => '', 'term' => '', 'children'=>array(),'term_meta'=>array()));
                    if ($item['term']) {
                        // Check Exists
                        $old=term_exists($item['term'],$tax);
                        if(!$old){
                            $term_data = wp_insert_term($item['term'], $tax, $item);
                        }else{
                            $term_data=$old;
                        }
                        if (!is_wp_error($term_data) and !empty($item['children'])) {
                            foreach ($item['children'] as $child) {
                                if(!term_exists($child['term'],$tax,$term_data['term_id'])){
                                    wp_insert_term($child['term'], $tax, array('parent' => $term_data['term_id']));
                                }
                            }
                        }

                        // Term Meta
                        if(!is_wp_error($term_data) and !empty($item['term_meta']) and function_exists('add_term_meta')){
                            foreach($item['term_meta'] as $key=>$meta){
                                $a=add_term_meta($term_data['term_id'],$key,$meta,true);
                                var_dump($a);
                            }

                        }
                    }

                }
            }

        }

        /**
         * Get Room by Hotel Metabox Fields
         *
         * @since 1.0
         * @author quandq
         *
         * @param $post_id
         * @return array|void
         */
        function _get_room_by_hotel( $post_id )
        {
            if(empty( $post_id ))
                return;
            $list     = array();
            $args     = array(
                'post_type'      => 'wpbooking_hotel_room' ,
                'post_parent'    => $post_id ,
                'posts_per_page' => 200 ,
                'post_status'    => array( 'pending' , 'draft' , 'future' , 'publish' ) ,
            );
            $my_query = new WP_Query( $args );
            if($my_query->have_posts()) {
                while( $my_query->have_posts() ) {
                    $my_query->the_post();
                    $list[] = array( 'ID' => get_the_ID() , 'post_title' => get_the_title() );
                }
            }
            wp_reset_postdata();

            return $list;
        }

        /**
         * Get Hotel Room Metabox Fields
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed|void
         */
        function get_room_meta_fields()
        {
            $fields = array(
                array( 'type' => 'open_section' ) ,
                array(
                    'label' => __( "Deluxe Queen Studio" , 'wpbooking' ) ,
                    'type'  => 'title' ,
                    'desc'  => esc_html__( 'Select a room type : Single , double , twin, twin / double , triple, quadruple, family, suite, studio, apartment, dormitory room, bed in dormitory, ...' , 'wpbooking' )
                ) ,
                array(
                    'label'    => esc_html__('Room Type', 'wpbooking'),
                    'type'     => 'dropdown',
                    'id'       => 'room_type',
                    'taxonomy' => 'wb_hotel_room_type',
                    'parent'   => 0,
                    'class'    => 'small'
                ),
                array(
                    'id'    => 'room_name',
                    'label' => esc_html__('Room name', 'wpbooking'),
                    'type'  => 'room_name_dropdown',
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__( 'Room name (optional)' , 'wpbooking' ) ,
                    'type'  => 'text' ,
                    'id'    => 'room_name_custom' ,
                ) ,
                array(
                    'label' => esc_html__('Smoke Allowed', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'smoke_allowed',
                    'value' => WPBooking_Config::inst()->item('smoking_policy'),
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__('Room Number', 'wpbooking'),
                    'type'  => 'text',
                    'id'    => 'room_number',
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__( 'Bed Rooms' , 'wpbooking' ) ,
                    'type'  => 'dropdown' ,
                    'id'    => 'bed_rooms' ,
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                   /* 'condition' => 'room_type:is(on)',*/
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__( 'Bath Rooms' , 'wpbooking' ) ,
                    'type'  => 'dropdown' ,
                    'id'    => 'bath_rooms' ,
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__( 'Living Rooms' , 'wpbooking' ) ,
                    'type'  => 'dropdown' ,
                    'id'    => 'living_rooms' ,
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array('type' => 'close_section'),

                // Bed Options
                array( 'type' => 'open_section' ) ,
                array(
                    'label' => __( "Bed options" , 'wpbooking' ) ,
                    'type'  => 'title' ,
                ) ,
                array(
                    'id'            => 'bed_room_options',
                    'label'         => __("What kind of beds are available in this room?", 'wpbooking'),
                    'type'          => 'bed_options',
                    'value'         => WPBooking_Config::inst()->item('bed_type'),
                    'add_new_label' => esc_html__('Add another bed', 'wpbooking'),
                    'fields'=>array(
                        'bed_options_single_',
                        'bed_options_single_num_guests',
                        'bed_options_single_private_bathroom',
                        'bed_options_multi_',
                    )
                ),
                array(
                    'id'            => 'living_room_options',
                    'type'          => 'living_options',
                    'class'     => 'small'
                ),

                array('type' => 'close_section'),

                // Base Price
                array( 'type' => 'open_section' ) ,
                array(
                    'label' => __( "Bed options" , 'wpbooking' ) ,
                    'type'  => 'title' ,
                ) ,
                array(
                    'id'    => 'room_price_x_persons',
                    'label' => __("Price for %d people", 'wpbooking'),
                    'type'  => 'money_input',
                    'desc'  => esc_html__('Please enter the price you want your guests to pay for this room. You can set custom prices later', 'wpbooking'),
                    'class' => 'small'
                ),
                array(
                    'id'    => 'addition_guest_allowed' ,
                    'label' => __( "Addition Guests" , 'wpbooking' ) ,
                    'type'  => 'on-off' ,
                ) ,
                array(
                    'id'        => 'each_addition_pay',
                    'label'     => __("Each addition guest will pay", 'wpbooking'),
                    'type'      => 'money_input',
                    'condition' => 'addition_guest_allowed:is(on)',
                    'class'     => 'small'
                ),
                array('type' => 'close_section'),

                // Guest information
                array( 'type' => 'open_section' ),
                array(
                    'label' => __('Guest Information'),
                    'type' => 'title',
                    'desc' => esc_html__('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium','wpbooking'),
                ),
                array(
                    'id' => 'gi_max_adult',
                    'type' => 'dropdown',
                    'label' => __('Max adult','wpbooking'),
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array(
                    'id' => 'gi_max_children',
                    'type' => 'dropdown',
                    'label' => __('Max children','wpbooking'),
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array( 'type' => 'close_section' ),

                // Extra Service
                array(
                    'type' => 'open_section'
                ),
                array(
                    'type' => 'title',
                    'label' => __('Extra Services','wpbooking'),
                    'desc' => esc_html__('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium','wpbooking')
                ),
                array(
                    'type' => 'extra_services',
                    'label' => __('Choose extra services','wpbooking'),
                    'id' => 'extra_services_hotel'
                ),
                array(
                    'type' => 'close_section'
                ),

                // Calendar
                array( 'type' => 'open_section' ) ,
                array(
                    'label' => __( "Price Settings" , 'wpbooking' ) ,
                    'type'  => 'title' ,
                    'desc' => esc_html__('You can setting price for room','wpbooking')
                ) ,
                array(
                    'id'   => 'calendar' ,
                    'type' => 'calendar' ,
                ) ,
                array( 'type' => 'close_section' ) ,
            );

            return apply_filters( 'wpbooking_hotel_room_meta_fields' , $fields );
        }


        /**
         * Ajax Show Room Form
         *
         * @since 1.0
         * @author dungdt
         */
        function _ajax_room_edit_template()
        {
            $res      = array(
                'status' => 0
            );
            $room_id  = WPBooking_Input::post( 'room_id' );
            $hotel_id = WPBooking_Input::post( 'hotel_id' );


            if(!$room_id) {

                // Validate Permission
                if(!$hotel_id) {
                    $res[ 'message' ] = esc_html__( 'Please Specific Hotel ID' , 'wpbooking' );
                    echo json_encode( $res );
                    die;
                } else {
                    $hotel = get_post( $hotel_id );
                    if(!$hotel) {
                        $res[ 'message' ] = esc_html__( 'Hotel is not exists' , 'wpbooking' );
                        echo json_encode( $res );
                        die;
                    }
                    // Check Role
                    if(!current_user_can( 'manage_options' ) and $hotel->post_parent != get_current_user_id()) {
                        $res[ 'message' ] = esc_html__( 'You do not have permission to do it' , 'wpbooking' );
                        echo json_encode( $res );
                        die;
                    }
                }


                // Create Draft Room
                $room_id = wp_insert_post( array(
                    'post_author' => get_current_user_id() ,
                    'post_title'  => esc_html__( 'Room Draft' , 'wpbooking' ) ,
                    'post_type'   => 'wpbooking_hotel_room' ,
                    'post_status' => 'draft' ,
                    'post_parent' => $hotel_id
                ) );

                if(is_wp_error( $room_id )) {
                    $res[ 'message' ] = esc_html__( 'Can not create room, please check again' , 'wpbooking' );
                    echo json_encode( $res );
                    die;
                }
            }

            $res[ 'status' ] = 1;
            $res[ 'html' ]   = "
                <input name='wb_room_id' type='hidden' value='" . esc_attr( $room_id ) . "'>
            ";
            $res[ 'html' ] .= sprintf( '<input type="hidden" name="wb_hotel_room_security" value="%s">' , wp_create_nonce( "wpbooking_hotel_room_" . $room_id ) );
            $fields = $this->get_room_meta_fields();
            foreach( (array)$fields as $field_id => $field ):

                if(empty( $field[ 'type' ] ))
                    continue;

                $default = array(
                    'id'          => '' ,
                    'label'       => '' ,
                    'type'        => '' ,
                    'desc'        => '' ,
                    'std'         => '' ,
                    'class'       => '' ,
                    'location'    => false ,
                    'map_lat'     => '' ,
                    'map_long'    => '' ,
                    'map_zoom'    => 13 ,
                    'server_type' => '' ,
                    'width'       => ''
                );

                $field = wp_parse_args( $field , $default );

                $class_extra = false;
                if($field[ 'location' ] == 'hndle-tag') {
                    $class_extra = 'wpbooking-hndle-tag-input';
                }
                $file = 'metabox-fields/' . $field[ 'type' ];
                //var_dump($file);

                $field_html = apply_filters( 'wpbooking_metabox_field_html_' . $field[ 'type' ] , false , $field );

                if($field_html)
                    $res[ 'html' ] .= $field_html;
                else
                    $res[ 'html' ] .= wpbooking_admin_load_view( $file , array( 'data'        => $field ,
                                                                                'class_extra' => $class_extra ,
                                                                                'post_id'     => $room_id
                    ) );


            endforeach;

            $res[ 'html' ] .= wpbooking_admin_load_view( 'metabox-fields/room-form-button' );

            echo json_encode( $res );
            die;
        }

        /**
         * Ajax Save Room Data
         *
         * @since 1.0
         * @author dungdt
         */
        public function _ajax_save_room()
        {
            $res = array( 'status' => 0 );

            $room_id = WPBooking_Input::post( 'wb_room_id' );

            if($room_id) {
                // Validate
                check_ajax_referer( "wpbooking_hotel_room_" . $room_id , 'wb_hotel_room_security' );


                if($name = WPBooking_Input::request('room_name_custom')){
                    $my_post = array(
                        'ID'           => $room_id,
                        'post_title'   => $name,
                    );
                    wp_update_post( $my_post );
                }

                $fields = $this->get_room_meta_fields();
                WPBooking_Metabox::inst()->do_save_metabox( $room_id , $fields , 'wpbooking_hotel_room_form' );

                $res['data']['number'] = get_post_meta($room_id, 'number', true);
                $res['data']['thumbnail'] = '';
                $res['data']['title'] = get_the_title($room_id);
                $res['data']['room_id'] = $room_id;

                $res[ 'status' ] = 1;
            }


            echo json_encode( $res );
            die;
        }

        /**
         * Ajax delete room
         *
         * @since: 1.0
         * @author: Tien37
         */

        public function _ajax_del_room_item(){
            $res = array( 'status' => 0 );

            $room_id = WPBooking_Input::post( 'wb_room_id' );
            if($room_id){
                check_ajax_referer('del_security_post_'.$room_id, 'wb_del_security');

                if(wp_delete_post($room_id) !== false){
                    $res['status'] = 1;
                }
            }
            echo json_encode($res);
            wp_die();
        }

        static function inst()
        {
            if(!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Hotel_Service_Type::inst();
}