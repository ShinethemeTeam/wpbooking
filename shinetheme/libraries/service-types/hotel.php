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
                'label' => __("Hotel", 'wpbooking'),
                'desc'  => esc_html__('Chỗ nghỉ cho khách du lịch, thường có nhà hàng, phòng họp và các dịch vụ khác dành cho khách', 'wpbooking')
            );

            parent::__construct();

            add_action('init', array($this, '_add_init_action'));
            add_action('wpbooking_do_setup', array($this, '_add_default_term'));


            /**
             * Ajax Show Room Form
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_show_room_form', array($this, '_ajax_room_edit_template'));

            /**
             * Ajax Save Room Data
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_save_hotel_room', array($this, '_ajax_save_room'));

            /**
             * Ajax delete room item
             *
             * @since 1.0
             * @author Tien37
             */
            add_action('wp_ajax_wpbooking_del_room_item', array($this, '_ajax_del_room_item'));


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
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => true,
                'capability_type'    => 'post',
                'hierarchical'       => false,
                //'menu_position'      => '59.9',
                'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
            );

            register_post_type('wpbooking_hotel_room', $args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x('Room Type', 'taxonomy general name', 'wpbooking'),
                'singular_name'     => _x('Room Type', 'taxonomy singular name', 'wpbooking'),
                'search_items'      => __('Search Room Type', 'wpbooking'),
                'all_items'         => __('All Room Type', 'wpbooking'),
                'parent_item'       => __('Parent Room Type', 'wpbooking'),
                'parent_item_colon' => __('Parent Room Type:', 'wpbooking'),
                'edit_item'         => __('Edit Room Type', 'wpbooking'),
                'update_item'       => __('Update Room Type', 'wpbooking'),
                'add_new_item'      => __('Add New Room Type', 'wpbooking'),
                'new_item_name'     => __('New Room Type Name', 'wpbooking'),
                'menu_name'         => __('Room Type', 'wpbooking'),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => false,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'hotel-room-type'),
            );
            register_taxonomy('wb_hotel_room_type', array('wpbooking_hotel_room'), $args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x('Room Amenities', 'taxonomy general name', 'wpbooking'),
                'singular_name'     => _x('Room Amenities', 'taxonomy singular name', 'wpbooking'),
                'search_items'      => __('Search Room Amenities', 'wpbooking'),
                'all_items'         => __('All Room Amenities', 'wpbooking'),
                'parent_item'       => __('Parent Room Amenities', 'wpbooking'),
                'parent_item_colon' => __('Parent Room Amenities:', 'wpbooking'),
                'edit_item'         => __('Edit Room Amenities', 'wpbooking'),
                'update_item'       => __('Update Room Amenities', 'wpbooking'),
                'add_new_item'      => __('Add New Room Amenity', 'wpbooking'),
                'new_item_name'     => __('New Room Amenity Name', 'wpbooking'),
                'menu_name'         => __('Room Amenities', 'wpbooking'),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'hotel-room-amenity'),
            );
            register_taxonomy('wb_hotel_room_amenity', array('wpbooking_service'), $args);


            // Hotel Extra Taxonomy
            $extra_tax = array();
            $taxs = WPBooking_Admin_Taxonomy_Controller::inst()->get_tax_service_type($this->type_id);
            if (!empty($taxs)) {
                foreach ($taxs as $tax) {
                    $tax_object = get_taxonomy($tax);
                    if (!is_wp_error($tax_object)) {
                        $extra_tax[] = array(
                            'label' => $tax_object['label'],
                            'id'    => $tax['name'],
                            'type'  => 'taxonomy_fee_select',
                            'taxonomy'=>$tax['name']
                        );
                    }
                }
            }

            // Metabox
            $this->set_metabox(array(
                'general_tab'     => array(
                    'label'  => esc_html__('1. Property Information', 'wpbooking'),
                    'fields' => array(
                        array(
                            'type' => 'open_section',
                        ),
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
                            'label' => __('Contact Number', 'wpbooking'),
                            'id'    => 'contact_number',
                            'desc'  => esc_html__('The contact phone', 'wpbooking'),
                            'type'  => 'phone_number',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Contact Email', 'wpbooking'),
                            'id'    => 'contact_email',
                            'type'  => 'text',
                            'placeholder'=>esc_html__('Example@domain.com','wpbooking'),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Website', 'wpbooking'),
                            'id'    => 'website',
                            'type'  => 'text',
                            'desc'  => esc_html__('Property website (optional)', 'wpbooking'),
                            'placeholder'=>esc_html__('http://exampledomain.com','wpbooking'),
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
                            'label'           => __('Address', 'wpbooking'),
                            'id'              => 'address',
                            'type'            => 'address',
                            'container_class' => 'mb35',
                        ),
                        array(
                            'label' => __('Map Lat & Long', 'wpbooking'),
                            'id'    => 'gmap',
                            'type'  => 'gmap',
                            'desc'  => esc_html__('This is the location we will provide guests. Click and drag the marker if you need to move it', 'wpbooking')
                        ),
                        array(
                            'type'    => 'desc_section',
                            'title'   => esc_html__('Your address matters! ', 'wpbooking'),
                            'content' => esc_html__('Please make sure to enter your full address including building name, apartment number, etc.', 'wpbooking')
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                            'prev' => false
                        ),

                    )
                ),
                'detail_tab'      => array(
                    'label'  => __('2. Property Details', 'wpbooking'),
                    'fields' => array(
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

                        // Miscellaneous
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Amenity", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label'    => __("Amenity", 'wpbooking'),
                            'id'       => 'wpbooking_amenity',
                            'taxonomy' => 'wpbooking_amenity',
                            'type'     => 'taxonomy_fee_select',
                        ),
                        $extra_tax,
                        array('type' => 'close_section'),
                        // End Miscellaneous

                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'room_detail_tab' => array(
                    'label'  => esc_html__('3. Room details', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => esc_html__('Your Rooms', 'wpbooking'),
                            'type'  => 'hotel_room_list',
                            'desc'  => esc_html__('Here is an overview of your rooms', 'wpbooking')
                        ),
                        array(
                            'type'        => 'section_navigation',
                            'next_label'  => esc_html__('Next Step', 'wpbooking'),
                            'ajax_saving' => 0
                        ),

                    )
                ),
                'facilities_tab'  => array(
                    'label'  => __('4. Facilities', 'wpbooking'),
                    'fields' => array(
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Extra bed optional", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("These are the bed options that can be added upon request.", "wpbooking")
                        ),
                        array(
                            'label' => __('Can you provide extra beds?', 'wpbooking'),
                            'id'    => 'extra_bed',
                            'type'  => 'radio',
                            'value' => array(
                                "yes" => esc_html__("Yes", 'wpbooking'),
                                "no"  => esc_html__("No", 'wpbooking'),
                            ),
                            'std'   => 'yes',
                            'class' => 'radio_pro',
                            'desc'  => esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium", "wpbooking")
                        ),
                        array(
                            'label'     => __('Select the number of extra beds that can be added.', 'wpbooking'),
                            'id'        => 'double_bed',
                            'type'      => 'dropdown',
                            'value'     => array(
                                1,
                                2,
                                3,
                                4,
                                5,
                                6,
                                7,
                                8,
                                9,
                                10,
                                11,
                                12,
                                13,
                                14,
                                15,
                                16,
                                17,
                                18,
                                19,
                                20
                            ),
                            'class'     => 'small',
                            'condition' => 'extra_bed:is(yes)',
                            'desc'      => esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium", "wpbooking")
                        ),
                        array(
                            'label'     => __("Price for each extra beds", 'wpbooking'),
                            'type'      => 'money_input',
                            'id'        => 'price_for_extra_bed',
                            'class'     => 'small',
                            'std'       => '0',
                            'condition' => 'extra_bed:is(yes)',
                            'desc'      => esc_html__('Example: 2 extra bed and price is 10.00, total cost is 20.00', 'wpbooking')
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'open_section',
                        ),
                        array(
                            'label' => __("Space", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("We display your room size to guests on your Booking.com propert", "wpbooking")
                        ),
                        array(
                            'label' => __('What is your preferred  unit of measure?', 'wpbooking'),
                            'id'    => 'room_measunit',
                            'type'  => 'radio',
                            'value' => array(
                                "metres" => esc_html__("Square metres", 'wpbooking'),
                                "feed"   => esc_html__("Square feet", 'wpbooking'),
                            ),
                            'std'   => 'metres',
                            'class' => 'radio_pro',
                            'desc'  => esc_html__("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium", "wpbooking")
                        ),
                        array(
                            'label'  => __('Room sizes', 'wpbooking'),
                            'id'     => 'room_size',
                            'type'   => 'room_size',
                            'fields' => array(
                                'deluxe_queen_studio',
                                'queen_room',
                                'double_room',
                                'single_room',
                            )
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Room amenities", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Room Amenities", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_amenity',
                            'label'    => __("Select Amenities", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_amenity'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Bathroom", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Amenities in Bathroom", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_bathroom',
                            'label'    => __("Select Bathroom", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_bathroom'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Media & technology", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Media & technology amenities", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_media_technology',
                            'label'    => __("Select Media & technology", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_media_technology'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Food and Drink", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Food and Drink", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_food_drink',
                            'label'    => __("Food and Drink", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_food_drink'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Services & extras", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Services & extras", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_services_extra',
                            'label'    => __("Services & extras", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_services_extra'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Outdoor & view", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Outdoor & view", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_outdoor_view',
                            'label'    => __("Outdoor & view", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_outdoor_view'
                        ),
                        array('type' => 'close_section'),


                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Accessibility", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Accessibility", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_accessibility',
                            'label'    => __("Accessibility", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_accessibility'
                        ),
                        array('type' => 'close_section'),


                        array(
                            'type'         => 'open_section',
                            'control'      => true,
                            'open_section' => false,
                        ),
                        array(
                            'label' => __("Entertainment & Family Services", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Entertainment & Family Services", "wpbooking")
                        ),
                        array(
                            'id'       => 'hotel_room_entertainment',
                            'label'    => __("Entertainment & Family Services", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_entertainment'
                        ),
                        array('type' => 'close_section'),


                        //Room amenities
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'policies_tab'    => array(
                    'label'  => __('5. Policies & Checkout', 'wpbooking'),
                    'fields' => array(
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Payment infomation", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("We will show in website yourdomain.com", "wpbooking")
                        ),
                        array(
                            'label' => __('We are accepted:', 'wpbooking'),
                            'id'    => 'creditcard_accepted',
                            'type'  => 'creditcard',
                        ),
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Pre-payment and cancellation policies", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Pre-payment and cancellation policies", "wpbooking")
                        ),
                        array(
                            'label' => __('Select deposit optional', 'wpbooking'),
                            'id'    => 'deposit_payment_status',
                            'type'  => 'dropdown',
                            'value' => array(
                                ''        => __('Disallow Deposit', 'wpbooking'),
                                'percent' => __('Deposit by percent', 'wpbooking'),
                                'amount'  => __('Deposit by amount', 'wpbooking'),
                            ),
                            'desc'  => esc_html__("You can select Disallow Deposit, Deposit by percent, Deposit by amount", "wpbooking"),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Select deposit optional', 'wpbooking'),
                            'id'    => 'deposit_payment_amount',
                            'type'  => 'number',
                            'desc'  => esc_html__("Leave empty for disallow deposit payment", "wpbooking"),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('How many days in advance can guests cancel free of  charge?', 'wpbooking'),
                            'id'    => 'cancel_free_days_prior',
                            'type'  => 'dropdown',
                            'value' => array(

                                '0'  => __('Day of arrival (6 pm)', 'wpbooking'),
                                '1'  => __('1 day', 'wpbooking'),
                                '2'  => __('2 days', 'wpbooking'),
                                '3'  => __('3 days', 'wpbooking'),
                                '7'  => __('7 days', 'wpbooking'),
                                '14' => __('14 days', 'wpbooking'),
                            ),
                            'desc'  => esc_html__("Day of arrival ( 18: 00 ) , 1 day , 2 days, 3 days, 7 days, 14 days", "wpbooking"),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Or guests will pay 100%', 'wpbooking'),
                            'id'    => 'cancel_guest_payment',
                            'type'  => 'dropdown',
                            'value' => array(
                                'first_night' => __('of the first night', 'wpbooking'),
                                'full_stay'   => __('of the full stay', 'wpbooking'),
                            ),
                            'desc'  => esc_html__("Of the first night, of the full stay", "wpbooking"),
                            'class' => 'small'
                        ),
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
                                'vat_excluded',
                                'vat_amount',
                                'vat_unit',
                            )
                        ),
                        array(
                            'label'  => __('City Tax', 'wpbooking'),
                            'id'     => 'citytax_different',
                            'type'   => 'citytax_different',
                            'fields' => array(
                                'citytax_excluded',
                                'citytax_amount',
                                'citytax_unit',
                            )
                        ),

                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Term & condition", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("We will show these information in checkout step.", "wpbooking")
                        ),
                        array(
                            'label' => __('Minimum Stay', 'wpbooking'),
                            'id'    => 'minimum_stay',
                            'type'  => 'dropdown',
                            'value' => array(
                                1,
                                2,
                                3,
                                4,
                                5,
                                6,
                                7,
                                8,
                                9,
                                10,
                                11,
                                12,
                                13,
                                14,
                                15,
                                16,
                                17,
                                18,
                                19,
                                20,
                                21,
                                22,
                                23,
                                24,
                                25,
                                26,
                                27,
                                28,
                                29,
                                30
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Terms & Conditions', 'wpbooking'),
                            'id'    => 'terms_conditions',
                            'type'  => 'textarea',
                            'rows'  => '5',
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                        ),
                    ),
                ),
                'photo_tab'       => array(
                    'label'  => __('6. Photos', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => __("Pictures", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Gallery", 'wpbooking'),
                            'id'    => 'gallery_hotel',
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
                            'type'       => 'section_navigation',
                            'next_label' => esc_html__('Save', 'wpbooking')
                        ),
                    )
                ),
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
            ));

        }

        function _add_default_term()
        {
            $terms = array(
                'wb_hotel_activity'              => array(
                    array(
                        'term' => 'Tennis court',
                    ),
                    array('term' => 'Billiards',),
                    array('term' => 'Table tennis',),
                    array('term' => 'Darts',),
                    array('term' => 'Squash',),
                    array('term' => 'Bowling',),
                    array('term' => 'Mini golf',),
                    array('term' => 'Golf course (within 3 km)',),
                    array('term' => 'Water park',),
                    array('term' => 'Water sport facilities (on site)',),
                    array('term' => 'Windsurfing',),
                    array('term' => 'Diving',),
                    array('term' => 'Snorkelling',),
                    array('term' => 'Canoeing',),
                    array('term' => 'Fishing',),
                    array('term' => 'Horse riding',),
                    array('term' => 'Cycling',),
                    array('term' => 'Hiking',),
                    array('term' => 'Skiing',),
                    array('term' => 'Ski storage',),
                    array('term' => 'Ski equipment hire (on site)',),
                    array('term' => 'Ski pass vendor',),
                    array('term' => 'Ski-to-door access',),
                    array('term' => 'Ski school',),

                ),
                'wb_hotel_food'                  => array(
                    array('term' => 'Restaurant',),
                    array('term' => 'Restaurant (à la carte)',),
                    array('term' => 'Restaurant (buffet)',),
                    array('term' => 'Bar',),
                    array('term' => 'Snack bar',),
                    array('term' => 'Grocery deliveries',),
                    array('term' => 'Packed lunches',),
                    array('term' => 'BBQ facilities',),
                    array('term' => 'Vending machine (drinks)',),
                    array('term' => 'Vending machine (snacks)',),
                    array('term' => 'Special diet menus (on request)',),
                    array('term' => 'Room service',),
                    array('term' => 'Breakfast in the room',),
                ),
                'wb_hotel_pool'                  => array(
                    array('term' => 'Indoor pool',),
                    array('term' => 'Indoor pool (seasonal)',),
                    array('term' => 'Indoor pool (all year)',),
                    array('term' => 'Outdoor pool',),
                    array('term' => 'Outdoor pool (seasonal)',),
                    array('term' => 'Outdoor pool (all year)',),
                    array('term' => 'Private beach area',),
                    array('term' => 'Beachfront',),
                    array('term' => 'Spa and wellness centre',),
                    array('term' => 'Sauna',),
                    array('term' => 'Hammam',),
                    array('term' => 'Hot tub/jacuzzi',),
                    array('term' => 'Fitness centre',),
                    array('term' => 'Solarium',),
                    array('term' => 'Hot spring bath',),
                    array('term' => 'Massage',),
                ),
                'wb_hotel_transport'             => array(
                    array('term' => 'Bikes available (free)',),
                    array('term' => 'Bicycle rental',),
                    array('term' => 'Car hire',),
                    array('term' => 'Airport shuttle (surcharge)',),
                    array('term' => 'Airport shuttle (free)',),
                    array('term' => 'Shuttle service (free)',),
                    array('term' => 'Shuttle service (surcharge)',),
                ),
                'wb_hotel_recep_serv'            => array(
                    array('term' => '24-hour front desk',),
                    array('term' => 'Private check-in/check-out',),
                    array('term' => 'Private check-in/check-out',),
                    array('term' => 'Concierge service',),
                    array('term' => 'Ticket service',),
                    array('term' => 'Tour desk',),
                    array('term' => 'Currency exchange',),
                    array('term' => 'ATM/cash machine on site',),
                    array('term' => 'Valet parking',),
                    array('term' => 'Luggage storage',),
                    array('term' => 'Lockers',),
                    array('term' => 'Safety deposit box',),
                    array('term' => 'Newspapers',),
                ),
                'wb_hotel_common_area'           => array(
                    array('term' => 'Garden',),
                    array('term' => 'Terrace',),
                    array('term' => 'Sun terrace',),
                    array('term' => 'Shared kitchen',),
                    array('term' => 'Shared lounge/TV area',),
                    array('term' => 'Games room',),
                    array('term' => 'Library',),
                    array('term' => 'Chapel/shrine',),
                ),
                'wb_hotel_family_services'       => array(
                    array('term' => 'Evening entertainment',),
                    array('term' => 'Nightclub/DJ',),
                    array('term' => 'Casino',),
                    array('term' => 'Karaoke',),
                    array('term' => 'Entertainment staff',),
                    array('term' => "Kids' club",),
                    array('term' => "Children's playground",),
                    array('term' => "Babysitting/child services",),
                ),
                'wb_hotel_cleaning_service'      => array(
                    array('term' => "Dry cleaning",),
                    array('term' => "Ironing service",),
                    array('term' => "Laundry",),
                    array('term' => "Daily maid service",),
                    array('term' => "Shoeshine",),
                    array('term' => "Trouser press",),
                ),
                'wb_hotel_business_facility'     => array(
                    array('term' => 'Meeting/banquet facilities'),
                    array('term' => 'Business centre'),
                    array('term' => 'Fax/photocopying'),
                ),
                'wb_hotel_shop'                  => array(
                    array('term' => 'Shops (on site)'),
                    array('term' => 'Mini-market on site'),
                    array('term' => 'Barber/beauty shop'),
                    array('term' => 'Gift shop'),
                ),
                'wb_hotel_miscellaneous'         => array(
                    array('term' => 'Adult only'),
                    array('term' => 'Allergy-free room'),
                    array('term' => 'Non-smoking throughout'),
                    array('term' => 'Designated smoking area'),
                    array('term' => 'Non-smoking rooms'),
                    array('term' => 'Facilities for disabled guests'),
                    array('term' => 'Lift'),
                    array('term' => 'Soundproof rooms'),
                    array('term' => 'Bridal suite'),
                    array('term' => 'VIP room facilities'),
                    array('term' => 'Air conditioning'),
                    array('term' => 'Heating'),
                ),
                'wb_hotel_room_amenity'          => array(
                    array(
                        'term' => 'Clothes rack',
                    ),
                    array('term' => 'Drying rack for clothing'),
                    array('term' => 'Fold-up bed'),
                    array('term' => 'Sofa bed'),
                    array('term' => 'Air Conditioning'),
                    array('term' => 'Wardrobe/Closet'),
                    array('term' => 'Carpeted'),
                    array('term' => 'Dressing Room'),
                    array('term' => 'Extra Long Beds (> 2 metres)'),
                    array('term' => 'Fan'),
                    array('term' => 'Fireplace'),
                    array('term' => 'Heating'),
                    array('term' => 'Interconnected room(s)  available'),
                    array('term' => 'Iron'),
                    array('term' => 'Ironing Facilities'),
                    array('term' => 'Mosquito net'),
                    array('term' => 'Private entrance'),
                    array('term' => 'Safety Deposit Box'),
                    array('term' => 'Sofa'),
                    array('term' => 'Soundproof'),
                    array('term' => 'Sitting area'),
                    array('term' => 'Tile/Marble floor'),
                    array('term' => 'Suit press'),
                    array('term' => 'Hardwood/Parquet floors'),
                    array('term' => 'Desk'),
                    array('term' => 'Hypoallergenic'),
                    array('term' => 'Cleaning products'),
                    array('term' => 'Electric blankets'),
                    array('term' => 'Bathroom'),
                    array('term' => 'Toilet paper'),
                    array('term' => 'Toilet With Grab Rails'),
                    array('term' => 'Bathtub'),
                    array('term' => 'Bidet'),
                    array('term' => 'Bathtub or shower'),
                    array('term' => 'Bathrobe'),
                    array('term' => 'Bathroom'),
                    array('term' => 'Free toiletries'),
                    array('term' => 'Hairdryer'),
                    array('term' => 'Spa tub'),
                    array('term' => 'Shared bathroom'),
                    array('term' => 'Shower'),
                    array('term' => 'Slippers'),
                    array('term' => 'Toilet'),
                ),
                'wb_hotel_room_bathroom'         => array(
                    array('term' => 'Toilet paper'),
                    array('term' => 'Toilet With Grab Rails'),
                    array('term' => 'Bathtub'),
                    array('term' => 'Bidet'),
                    array('term' => 'Bathtub or shower'),
                    array('term' => 'Bathrobe'),
                    array('term' => 'Bathroom'),
                    array('term' => 'Free toiletries'),
                    array('term' => 'Hairdryer'),
                    array('term' => 'Spa tub'),
                    array('term' => 'Shared bathroom'),
                    array('term' => 'Shower'),
                    array('term' => 'Slippers'),
                    array('term' => 'Toilet'),
                ),
                'wb_hotel_room_media_technology' => array(
                    array('term' => 'Computer'),
                    array('term' => 'Game console'),
                    array('term' => 'Game console - Nintendo Wii'),
                    array('term' => 'Game console - PS2'),
                    array('term' => 'Game console - PS3'),
                    array('term' => 'Game console - Xbox 360'),
                    array('term' => 'Laptop'),
                    array('term' => 'iPad'),
                    array('term' => 'Cable channels'),
                    array('term' => 'CD Player'),
                    array('term' => 'DVD Player'),
                    array('term' => 'Fax'),
                    array('term' => 'iPod dock'),
                    array('term' => 'Laptop safe'),
                    array('term' => 'Flat-screen TV'),
                    array('term' => 'Pay-per-view channels'),
                    array('term' => 'Radio'),
                    array('term' => 'Satellite channels'),
                    array('term' => 'Telephone'),
                    array('term' => 'TV'),
                    array('term' => 'Video'),
                    array('term' => 'Video games'),
                    array('term' => 'Blu-ray player'),
                ),
                'wb_hotel_room_food_drink'       => array(
                    array('term' => 'Dining area'),
                    array('term' => 'Dining table'),
                    array('term' => 'Barbecue'),
                    array('term' => 'Stovetop'),
                    array('term' => 'Toaster'),
                    array('term' => 'Electric kettle'),
                    array('term' => 'Outdoor dining area'),
                    array('term' => 'Outdoor furniture'),
                    array('term' => 'Minibar'),
                    array('term' => 'Kitchenette'),
                    array('term' => 'Kitchenware'),
                    array('term' => 'Microwave'),
                    array('term' => 'Refrigerator'),
                    array('term' => 'Tea/Coffee maker'),
                    array('term' => 'Coffee machine'),
                    array('term' => 'High chair'),
                ),
                'wb_hotel_room_services_extra'   => array(
                    array('term' => 'Executive Lounge Access'),
                    array('term' => 'Alarm clock'),
                    array('term' => 'Wake-up service'),
                    array('term' => 'Wake up service/Alarm clock'),
                    array('term' => 'Linens'),
                    array('term' => 'Towels'),
                    array('term' => 'Towels/Sheets (extra fee)'),
                ),
                'wb_hotel_room_outdoor_view'     => array(
                    array('term' => 'Balcony'),
                    array('term' => 'Patio'),
                    array('term' => 'View'),
                    array('term' => 'Terrace'),
                    array('term' => 'City view'),
                    array('term' => 'Garden view'),
                    array('term' => 'Lake view'),
                    array('term' => 'Landmark view'),
                    array('term' => 'Mountain view'),
                    array('term' => 'Pool view'),
                    array('term' => 'River view'),
                    array('term' => 'Sea view'),
                ),
                'wb_hotel_room_accessibility'    => array(
                    array('term' => 'Room is located on the ground floor'),
                    array('term' => 'Room is completely wheelchair accessible'),
                    array('term' => 'Upper floors accessible by elevator'),
                    array('term' => 'Upper floors accessible by stairs only'),
                ),
                'wb_hotel_room_entertainment'    => array(
                    array('term' => 'Baby safety gates'),
                    array('term' => 'Board games/puzzles'),
                    array('term' => 'Books, DVDs or music for children'),
                    array('term' => 'Child safety socket covers'),
                ),

                // Room Type
                'wb_hotel_room_type'             => array(
                    array(
                        "term"     => esc_html__("Single", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Budget Single Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Single Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Single Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Single Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Economy Single Room", 'wpbooking')),
                            array("term" => esc_html__("Large Single Room", 'wpbooking')),
                            array("term" => esc_html__("New Year's Eve Special - Single Room", 'wpbooking')),
                            array("term" => esc_html__("Single Room", 'wpbooking')),
                            array("term" => esc_html__("Single Room - Disability Access", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Bath", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Garden View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Lake View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Mountain View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Park View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Pool View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Private Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Private External Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Shared Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Shared Shower and Toilet", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Shared Toilet", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Shower", 'wpbooking')),
                            array("term" => esc_html__("Single Room with Terrace", 'wpbooking')),
                            array("term" => esc_html__("Small Single Room", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room with Mountain View", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room with Sauna", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room with Shared Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Standard Single Room with Shower", 'wpbooking')),
                            array("term" => esc_html__("Superior Single Room", 'wpbooking')),
                            array("term" => esc_html__("Superior Single Room with Lake View", 'wpbooking')),
                            array("term" => esc_html__("Superior Single Room with Sea View", 'wpbooking')),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Double", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Budget Double Room", 'wpbooking')),
                            array("term" => esc_html__("Business Double Room with Gym Access", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room (1 adult + 1 child)", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room (1 adult + 2 children)", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room (2 Adults + 1 Child)", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Balcony and Sea View", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Bath", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Castle View", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Extra Bed", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Shower", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double Room with Side Sea View", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Double or Twin Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe King Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Queen Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Room", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Room (1 adult + 1 child)", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Room (1 adult + 2 children)", 'wpbooking')),
                            array("term" => esc_html__("Deluxe Room (2 Adults + 1 Child)", 'wpbooking')),
                            array("term" => esc_html__("Double Room", 'wpbooking')),
                            array("term" => esc_html__("Double Room (1 Adult + 1 Child)", 'wpbooking')),
                            array("term" => esc_html__("Double Room - Disability Access", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Balcony (2 Adults + 1 Child)", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Balcony (3 Adults)", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Balcony and Sea View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Extra Bed", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Garden View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Lake View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Mountain View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Patio", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Pool View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Private Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Private External Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Shared Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Shared Toilet", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Spa Bath", 'wpbooking')),
                            array("term" => esc_html__("Double Room with Terrace", 'wpbooking')),
                            array("term" => esc_html__("Economy Double Room", 'wpbooking')),
                            array("term" => esc_html__("King Room", 'wpbooking')),
                            array("term" => esc_html__("King Room - Disability Access", 'wpbooking')),
                            array("term" => esc_html__("King Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("King Room with Garden View", 'wpbooking')),
                            array("term" => esc_html__("King Room with Lake View", 'wpbooking')),
                            array("term" => esc_html__("King Room with Mountain View", 'wpbooking')),
                            array("term" => esc_html__("King Room with Pool View", 'wpbooking')),
                            array("term" => esc_html__("King Room with Roll-In Shower - Disability Access", 'wpbooking')),
                            array("term" => esc_html__("King Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("King Room with Spa Bath", 'wpbooking')),
                            array("term" => esc_html__("Large Double Room", 'wpbooking')),
                            array("term" => esc_html__("Queen Room", 'wpbooking')),
                            array("term" => esc_html__("Queen Room - Disability Access", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Balcony", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Garden View", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Pool View", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Sea View", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Shared Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Queen Room with Spa Bath", 'wpbooking')),
                            array("term" => esc_html__("Small Double Room", 'wpbooking')),
                            array("term" => esc_html__("Standard Double Room", 'wpbooking')),
                            array("term" => esc_html__("Standard Double Room with Fan", 'wpbooking')),
                            array("term" => esc_html__("Standard Double Room with Shared Bathroom", 'wpbooking')),
                            array("term" => esc_html__("Standard King Room", 'wpbooking')),
                            array("term" => esc_html__("Standard Queen Room", 'wpbooking')),
                            array("term" => esc_html__("Superior Double Room", 'wpbooking')),
                            array("term" => esc_html__("Superior King Room", 'wpbooking')),
                            array("term" => esc_html__("Superior Queen Room", 'wpbooking')),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Twin", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Budget Twin Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double Room with Two Double Beds", "wpbooking")),
                            array("term" => esc_html__("Deluxe Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Deluxe Twin Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Double Room with Two Double Beds", "wpbooking")),
                            array("term" => esc_html__("Economy Twin Room", "wpbooking")),
                            array("term" => esc_html__("King Room with Two King Beds", "wpbooking")),
                            array("term" => esc_html__("Large Twin Room", "wpbooking")),
                            array("term" => esc_html__("Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Queen Room with Two Queen Beds - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Small Twin Room", "wpbooking")),
                            array("term" => esc_html__("Standard Double Room with Two Double Beds", "wpbooking")),
                            array("term" => esc_html__("Standard Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Standard Twin Room with Sofa", "wpbooking")),
                            array("term" => esc_html__("Superior Double Room with Two Double Beds", "wpbooking")),
                            array("term" => esc_html__("Superior King or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Superior Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Superior Twin Room", "wpbooking")),
                            array("term" => esc_html__("Superior Twin Room with City View", "wpbooking")),
                            array("term" => esc_html__("Superior Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Superior Twin Room with Sauna", "wpbooking")),
                            array("term" => esc_html__("Superior Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Twin Room", "wpbooking")),
                            array("term" => esc_html__("Twin Room - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Bath", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Bathroom", "wpbooking")),
                            array("term" => esc_html__("Twin Room with City View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Extra Bed", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Pool View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Private Bathroom", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Private External Bathroom", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Shared Toilet", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Twin Room with Terrace", "wpbooking")),
                            array("term" => esc_html__("Twin Room with View", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Twin/Double"),
                        'children' => array(
                            array("term" => esc_html__("Budget Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Cabin on Boat", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with City View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Ocean View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Pool Access", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Pool View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with River View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Double or Twin Room with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Bathroom", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Canal View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with City View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Extra Bed", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Harbour View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Partial Sea View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Pool View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Private Bathroom", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Private External Bathroom", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Side Sea View", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Spa Access", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Swimming Pool Access", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with Terrace", "wpbooking")),
                            array("term" => esc_html__("Double or Twin Room with View", "wpbooking")),
                            array("term" => esc_html__("Economy Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Large Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Small Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Standard Cabin on Boat", "wpbooking")),
                            array("term" => esc_html__("Standard Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Standard Double or Twin Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Standard Double or Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Standard Double or Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Superior Cabin on Boat", "wpbooking")),
                            array("term" => esc_html__("Superior Deluxe Double or Twin Room ", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with City View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Pool View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Superior Double or Twin Room with Terrace", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Triple", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Basic Triple Room", "wpbooking")),
                            array("term" => esc_html__("Basic Triple Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Budget Triple Room", "wpbooking")),
                            array("term" => esc_html__("Classic Triple Room", "wpbooking")),
                            array("term" => esc_html__("Comfort Triple Room", "wpbooking")),
                            array("term" => esc_html__("Comfort Triple Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Deluxe Triple Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Triple Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Economy Triple Room", "wpbooking")),
                            array("term" => esc_html__("Economy Triple Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Executive Triple Room", "wpbooking")),
                            array("term" => esc_html__("Luxury Triple Room", "wpbooking")),
                            array("term" => esc_html__("Standard Triple Room", "wpbooking")),
                            array("term" => esc_html__("Standard Triple Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Superior Triple Room", "wpbooking")),
                            array("term" => esc_html__("Superior Triple Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Triple Room", "wpbooking")),
                            array("term" => esc_html__("Triple Room - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Bath", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Bathroom", "wpbooking")),
                            array("term" => esc_html__("Triple Room with City View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Pool View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Private Bathroom", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Private External Bathroom", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Shared Toilet", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Triple Room with Terrace", "wpbooking")),
                            array("term" => esc_html__("Triple Room with View", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Quadruple", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Classic Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Comfort Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Duplex Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Economy Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Economy Quadruple Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Executive Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Japanese-Style Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("King Room with Two King Beds", "wpbooking")),
                            array("term" => esc_html__("Luxury Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Premium Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Bath", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Bathroom", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Private Bathroom", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Private External Bathroom", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Quadruple Room with Terrace", "wpbooking")),
                            array("term" => esc_html__("Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Queen Room with Two Queen Beds - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Standard Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Standard Queen Room with Two Queen Beds", "wpbooking")),
                            array("term" => esc_html__("Superior Quadruple Room", "wpbooking")),
                            array("term" => esc_html__("Superior Queen Room with Two Queen Beds", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Family", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Deluxe Family Room", "wpbooking")),
                            array("term" => esc_html__("Deluxe Family Suite", "wpbooking")),
                            array("term" => esc_html__("Family Bungalow", "wpbooking")),
                            array("term" => esc_html__("Family Cabin on Boat", "wpbooking")),
                            array("term" => esc_html__("Family Double Room", "wpbooking")),
                            array("term" => esc_html__("Family Junior Suite", "wpbooking")),
                            array("term" => esc_html__("Family Room", "wpbooking")),
                            array("term" => esc_html__("Family Room - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Family Room with Balcony", "wpbooking")),
                            array("term" => esc_html__("Family Room with Bath", "wpbooking")),
                            array("term" => esc_html__("Family Room with Bathroom", "wpbooking")),
                            array("term" => esc_html__("Family Room with Garden View", "wpbooking")),
                            array("term" => esc_html__("Family Room with Lake View", "wpbooking")),
                            array("term" => esc_html__("Family Room with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Family Room with Private Bathroom", "wpbooking")),
                            array("term" => esc_html__("Family Room with Sauna", "wpbooking")),
                            array("term" => esc_html__("Family Room with Sea View", "wpbooking")),
                            array("term" => esc_html__("Family Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Family Room with Shower", "wpbooking")),
                            array("term" => esc_html__("Family Room with Side Sea View", "wpbooking")),
                            array("term" => esc_html__("Family Room with Terrace", "wpbooking")),
                            array("term" => esc_html__("Family Studio", "wpbooking")),
                            array("term" => esc_html__("Family Suite", "wpbooking")),
                            array("term" => esc_html__("Family Suite with Balcony", "wpbooking")),
                            array("term" => esc_html__("Standard Family Room", "wpbooking")),
                            array("term" => esc_html__("Superior Family Room", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"      => esc_html__("Suite", 'wpbooking'),
                        'term_meta' => array(
                            'wpbooking_is_multi_bedroom'    => 1,
                            'wpbooking_is_multi_livingroom' => 1,
                        ),
                        'children'  => array(
                            array("term" => esc_html__("Deluxe Double Studio", "wpbooking")),
                            array("term" => esc_html__("Deluxe Junior Suite", "wpbooking")),
                            array("term" => esc_html__("Deluxe King Studio", "wpbooking")),
                            array("term" => esc_html__("Deluxe King Suite", "wpbooking")),
                            array("term" => esc_html__("Deluxe Queen Studio ", "wpbooking")),
                            array("term" => esc_html__("Deluxe Queen Suite", "wpbooking")),
                            array("term" => esc_html__("Deluxe Studio", "wpbooking")),
                            array("term" => esc_html__("Deluxe Suite", "wpbooking")),
                            array("term" => esc_html__("Deluxe Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("Deluxe Suite with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Duplex Suite", "wpbooking")),
                            array("term" => esc_html__("Executive Suite", "wpbooking")),
                            array("term" => esc_html__("Family Studio", "wpbooking")),
                            array("term" => esc_html__("Family Suite", "wpbooking")),
                            array("term" => esc_html__("Junior Suite", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Balcony", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Canal View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Garden View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Ocean View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Pool View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Private Pool", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Sauna", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("Junior Suite with Terrace", "wpbooking")),
                            array("term" => esc_html__("King Studio", "wpbooking")),
                            array("term" => esc_html__("King Studio with Sofa Bed", "wpbooking")),
                            array("term" => esc_html__("King Suite", "wpbooking")),
                            array("term" => esc_html__("King Suite with Balcony", "wpbooking")),
                            array("term" => esc_html__("King Suite with Ocean View", "wpbooking")),
                            array("term" => esc_html__("King Suite with Pool View", "wpbooking")),
                            array("term" => esc_html__("King Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("King Suite with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("One-Bedroom Suite", "wpbooking")),
                            array("term" => esc_html__("Presidential Suite", "wpbooking")),
                            array("term" => esc_html__("Queen Studio", "wpbooking")),
                            array("term" => esc_html__("Queen Studio - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Queen Suite", "wpbooking")),
                            array("term" => esc_html__("Queen Suite with Pool View", "wpbooking")),
                            array("term" => esc_html__("Queen Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("Queen Suite with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Standard Double Suite", "wpbooking")),
                            array("term" => esc_html__("Standard Studio", "wpbooking")),
                            array("term" => esc_html__("Standard Suite", "wpbooking")),
                            array("term" => esc_html__("Standard Triple Studio", "wpbooking")),
                            array("term" => esc_html__("Studio", "wpbooking")),
                            array("term" => esc_html__("Studio - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Studio with Balcony", "wpbooking")),
                            array("term" => esc_html__("Studio with Garden View", "wpbooking")),
                            array("term" => esc_html__("Studio with Ocean View", "wpbooking")),
                            array("term" => esc_html__("Studio with Pool View", "wpbooking")),
                            array("term" => esc_html__("Studio with Sea View", "wpbooking")),
                            array("term" => esc_html__("Studio with Sofa Bed", "wpbooking")),
                            array("term" => esc_html__("Studio with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Studio with Terrace", "wpbooking")),
                            array("term" => esc_html__("Suite", "wpbooking")),
                            array("term" => esc_html__("Suite with Balcony", "wpbooking")),
                            array("term" => esc_html__("Suite with City View", "wpbooking")),
                            array("term" => esc_html__("Suite with Garden View", "wpbooking")),
                            array("term" => esc_html__("Suite with Hot Tub", "wpbooking")),
                            array("term" => esc_html__("Suite with Lake View", "wpbooking")),
                            array("term" => esc_html__("Suite with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Suite with Pool View", "wpbooking")),
                            array("term" => esc_html__("Suite with Private Pool", "wpbooking")),
                            array("term" => esc_html__("Suite with River View", "wpbooking")),
                            array("term" => esc_html__("Suite with Sauna", "wpbooking")),
                            array("term" => esc_html__("Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("Suite with Spa Access", "wpbooking")),
                            array("term" => esc_html__("Suite with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Suite with Terrace", "wpbooking")),
                            array("term" => esc_html__("Superior King Suite", "wpbooking")),
                            array("term" => esc_html__("Superior Studio", "wpbooking")),
                            array("term" => esc_html__("Superior Suite", "wpbooking")),
                            array("term" => esc_html__("Superior Suite with Sea View", "wpbooking")),
                            array("term" => esc_html__("Three-Bedroom Suite", "wpbooking")),
                            array("term" => esc_html__("Two-Bedroom Suite", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"     => esc_html__("Studio", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Deluxe Double Studio", "wpbooking")),
                            array("term" => esc_html__("Deluxe King Studio", "wpbooking")),
                            array("term" => esc_html__("Deluxe Queen Studio ", "wpbooking")),
                            array("term" => esc_html__("Deluxe Studio", "wpbooking")),
                            array("term" => esc_html__("Duplex Studio", "wpbooking")),
                            array("term" => esc_html__("Family Studio", "wpbooking")),
                            array("term" => esc_html__("King Studio", "wpbooking")),
                            array("term" => esc_html__("King Studio with Sofa Bed", "wpbooking")),
                            array("term" => esc_html__("Queen Studio", "wpbooking")),
                            array("term" => esc_html__("Queen Studio - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Standard Studio", "wpbooking")),
                            array("term" => esc_html__("Standard Triple Studio", "wpbooking")),
                            array("term" => esc_html__("Studio", "wpbooking")),
                            array("term" => esc_html__("Studio - Disability Access", "wpbooking")),
                            array("term" => esc_html__("Studio - Split Level", "wpbooking")),
                            array("term" => esc_html__("Studio with Balcony", "wpbooking")),
                            array("term" => esc_html__("Studio with Garden View", "wpbooking")),
                            array("term" => esc_html__("Studio with Lake View", "wpbooking")),
                            array("term" => esc_html__("Studio with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Studio with Ocean View", "wpbooking")),
                            array("term" => esc_html__("Studio with Pool View", "wpbooking")),
                            array("term" => esc_html__("Studio with Sea View", "wpbooking")),
                            array("term" => esc_html__("Studio with Sofa Bed", "wpbooking")),
                            array("term" => esc_html__("Studio with Spa Bath", "wpbooking")),
                            array("term" => esc_html__("Studio with Terrace", "wpbooking")),
                            array("term" => esc_html__("Superior Studio", "wpbooking")),
                        ),
                    ),
                    array(
                        "term"      => esc_html__("Apartment", 'wpbooking'),
                        'term_meta' => array(
                            'wpbooking_is_multi_bedroom'    => 1,
                            'wpbooking_is_multi_livingroom' => 1,
                        ),
                        'children'  => array(
                            array("term" => esc_html__("Apartment", "wpbooking")),
                            array("term" => esc_html__("Apartment - Ground Floor", "wpbooking")),
                            array("term" => esc_html__("Apartment - Split Level", "wpbooking")),
                            array("term" => esc_html__("Apartment With Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Apartment with Balcony", "wpbooking")),
                            array("term" => esc_html__("Apartment with Garden View", "wpbooking")),
                            array("term" => esc_html__("Apartment with Lake View", "wpbooking")),
                            array("term" => esc_html__("Apartment with Mountain View", "wpbooking")),
                            array("term" => esc_html__("Apartment with Pool View ", "wpbooking")),
                            array("term" => esc_html__("Apartment with Sauna", "wpbooking")),
                            array("term" => esc_html__("Apartment with Sea View", "wpbooking")),
                            array("term" => esc_html__("Apartment with Shower", "wpbooking")),
                            array("term" => esc_html__("Apartment with Terrace", "wpbooking")),
                            array("term" => esc_html__("Deluxe Apartment", "wpbooking")),
                            array("term" => esc_html__("Duplex Apartment", "wpbooking")),
                            array("term" => esc_html__("Loft", "wpbooking")),
                            array("term" => esc_html__("Maisonette", "wpbooking")),
                            array("term" => esc_html__("One-Bedroom Apartment", "wpbooking")),
                            array("term" => esc_html__("Penthouse Apartment", "wpbooking")),
                            array("term" => esc_html__("Standard Apartment", "wpbooking")),
                            array("term" => esc_html__("Studio Apartment", "wpbooking")),
                            array("term" => esc_html__("Studio Apartment with Sea View", "wpbooking")),
                            array("term" => esc_html__("Superior Apartment", "wpbooking")),
                            array("term" => esc_html__("Superior Apartment with Sauna", "wpbooking")),
                            array("term" => esc_html__("Three-Bedroom Apartment", "wpbooking")),
                            array("term" => esc_html__("Two-Bedroom Apartment", "wpbooking")),
                        ),

                    ),
                    array(
                        "term"     => esc_html__("Dormitory room", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Bed in 10-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 10-Bed Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 10-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 8-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 8-Bed Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 8-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Female Dormitory Room ", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Male Dormitory Room ", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Mixed Dormitory Room", "wpbooking")),
                        ),

                    ),
                    array(
                        "term"     => esc_html__("Bed in Dormitory", 'wpbooking'),
                        'children' => array(
                            array("term" => esc_html__("Bed in 10-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 4-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 6-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 8-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in 8-Bed Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bed in Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Female Dormitory Room ", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Male Dormitory Room ", "wpbooking")),
                            array("term" => esc_html__("Bunk Bed in Mixed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in 10-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in 4-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in 6-Bed Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Female Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Male Dormitory Room", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Male Dormitory Room with Shared Bathroom", "wpbooking")),
                            array("term" => esc_html__("Single Bed in Mixed Dormitory Room", "wpbooking")),
                        )
                    )
                )

            );

            foreach ($terms as $tax => $term) {
                foreach ($term as $item) {
                    $item = wp_parse_args($item, array('parent' => '', 'term' => '', 'children' => array(), 'term_meta' => array()));
                    if ($item['term']) {
                        // Check Exists
                        $old = term_exists($item['term'], $tax);
                        if (!$old) {
                            $term_data = wp_insert_term($item['term'], $tax, $item);
                        } else {
                            $term_data = $old;
                        }
                        if (!is_wp_error($term_data) and !empty($item['children'])) {
                            foreach ($item['children'] as $child) {
                                if (!term_exists($child['term'], $tax, $term_data['term_id'])) {
                                    wp_insert_term($child['term'], $tax, array('parent' => $term_data['term_id']));
                                }
                            }
                        }

                        // Term Meta
                        if (!is_wp_error($term_data) and !empty($item['term_meta']) and function_exists('add_term_meta')) {
                            foreach ($item['term_meta'] as $key => $meta) {
                                $a = add_term_meta($term_data['term_id'], $key, $meta, true);
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
        function _get_room_by_hotel($post_id)
        {
            if (empty($post_id))
                return;
            $list = array();
            $args = array(
                'post_type'      => 'wpbooking_hotel_room',
                'post_parent'    => $post_id,
                'posts_per_page' => 200,
                'post_status'    => array('pending', 'draft', 'future', 'publish'),
            );
            $my_query = new WP_Query($args);
            if ($my_query->have_posts()) {
                while ($my_query->have_posts()) {
                    $my_query->the_post();
                    $list[] = array('ID' => get_the_ID(), 'post_title' => get_the_title());
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
                array('type' => 'open_section'),
                array(
                    'label' => __("Deluxe Queen Studio", 'wpbooking'),
                    'type'  => 'title',
                    'desc'  => esc_html__('Select a room type : Single , double , twin, twin / double , triple, quadruple, family, suite, studio, apartment, dormitory room, bed in dormitory, ...', 'wpbooking')
                ),
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
                    'label' => esc_html__('Room name (optional)', 'wpbooking'),
                    'type'  => 'text',
                    'id'    => 'room_name_custom',
                ),
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
                    'label' => esc_html__('Bed Rooms', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'bed_rooms',
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    /* 'condition' => 'room_type:is(on)',*/
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__('Bath Rooms', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'bath_rooms',
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__('Living Rooms', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'living_rooms',
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array('type' => 'close_section'),

                // Bed Options
                array('type' => 'open_section'),
                array(
                    'label' => __("Bed options", 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'id'            => 'bed_room_options',
                    'label'         => __("What kind of beds are available in this room?", 'wpbooking'),
                    'type'          => 'bed_options',
                    'value'         => WPBooking_Config::inst()->item('bed_type'),
                    'add_new_label' => esc_html__('Add another bed', 'wpbooking'),
                    'fields'        => array(
                        'bed_options_single_',
                        'bed_options_single_num_guests',
                        'bed_options_single_private_bathroom',
                        'bed_options_multi_',
                    )
                ),
                array(
                    'id'    => 'living_room_options',
                    'type'  => 'living_options',
                    'class' => 'small'
                ),

                array('type' => 'close_section'),

                /* // Base Price
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
                array('type' => 'close_section'),*/

                // Guest information
                array('type' => 'open_section'),
                array(
                    'label' => __('Guest Information'),
                    'type'  => 'title',
                    'desc'  => esc_html__('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium', 'wpbooking'),
                ),
                array(
                    'id'    => 'gi_max_adult',
                    'type'  => 'dropdown',
                    'label' => __('Max adult', 'wpbooking'),
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array(
                    'id'    => 'gi_max_children',
                    'type'  => 'dropdown',
                    'label' => __('Max children', 'wpbooking'),
                    'value' => array(
                        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'class' => 'small'
                ),
                array('type' => 'close_section'),

                // Extra Service
                array(
                    'type' => 'open_section'
                ),
                array(
                    'type'  => 'title',
                    'label' => __('Extra Services', 'wpbooking'),
                    'desc'  => esc_html__('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium', 'wpbooking')
                ),
                array(
                    'type'  => 'extra_services',
                    'label' => __('Choose extra services', 'wpbooking'),
                    'id'    => 'extra_services_hotel'
                ),
                array(
                    'type' => 'close_section'
                ),

                // Calendar
                array('type' => 'open_section'),
                array(
                    'label' => __("Price Settings", 'wpbooking'),
                    'type'  => 'title',
                    'desc'  => esc_html__('You can setting price for room', 'wpbooking')
                ),
                array(
                    'id'   => 'calendar',
                    'type' => 'calendar',
                ),
                array('type' => 'close_section'),
            );

            return apply_filters('wpbooking_hotel_room_meta_fields', $fields);
        }


        /**
         * Ajax Show Room Form
         *
         * @since 1.0
         * @author dungdt
         */
        function _ajax_room_edit_template()
        {
            $res = array(
                'status' => 0
            );
            $room_id = WPBooking_Input::post('room_id');
            $hotel_id = WPBooking_Input::post('hotel_id');


            if (!$room_id) {

                // Validate Permission
                if (!$hotel_id) {
                    $res['message'] = esc_html__('Please Specific Hotel ID', 'wpbooking');
                    echo json_encode($res);
                    die;
                } else {
                    $hotel = get_post($hotel_id);
                    if (!$hotel) {
                        $res['message'] = esc_html__('Hotel is not exists', 'wpbooking');
                        echo json_encode($res);
                        die;
                    }
                    // Check Role
                    if (!current_user_can('manage_options') and $hotel->post_parent != get_current_user_id()) {
                        $res['message'] = esc_html__('You do not have permission to do it', 'wpbooking');
                        echo json_encode($res);
                        die;
                    }
                }


                // Create Draft Room
                $room_id = wp_insert_post(array(
                    'post_author' => get_current_user_id(),
                    'post_title'  => esc_html__('Room Draft', 'wpbooking'),
                    'post_type'   => 'wpbooking_hotel_room',
                    'post_status' => 'draft',
                    'post_parent' => $hotel_id
                ));

                if (is_wp_error($room_id)) {
                    $res['message'] = esc_html__('Can not create room, please check again', 'wpbooking');
                    echo json_encode($res);
                    die;
                }
            }

            $res['status'] = 1;
            $res['html'] = "
                <input name='wb_room_id' type='hidden' value='" . esc_attr($room_id) . "'>
            ";
            $res['html'] .= sprintf('<input type="hidden" name="wb_hotel_room_security" value="%s">', wp_create_nonce("wpbooking_hotel_room_" . $room_id));
            $fields = $this->get_room_meta_fields();
            foreach ((array)$fields as $field_id => $field):

                if (empty($field['type']))
                    continue;

                $default = array(
                    'id'          => '',
                    'label'       => '',
                    'type'        => '',
                    'desc'        => '',
                    'std'         => '',
                    'class'       => '',
                    'location'    => false,
                    'map_lat'     => '',
                    'map_long'    => '',
                    'map_zoom'    => 13,
                    'server_type' => '',
                    'width'       => ''
                );

                $field = wp_parse_args($field, $default);

                $class_extra = false;
                if ($field['location'] == 'hndle-tag') {
                    $class_extra = 'wpbooking-hndle-tag-input';
                }
                $file = 'metabox-fields/' . $field['type'];
                //var_dump($file);

                $field_html = apply_filters('wpbooking_metabox_field_html_' . $field['type'], false, $field);

                if ($field_html)
                    $res['html'] .= $field_html;
                else
                    $res['html'] .= wpbooking_admin_load_view($file, array('data'        => $field,
                                                                           'class_extra' => $class_extra,
                                                                           'post_id'     => $room_id
                    ));


            endforeach;

            $res['html'] .= wpbooking_admin_load_view('metabox-fields/room-form-button');

            echo json_encode($res);
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
            $res = array('status' => 0);

            $room_id = WPBooking_Input::post('wb_room_id');

            if ($room_id) {
                // Validate
                check_ajax_referer("wpbooking_hotel_room_" . $room_id, 'wb_hotel_room_security');


                if ($name = WPBooking_Input::request('room_name_custom')) {
                    $my_post = array(
                        'ID'         => $room_id,
                        'post_title' => $name,
                    );
                    wp_update_post($my_post);
                }

                $fields = $this->get_room_meta_fields();
                WPBooking_Metabox::inst()->do_save_metabox($room_id, $fields, 'wpbooking_hotel_room_form');

                // Save Extra Fields
                //property_available_for
                if (isset($_POST['property_available_for'])) update_post_meta($room_id, 'property_available_for', $_POST['property_available_for']);

                $list_room_new = WPBooking_Hotel_Service_Type::inst()->_get_room_by_hotel(wp_get_post_parent_id($room_id));

                $list_room_new = json_encode($list_room_new);
                $res['data']['list_room'] = $list_room_new;

                $res['data']['number'] = get_post_meta($room_id, 'room_number', true);
                $res['data']['thumbnail'] = '';
                $res['data']['title'] = get_the_title($room_id);
                $res['data']['room_id'] = $room_id;
                $res['data']['security'] = wp_create_nonce('del_security_post_' . $room_id);

                $res['updated_content'] = apply_filters('wpbooking_hotel_room_form_updated_content', array(), $room_id);

                $res['status'] = 1;
            }


            echo json_encode($res);
            die;
        }

        /**
         * Ajax delete room
         *
         * @since: 1.0
         * @author: Tien37
         */

        public function _ajax_del_room_item()
        {
            $res = array('status' => 0);

            $room_id = WPBooking_Input::post('wb_room_id');

            if ($room_id) {
                check_ajax_referer('del_security_post_' . $room_id, 'wb_del_security');
                $parent_id = wp_get_post_parent_id($room_id);
                if (wp_delete_post($room_id) !== false) {
                    $res['status'] = 1;
                    $list_room_new = WPBooking_Hotel_Service_Type::inst()->_get_room_by_hotel($parent_id);
                    $list_room_new = json_encode($list_room_new);
                    $res['data']['list_room'] = $list_room_new;
                }
            }
            echo json_encode($res);
            wp_die();
        }

        static function inst()
        {
            if (!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Hotel_Service_Type::inst();
}