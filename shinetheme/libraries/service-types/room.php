<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/24/2016
 * Time: 4:23 PM
 */
if (!class_exists('WPBooking_Room_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
    class WPBooking_Room_Service_Type extends WPBooking_Abstract_Service_Type
    {
        static $_inst = FALSE;

        protected $type_id = 'room';

        function __construct()
        {
            $this->type_info = array(
                'label' => __("Room", 'wpbooking'),
                'desc'  => __('<b>Hotel : </b> Thuê phòng riêng', 'wpbooking')
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
                    'type'  => 'list-item',
                    'value' => array()
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
                    'label' => __('Booking Options', 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'id'        => 'order_form',
                    'label'     => __('Order Form', 'wpbooking'),
                    'type'      => 'post-select',
                    'post_type' => array('wpbooking_form')
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

            add_action('init', array($this, '_register_taxonomy'));


            add_filter('wpbooking_model_table_wpbooking_service_columns', array($this, '_add_meta_table_column'));


            add_filter('wpbooking_add_to_cart_validate_' . $this->type_id, array($this, '_add_to_cart_validate'), 10, 4);

            /**
             * Validate Duplicate Item in Cart
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('wpbooking_do_checkout_validate', array($this, '_validate_checkout'), 10, 2);

            /**
             * Change Cart Item Price
             *
             * @since 1.0
             * @author dungdt
             *
             */
            add_filter('wpbooking_cart_item_price_' . $this->type_id, array($this, '_change_cart_item_price'), 10, 3);

            /**
             * Change Order Item Price
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('wpbooking_order_item_total_' . $this->type_id, array($this, '_change_order_item_price'), 10, 4);

            // Add more params to cart items
            add_filter('wpbooking_cart_item_params_' . $this->type_id, array($this, '_change_cart_item_params'), 10, 2);

            add_filter('comments_open', array($this, '_comments_open'), 10, 2);
            add_action('pre_comment_on_post', array($this, '_validate_comment'));

            //wpbooking_archive_loop_image_size
            add_filter('wpbooking_archive_loop_image_size', array($this, '_apply_thumb_size'), 10, 3);
            add_filter('wpbooking_single_loop_image_size', array($this, '_apply_gallery_size'), 10, 3);

            // Archive Room Type
            add_action('wpbooking_after_service_address_rate', array($this, '_show_room_type'), 10, 3);

            //add_action('after_setup_theme',array($this,'_add_image_size'));

            /**
             * Enable Vote For Review
             */
            add_filter('wpbooking_enable_vote_for_review_' . $this->type_id, array($this, '_enable_vote_for_review'), 10, 4);


        }

        function _register_taxonomy()
        {
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
                'hierarchical'      => TRUE,
                'labels'            => $labels,
                'show_ui'           => TRUE,
                'show_admin_column' => TRUE,
                'query_var'         => TRUE,
                'rewrite'           => array('slug' => 'room-type'),
            );
            $args = apply_filters('wpbooking_register_room_type_taxonomy', $args);

            register_taxonomy('wpbooking_room_type', array('wpbooking_service'), $args);

            WPBooking_Assets::add_css("#wpbooking_room_typediv{display:none!important}");

            // Metabox
            $this->set_metabox(array(
                'general_tab'=>array(
                        'label'=>esc_html__('1. About','wpbooking'),
                        'fields'=>array(
                            array(
                                'type'  => 'open_section',
                            ),
                            array(
                                'label' => __("About Your Property", 'wpbooking'),
                                'type'  => 'title',
                                'desc'  => esc_html__('Thông tin room', 'wpbooking'),
                            ),
                            array(
                                'id'    => 'enable_property',
                                'label' => __("Enable Property", 'wpbooking'),
                                'type'  => 'on-off',
                                'std'   => 'on',
                                'desc'  => esc_html__('Listing will appear in search results.', 'wpbooking'),
                            ),
                            array(
                                'label' => __('Bedrooms', 'wpbooking'),
                                'id'    => 'bedroom',
                                'type'  => 'dropdown',
                                'value' => array(
                                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                                ),
                                'class' => 'small'
                            ),
                            array(
                                'label' => __('Bathrooms', 'wpbooking'),
                                'id'    => 'bathrooms',
                                'type'  => 'dropdown',
                                'value' => array(
                                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                                ),
                                'class' => 'small'
                            ),
                            array(
                                'label' => __('Max Guests', 'wpbooking'),
                                'id'    => 'max_guests',
                                'type'  => 'dropdown',
                                'value' => array(
                                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                                ),
                                'class' => 'small'
                            ),
                            array(
                                'label' => esc_html__('External Booking URL', 'wpbooking'),
                                'id'    => 'external_booking_url',
                                'type'  => 'text'
                            ),
                            array(
                                'type'  => 'close_section',
                            ),
                            array(
                                'type'  => 'open_section',
                            ),
                            array(
                                'label' => __("Property Location", 'wpbooking'),
                                'type'  => 'title',
                            ),
                            array(
                                'label' => __('Map Lat & Long', 'wpbooking'),
                                'id'    => 'gmap',
                                'type'  => 'gmap'
                            ),
                            array(
                                'label'           => __('Address', 'wpbooking'),
                                'id'              => 'address',
                                'type'            => 'address',
                                'container_class' => 'mb35'
                            ),
                            array(
                                'label'        => __("Rate & Availability", 'wpbooking'),
                                'type'         => 'title',
                                'help_popover' => esc_html__('eg. If your nightly rate is 110USD, and your weekly rate is 700USD, a 3 night stay will cost 330USD, a 7 night stay will cost 700USD, and a 10 night stay will cost 1000USD (700 / 7 * 10).', 'wpbooking')
                            ),
                            array(
                                'label' => __("Nightly Rate", 'wpbooking'),
                                'type'  => 'money_input',
                                'id'    => 'price',
                                'class' => 'small'
                            ),
                            array(
                                'label' => __("Weekly Rate", 'wpbooking'),
                                'type'  => 'money_input',
                                'id'    => 'weekly_rate',
                                'class' => 'small'
                            ),
                            array(
                                'label'           => __("Monthly Rate", 'wpbooking'),
                                'type'            => 'money_input',
                                'id'              => 'monthly_rate',
                                'class'           => 'small',
                                'container_class' => 'mb35'
                            ),
                            array(
                                'type'  => 'close_section',
                            ),
                            array(
                                'type'  => 'open_section',
                            ),
                            array(
                                'label' => __("Additional Guests / Taxes/ Misc", 'wpbooking'),
                                'type'  => 'title',
                            ),
                            array(
                                'label' => __('Allowed', 'wpbooking'),
                                'type'  => 'on-off',
                                'id'    => 'enable_additional_guest_tax',
                                'std'   => 'off'
                            ),
                            array(
                                'label'       => __("Rates are based on occupancy of", 'wpbooking'),
                                'type'        => 'text',
                                'id'          => 'rate_based_on',
                                'class'       => 'small',
                                'help_inline' => esc_html__('guest(s)', 'wpbooking')
                            ),
                            array(
                                'label'       => __("Each additional guest will pay", 'wpbooking'),
                                'type'        => 'money_input',
                                'id'          => 'additional_guest_money',
                                'class'       => 'small',
                                'help_inline' => esc_html__('/night', 'wpbooking')
                            ),
                            array(
                                'label' => __("Tax (%)", 'wpbooking'),
                                'type'  => 'text',
                                'id'    => 'tax',
                                'class' => 'small'
                            ),
                            array(
                                'type'  => 'close_section',
                            ),
                            array(
                                'type' => 'section_navigation',
                                'prev' => FALSE
                            ),
                        )
                    ),
                'detail_tab'=>array(
                    'label' => __('2. Details', 'wpbooking'),
                    'fields'=>array(
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("More About Your Property", 'wpbooking'),
                            'type'  => 'title',
                        ),

                        array(
                            'label' => __('Amenities', 'wpbooking'),
                            'id'    => 'wpb_taxonomy',
                            'type'  => 'taxonomies',
                        ),
                        array(
                            'label' => __('Double Bed', 'wpbooking'),
                            'id'    => 'double_bed',
                            'type'  => 'dropdown',
                            'value' => array(
                                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Single Bed', 'wpbooking'),
                            'id'    => 'single_bed',
                            'type'  => 'dropdown',
                            'value' => array(
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Sofa Bed', 'wpbooking'),
                            'id'    => 'sofa_bed',
                            'type'  => 'dropdown',
                            'value' => array(
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Property Floor', 'wpbooking'),
                            'id'    => 'property_floor',
                            'type'  => 'dropdown',
                            'value' => array(
                                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label'           => __('Property Size', 'wpbooking'),
                            'id'              => 'property_size',
                            'type'            => 'property_size',
                            'unit_id'         => 'property_unit',
                            'container_class' => 'mb35'
                        ),
                        array(
                            'type'  => 'close_section',
                        ),
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("Extra Services:", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Extra Services:", 'wpbooking'),
                            'type'  => 'extra_services',
                            'id'    => 'extra_services'
                        ),
                        array(
                            'type'  => 'close_section',
                        ),
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'policies_tab'=>array(
                    'label' => __('3. Policies', 'wpbooking'),
                    'fields'=>array(
                        array(
                            'type'  => 'open_section',
                        ),
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
                            'type'  => 'close_section',
                        ),
                        array(
                            'type'  => 'open_section',
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
                            'type'  => 'close_section',
                        ),
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("Cancellation Policies", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'type' => 'cancellation_policies_text',
                        ),
                        array(
                            'type'  => 'close_section',
                        ),
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'photo_tab'=>array(
                    'label' => __('4. Photos', 'wpbooking'),
                    'fields'=>array(
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("Pictures", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Gallery", 'wpbooking'),
                            'id'    => 'gallery',
                            'type'  => 'gallery',
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
                            'type'  => 'close_section',
                        ),
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'calendar_tab'=>array(
                    'label' => __('5. Calendar', 'wpbooking'),
                    'fields'=>array(
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'type'  => 'title',
                            'label' => esc_html__('Availability Template', 'wpbooking')
                        ),
                        array(
                            'id'   => 'calendar',
                            'type' => 'calendar'
                        ),
                        array(
                            'type'  => 'close_section',
                        ),

                    )
                )
            ));
        }

        function _show_room_type($post_id, $service_type, $service_object)
        {
            if ($this->type_id == $service_type) {
                $terms = wp_get_post_terms($post_id, 'wpbooking_room_type');
                if (!empty($terms) and !is_wp_error($terms)) {
                    $output[] = '<div class="wpbooking-room-type">';
                    foreach ($terms as $term) {
                        $output[] = sprintf('<a href="%s">%s</a>', get_term_link($term, 'wpbooking_room_type'), $term->name);
                    }
                    $output[] = '</div>';

                    $output = apply_filters('wpbooking_room_show_room_type', $output);
                    echo implode(' ', $output);
                }
            }
        }

        function _add_image_size()
        {
            $thumb = $this->thumb_size('150,150,off');
            $thumb = explode(',', $thumb);
            if (count($thumb) == 3) {
                if ($thumb[2] == 'off') $thumb[2] = FALSE;

                add_image_size('wpbooking_room_thumb_size', $thumb[0], $thumb[1], $thumb[2] = FALSE);
            }

            $thumb = $this->gallery_size('800,600,off');
            $thumb = explode(',', $thumb);
            if (count($thumb) == 3) {
                if ($thumb[2] == 'off') $thumb[2] = FALSE;
                add_image_size('wpbooking_room_gallery_size', $thumb[0], $thumb[1]);
            }

        }

        /**
         * Add some extra columns for room
         *
         * @param $columns
         * @return array
         * @author dungdt
         * @since 1.0
         */
        function _add_meta_table_column($columns)
        {
            $columns['bedroom'] = array('type' => 'FLOAT');
            $columns['bathrooms'] = array('type' => 'FLOAT');

            $columns['double_bed'] = array('type' => 'INT');
            $columns['single_bed'] = array('type' => 'INT');
            $columns['sofa_bed'] = array('type' => 'INT');
            $columns['property_floor'] = array('type' => 'INT');
            $columns['property_size'] = array('type' => 'FLOAT');

//			$columns['require_customer_confirm'] = array('type' => 'VARCHAR', 'length' => '10');
//			$columns['require_partner_confirm'] = array('type' => 'VARCHAR', 'length' => '10');

            return $columns;
        }

        /**
         * @author dungdt
         * @since 1.0
         */
        function _add_metabox($fields)
        {
            $new_fields = array(

                array(
                    'label' => __('Space of Room', 'wpbooking'),
                    'type'  => 'accordion-start'
                ),
                array(
                    'label' => __('Bedrooms', 'wpbooking'),
                    'id'    => 'bedroom',
                    'type'  => 'number',
                    'width' => 'two'
                ),
                array(
                    'label' => __('Bathrooms', 'wpbooking'),
                    'id'    => 'bathroom',
                    'type'  => 'number',
                    'width' => 'two'
                ),
                array(
                    'label' => __('Beds', 'wpbooking'),
                    'id'    => 'bed',
                    'type'  => 'number',
                    'width' => 'two'
                ),
                array(
                    'label' => __('Check-in Time', 'wpbooking'),
                    'id'    => 'check_in_time',
                    'type'  => 'text',
                    'class' => 'time-picker',
                    'width' => 'two'
                ),
                array(
                    'label' => __('Check-out Time', 'wpbooking'),
                    'id'    => 'check_out_time',
                    'type'  => 'text',
                    'class' => 'time-picker',
                    'width' => 'two'
                ),

                array(
                    'label' => __('No. Adult', 'wpbooking'),
                    'id'    => 'number_adult',
                    'type'  => 'number',
                    'width' => 'two'
                ),
                array(
                    'label' => __('No. Children', 'wpbooking'),
                    'id'    => 'number_children',
                    'type'  => 'number',
                    'width' => 'two'
                ),
                array(
                    'type' => 'accordion-end'
                ),
            );
            $fields = array_merge($fields, $new_fields);

            return $fields;
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

            $check_in = WPBooking_Input::post('check_in');
            $check_out = WPBooking_Input::post('check_out');

            if ($check_in) {
                $check_in_timestamp = strtotime($check_in);

                if ($check_out) {
                    $check_out_timestamp = strtotime($check_out);
                } else {
                    $check_out_timestamp = $check_in_timestamp;
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

            // Max Guest
            $guest = WPBooking_Input::post('guest');
            $max_guests = $service->get_max_guests();
            if ($max_guests and $guest > $max_guests) {
                $is_validated = FALSE;
                wpbooking_set_message(sprintf(esc_html__('Maximum Guests is %s', 'wpbooking'), $max_guests), 'error');
            }

            if (!$this->validate_cart_duplicate($cart_params)) {
                $is_validated = FALSE;
                wpbooking_set_message(esc_html__('This item is already exist in your cart', 'wpbooking'), 'error');

            }

            return $is_validated;
        }

        /**
         * Validate Cart before checkout
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $is_validated
         * @param $cart
         * @return mixed
         */
        function _validate_checkout($is_validated, $cart = array())
        {
            if ($is_validated) {
                if (!empty($cart)) {
                    foreach ($cart as $key => $cart_item) {
                        if (!$this->validate_cart_duplicate($cart_item, $key)) {
                            wpbooking_set_message(sprintf(esc_html__('Item: %s is duplicate. Please check your cart again', 'wpbooking'), '<i>' . get_the_title($cart_item['post_id']) . '</i>'), 'error');

                            return false;
                        } else {

                            // Validate Availability last time
                            $cart_item = wp_parse_args($cart_item, array(
                                'check_out_timestamp' => '',
                                'check_in_timestamp'  => ''
                            ));
                            if ($cart_item['check_out_timestamp']) {
                                $cart_item['check_out_timestamp'] = $cart_item['check_in_timestamp'];
                            }
                            $check_in_timestamp = $cart_item['check_in_timestamp'];
                            $check_out_timestamp = $cart_item['check_out_timestamp'];

                            $service = new WB_Service($cart_item['post_id']);
                            $res = $service->check_availability($check_in_timestamp, $check_out_timestamp);
                            if (!$res['status']) {

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

                                return false;
                            }
                        }
                    }
                }
            }

            return $is_validated;
        }

        /**
         * Validate Duplicate Cart Item -  Checkin - Checkout Validate
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @param bool $cart_item_key
         * @return bool
         */
        function validate_cart_duplicate($cart_item, $cart_item_key = false)
        {
            $cart_item = wp_parse_args($cart_item,
                array(
                    'check_in_timestamp'  => 0,
                    'check_out_timestamp' => 0,
                ));
            if (!$cart_item['check_in_timestamp'] or !$cart_item['check_out_timestamp']) return true; // If, somehow, its dose not contain check in and checkout time -> return true

            $carts = WPBooking_Order::inst()->get_cart();
            if (!empty($carts)) {

                foreach ($carts as $key => $cart) {
                    $cart = wp_parse_args($cart,
                        array(
                            'check_in_timestamp'  => 0,
                            'check_out_timestamp' => 0,
                        ));



                    if ($cart_item_key and $cart_item_key == $key) continue;


                    if ($cart['check_in_timestamp'] and $cart['check_out_timestamp']) {
                        if ($cart['post_id'] == $cart_item['post_id'] and
                            (
                                ($cart['check_in_timestamp'] >= $cart_item['check_in_timestamp'] and $cart['check_in_timestamp'] <= $cart_item['check_out_timestamp']) or
                                ($cart['check_in_timestamp'] < $cart_item['check_in_timestamp'] and $cart_item['check_in_timestamp'] <= $cart['check_out_timestamp'])
                            )
                        ) {

                            return false;
                        }
                    }

                }
            }

            return true;
        }

        /**
         * Add Specific params to cart item before adding to cart
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @param bool|FALSE $post_id
         * @return array
         */
        function _change_cart_item_params($cart_item, $post_id = FALSE)
        {
            $service = new WB_Service($cart_item['post_id']);
            $calendar = WPBooking_Calendar_Model::inst();

            $cart_item = wp_parse_args($cart_item, array(
                'check_in_timestamp'  => FALSE,
                'check_out_timestamp' => FALSE,
            ));

            $cart_item['guest'] = WPBooking_Input::post('guest');
            $cart_item['monthly_rate'] = $service->get_meta('monthly_rate');
            $cart_item['weekly_rate'] = $service->get_meta('weekly_rate');
            $cart_item['enable_additional_guest_tax'] = $service->get_meta('enable_additional_guest_tax');
            $cart_item['rate_based_on'] = $service->get_meta('rate_based_on');
            $cart_item['additional_guest_money'] = $service->get_meta('additional_guest_money');
            $cart_item['tax'] = $service->get_meta('tax');
            $cart_item['deposit_type'] = $service->get_meta('deposit_type');
            $cart_item['deposit_amount'] = $service->get_meta('deposit_amount');
            $cart_item['default_extra_services'] = $service->get_extra_services();

            if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {
                $cart_item['calendar_prices'] = $calendar->get_prices($cart_item['post_id'], $cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
            }

            return $cart_item;
        }

        /**
         * @param $price
         * @param $cart_item
         * @param array $args
         * @return float|int
         */
        function change_price($price, $cart_item, $args = array())
        {
            $args = wp_parse_args($args, array(
                'without_deposit'        => FALSE,
                'without_tax'            => false,
                'without_extra_price'    => false,
                'without_addition_price' => false,
                'without_discount'       => false
            ));
            $cart_item = wp_parse_args($cart_item, array(
                'extra_services'              => array(),
                'default_extra_services'      => array(),
                'check_in_timestamp'          => FALSE,
                'check_out_timestamp'         => FALSE,
                'post_id'                     => FALSE,
                'guest'                       => FALSE,
                'monthly_rate'                => FALSE,
                'weekly_rate'                 => FALSE,
                'calendar_prices'             => array(),
                'enable_additional_guest_tax' => '',
                'rate_based_on'               => FALSE,
                'additional_guest_money'      => FALSE,
                'tax'                         => FALSE,
                'coupon_code'                 => '',
                'coupon_data'                 => array(),
            ));

            $days = 0; //Date Diff Checkin and Checkout in days

            // try to get from order form
            if (!$cart_item['guest'] and !empty($cart_item['order_form']['guest']['value'])) {
                $cart_item['guest'] = $cart_item['order_form']['guest']['value'];
            }

            // Calendar Price
            if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {

                $calendar_prices = $cart_item['calendar_prices'];

                $price = $cart_item['base_price'];

                $days = wpbooking_timestamp_diff_day($cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
                if (!$days) $days = 1;

                // Monthly Rate
                if ($days >= 30 and $monthly = $cart_item['monthly_rate']) {

                    $price = ($monthly * $days) / 30;

                } elseif ($days >= 7 and $weekly = $cart_item['weekly_rate']) {

                    // Weekly Rate
                    $price = ($weekly * $days) / 7;

                } else {
                    // If there is no price in the calendar, we use base price
                    if (empty($calendar_prices)) {
                        $price *= $days;

                    } else {

                        $tmp_calendar = array();
                        foreach ($calendar_prices as $key => $value) {
                            $tmp_calendar[$value['start']] = $value;
                        }

                        // Use Calendar Data
                        $price = 0;
                        $check_in_temp = $cart_item['check_in_timestamp'];
                        while ($check_in_temp < $cart_item['check_out_timestamp']) {

                            // If in calendar
                            if (array_key_exists($check_in_temp, $tmp_calendar)) {
                                $price += $tmp_calendar[$check_in_temp]['price'];
                            } else {
                                // Not in calendar data, get from base price
                                $price += $cart_item['base_price'];
                            }

                            $check_in_temp = strtotime('+1 day', $check_in_temp);
                        }
                    }
                }

            }

            /**
             * Calculate Extra Services
             */
            if (!$args['without_extra_price']) {
                $extra_service_price = WB_Service_Helper::calculate_extra_price($cart_item, $cart_item['default_extra_services']);
                if ($extra_service_price) $price += $extra_service_price;
            }

            /**
             * Calculate Additional Guest and Tax
             */
            if ($cart_item['enable_additional_guest_tax'] == 'on') {

                //Additional Guest
                if ($cart_item['guest'] and $cart_item['rate_based_on'] and $addition_money = $cart_item['additional_guest_money'] and $days and !$args['without_addition_price']) {
                    $addition = ($cart_item['guest'] - $cart_item['rate_based_on']) * $addition_money * $days;

                    if ($addition > 0) $price += $addition;
                }
                // Tax
                if ($tax = $cart_item['tax'] and !$args['without_tax'])
                    $price += $price * ($tax / 100);
            }

            /**
             * Discount Calculate
             */
            if (!$args['without_discount'] and !empty($cart_item['coupon_data']) and !empty($cart_item['coupon_code'])) {
                $discount = WB_Service_Helper::calculate_discount($cart_item, $price);
                $price -= $discount;
            }

            /**
             * Calculate Deposit
             */
            if (!empty($cart_item['deposit_amount']) and !$args['without_deposit']) {

                $price = WB_Service_Helper::calculate_deposit($cart_item, $price);
            }

            return $price;
        }

        /**
         * Change Cart Item Price Hook Callback - Calculate Price Right-in-time
         *
         * @author dungdt
         * @since 1.0
         *
         * @param $price
         * @param $cart_item
         * @param $args
         * @return float
         */
        function _change_cart_item_price($price, $cart_item, $args = array())
        {

            $cart_item = wp_parse_args($cart_item, array(
                'extra_services'              => array(),
                'check_in_timestamp'          => FALSE,
                'check_out_timestamp'         => FALSE,
                'post_id'                     => FALSE,
                'guest'                       => FALSE,
                'monthly_rate'                => FALSE,
                'weekly_rate'                 => FALSE,
                'calendar_prices'             => array(),
                'enable_additional_guest_tax' => '',
                'rate_based_on'               => FALSE,
                'additional_guest_money'      => FALSE,
                'tax'                         => FALSE
            ));

            if ($coupon_code = WPBooking_Order::inst()->get_cart_coupon()) {
                $coupon = new WB_Coupon($coupon_code);
                $cart_item['coupon_code'] = $coupon_code;
                $cart_item['coupon_data'] = $coupon->get_full_data();
            }

            return $this->change_price($price, $cart_item, $args);
        }


        function _change_order_item_price($price, $order_item, $args = array())
        {

            $args = wp_parse_args($args, array(
                'without_deposit'        => FALSE,
                'without_tax'            => false,
                'without_extra_price'    => false,
                'without_addition_price' => false,
                'without_discount'       => false
            ));

            $order_item = wp_parse_args($order_item, array(
                'raw_data' => '',
            ));
            if ($order_item['raw_data'] and $cart_item = unserialize($order_item['raw_data'])) {

                $cart_item = wp_parse_args($cart_item, array(
                    'extra_services'              => array(),
                    'check_in_timestamp'          => FALSE,
                    'check_out_timestamp'         => FALSE,
                    'post_id'                     => FALSE,
                    'guest'                       => FALSE,
                    'monthly_rate'                => FALSE,
                    'weekly_rate'                 => FALSE,
                    'calendar_prices'             => array(),
                    'enable_additional_guest_tax' => '',
                    'rate_based_on'               => FALSE,
                    'additional_guest_money'      => FALSE,
                    'tax'                         => FALSE,
                    'coupon_code'                 => '',
                    'coupon_data'                 => ''
                ));
                if ($coupon_code = get_post_meta($order_item['order_id'],'coupon_code',true)) {

                    $coupon_data = get_post_meta($order_item['order_id'],'coupon_data',true);

                    if(empty($cart_item['coupon_code'])) $cart_item['coupon_code']=  $coupon_code;
                    if(empty($cart_item['coupon_data'])) $cart_item['coupon_data']=  $coupon_data;
                }

                $price = $this->change_price($price, $cart_item, $args);
            }

            return $price;

        }


        /**
         * Show Cart Item Information Based on Service Type ID
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $cart_item
         * @param $options array
         */
        function _show_cart_item_information($cart_item, $options = array())
        {
            $options = wp_parse_args($options, array(
                'for_email'    => FALSE,
                'current_page' => false
            ));
            $cart_item = wp_parse_args($cart_item, array(
                'check_in_timestamp'          => FALSE,
                'check_out_timestamp'         => FALSE,
                'order_form'                  => array(),
                'post_id'                     => FALSE,
                'extra_services'              => array(),
                'guest'                       => 0,
                'enable_additional_guest_tax' => FALSE,
                'rate_based_on'               => FALSE,
                'additional_guest_money'      => FALSE,
                'tax'                         => FALSE
            ));
            $days = FALSE;

            if (!$cart_item['guest'] and !empty($cart_item['order_form']['guest']['value'])) {
                $cart_item['guest'] = $cart_item['order_form']['guest']['value'];
            }

            if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {
                $days = wpbooking_timestamp_diff_day($cart_item['check_in_timestamp'], $cart_item['check_out_timestamp']);
            }

            $terms = wp_get_post_terms($cart_item['post_id'], 'wpbooking_room_type');
            if (!empty($terms) and !is_wp_error($terms) and $options['current_page'] != 'cart') {
                /**
                 * Hide Room Type from 09-sep-2016
                 *
                 * @reason - Apply New Design from QuyLe
                 * @author dungdt
                 * @since 1.0
                 */
//                $output[] = '<div class="wpbooking-room-type">';
//                $key = 0;
//                foreach ($terms as $term) {
//                    $html = sprintf('<a href="%s">%s</a>', get_term_link($term, 'wpbooking_room_type'), $term->name);
//                    if ($key < count($term) - 1) {
//                        $html .= ',';
//                    }
//                    $output[] = $html;
//                    $key++;
//                }
//
//                $output[] = '</div>';
//
//                $output = apply_filters('wpbooking_room_show_room_type', $output);
//                echo implode(' ', $output);
            }
            $extra_html = array();


            /**
             * Calculate Base Price
             */
            $extra_html[] = sprintf("<li class='field-item %s'>
												<span class='field-title'>%s:</span>
												<span class='field-value'>%s</span>
											</li>",
                'tax',
                esc_html__('Price', 'wpbooking'),
                WPBooking_Order::inst()->get_cart_item_total_html($cart_item, array(
                    'without_deposit' => TRUE,
                    'without_discount'=>true,
                    'without_extra_price'=>true,
                    'without_tax'=>true
                ))
            );

            /**
             * Calculate Extra Price
             */
            if(!empty($cart_item['default_extra_services']) and $extra_price=WB_Service_Helper::calculate_extra_price($cart_item,$cart_item['default_extra_services'])){
                $extra_html[] = sprintf("<li class='field-item %s'>
												<span class='field-title'>%s:</span>
												<span class='field-value'>%s</span>
											</li>",
                    'extra_price',
                    esc_html__('Extra Price', 'wpbooking'),
                    WPBooking_Currency::format_money($extra_price)
                );
            }
           

            if ($cart_item['enable_additional_guest_tax'] == 'on') {

                // Addition Guest
                if ($cart_item['guest'] and $addition_money = $cart_item['additional_guest_money'] and $cart_item['rate_based_on'] and $days) {
                    if ($cart_item['guest'] > $cart_item['rate_based_on']) {
                        $extra_html[] = sprintf("<li class='field-item %s'>
												<span class='field-title'>%s:</span>
												<span class='field-value'>%s</span>
											</li>",
                            'additional_guest_money',
                            esc_html__('Additional Guests', 'wpbooking'),
                            WPBooking_Currency::format_money(($cart_item['guest'] - $cart_item['rate_based_on']) * $addition_money * $days)
                        );
                    }
                }
                // Tax
                if ($tax = $cart_item['tax']) {
                    $extra_html[] = sprintf("<li class='field-item %s'>
												<span class='field-title'>%s:</span>
												<span class='field-value'>%s</span>
											</li>",
                        'tax',
                        esc_html__('Tax', 'wpbooking'),
                        $tax . '%'
                    );
                }

            }


            /**
             * Calculate Deposit
             */
            if (!empty($cart_item['deposit_amount']) and !empty($cart_item['deposit_type'])) {


                switch ($cart_item['deposit_type']) {
                    case "percent":
                        $extra_html[] = sprintf("<li class='field-item %s'>
												<span class='field-title'>%s:</span>
												<span class='field-value'>%s</span>
											</li>",
                            'deposit_amount',
                            esc_html__('Deposit', 'wpbooking'),
                            $cart_item['deposit_amount'] . '%'
                        );
                        break;
                    case "value":
                    default:
                        $extra_html[] = sprintf("<li class='field-item %s'>
													<span class='field-title'>%s:</span>
													<span class='field-value'>%s</span>
												</li>",
                            'deposit_amount',
                            esc_html__('Deposit', 'wpbooking'),
                            WPBooking_Currency::format_money($cart_item['deposit_amount'])
                        );
                        break;

                }
            }

            /**
             * Calculate Discount
             */
            if ($discount=WB_Service_Helper::calculate_discount($cart_item,WPBooking_Order::inst()->get_cart_item_total($cart_item,array('without_deposit'=>true,'without_discount'=>true)))) {

                $extra_html[] = sprintf("<li class='field-item %s'>
                                        <span class='field-title'>%s:</span>
                                        <span class='field-value'>%s</span>
                                    </li>",
                    'discount',
                    esc_html__('Discount', 'wpbooking'),
                    WPBooking_Currency::format_money($discount)
                );
            }


            // Show Order Form Field
            $order_form = $cart_item['order_form'];
            if ((!empty($order_form) and is_array($order_form)) or !empty($extra_price_html)) {
                echo '<div class="cart-item-order-form-fields-wrap">';
                echo '<span class="booking-detail-label">' . esc_html__('Booking Details:', 'wpbooking') . '</span>';
                echo "<ul class='cart-item-order-form-fields'>";
                foreach ($order_form as $key => $value) {

                    $value = wp_parse_args($value, array(
                        'data'       => '',
                        'field_type' => ''
                    ));

                    $value_html = WPBooking_Admin_Form_Build::inst()->get_form_field_data($value, $cart_item['post_id']);

                    if ($value_html) {
                        printf("<li class='field-item %s'>
								<span class='field-title'>%s:</span>
								<span class='field-value'>%s</span>
							</li>", $key, $value['title'], $value_html);
                    }

                    do_action('wpbooking_form_field_to_html', $value);
                    do_action('wpbooking_form_field_to_html_' . $value['field_type'], $value);
                }

                if ($extra_html) {
                    echo implode("\r\n", $extra_html);
                }
                echo "</ul>";
                if (!$options['for_email']) {
                    echo '<span class="show-more-less"><span class="more">' . esc_html__('More', 'wpbooking') . ' <i class="fa fa-angle-double-down"></i></span><span class="less">' . esc_html__('Less', 'wpbooking') . ' <i class="fa fa-angle-double-up"></i></span></span>';
                }
                echo "</div>";
            }

        }

        /**
         * Show Order Item Information Based on Service Type ID
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_item
         * @param $options array
         */
        function _show_order_item_information($order_item, $options = array())
        {
            $options = wp_parse_args($options, array(
                'for_email' => FALSE
            ));
            $order_item = wp_parse_args($order_item, array(
                'check_in_timestamp'          => '',
                'check_out_timestamp'         => '',
                'order_form'                  => '',
                'payment_status'              => '',
                'status'                      => '',
                'post_id'                     => FALSE,
                'enable_additional_guest_tax' => FALSE,
                'rate_based_on'               => FALSE,
                'additional_guest_money'      => FALSE,
                'tax'                         => FALSE,
                'raw_data'                    => FALSE
            ));

            if ($order_item['raw_data'] and $cart_item = unserialize($order_item['raw_data'])) {
                $this->_show_cart_item_information($cart_item, $options);

            }

        }

        function _add_page_archive_search($args)
        {
            $id_page = $this->get_option('archive_page');
            $args = array($id_page => $this->type_id);

            return $args;
        }

        function _add_default_query_hook()
        {
            global $wpdb;
            $table_prefix = WPBooking_Service_Model::inst()->get_table_name();
            $injection = WPBooking_Query_Inject::inst();
            $tax_query = $injection->get_arg('tax_query');
            $rate_calculate = FALSE;

            // Guest
            if ($guest = WPBooking_Input::get('guest')) {
                $injection->where($table_prefix . '.max_guests >=', $guest);
            }


            // Taxonomy
            $tax = WPBooking_Input::request('taxonomy');
            if (!empty($tax) and is_array($tax)) {
                $taxonomy_operator = WPBooking_Input::request('taxonomy_operator');
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

            // Posts Per page
            if ($posts_per_page = $this->posts_per_page()) {
                $injection->add_arg('posts_per_page', $posts_per_page);
            }

            // Order By
            if ($sortby = WPBooking_Input::request('wb_sort_by')) {
                var_dump($sortby);
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


            // Beds
            if ($double_bed = WPBooking_Input::get('double_bed')) {
                $injection->where('double_bed>=', $double_bed);
            }
            if ($single_bed = WPBooking_Input::get('single_bed')) {
                $injection->where('single_bed>=', $single_bed);
            }
            if ($sofa_bed = WPBooking_Input::get('sofa_bed')) {
                $injection->where('sofa_bed>=', $sofa_bed);
            }

            // property_floor
            if ($property_floor = WPBooking_Input::get('property_floor')) {
                $injection->where('property_floor>=', $property_floor);
            }

            // Property Size
            if ($property_size = WPBooking_Input::get('property_size')) {
                $injection->where('property_size>=', $property_size);
            }

            // Bedrooms
            if ($bedrooms = WPBooking_Input::get('bedroom')) {
                $injection->where('bedroom>=', $bedrooms);
            }
            // Bathrooms
            if ($bathrooms = WPBooking_Input::get('bathroom')) {
                $injection->where('bathrooms>=', $bathrooms);
            }

            // Review
            if ($review_rate = WPBooking_Input::request('review_rate') and is_array(explode(',', $review_rate))) {

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

            parent::_add_default_query_hook();
        }


        /**
         * Validate Before Post Comment
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $comment_post_ID
         */
        function _validate_comment($comment_post_ID)
        {
            $service_type = get_post_meta($comment_post_ID, 'service_type', TRUE);

            if ($service_type == $this->type_id) {
                // Validate start
                $is_validated = TRUE;

                if (!$this->get_option('enable_review')) {
                    //wpbooking_set_message(esc_html__('This service is not allowed to write review', 'wpbooking'));
                    $is_validated = FALSE;
                }


                // room_maximum_review
                if ($max = $this->room_maximum_review() and is_user_logged_in()) {
                    $comment = WPBooking_Comment_Model::inst();
                    $count = $comment->select('count(comment_ID) as total')
                        ->where(array('comment_post_ID' => $comment_post_ID, 'comment_parent' => 0, 'user_id' => get_current_user_id()))
                        ->get()->row();

                    if (!empty($count['total']) and $count['total'] >= $max) {

                        //wpbooking_set_message(sprintf(esc_html__('Maximum number of review you can post is %d', 'wpbooking'), $max));
                        $is_validated = FALSE;
                    }
                }

                // review_without_booking
                if (!$this->review_without_booking() and is_user_logged_in()) {
                    $order_item = WPBooking_Order_Model::inst();
                    $count = $order_item->select('count(id) as total')->where(array('post_id' => $comment_post_ID, 'customer_id' => get_current_user_id()))->get()->row();
                    if (empty($count['total']) or $count['total'] < 1) {

                        //wpbooking_set_message(esc_html__('This Room required booking before writing review', 'wpbooking'));
                        $is_validated = FALSE;
                    }
                }

                $is_validated = apply_filters('wpbooking_validate_before_post_comment_service_type_room', $is_validated, $comment_post_ID);

                if (!$is_validated) {
                    wp_redirect(get_permalink($comment_post_ID));
                    die;
                }
            }
        }


        /**
         * Hook Filter To Show Review Form for Room
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $open
         * @param $post_id
         * @return bool|mixed|string|void
         */
        function _comments_open($open, $post_id)
        {
            $service_type = get_post_meta($post_id, 'service_type', TRUE);
            if ($post_id and $service_type == $this->type_id) {
                $open = $this->get_option('enable_review');

                if (is_user_logged_in()) {
                    // room_maximum_review
                    if ($max = $this->room_maximum_review()) {
                        $comment = WPBooking_Comment_Model::inst();
                        $count = $comment->select('count(comment_ID) as total')
                            ->where(array('comment_post_ID' => $post_id, 'comment_parent' => 0, 'user_id' => get_current_user_id()))
                            ->get()->row();
                        $count = !empty($count['total']) ? $count['total'] : 0;

                        if ($count >= $max) {
                            wpbooking_set_message(sprintf(esc_html__('Maximum number of review you can post is %d', 'wpbooking'), $max));
                            $open = FALSE;
                        }
                    }

                    // review_without_booking
                    if (!$this->review_without_booking()) {
                        $order_item = WPBooking_Order_Model::inst();
                        $count = $order_item->select('count(id) as total')->where(array('post_id' => $post_id, 'customer_id' => get_current_user_id()))->get()->row();
                        if (empty($count['total']) or $count['total'] < 1) {

                            wpbooking_set_message(esc_html__('This Room required booking before writing review', 'wpbooking'));
                            $open = FALSE;
                        }
                    }

                    // Review in their own posts
                    if (!$this->get_option('allowed_review_on_own_listing')) {
                        $author_id = get_post_field('post_author', $post_id);
                        if ($author_id == get_current_user_id()) {
                            $open = FALSE;
                        }
                    }
                }


            }

            return $open;
        }


        /**
         * Enable vote for review
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $enable bool
         * @param $post_id int
         * @param @service_type string
         * @return bool
         */
        function _enable_vote_for_review($enable, $post_id, $service_type, $review_id)
        {
            //_allowed_vote_for_own_review
            $enable = $this->get_option('show_rate_review_button', FALSE);
            if (is_user_logged_in()) {

                $enable = TRUE;
                $comment = get_comment($review_id);

                if (!$this->get_option('allowed_vote_for_own_review') and get_current_user_id() == $comment->user_id) {
                    $enable = FALSE;
                }
            }

            return $enable;
        }

        function required_partner_approved_review()
        {
            return $this->get_option('required_partner_approved_review', FALSE);
        }

        function room_maximum_review()
        {
            return $this->get_option('maximum_review');
        }

        function review_without_booking()
        {
            return $this->get_option('review_without_booking');
        }

        function posts_per_page()
        {
            return $this->get_option('posts_per_page');
        }


        function thumb_size($default = FALSE)
        {
            return $this->get_option('thumb_size', $default);
        }

        function gallery_size($default = FALSE)
        {
            return $this->get_option('gallery_size', $default);
        }

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

        function _apply_gallery_size($size, $service_type, $post_id)
        {
            if ($service_type == $this->type_id) {

                $thumb = $this->gallery_size('800,600,off');
                $thumb = explode(',', $thumb);
                if (count($thumb) == 3) {
                    if ($thumb[2] == 'off') $thumb[2] = FALSE;
                    $size = array($thumb[0], $thumb[1]);
                }
            }

            return $size;
        }

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
            // TODO: Implement get_service_fields() method.
            return array(
                array(
                    'name'    => 'field_type',
                    'label'   => __('Field Type', "wpbooking"),
                    'type'    => "dropdown",
                    'options' => array(
                        ""                    => __("-- Select --", "wpbooking"),
                        "location_id"         => __("Location Dropdown", "wpbooking"),
                        "check_in"            => __("Check In", "wpbooking"),
                        "check_out"           => __("Check Out", "wpbooking"),
                        "taxonomy"            => __("Taxonomy", "wpbooking"),
                        "review_rate"         => __("Review Rate", "wpbooking"),
                        "price"               => __("Price", "wpbooking"),
                        "guest"               => __("Guest", "wpbooking"),
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


        static function inst()
        {
            if (!self::$_inst) {
                self::$_inst = new self();
            }

            return self::$_inst;
        }
    }

    WPBooking_Room_Service_Type::inst();
}

