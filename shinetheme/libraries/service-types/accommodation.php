<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 3:47 PM
 */
if (!class_exists('WPBooking_Accommodation_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
    class WPBooking_Accommodation_Service_Type extends WPBooking_Abstract_Service_Type
    {
        static $_inst = false;

        protected $type_id = 'accommodation';

        function __construct()
        {
            $this->type_info = array(
                'label' => __("Accommodation", 'wpbooking'),
                'desc'  => esc_html__('Chỗ nghỉ cho khách du lịch, thường có nhà hàng, phòng họp và các dịch vụ khác dành cho khách', 'wpbooking')
            );

            $this->settings = array(

                array(
                    'id'    => 'title',
                    'label' => __('General Options', 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'id'    => 'review',
                    'label' => __('Review', 'wpbooking'),
                    'type'  => 'multi-checkbox',
                    'value' => array(
                        array(
                            'id'    => 'service_type_' . $this->type_id . '_enable_review',
                            'label' => __('Enable Review', 'wpbooking')
                        ),
                        array(
                            'id'    => 'service_type_' . $this->type_id . '_review_without_booking',
                            'label' => __('Allow user to review without booking', 'wpbooking')
                        ),

                        array(
                            'id'    => 'service_type_' . $this->type_id . '_show_rate_review_button',
                            'label' => __('Show Rate (Help-full) button in each review?', 'wpbooking')
                        ),
                        array(
                            'id'    => 'service_type_' . $this->type_id . '_allowed_review_on_own_listing',
                            'label' => __('User can write review on their own listing?', 'wpbooking')
                        ),
                        array(
                            'id'    => 'service_type_' . $this->type_id . '_allowed_vote_for_own_review',
                            'label' => __('User can vote for their own review?', 'wpbooking')
                        ),
//						array(
//							'id'    => 'service_type_'.$this->type_id . '_required_partner_approved_review',
//							'label' => __('Review require Partner Approved?', 'wpbooking')
//						),
                    )
                ),

                array(
                    'id'    => 'review_stats',
                    'label' => __("Review Stats", 'wpbooking'),
                    'type'  => 'taxonomy-checkbox',
                    'taxonomy'=>'wpbooking_review_stats',
                    'show_create'=>true,
                    'show_only_selected'=>true
                ),
                array(
                    'id'    => 'maximum_review',
                    'label' => __("Maximum review per user", 'wpbooking'),
                    'type'  => 'number',
                    'std'   => 1
                ),
                array(
                    'type' => 'hr'
                ),
                array(
                    'id'    => 'title',
                    'label' => __('Layout', 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'id'    => 'posts_per_page',
                    'label' => __("Item per page", 'wpbooking'),
                    'type'  => 'number',
                    'std'   => 10
                ),
                array(
                    'id'    => "thumb_size",
                    'label' => __("Thumb Size", 'travel-booking'),
                    'type'  => 'image-size'
                ),
                array(
                    'id'    => "gallery_size",
                    'label' => __("Gallery Size", 'travel-booking'),
                    'type'  => 'image-size'
                ),
            );

            parent::__construct();

            add_action('init', array($this, '_add_init_action'));


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

            /**
             * Ajax search room
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wp_ajax_ajax_search_room', array($this, 'ajax_search_room'));
            add_action('wp_ajax_nopriv_ajax_search_room', array($this, 'ajax_search_room'));

            /**
             * Ajax search room
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wp_ajax_wpbooking_reload_image_list_room', array($this, 'wpbooking_reload_image_list_room'));
            add_action('wp_ajax_nopriv_wpbooking_reload_image_list_room', array($this, 'wpbooking_reload_image_list_room'));

            /**
             * Filter List Room Size
             *
             * @since 1.0
             * @author quandq
             */
            add_filter('wpbooking_hotel_room_form_updated_content', array($this, '_get_list_room_size'), 10, 3);


            //wpbooking_archive_loop_image_size
            add_filter('wpbooking_archive_loop_image_size', array($this, '_apply_thumb_size'), 10, 3);


            /**
             * Change Base Price
             *
             * @since 1.0
             */
            add_filter('wpbooking_service_base_price_' . $this->type_id, array($this, '_change_base_price'), 10, 3);

            /**
             * Move name and email field to top in comment
             */

            add_filter('comment_form_fields',array($this,'_move_fields_comment_top'));

            /**
             * Add more params to cart items
             *
             * @since 1.0
             * @author quandq
             */
            add_filter('wpbooking_cart_item_params_' . $this->type_id, array($this, '_change_cart_item_params'), 10, 2);

            /**
             * validate add to cart
             *
             * @since 1.0
             * @author quandq
             *
             */
            add_filter('wpbooking_add_to_cart_validate_' . $this->type_id, array($this, '_add_to_cart_validate'), 10, 4);

            /**
             * Add info room
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wpbooking_other_item_information_'.$this->type_id, array($this, '_add_info_item_room'),10,2);
            add_action('wpbooking_other_total_item_information_'.$this->type_id, array($this, '_add_info_total_item_room'),10,2);
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
                'meta_box_cb'       => false,
                'rewrite'           => array('slug' => 'hotel-room-type'),
            );
            register_taxonomy('wb_hotel_room_type', array('wpbooking_hotel_room'), $args);

            // Register Taxonomy
            $labels = array(
                'name'              => _x('Room Facilities', 'taxonomy general name', 'wpbooking'),
                'singular_name'     => _x('Room Facilities', 'taxonomy singular name', 'wpbooking'),
                'search_items'      => __('Search Room Facilities', 'wpbooking'),
                'all_items'         => __('All Room Facilities', 'wpbooking'),
                'parent_item'       => __('Parent Room Facilities', 'wpbooking'),
                'parent_item_colon' => __('Parent Room Facilities:', 'wpbooking'),
                'edit_item'         => __('Edit Room Facilities', 'wpbooking'),
                'update_item'       => __('Update Room Facilities', 'wpbooking'),
                'add_new_item'      => __('Add New Room Facilities', 'wpbooking'),
                'new_item_name'     => __('New Room Facilities Name', 'wpbooking'),
                'menu_name'         => __('Room Facilities', 'wpbooking'),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'meta_box_cb'       => false,
                'rewrite'           => array('slug' => 'hotel-room-facilities'),
            );
            register_taxonomy('wb_hotel_room_facilities', array('wpbooking_service'), $args);


            // Metabox
            $this->set_metabox(array(
                'general_tab'     => array(
                    'label'  => esc_html__('1. Property Information', 'wpbooking'),
                    'fields' => array(
                        array(
                            'type' => 'open_section',
                        ),
                        array(
                            'label' => __("About Your Property", 'wpbooking'),
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
                            'desc'  => esc_html__('Standard of property from 1 to 5 star.', 'wpbooking'),
                            'class' => 'small'
                        ),
                        array(
                            'label'        => __('Contact Number', 'wpbooking'),
                            'id'           => 'contact_number',
                            'desc'         => esc_html__('The contact phone', 'wpbooking'),
                            'type'         => 'text',
                            'class'        => 'small',
                            'tooltip_desc' => esc_html__('The contact phone', 'wpbooking')
                        ),
                        array(
                            'label'       => __('Contact Email', 'wpbooking'),
                            'id'          => 'contact_email',
                            'type'        => 'text',
                            'placeholder' => esc_html__('Example@domain.com', 'wpbooking'),
                            'class'       => 'small'
                        ),
                        array(
                            'label'       => __('Website', 'wpbooking'),
                            'id'          => 'website',
                            'type'        => 'text',
                            'desc'        => esc_html__('Property website (optional)', 'wpbooking'),
                            'placeholder' => esc_html__('http://exampledomain.com', 'wpbooking'),
                            'class'       => 'small'
                        ),
                        array('type' => 'close_section'),
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Property Location", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Property's address and your contact number", 'wpbooking'),
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
                            'id'     => 'check_in',
                            'fields' => array('checkin_from', 'checkin_to'),// Fields to save
                        ),
                        array(
                            'label'  => esc_html__('Check Out time', 'wpbooking'),
                            'desc'   => esc_html__('Check Out time', 'wpbooking'),
                            'type'   => 'check_out',
                            'id'     => 'check_out',
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
                            'id'       => 'wpbooking_select_amenity',
                            'taxonomy' => 'wpbooking_amenity',
                            'type'     => 'taxonomy_select',
                        ),
                        array(
                            'type'         => 'custom-taxonomy',
                            'service_type' => $this->type_id
                        ),
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
                        array(
                            'type' => 'open_section',
                        ),
                        array(
                            'label' => __("Space", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Chúng tôi hiển thị kích thước phòng cho khách", "wpbooking")
                        ),
                        array(
                            'label' => __('What is your preferred  unit of measurement?', 'wpbooking'),
                            'id'    => 'room_measunit',
                            'type'  => 'radio',
                            'value' => array(
                                "metres" => esc_html__("Square metres", 'wpbooking'),
                                "feed"   => esc_html__("Square feet", 'wpbooking'),
                            ),
                            'std'   => 'metres',
                            'class' => 'radio_pro',
                            'desc'  => esc_html__("Chọn đơn vị đo lường ưa thích của bạn", "wpbooking")
                        ),
                        array(
                            'label'  => __('Room sizes', 'wpbooking'),
                            'id'     => 'room_size',
                            'type'   => 'room_size',
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'open_section',
                        ),
                        array(
                            'label' => __("Room facilities", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'id'       => 'hotel_room_facilities',
                            'label'    => __("Facilities", 'wpbooking'),
                            'type'     => 'taxonomy_room_select',
                            'taxonomy' => 'wb_hotel_room_facilities'
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
                                'day_of_arrival'  => __('Day of arrival (6 pm)', 'wpbooking'),
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
         * Get Room Metabox Fields
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
                    'label' => __("Room Name", 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'label' => esc_html__('Room name (optional)', 'wpbooking'),
                    'type'  => 'text',
                    'id'    => 'room_name',
                    'desc'  => __("Create an optional, custom name for your reference.", 'wpbooking'),
                ),
                array(
                    'label'    => esc_html__('Room Type', 'wpbooking'),
                    'type'     => 'dropdown',
                    'id'       => 'room_type',
                    'taxonomy' => 'wb_hotel_room_type',
                    'parent'   => 0,
                    'class'    => 'small',
                    'desc'  => __("Dựa vào tiện nghi của phòng, hãy chọn 1 kiểu chính xác nhất.", 'wpbooking'),
                ),
                array(
                    'label' => esc_html__('Room Number', 'wpbooking'),
                    'type'  => 'number',
                    'id'    => 'room_number',
                    'class' => 'small',
                    'desc'  => __("Out of 2 rooms at your property", 'wpbooking'),
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
                array(
                    'label' => esc_html__('Max guests', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'max_guests',
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
                    'desc'  => esc_html__('Thiết lập các dịch vụ mở rộng cho tài sản của bạn', 'wpbooking')
                ),
                array(
                    'type'           => 'extra_services',
                    'label'          => __('Choose extra services', 'wpbooking'),
                    'id'             => 'extra_services',
                    'extra_services' => $this->get_extra_services(),
                    'service_type'   => $this->type_id
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
            $room_id = $this->post('room_id');
            $hotel_id = $this->post('hotel_id');


            if (!$room_id) {

                // Validate Permission
                if (!$hotel_id) {
                    $res['message'] = esc_html__('Please Specific Property ID', 'wpbooking');
                    echo json_encode($res);
                    die;
                } else {
                    $hotel = get_post($hotel_id);
                    if (!$hotel) {
                        $res['message'] = esc_html__('Property is not exists', 'wpbooking');
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

            $room_id = $this->post('wb_room_id');

            if ($room_id) {
                // Validate
                check_ajax_referer("wpbooking_hotel_room_" . $room_id, 'wb_hotel_room_security');


                if ($name = $this->request('room_name')) {
                    $my_post = array(
                        'ID'         => $room_id,
                        'post_title' => $name,
                        'post_status' => 'publish',
                    );
                    wp_update_post($my_post);
                }

                $fields = $this->get_room_meta_fields();
                WPBooking_Metabox::inst()->do_save_metabox($room_id, $fields, 'wpbooking_hotel_room_form');

                // Save Extra Fields
                //property_available_for
                if (isset($_POST['property_available_for'])) update_post_meta($room_id, 'property_available_for', $_POST['property_available_for']);

                $hotel_id = wp_get_post_parent_id($room_id);
                $list_room_new = $this->_get_room_by_hotel($hotel_id);


                $list_room_new = json_encode($list_room_new);
                $res['data']['list_room'] = $list_room_new;

                $res['data']['number'] = get_post_meta($room_id, 'room_number', true);
                $res['data']['thumbnail'] = '';
                $res['data']['title'] = get_the_title($room_id);
                $res['data']['room_id'] = $room_id;
                $res['data']['security'] = wp_create_nonce('del_security_post_' . $room_id);

                $res['updated_content'] = apply_filters('wpbooking_hotel_room_form_updated_content', array(), $room_id, $hotel_id);

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
            $room_id = $this->post('wb_room_id');
            $hotel_id = wp_get_post_parent_id($room_id);
            if ($room_id) {
                check_ajax_referer('del_security_post_' . $room_id, 'wb_del_security');
                $parent_id = wp_get_post_parent_id($room_id);
                if (wp_delete_post($room_id) !== false) {
                    $res['status'] = 1;
                    $list_room_new = $this->_get_room_by_hotel($parent_id);
                    $list_room_new = json_encode($list_room_new);
                    $res['data']['list_room'] = $list_room_new;
                }
            }
            $res['updated_content'] = apply_filters('wpbooking_hotel_room_form_updated_content', array(), $room_id, $hotel_id);
            echo json_encode($res);
            wp_die();
        }

        /**
         * Ajax search room
         *
         * @since: 1.0
         * @author: quandq
         */
        function ajax_search_room()
        {
            if ($this->post('room_search')) {
                if (!wp_verify_nonce($this->post('room_search'), 'room_search')) {
                    $result = array(
                        'status' => 0,
                        'data'   => "",
                    );
                    echo json_encode($result);
                    die;
                }
                $result = array(
                    'status' => 1,
                    'data'   => "",
                );
                $hotel_id = get_the_ID();
                self::search_room();
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        $result['data'] .= wpbooking_load_view('single/loop-room', array('hotel_id' => $hotel_id));
                    }
                } else {
                    $result = array(
                        'status'  => 0,
                        'data'    => '',
                        'message' => __('No Room.', 'wpbooking'),
                    );
                    echo json_encode($result);
                    die;
                }
                wp_reset_query();
                echo json_encode($result);
                wp_die();
            }
        }


        /**
         *  Query Room
         *
         * @since: 1.0
         * @author: quandq
         *
         */

        function _add_default_query_hook()
        {
            global $wpdb;
            $table_prefix = WPBooking_Service_Model::inst()->get_table_name();
            $injection = WPBooking_Query_Inject::inst();
            $tax_query = $injection->get_arg('tax_query');
            $rate_calculate = FALSE;

            // Taxonomy
            $tax = $this->request('taxonomy');
            if (!empty($tax) and is_array($tax)) {
                $taxonomy_operator = $this->request('taxonomy_operator');
                $tax_query_child = array();
                foreach ($tax as $key => $value) {
                    if ($value) {
                        if (!empty($taxonomy_operator[$key])) {
                            $operator = $taxonomy_operator[$key];
                        } else {
                            $operator = "OR";
                        }
                        if ($operator == 'OR') $operator = 'IN';
                        $value = explode(',', $value);
                        if (!empty($value) and is_array($value)) {
                            foreach ($value as $k => $v) {
                                if (!empty($v)) {
                                    $ids[] = $v;
                                }
                            }
                        }
                        if (!empty($ids)) {
                            $tax_query[] = array(
                                'taxonomy' => $key,
                                'terms'    => $ids,
                                'operator' => $operator,
                            );
                        }
                        $ids = array();
                    }
                }


                if (!empty($tax_query_child))
                    $tax_query[] = $tax_query_child;
            }

            // Star Rating
            if ($star_rating = $this->get('star_rating') and is_array(explode(',', $star_rating))) {

                $star_rating_arr = explode(',', $star_rating);
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'star_rating',
                        'type'    => 'CHAR',
                        'value'   => $star_rating_arr,
                        'compare' => 'IN'
                    )
                );
            }
            // Review
            if ($review_rate = $this->request('review_rate') and is_array(explode(',', $review_rate))) {

                $rate_calculate = 1;


                foreach (explode(',', $review_rate) as $k => $v) {
                    $clause = 'AND';
                    if ($k) $clause = 'OR';
                    $injection->having("(avg_rate>=" . $v . ' and avg_rate<' . ($v + 1) . ') ', $clause);

                }

            }

            if ($rate_calculate) {
                $injection->select('avg(' . $wpdb->commentmeta . '.meta_value) as avg_rate')
                    ->join('comments', $wpdb->prefix . 'comments.comment_post_ID=' . $wpdb->posts . '.ID and  ' . $wpdb->comments . '.comment_approved=1', 'LEFT')
                    ->join('commentmeta', $wpdb->prefix . 'commentmeta.comment_id=' . $wpdb->prefix . 'comments.comment_ID and ' . $wpdb->commentmeta . ".meta_key='wpbooking_review'", 'LEFT');
            }

            if (!empty($tax_query))
                $injection->add_arg('tax_query', $tax_query);

            if (!empty($meta_query))
                $injection->add_arg('meta_query', $meta_query);

            // Order By
            if ($sortby = $this->request('wb_sort_by')) {
                switch ($sortby) {
                    case "price_asc":
                        $injection->orderby($table_prefix . '.price', 'asc');
                        break;
                    case "price_desc":
                        $injection->orderby($table_prefix . '.price', 'desc');
                        break;
                    case "date_asc":
                        $injection->add_arg('orderby', 'date');
                        $injection->add_arg('order', 'asc');
                        break;
                    case "date_desc":
                        $injection->add_arg('orderby', 'date');
                        $injection->add_arg('order', 'desc');
                        break;
                    case "rate_asc":
                    case "rate_desc":
                        $rate_calculate = 1;
                        if ($sortby == 'rate_asc') {
                            $injection->orderby('avg_rate', 'asc');
                        } else {
                            $injection->orderby('avg_rate', 'desc');
                        }

                        break;
                }
            }

            parent::_add_default_query_hook();

        }

        /**
         *
         */
        function search_room()
        {
            $hotel_id = get_the_ID();
            $arg = array(
                'post_type'      => 'wpbooking_hotel_room',
                'posts_per_page' => '200',
                'post_status'    => 'publish',
                'post_parent'    => $hotel_id
            );
            $adults = $this->request('adults');
            $children = $this->request('children');
            $max_guests = $adults + $children;
            if (!empty($max_guests)) {
                $arg['meta_query'][] = array(
                    'key'     => 'max_guests',
                    'value'   => $max_guests,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                );
            }
            global $wp_query;
            query_posts($arg);
        }

        /**
         * Filter List Room Size
         *
         * @since: 1.0
         * @author: quandq
         *
         * @param $data
         * @return mixed
         */
        function _get_list_room_size($data, $room_id, $hotel_id)
        {
            $html = '<div class="wpbooking-row room_size_content">';
            $arg = array(
                'post_type'      => 'wpbooking_hotel_room',
                'posts_per_page' => '200',
                'post_status'    => array('publish', 'draft', 'pending', 'future', 'private', 'inherit'),
                'post_parent'    => $hotel_id
            );
            query_posts($arg);
            while (have_posts()) {
                the_post();
                $html .= '<div class="wpbooking-col-sm-6">
                            <div class="form-group">
                                <p>' . get_the_title() . '</p>
                                <div class="input-group">
                                    <input class="form-control" id="room_size[' . get_the_ID() . ']" name="room_size[' . get_the_ID() . ']" type="number" value="' . get_post_meta(get_the_ID(), 'room_size', true) . '">
                                    <span data-condition="room_measunit:is(metres)" class="input-group-addon wpbooking-condition" style="display: none;">m<sup>2</sup></span>
                                    <span data-condition="room_measunit:is(feed)" class="input-group-addon wpbooking-condition">ft<sup>2</sup></span>
                                </div>
                            </div>
                        </div>';
            }
            $html .= '</div>';
            wp_reset_query();
            $data['.room_size_content'] = $html;

            return $data;

        }

        /**
         * @param $size
         * @param $service_type
         * @param $post_id
         * @return array
         */
        function _apply_thumb_size($size, $service_type, $post_id)
        {
            if ($service_type == $this->type_id) {
                $thumb = $this->thumb_size('150,150,off');
                $thumb = explode(',', $thumb);
                if (count($thumb) == 3) {
                    if ($thumb[2] == 'off') $thumb[2] = FALSE;

                    $size = array($thumb[0], $thumb[1]);
                }

            }

            return $size;
        }

        /**
         * @param bool $default
         * @return bool|mixed|void
         */
        function thumb_size($default = FALSE)
        {
            return $this->get_option('thumb_size_hotel', $default);
        }

        /**
         * @return array
         */
        public function get_search_fields()
        {
            $taxonomy = get_object_taxonomies('wpbooking_service', 'array');
            $list_taxonomy = array();
            if (!empty($taxonomy)) {
                foreach ($taxonomy as $k => $v) {
                    if ($k == 'wpbooking_location') continue;
                    if ($k == 'wpbooking_extra_service') continue;
                    $list_taxonomy[$k] = $v->label;
                }
            }

            // TODO: Implement get_search_fields() method.
            return array(
                array(
                    'name'    => 'field_type',
                    'label'   => __('Field Type', "wpbooking"),
                    'type'    => "dropdown",
                    'options' => array(
                        ""            => __("-- Select --", "wpbooking"),
                        "location_id" => __("Location Dropdown", "wpbooking"),
                        "check_in"    => __("Check In", "wpbooking"),
                        "check_out"   => __("Check Out", "wpbooking"),
                        "adult_child" => __("Adult And Children", "wpbooking"),
                        "taxonomy"    => __("Taxonomy", "wpbooking"),
                        "review_rate" => __("Review Rate", "wpbooking"),
                        "star_rating" => __("Star Of Property", "wpbooking"),
                        "price"       => __("Price", "wpbooking"),
                    )
                ),
                array(
                    'name'  => 'title',
                    'label' => __('Title', "wpbooking"),
                    'type'  => "text",
                    'value' => ""
                ),
                array(
                    'name'  => 'placeholder',
                    'label' => __('Placeholder', "wpbooking"),
                    'desc'  => __('Placeholder', "wpbooking"),
                    'type'  => 'text',
                ),
                array(
                    'name'    => 'taxonomy',
                    'label'   => __('- Taxonomy', "wpbooking"),
                    'type'    => "dropdown",
                    'class'   => "hide",
                    'options' => $list_taxonomy
                ),
                array(
                    'name'    => 'taxonomy_show',
                    'label'   => __('- Display Style', "wpbooking"),
                    'type'    => "dropdown",
                    'class'   => "hide",
                    'options' => array(
                        "dropdown"  => __("Dropdown", "wpbooking"),
                        "check_box" => __("Check Box", "wpbooking"),
                    )
                ),
                array(
                    'name'    => 'taxonomy_operator',
                    'label'   => __('- Operator', "wpbooking"),
                    'type'    => "dropdown",
                    'class'   => "hide",
                    'options' => array(
                        "AND" => __("And", "wpbooking"),
                        "OR"  => __("Or", "wpbooking"),
                    )
                ),
                array(
                    'name'    => 'required',
                    'label'   => __('Required', "wpbooking"),
                    'type'    => "dropdown",
                    'options' => array(
                        "no"  => __("No", "wpbooking"),
                        "yes" => __("Yes", "wpbooking"),
                    )
                ),
                array(
                    'name'  => 'in_more_filter',
                    'label' => __('In Advance Search?', "wpbooking"),
                    'type'  => "checkbox",
                ),

            );
        }

        /**
         * Hook Callback Change Base Price
         *
         * @since 1.0
         * @author
         *
         * @param $base_price
         * @param $post_id
         * @param $service_type
         * @return mixed
         */
        public function _change_base_price($base_price, $post_id, $service_type)
        {

            $base_price = WPBooking_Meta_Model::inst()->get_price_accommodation($post_id);

            return $base_price;
        }

        /**
         * Move fields in comment to top
         */
        public function _move_fields_comment_top($fields){
            $comment_field = $fields['comment'];
            unset($fields['comment']);
            $fields['comment'] = $comment_field;
            return $fields;
        }

        /**
         * Add Specific params to cart item before adding to cart
         *
         * @since 1.0
         * @author quandq
         *
         * @param $cart_item
         * @param bool|FALSE $post_id
         * @return array
         */
        function _change_cart_item_params($cart_item, $post_id = FALSE)
        {

            $calendar = WPBooking_Calendar_Model::inst();
            $cart_item = wp_parse_args($cart_item, array(
                'check_in_timestamp'  => FALSE,
                'check_out_timestamp' => FALSE,
            ));

            $wpbooking_option_number_room = $this->post('wpbooking_option_number_room');
            $extra_services = $this->post('wpbooking_extra');
            if(!empty($wpbooking_option_number_room)){
                foreach($wpbooking_option_number_room as $k=>$v){
                    if(!empty($v)){
                        $my_extra_services = get_post_meta($k,'extra_services',true);
                        $extra_service = array();
                        if(!empty($extra_services[$k])){
                            $extra_service['title'] = esc_html__('Extra Service','wpbooking');
                            foreach($extra_services[$k] as $key=>$value){
                                if(!empty($value['is_check']) and !empty($my_extra_services[$key])){
                                    $extra_service['data'][$key] = array(
                                        'title'=>$value['is_check'],
                                        'quantity'=>$value['quantity'],
                                        'price'=>$my_extra_services[$key]['money'],
                                    );
                                }
                            }
                        }
                        $number_room = $this->request('wpbooking_option_number_room');
                        if(!empty($number_room[$k])){
                            $number_room = $number_room[$k];
                        }else{
                            $number_room = 0;
                        }
                        $cart_item['rooms'][$k] = array(
                            'room_id'=>$k,
                            'number'=>$number_room,
                            'extra_fees'=>array(
                                'extra_service'=>$extra_service
                            )
                        );
                        if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {
                            $cart_item['rooms'][$k]['calendar_prices'] = $calendar->get_prices( $k , $cart_item[ 'check_in_timestamp' ] , $cart_item[ 'check_out_timestamp' ] );
                        }

                    }
                }
            }

            return $cart_item;
        }
        /**
         * Calendar Validate Before Add To Cart
         *
         * @author dungdt
         * @since 1.0
         *
         * @param $is_validated
         * @param $service_type
         * @param $post_id
         * @return mixed
         */
        function _add_to_cart_validate($is_validated, $service_type, $post_id, $cart_params)
        {


            $service = new WB_Service($post_id);

            $check_in = $this->post('wpbooking_check_in');
            $check_out = $this->post('wpbooking_check_out');
            $wpbooking_option_number_room = $this->post('wpbooking_option_number_room');
            $check_number_room = false;
            if(!empty($wpbooking_option_number_room)) {
                foreach( $wpbooking_option_number_room as $k => $v ) {
                    if(!empty( $v )) {
                        $check_number_room = true;
                    }
                }
            }
            if(empty($check_number_room)){
                wpbooking_set_message(esc_html__("Please select room","wpbooking"));
                $is_validated = FALSE;
                return $is_validated;
            }

            if(empty($check_in) and empty($check_out)){
                wpbooking_set_message(esc_html__("Please select Check-in and Check-out date","wpbooking"));
                $is_validated = FALSE;
                return $is_validated;
            }
            if(empty($check_in)){
                wpbooking_set_message(esc_html__("Please select Check-in date","wpbooking"));
                $is_validated = FALSE;
                return $is_validated;
            }
            if(empty($check_out)){
                wpbooking_set_message(esc_html__("Please select Check-out date","wpbooking"));
                $is_validated = FALSE;
                return $is_validated;
            }



            if ($check_in) {

                $check_in_timestamp = strtotime($check_in);

                if ($check_out) {
                    $check_out_timestamp = strtotime($check_out);
                } else {
                    $check_out_timestamp = $check_in_timestamp;
                }

                if($check_out_timestamp < $check_in_timestamp){
                    wpbooking_set_message(esc_html__("Ngày check out phải sau ngày check in","wpbooking"));
                    $is_validated = FALSE;
                    return $is_validated;
                }
                $res = $service->check_availability($check_in_timestamp, $check_out_timestamp);

                if (!$res['status']) {
                    $is_validated = FALSE;

                    // If there are some day not available, return the message
                    if (!empty($res['can_not_check_in'])) {
                        wpbooking_set_message(sprintf("You can not check-in at: %s", 'wpbooking'), date(get_option('date_format'), $check_in_timestamp));
                    }
                    if (!empty($res['can_not_check_out'])) {
                        wpbooking_set_message(sprintf("You can not check-out at: %s", 'wpbooking'), date(get_option('date_format'), $check_out_timestamp));
                    }

                    if (!empty($res['unavailable_dates'])) {
                        $message = esc_html__("Those dates are not available: %s", 'wpbooking');
                        $not_avai_string = FALSE;
                        foreach ($res['unavailable_dates'] as $k => $v) {
                            $not_avai_string .= date(get_option('date_format'), $v) . ', ';
                        }
                        $not_avai_string = substr($not_avai_string, 0, -2);

                        wpbooking_set_message(sprintf($message, $not_avai_string), 'error');
                    }

                }

                // Validate Minimum Stay
                if ($check_in_timestamp and $check_out_timestamp) {
                    $minimum_stay = $service->get_minimum_stay();
                    $dDiff = wpbooking_timestamp_diff_day($check_in_timestamp, $check_out_timestamp);
                    if ($dDiff < $minimum_stay) {
                        $is_validated = FALSE;
                        wpbooking_set_message(sprintf(esc_html__('Minimum stay is %s day(s)', 'wpbooking'), $minimum_stay), 'error');
                    }
                }

            }


            return $is_validated;
        }

        function wpbooking_reload_image_list_room(){
            $post_id = $this->request('wb_post_id');
            $tab = $this->request('wb_meta_section');
            $service = new WB_Service($post_id);
            if($service->get_type() == $this->type_id and $tab=='photo_tab'){
                $arg = array(
                    'post_type'      => 'wpbooking_hotel_room',
                    'posts_per_page' => '200',
                    'post_status'    => array('publish', 'draft', 'pending', 'future', 'private', 'inherit'),
                    'post_parent'    => $post_id
                );
                $list_room = array();
                global $wp_query;
                query_posts($arg);
                while(have_posts()){
                    the_post();
                    $image_id = '';
                    $gallery = get_post_meta(get_the_ID(),'gallery_room',true);
                    if(!empty($gallery)){
                        foreach($gallery as $k=>$v){
                            if(empty($image_id)){
                                $image_id = $v;
                            }
                        }
                    }
                    $list_room[get_the_ID()] = wp_get_attachment_image($image_id,array(220,120));
                }
                wp_reset_query();
            }
            echo json_encode($list_room);
            wp_die();
        }

        /**
         * @param $cart
         */
        function _add_info_item_room($cart){
            echo wpbooking_load_view('checkout/other/other-item-room',array('cart'=>$cart));
        }

        /**
         * @param $cart
         */
        function _add_info_total_item_room($cart){
            if(!empty($cart['rooms'])) {
                $number = count( $cart[ 'rooms' ] );
                if($number>1){
                    $html =  sprintf(esc_html__("%s rooms",'wpbookng'),$number);
                }else{
                    $html = sprintf(esc_html__("%s room",'wpbookng'),$number);
                }
                echo '<span class="total-title">'.$html.'</span>
                      <span class="total-amount">'.WPBooking_Currency::format_money(2000).'</span>';
            }
        }
        static function inst()
        {
            if (!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Accommodation_Service_Type::inst();
}