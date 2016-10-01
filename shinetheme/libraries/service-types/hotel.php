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
        static $_inst = FALSE;

        protected $type_id = 'hotel';

        function __construct()
        {
            $this->type_info = array(
                'label' => __("Hotel", 'wpbooking'),
                'desc'  => esc_html__('Chỗ nghỉ cho khách du lịch, thường có nhà hàng, phòng họp và các dịch vụ khác dành cho khách', 'wpbooking')
            );

            parent::__construct();

            add_action('init', array($this, '_add_init_action'));
            add_action('wpbooking_do_setup',array($this,'_add_default_term'));
        }


        public function _add_init_action()
        {
            $labels = array(
                'name'               => _x('Hotel Room', 'post type general name', 'wpbooking'),
                'singular_name'      => _x('Hotel Room', 'post type singular name', 'wpbooking'),
                'menu_name'          => _x('Hotel Room', 'admin menu', 'wpbooking'),
                'name_admin_bar'     => _x('Hotel Room', 'add new on admin bar', 'wpbooking'),
                'add_new'            => _x('Add New', 'Hotel Room', 'wpbooking'),
                'add_new_item'       => __('Add New Hotel Room', 'wpbooking'),
                'new_item'           => __('New Hotel Room', 'wpbooking'),
                'edit_item'          => __('Edit Hotel Room', 'wpbooking'),
                'view_item'          => __('View Hotel Room', 'wpbooking'),
                'all_items'          => __('All Hotel Room', 'wpbooking'),
                'search_items'       => __('Search Hotel Room', 'wpbooking'),
                'parent_item_colon'  => __('Parent Hotel Room:', 'wpbooking'),
                'not_found'          => __('No Hotel Room found.', 'wpbooking'),
                'not_found_in_trash' => __('No Hotel Room found in Trash.', 'wpbooking')
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __('Description.', 'wpbooking'),
                'public'             => TRUE,
                'publicly_queryable' => TRUE,
                'show_ui'            => TRUE,
                'show_in_menu'       => TRUE,
                'query_var'          => TRUE,
                'capability_type'    => 'post',
                'hierarchical'       => FALSE,
                //'menu_position'      => '59.9',
                'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
            );

            register_post_type('wpbooking_hotel_room', $args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Amenities', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Amenities', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Amenities', 'wpbooking' ),
                'all_items'         => __( 'All Room Amenities', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Amenities', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Amenities:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Amenities', 'wpbooking' ),
                'update_item'       => __( 'Update Room Amenities', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Amenity', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Amenity Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Amenities', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-amenity' ),
            );
            register_taxonomy('wb_hotel_room_amenity',array('wpbooking_service'),$args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Bathroom', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Bathroom', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Bathroom', 'wpbooking' ),
                'all_items'         => __( 'All Room Bathroom', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Bathroom', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Bathroom:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Bathroom', 'wpbooking' ),
                'update_item'       => __( 'Update Room Bathroom', 'wpbooking' ),
                'add_new_item'      => __( 'Add Room New Bathroom', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Bathroom Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Bathroom', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-bathroom' ),
            );
            register_taxonomy('wb_hotel_room_bathroom',array('wpbooking_service'),$args);
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Media & technology', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Media & technology', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Media & technology', 'wpbooking' ),
                'all_items'         => __( 'All Room Media & technology', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Media & technology', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Media & technology:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Media & technology', 'wpbooking' ),
                'update_item'       => __( 'Update Room Media & technology', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Media & technology', 'wpbooking' ),
                'new_item_name'     => __( 'NewRoom  Media & technology Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Media & technology', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-media-technology' ),
            );
            register_taxonomy('wb_hotel_room_media_technology',array('wpbooking_service'),$args);
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Food and Drink', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Food and Drink', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Food and Drink', 'wpbooking' ),
                'all_items'         => __( 'All Room Food and Drink', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Food and Drink', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Food and Drink:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Food and Drink', 'wpbooking' ),
                'update_item'       => __( 'Update Room Food and Drink', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Food and Drink', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Food and Drink Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Food and Drink', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-food-drink' ),
            );
            register_taxonomy('wb_hotel_room_food_drink',array('wpbooking_service'),$args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Services & extras', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Services & extras', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Services & extras', 'wpbooking' ),
                'all_items'         => __( 'All Room Services & extras', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Services & extras', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Services & extras:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Services & extras', 'wpbooking' ),
                'update_item'       => __( 'Update Room Services & extras', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Services & extras', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Services & extras Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Services & extras', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-services-extra' ),
            );
            register_taxonomy('wb_hotel_room_services_extra',array('wpbooking_service'),$args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Outdoor & view', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Outdoor & view', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Outdoor & view', 'wpbooking' ),
                'all_items'         => __( 'All Room Outdoor & view', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Outdoor & view', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Outdoor & view:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Outdoor & view', 'wpbooking' ),
                'update_item'       => __( 'Update Room Outdoor & view', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Outdoor & view', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Outdoor & view Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Outdoor & view', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-outdoor-view' ),
            );
            register_taxonomy('wb_hotel_room_outdoor_view',array('wpbooking_service'),$args);
            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Accessibility', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Accessibility', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Accessibility', 'wpbooking' ),
                'all_items'         => __( 'All Room Accessibility', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Accessibility', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Accessibility:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Accessibility', 'wpbooking' ),
                'update_item'       => __( 'Update Room Accessibility', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Accessibility', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Accessibility Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Accessibility', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-accessibility' ),
            );
            register_taxonomy('wb_hotel_room_accessibility',array('wpbooking_service'),$args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Room Entertainment & Family Services', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Room Entertainment & Family Services', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Room Entertainment & Family Services', 'wpbooking' ),
                'all_items'         => __( 'All Room Entertainment & Family Services', 'wpbooking' ),
                'parent_item'       => __( 'Parent Room Entertainment & Family Services', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Room Entertainment & Family Services:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Room Entertainment & Family Services', 'wpbooking' ),
                'update_item'       => __( 'Update Room Entertainment & Family Services', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Room Entertainment & Family Services', 'wpbooking' ),
                'new_item_name'     => __( 'New Room Entertainment & Family Services Name', 'wpbooking' ),
                'menu_name'         => __( 'Room Entertainment & Family Services', 'wpbooking' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-room-entertainment-services' ),
            );
            register_taxonomy('wb_hotel_room_entertainment_services',array('wpbooking_service'),$args);


            // Metabox
            $this->set_metabox(array(
                'general_tab'  => array(
                    'label'  => esc_html__('1. Property Information', 'wpbooking'),
                    'fields' => array(
                        array('type' => 'open_section'),
                        array(
                            'label' => __("About Your Hotel", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('Basic information', 'wpbooking'),
                        ),
                        array(
                            'id'    => 'enable_property',
                            'label' => __("Enable Property", 'wpbooking'),
                            'type'  => 'on-off',
                            'std'   => 'on',
                            'desc'  => esc_html__('Listing will appear in search results.', 'wpbooking'),
                        ),
                        array(
                            'id'    => 'star_rating',
                            'label' => __("Star Rating", 'wpbooking'),
                            'type'  => 'star-select',
                            'desc'  => esc_html__('Standard of hotel from 1 to 5 star.', 'wpbooking'),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Total Room', 'wpbooking'),
                            'id'    => 'total_room',
                            'desc'  => esc_html__('Number of rooms in your hotel.', 'wpbooking'),
                            'type'  => 'text',
                            'class' => 'small',
                            'std' => 1
                        ),
                        array(
                            'label' => __('Website', 'wpbooking'),
                            'id'    => 'website',
                            'type'  => 'text',
                            'desc'  => esc_html__('Property website (optional)', 'wpbooking'),
                            'class' => 'small'
                        ),
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Hotel Location", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Hotel's address and your contact number", 'wpbooking'),
                        ),
                        array(
                            'label' => __('Contact Name', 'wpbooking'),
                            'id'    => 'contact_name',
                            'desc'  => esc_html__('Who will receive the litter', 'wpbooking'),
                            'type'  => 'text',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Contact Number', 'wpbooking'),
                            'id'    => 'contact_number',
                            'desc'  => esc_html__('The contact phone', 'wpbooking'),
                            'type'  => 'phone_number',
                            'class' => 'small'
                        ),
                        array(
                            'label'           => __('Address', 'wpbooking'),
                            'id'              => 'address',
                            'type'            => 'address',
                            'container_class' => 'mb35',
                            'fields'=>array(
                                'rom',
                                ''
                            )
                        ),
                        array(
                            'label' => __('Map Lat & Long', 'wpbooking'),
                            'id'    => 'gmap',
                            'type'  => 'gmap',
                            'desc'=>esc_html__('This is the location we will provide guests. Click and drag the','wpbooking')
                        ),
                        array(
                            'type' => 'desc_section',
                            'title' => esc_html__('Your address matters! ', 'wpbooking'),
                            'content'  => esc_html__('Please make sure to enter your full address including building name, apartment number, etc.', 'wpbooking')
                        ) ,
                        array( 'type'  => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                            'prev' => FALSE
                        ),

                    )
                ),
                'detail_tab'=>array(
                    'label' => __('2. Property Details', 'wpbooking'),
                    'fields'=>array(
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Check In & Check Out", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('Time to check in, out in your property', 'wpbooking')
                        ),
                        array(
                            'label'  => esc_html__('Check In time', 'wpbooking'),
                            'desc'   => esc_html__('Check In time', 'wpbooking'),
                            'type'   => 'check_in',
                            'fields' => array('checkin_from', 'checkin_to'),// Fields to save
                        ),
                        array(
                            'label'  => esc_html__('Check Out time', 'wpbooking'),
                            'desc'   => esc_html__('Check Out time', 'wpbooking'),
                            'type'   => 'check_out',
                            'fields' => array('checkout_from', 'checkout_to'),// Fields to save
                        ),
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Internet", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('Internet access is important for many travellers. Free WiFi is a huge selling point, too!', 'wpbooking')
                        ),
                        array(
                            'label' => __('Is internet available to guests?', 'wpbooking'),
                            'id'    => 'internet_status',
                            'type'  => 'dropdown',
                            'value' => array(
                                'no_internet'   => esc_html__('No', 'wpbooking'),
                                'free_internet' => esc_html__('Yes, Free', 'wpbooking'),
                                'paid_internet' => esc_html__('Yes, Paid', 'wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label'     => __('Connection type', 'wpbooking'),
                            'id'        => 'internet_connection_type',
                            'type'      => 'dropdown',
                            'value'     => array(
                                'cable' => esc_html__('Cable', 'wpbooking'),
                                'wifi'  => esc_html__('Wifi', 'wpbooking'),
                            ),
                            'class' => 'small',
                            'condition' => 'internet_status:not(no_internet)'
                        ),
                        array(
                            'label'     => __('Connection location', 'wpbooking'),
                            'id'        => 'internet_connection_location',
                            'type'      => 'dropdown',
                            'value'     => array(
                                'public_area' => esc_html__('Public areas', 'wpbooking'),
                                'some_rooms' => esc_html__('Some rooms', 'wpbooking'),
                                'all_rooms' => esc_html__('All rooms', 'wpbooking'),
                                'entire_property' => esc_html__('Entire Property', 'wpbooking'),
                            ),
                            'class' => 'small',
                            'condition' => 'internet_status:not(no_internet)'
                        ),
                        array(
                            'label'     => __('Price for internet (per day)', 'wpbooking'),
                            'id'        => 'internet_price',
                            'type'      => 'money_input',
                            'class' => 'small',
                            'condition' => 'internet_status:not(no_internet)'
                        ),
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Parking lot", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('This information is especially important for those travelling to your accommodation by car.', 'wpbooking')
                        ),
                        array(
                            'label' => __('Is parking available to guests?', 'wpbooking'),
                            'id'    => 'parking_status',
                            'type'  => 'dropdown',
                            'value' => array(
                                'no_parking'   => esc_html__('No', 'wpbooking'),
                                'free_parking' => esc_html__('Yes, Free', 'wpbooking'),
                                'paid_parking' => esc_html__('Yes, Paid', 'wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Parking lot type', 'wpbooking'),
                            'id'    => 'parking_lot_type',
                            'type'  => 'dropdown',
                            'value' => array(
                                'private'   => esc_html__('Private', 'wpbooking'),
                                'public'   => esc_html__('Public', 'wpbooking'),
                            ),
                            'condition' => 'parking_status:not(no_parking)',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Parking lot area', 'wpbooking'),
                            'id'    => 'parking_lot_area',
                            'type'  => 'dropdown',
                            'value' => array(
                                'onside'   => esc_html__('On Side', 'wpbooking'),
                                'outside'   => esc_html__('Out Side', 'wpbooking'),
                            ),
                            'condition' => 'parking_status:not(no_parking)',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Do guests need to reserve a parking space?', 'wpbooking'),
                            'id'    => 'parking_need_reserve',
                            'type'  => 'dropdown',
                            'value' => array(
                                'need_reservation'   => esc_html__('Reservation Needed', 'wpbooking'),
                                'no_need_reservation'   => esc_html__('No Reservation Needed', 'wpbooking'),
                            ),
                            'condition' => 'parking_status:not(no_parking)',
                            'class' => 'small'
                        ),
                        array(
                            'label'     => __('Price for parking (per day)', 'wpbooking'),
                            'id'        => 'parking_price',
                            'type'      => 'money_input',
                            'class' => 'small',
                            'condition' => 'parking_status:not(no_internet)'
                        ),
                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Breakfast", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('Indicate if breakfast is included in the price, or if it\'s an optional add-on.', 'wpbooking')
                        ),
                        array(
                            'label' => __('Is breakfast available to guests?', 'wpbooking'),
                            'id'    => 'breakfast_status',
                            'type'  => 'dropdown',
                            'value' => array(
                                'no_breakfast'   => esc_html__('No', 'wpbooking'),
                                'yes_included' => esc_html__("Yes, it's included in the price", 'wpbooking'),
                                'no_optional' => esc_html__("Yes, it's optional", 'wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Price for breakfast (per person, per day)', 'wpbooking'),
                            'id'    => 'breakfast_price',
                            'type'  => 'money_input',
                            'class' => 'small',
                            'condition' => 'breakfast_status:not(no_breakfast)'
                        ),
                        array(
                            'label' => __('What kind of breakfast is available?', 'wpbooking'),
                            'id'    => 'breakfast_types',
                            'type'  => 'repeat_dropdown',
                            'value'=>WPBooking_Config::inst()->item('breakfast_types'),
                            'class' => 'small',
                            'condition' => 'breakfast_status:not(no_breakfast)',
                            'add_new_label'=>esc_html__('Add another breakfast type','wpbooking')
                        ),
                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Children", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'id'=>'children_allowed',
                            'label'=>esc_html__('Can you accommodate children?','wpbooking'),
                            'type'=>'on-off',
                        ),
                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Pet", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Some guests like to travel with their furry friends. Indicate if you allow pets and if any charges apply.','wpbooking')
                        ),

                        array(
                            'label' => __('Do you allow pets', 'wpbooking'),
                            'id'    => 'pets_allowed',
                            'type'  => 'dropdown',
                            'value' => array(
                                'no'   => esc_html__('No', 'wpbooking'),
                                'yes' => esc_html__("Yes", 'wpbooking'),
                                'request' => esc_html__("Upon request", 'wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Are there additional charges for pets?', 'wpbooking'),
                            'id'    => 'pets_fee',
                            'type'  => 'dropdown',
                            'value' => array(
                                'free'   => esc_html__('Pets can stay for free', 'wpbooking'),
                                'paid' => esc_html__("Charges may apply", 'wpbooking'),
                            ),
                            'class' => 'small',
                            'condition'=>'pets_allowed:not(no)'
                        ),

                        array('type' => 'close_section'),

                        // Languages
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Languages", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Select the language(s) in which you or your staff can help guests.','wpbooking')
                        ),
                        array(
                            'label' => __('Languages', 'wpbooking'),
                            'id'    => 'lang_spoken_by_staff',
                            'type'  => 'repeat_dropdown',
                            'value'=>WPBooking_Config::inst()->item('lang_spoken_by_staff'),
                            'class' => 'small',
                            'add_new_label'=>esc_html__('Add another language','wpbooking')
                        ),
                        array('type' => 'close_section'),
                        // End Languages


                        // Activities
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Activities", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate activities which you offer on-site.','wpbooking')
                        ),
                        array(
                            'label' => __("Activities", 'wpbooking'),
                            'id'    => 'wb_hotel_activity',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_activity'
                        ),
                        array('type' => 'close_section'),
                        // End Activities

                        // Food & drink
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Food & drink", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate which options are available on-site.','wpbooking')
                        ),
                        array(
                            'label' => __("Food & drink", 'wpbooking'),
                            'id'    => 'wb_hotel_food',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_food'
                        ),
                        array('type' => 'close_section'),
                        // Food & drink

                        // Pool and wellness
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Pool and wellness", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate the seasonal and year-round facilities you provide on-site.','wpbooking')
                        ),
                        array(
                            'label' => __("Pool and wellness", 'wpbooking'),
                            'id'    => 'wb_hotel_pool',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_pool'
                        ),
                        array('type' => 'close_section'),
                        // Food & drink

                        // Pool and wellness
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Pool and wellness", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate the seasonal and year-round facilities you provide on-site.','wpbooking')
                        ),
                        array(
                            'label' => __("Pool and wellness", 'wpbooking'),
                            'id'    => 'wb_hotel_pool',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_pool'
                        ),
                        array('type' => 'close_section'),
                        // Pool and wellness


                        // Transport
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Transport", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate the transport your property can provide or arrange for guests.','wpbooking')
                        ),
                        array(
                            'label' => __("Transport", 'wpbooking'),
                            'id'    => 'wb_hotel_transport',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_transport'
                        ),
                        array('type' => 'close_section'),
                        // Transport

                        // Reception services
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Reception services", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Reception services", 'wpbooking'),
                            'id'    => 'wb_hotel_recep_serv',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_recep_serv'
                        ),
                        array('type' => 'close_section'),
                        // End Reception services

                        // Common areas
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Common areas", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Common areas", 'wpbooking'),
                            'id'    => 'wb_hotel_common_area',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_common_area'
                        ),
                        array('type' => 'close_section'),
                        // End Common areas

                        // Entertainment and family services
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Entertainment and family services", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate if you provide entertainment for kids and adults onsite.','wpbooking')
                        ),
                        array(
                            'label' => __("Entertainment and family services", 'wpbooking'),
                            'id'    => 'wb_hotel_family_services',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_family_services'
                        ),
                        array('type' => 'close_section'),
                        // End Entertainment and family services

                        // Cleaning services
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Cleaning services", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Cleaning services", 'wpbooking'),
                            'id'    => 'wb_hotel_cleaning_service',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_cleaning_service'
                        ),
                        array('type' => 'close_section'),
                        // End Cleaning services

                        // Business facilities
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Business facilities", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Business facilities", 'wpbooking'),
                            'id'    => 'wb_hotel_business_facility',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_business_facility'
                        ),
                        array('type' => 'close_section'),
                        // End Business facilities

                        // Shops
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Shops", 'wpbooking'),
                            'type'  => 'title',
                            'desc'=>esc_html__('Indicate any shops your property has onsite.','wpbooking')
                        ),
                        array(
                            'label' => __("Shops", 'wpbooking'),
                            'id'    => 'wb_hotel_shop',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_shop'
                        ),
                        array('type' => 'close_section'),
                        // End Shops

                        // Miscellaneous
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Miscellaneous", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Miscellaneous", 'wpbooking'),
                            'id'    => 'wb_hotel_miscellaneous',
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>'wb_hotel_miscellaneous'
                        ),
                        array('type' => 'close_section'),
                        // End Miscellaneous

                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'room_detail_tab'=>array(
                    'label'=>esc_html__('3. Room details','wpbooking'),
                    'fields'=>array(
                        array(
                            'label'=>esc_html__('Your Rooms','wpbooking'),
                            'type'=>'hotel_room_list',
                            'desc'=>esc_html__('Here is an overview of your rooms','wpbooking')
                        )
                    )
                ),

                'policies_tab' => array(
                    'label'  => __('3. Policies', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => __("Property Policies", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __('Deposit Type', 'wpbooking'),
                            'id'    => 'deposit_type',
                            'type'  => 'dropdown',
                            'value' => array(
                                'value'   => __('Value', 'wpbooking'),
                                'percent' => __('Percent', 'wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Deposit Amount', 'wpbooking'),
                            'id'    => 'deposit_amount',
                            'type'  => 'money_input',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Minimum Stay', 'wpbooking'),
                            'id'    => 'minimum_stay',
                            'type'  => 'dropdown',
                            'value' => array(
                                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label'           => __('Cancellation Allowed', 'wpbooking'),
                            'id'              => 'cancellation_allowed',
                            'type'            => 'on-off',
                            'std'             => 1,
                            'container_class' => 'mb35'
                        ),
                        array(
                            'label' => __('Terms & Conditions', 'wpbooking'),
                            'id'    => 'terms_conditions',
                            'type'  => 'textarea',
                        ),
                        array(
                            'label' => __("Host's Regulations", 'wpbooking'),
                            'id'    => 'host_regulations',
                            'type'  => 'list-item',
                            'value' => array(
                                array(
                                    'id'    => 'content',
                                    'label' => esc_html__('Content', 'wpbooking'),
                                    'type'  => 'textarea'
                                ),
                            )
                        ),
                        array(
                            'label' => __("Check In & Check Out", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __('Instructions', 'wpbooking'),
                            'id'    => 'check_in_out_instructions',
                            'type'  => 'textarea',
                        ),
                        array(
                            'label' => __('Check In Time', 'wpbooking'),
                            'id'    => 'check_in_time',
                            'type'  => 'time_select',
                        ),
                        array(
                            'label' => __('Check Out Time', 'wpbooking'),
                            'id'    => 'check_out_time',
                            'type'  => 'time_select',
                        ),
                        array(
                            'label' => __("Cancellation Policies", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'type' => 'cancellation_policies_text',
                        ),
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'facilities_tab'=>array(
                    'label' => __('4. Facilities', 'wpbooking'),
                    'fields'=>array(
                        array( 'type'  => 'open_section'),
                        array(
                            'label' => __("Extra bed optional", 'wpbooking'),
                            'type'  => 'title',
                            'desc' =>esc_html__("These are the bed options that can be added upon request.","wpbooking")
                        ),
                        array(
                            'label' => __('Can you provide extra beds?', 'wpbooking'),
                            'id'    => 'extra_bed',
                            'type'  => 'radio',
                            'value' => array(
                                "yes"=>esc_html__("Yes",'wpbooking'),
                                "no"=>esc_html__("No",'wpbooking'),
                            ),
                            'class' => 'radio_pro',
                            'desc' =>esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium","wpbooking")
                        ),
                        array(
                            'label' => __('Select the number of extra beds that can be added.', 'wpbooking'),
                            'id'    => 'double_bed',
                            'type'  => 'dropdown',
                            'value' => array(
                                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                            ),
                            'class' => 'small',
                            'desc' =>esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium","wpbooking")
                        ),
                        array(
                            'label'       => __("Price for each extra beds", 'wpbooking'),
                            'type'        => 'money_input',
                            'id'          => 'price_for_extra_bed',
                            'class'       => 'small',
                            'std'           => '0',
                            'desc' => esc_html__('Example: 2 extra bed and price is 10.00, total cost is 20.00', 'wpbooking')
                        ),
                        array( 'type'  => 'close_section'),
                        array( 'type'  => 'open_section'),
                        array(
                            'label' => __("Space", 'wpbooking'),
                            'type'  => 'title',
                            'desc' =>esc_html__("We display your room size to guests on your Booking.com propert","wpbooking")
                        ),
                        array(
                            'label' => __('What is your preferred  unit of measure?', 'wpbooking'),
                            'id'    => 'room_measunit',
                            'type'  => 'radio',
                            'value' => array(
                                "metres"=>esc_html__("Square metres",'wpbooking'),
                                "feed"=>esc_html__("Square feet",'wpbooking'),
                            ),
                            'class' => 'radio_pro',
                            'desc' =>esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium","wpbooking")
                        ),
                        array(
                            'label' => __('Room sizes', 'wpbooking'),
                            'id'    => 'room_size',
                            'type'  => 'room_size',
                            'fields'=>array(
                                'deluxe_queen_studio',
                                'queen_room',
                                'double_room',
                                'single_room',
                            )
                        ),
                        array( 'type'  => 'close_section'),
                        array( 'type'  => 'open_section'),
                        array(
                            'label' => __("Room amenities", 'wpbooking'),
                            'type'  => 'title',
                            'desc' =>esc_html__("Room Amenities","wpbooking")
                        ),
                        array(
                            'id'    => 'hotel_room_amenity',
                            'label' => __("Select Amenities", 'wpbooking'),
                            'type'  => 'taxonomy_room_select',
                            'taxonomy'=>'wb_hotel_room_amenity'
                        ),
                        array( 'type'  => 'close_section'),
                        array( 'type'  => 'open_section'),
                        array(
                            'label' => __("Bathroom", 'wpbooking'),
                            'type'  => 'title',
                            'desc' =>esc_html__("Amenities in Bathroom","wpbooking")
                        ),
                        array(
                            'id'    => 'hotel_room_bathroom',
                            'label' => __("Select Bathroom", 'wpbooking'),
                            'type'  => 'taxonomy_room_select',
                            'taxonomy'=>'wb_hotel_room_bathroom'
                        ),
                        array( 'type'  => 'close_section'),
                        array( 'type'  => 'open_section'),
                        array(
                            'label' => __("Media & technology", 'wpbooking'),
                            'type'  => 'title',
                            'desc' =>esc_html__("Media & technology amenities","wpbooking")
                        ),
                        array(
                            'id'    => 'hotel_room_media_technology',
                            'label' => __("Select Media & technology", 'wpbooking'),
                            'type'  => 'taxonomy_room_select',
                            'taxonomy'=>'wb_hotel_room_media_technology'
                        ),
                        array( 'type'  => 'close_section'),
                        //Room amenities
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'photo_tab'    => array(
                    'label'  => __('5. Photos', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => __("Pictures", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Gallery", 'wpbooking'),
                            'id'    => 'gallery',
                            'type'  => 'gallery_hotel',
                            'desc'  => __('Picture recommendations

				We recommend having pictures in the following order (if available):

				Living area
				Bedroom(s)
				Kitchen
				View from the apartment/house
				Exterior of apartment/building
				Please no generic pictures of the city
				Pictures showing animals, people, watermarks, logos and images composed of multiple
				smaller images will be removed.', 'wpbooking')
                        ),

                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'calendar_tab' => array(
                    'label'  => __('5. Calendar', 'wpbooking'),
                    'fields' => array(

                        array(
                            'type'  => 'title',
                            'label' => esc_html__('Availability Template', 'wpbooking')
                        ),
                        array(
                            'id'   => 'calendar',
                            'type' => 'calendar'
                        )
                    )
                )
            ));

            // Register Taxonomy
            $labels = array(
                'name'              => _x( 'Hotel Activities', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Activities', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Activities', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Activities', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Activities', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Activities:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Activities', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Activities', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Activity', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Activity Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Activities', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-activity' ),
            );
            register_taxonomy('wb_hotel_activity',array('wpbooking_service'),$args);

            $labels = array(
                'name'              => _x( 'Hotel Food & Drinks', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Food & Drinks', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Food & Drinks', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Food & Drinks', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Food & Drinks', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Food & Drinks:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Food & Drinks', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Food & Drinks', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Food & Drinks', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Food & Drinks Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Food & Drinks', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-food-drinks' ),
            );
            register_taxonomy('wb_hotel_food',array('wpbooking_service'),$args);

            // Pool and wellness
            $labels = array(
                'name'              => _x( 'Hotel Pool & wellness', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Pool & wellness', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Pool & wellness', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Pool & wellnesss', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Pool & wellness', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Pool & wellness:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Pool & wellness', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Pool & wellness', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Pool & wellness', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Pool & wellness Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Pool & wellness', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-pool-wellness' ),
            );
            register_taxonomy('wb_hotel_pool',array('wpbooking_service'),$args);

            // Transport
            $labels = array(
                'name'              => _x( 'Hotel Transport', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Transport', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Transport', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Transport', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Transport', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Transport:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Transport', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Transport', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Transport', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Transport Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Transport', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-transport' ),
            );
            register_taxonomy('wb_hotel_transport',array('wpbooking_service'),$args);


            // Reception services
            $labels = array(
                'name'              => _x( 'Hotel Reception services', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Reception services', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Reception services', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Reception services', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Reception services', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Reception services:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Reception services', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Reception services', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Reception services', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Reception services Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Reception services', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-reception-services' ),
            );
            register_taxonomy('wb_hotel_recep_serv',array('wpbooking_service'),$args);


            // Common areas
            $labels = array(
                'name'              => _x( 'Hotel Common areas', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Common areas', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Common areas', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Common areas', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Common areas', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Common areas:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Common areas', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Common area', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Common area', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Common area Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Common areas', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-common-areas' ),
            );
            register_taxonomy('wb_hotel_common_area',array('wpbooking_service'),$args);

            // Entertainment and family services
            $labels = array(
                'name'              => _x( 'Hotel Family services', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Family services', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Family services', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Family services', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Family services', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Family services:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Family services', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Family services', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Family services', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Family services Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Entertainment & Family services', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-family-services' ),
            );
            register_taxonomy('wb_hotel_family_services',array('wpbooking_service'),$args);

            // Cleaning services
            $labels = array(
                'name'              => _x( 'Hotel Cleaning services', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Cleaning services', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Cleaning services', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Cleaning services', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Cleaning services', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Cleaning services:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Cleaning services', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Cleaning services', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Cleaning services', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Cleaning services Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Cleaning services', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-cleaning-service' ),
            );
            register_taxonomy('wb_hotel_cleaning_service',array('wpbooking_service'),$args);

            // Business facilities
            $labels = array(
                'name'              => _x( 'Hotel Business facilities', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Business facilities', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Business facilities', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Business facilities', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Business facilities', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Business facilities:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Business facilities', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Business facilities', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Business facilities', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Business facilities Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Business facilities', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-business-facility' ),
            );
            register_taxonomy('wb_hotel_business_facility',array('wpbooking_service'),$args);

            // Shops
            $labels = array(
                'name'              => _x( 'Hotel Shops', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Shops', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Shops', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Shops', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Shops', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Shops:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Shops', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Shops', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Shops', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Shops Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Shops', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-shops' ),
            );
            register_taxonomy('wb_hotel_shop',array('wpbooking_service'),$args);


            // Miscellaneous
            $labels = array(
                'name'              => _x( 'Hotel Miscellaneous', 'taxonomy general name', 'wpbooking' ),
                'singular_name'     => _x( 'Hotel Miscellaneous', 'taxonomy singular name', 'wpbooking' ),
                'search_items'      => __( 'Search Hotel Miscellaneous', 'wpbooking' ),
                'all_items'         => __( 'All Hotel Miscellaneous', 'wpbooking' ),
                'parent_item'       => __( 'Parent Hotel Miscellaneous', 'wpbooking' ),
                'parent_item_colon' => __( 'Parent Hotel Miscellaneous:', 'wpbooking' ),
                'edit_item'         => __( 'Edit Hotel Miscellaneous', 'wpbooking' ),
                'update_item'       => __( 'Update Hotel Miscellaneous', 'wpbooking' ),
                'add_new_item'      => __( 'Add New Hotel Miscellaneous', 'wpbooking' ),
                'new_item_name'     => __( 'New Hotel Miscellaneous Name', 'wpbooking' ),
                'menu_name'         => __( 'Hotel Miscellaneous', 'wpbooking' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'hotel-miscellaneous' ),
            );
            register_taxonomy('wb_hotel_miscellaneous',array('wpbooking_service'),$args);
        }

        function _add_default_term()
        {
            $terms=array(
                'wb_hotel_activity'=>array(
                    array(
                        'term'=>'Tennis court',
                    ),
                    array('term'=>'Billiards',),
                    array('term'=>'Table tennis',),
                    array('term'=>'Darts',),
                    array('term'=>'Squash',),
                    array('term'=>'Bowling',),
                    array('term'=>'Mini golf',),
                    array('term'=>'Golf course (within 3 km)',),
                    array('term'=>'Water park',),
                    array('term'=>'Water sport facilities (on site)',),
                    array('term'=>'Windsurfing',),
                    array('term'=>'Diving',),
                    array('term'=>'Snorkelling',),
                    array('term'=>'Canoeing',),
                    array('term'=>'Fishing',),
                    array('term'=>'Horse riding',),
                    array('term'=>'Cycling',),
                    array('term'=>'Hiking',),
                    array('term'=>'Skiing',),
                    array('term'=>'Ski storage',),
                    array('term'=>'Ski equipment hire (on site)',),
                    array('term'=>'Ski pass vendor',),
                    array('term'=>'Ski-to-door access',),
                    array('term'=>'Ski school',),

                ),
                'wb_hotel_food'=>array(
                    array('term'=>'Restaurant',),
                    array('term'=>'Restaurant (à la carte)',),
                    array('term'=>'Restaurant (buffet)',),
                    array('term'=>'Bar',),
                    array('term'=>'Snack bar',),
                    array('term'=>'Grocery deliveries',),
                    array('term'=>'Packed lunches',),
                    array('term'=>'BBQ facilities',),
                    array('term'=>'Vending machine (drinks)',),
                    array('term'=>'Vending machine (snacks)',),
                    array('term'=>'Special diet menus (on request)',),
                    array('term'=>'Room service',),
                    array('term'=>'Breakfast in the room',),
                ),
                'wb_hotel_pool'=>array(
                    array('term'=>'Indoor pool',),
                    array('term'=>'Indoor pool (seasonal)',),
                    array('term'=>'Indoor pool (all year)',),
                    array('term'=>'Outdoor pool',),
                    array('term'=>'Outdoor pool (seasonal)',),
                    array('term'=>'Outdoor pool (all year)',),
                    array('term'=>'Private beach area',),
                    array('term'=>'Beachfront',),
                    array('term'=>'Spa and wellness centre',),
                    array('term'=>'Sauna',),
                    array('term'=>'Hammam',),
                    array('term'=>'Hot tub/jacuzzi',),
                    array('term'=>'Fitness centre',),
                    array('term'=>'Solarium',),
                    array('term'=>'Hot spring bath',),
                    array('term'=>'Massage',),
                ),
                'wb_hotel_transport'=>array(
                    array('term'=>'Bikes available (free)',),
                    array('term'=>'Bicycle rental',),
                    array('term'=>'Car hire',),
                    array('term'=>'Airport shuttle (surcharge)',),
                    array('term'=>'Airport shuttle (free)',),
                    array('term'=>'Shuttle service (free)',),
                    array('term'=>'Shuttle service (surcharge)',),
                ),
                'wb_hotel_recep_serv'=>array(
                    array('term'=>'24-hour front desk',),
                    array('term'=>'Private check-in/check-out',),
                    array('term'=>'Private check-in/check-out',),
                    array('term'=>'Concierge service',),
                    array('term'=>'Ticket service',),
                    array('term'=>'Tour desk',),
                    array('term'=>'Currency exchange',),
                    array('term'=>'ATM/cash machine on site',),
                    array('term'=>'Valet parking',),
                    array('term'=>'Luggage storage',),
                    array('term'=>'Lockers',),
                    array('term'=>'Safety deposit box',),
                    array('term'=>'Newspapers',),
                ),
                'wb_hotel_common_area'=>array(
                    array('term'=>'Garden',),
                    array('term'=>'Terrace',),
                    array('term'=>'Sun terrace',),
                    array('term'=>'Shared kitchen',),
                    array('term'=>'Shared lounge/TV area',),
                    array('term'=>'Games room',),
                    array('term'=>'Library',),
                    array('term'=>'Chapel/shrine',),
                ),
                'wb_hotel_family_services'=>array(
                    array('term'=>'Evening entertainment',),
                    array('term'=>'Nightclub/DJ',),
                    array('term'=>'Casino',),
                    array('term'=>'Karaoke',),
                    array('term'=>'Entertainment staff',),
                    array('term'=>"Kids' club",),
                    array('term'=>"Children's playground",),
                    array('term'=>"Babysitting/child services",),
                ),
                'wb_hotel_cleaning_service'=>array(
                    array('term'=>"Dry cleaning",),
                    array('term'=>"Ironing service",),
                    array('term'=>"Laundry",),
                    array('term'=>"Daily maid service",),
                    array('term'=>"Shoeshine",),
                    array('term'=>"Trouser press",),
                ),
                'wb_hotel_business_facility'=>array(
                    array('term'=>'Meeting/banquet facilities'),
                    array('term'=>'Business centre'),
                    array('term'=>'Fax/photocopying'),
                ),
                'wb_hotel_shop'=>array(
                    array('term'=>'Shops (on site)'),
                    array('term'=>'Mini-market on site'),
                    array('term'=>'Barber/beauty shop'),
                    array('term'=>'Gift shop'),
                ),
                'wb_hotel_miscellaneous'=>array(
                    array('term'=>'Adult only'),
                    array('term'=>'Allergy-free room'),
                    array('term'=>'Non-smoking throughout'),
                    array('term'=>'Designated smoking area'),
                    array('term'=>'Non-smoking rooms'),
                    array('term'=>'Facilities for disabled guests'),
                    array('term'=>'Lift'),
                    array('term'=>'Soundproof rooms'),
                    array('term'=>'Bridal suite'),
                    array('term'=>'VIP room facilities'),
                    array('term'=>'Air conditioning'),
                    array('term'=>'Heating'),
                ),
                'wb_hotel_room_amenity'=>array(
                    array(
                        'term'=>'Clothes rack',
                    ),
                    array('term'=>'Drying rack for clothing'),
                    array('term'=>'Fold-up bed'),
                    array('term'=>'Sofa bed'),
                    array('term'=>'Air Conditioning'),
                    array('term'=>'Wardrobe/Closet'),
                    array('term'=>'Carpeted'),
                    array('term'=>'Dressing Room'),
                    array('term'=>'Extra Long Beds (> 2 metres)'),
                    array('term'=>'Fan'),
                    array('term'=>'Fireplace'),
                    array('term'=>'Heating'),
                    array('term'=>'Interconnected room(s)  available'),
                    array('term'=>'Iron'),
                    array('term'=>'Ironing Facilities'),
                    array('term'=>'Mosquito net'),
                    array('term'=>'Private entrance'),
                    array('term'=>'Safety Deposit Box'),
                    array('term'=>'Sofa'),
                    array('term'=>'Soundproof'),
                    array('term'=>'Sitting area'),
                    array('term'=>'Tile/Marble floor'),
                    array('term'=>'Suit press'),
                    array('term'=>'Hardwood/Parquet floors'),
                    array('term'=>'Desk'),
                    array('term'=>'Hypoallergenic'),
                    array('term'=>'Cleaning products'),
                    array('term'=>'Electric blankets'),
                    array('term'=>'Bathroom'),
                    array('term'=>'Toilet paper'),
                    array('term'=>'Toilet With Grab Rails'),
                    array('term'=>'Bathtub'),
                    array('term'=>'Bidet'),
                    array('term'=>'Bathtub or shower'),
                    array('term'=>'Bathrobe'),
                    array('term'=>'Bathroom'),
                    array('term'=>'Free toiletries'),
                    array('term'=>'Hairdryer'),
                    array('term'=>'Spa tub'),
                    array('term'=>'Shared bathroom'),
                    array('term'=>'Shower'),
                    array('term'=>'Slippers'),
                    array('term'=>'Toilet'),
                ),
                'wb_hotel_room_bathroom'=>array(
                    array('term'=>'Toilet paper'),
                    array('term'=>'Toilet With Grab Rails'),
                    array('term'=>'Bathtub'),
                    array('term'=>'Bidet'),
                    array('term'=>'Bathtub or shower'),
                    array('term'=>'Bathrobe'),
                    array('term'=>'Bathroom'),
                    array('term'=>'Free toiletries'),
                    array('term'=>'Hairdryer'),
                    array('term'=>'Spa tub'),
                    array('term'=>'Shared bathroom'),
                    array('term'=>'Shower'),
                    array('term'=>'Slippers'),
                    array('term'=>'Toilet'),
                ),
                'wb_hotel_room_media_technology'=>array(
                    array('term'=>'Computer'),
                    array('term'=>'Game console'),
                    array('term'=>'Game console - Nintendo Wii'),
                    array('term'=>'Game console - PS2'),
                    array('term'=>'Game console - PS3'),
                    array('term'=>'Game console - Xbox 360'),
                    array('term'=>'Laptop'),
                    array('term'=>'iPad'),
                    array('term'=>'Cable channels'),
                    array('term'=>'CD Player'),
                    array('term'=>'DVD Player'),
                    array('term'=>'Fax'),
                    array('term'=>'iPod dock'),
                    array('term'=>'Laptop safe'),
                    array('term'=>'Flat-screen TV'),
                    array('term'=>'Pay-per-view channels'),
                    array('term'=>'Radio'),
                    array('term'=>'Satellite channels'),
                    array('term'=>'Telephone'),
                    array('term'=>'TV'),
                    array('term'=>'Video'),
                    array('term'=>'Video games'),
                    array('term'=>'Blu-ray player'),
                ),
            );

            foreach($terms as $tax=>$term){

                foreach($term as $item){
                    $item=wp_parse_args($item,array('parent'=>'','term'=>''));
                    if($item['term']){
                        wp_insert_term($item['term'],$tax,$item);
                    }
                }
            }
        }

        static function _get_room_by_hotel($post_id){
            if(empty($post_id)) return;
            global $wpdb;
            $sql = "SELECT ID,post_title
                    FROM
                    {$wpdb->posts}
                    WHERE 
                    1=1
                    AND {$wpdb->posts}.post_parent = {$post_id} 
                    AND {$wpdb->posts}.post_status = 'publish'";
            $rs = $wpdb->get_results($sql);
            return $rs;
        }


        static function inst()
        {
            if (!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Hotel_Service_Type::inst();
}