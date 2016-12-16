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
                'desc'  => esc_html__('You can post any kind of property like hotel, hostel, room like airbnb... anything called accommodation', 'wpbooking')
            );

            $this->settings = array(

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
             * @author quandq
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
             * Validate add to cart
             *
             * @since 1.0
             * @author quandq
             *
             */
            add_filter('wpbooking_add_to_cart_validate_' . $this->type_id, array($this, '_add_to_cart_validate'), 10, 4);

            /**
             * Validate Do Checkout
             *
             * @since 1.0
             * @author quandq
             */
            add_filter('wpbooking_do_checkout_validate_'. $this->type_id, array($this, '_validate_checkout'), 10, 2);

            /**
             * Validate add to cart
             *
             * @since 1.0
             * @author quandq
             *
             */
            add_filter('wpbooking_get_cart_total_' . $this->type_id, array($this, '_get_cart_total_price_hotel_room'), 10, 4);


            /**
             * Add info room checkout
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wpbooking_review_checkout_item_information_'.$this->type_id, array($this, '_add_info_checkout_item_room'),10,2);
            add_action('wpbooking_check_total_item_information_'.$this->type_id, array($this, '_add_info_total_item_room'),10,2);
            add_action('wpbooking_save_order_'.$this->type_id, array($this, '_save_order_hotel_room'),10,2);
            /**
             * Change Tax Room CheckOut
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wpbooking_get_cart_tax_price_'.$this->type_id, array($this, '_change_tax_room_checkout'),10,2);

            /**
             * Add info Room Order Detail
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wpbooking_order_detail_item_information_'.$this->type_id, array($this, '_add_info_order_detail_item_room'),10,2);
            add_action('wpbooking_order_detail_total_item_information_'.$this->type_id, array($this, '_add_info_order_total_item_room'),10,2);

            add_action('wpbooking_email_detail_item_information_'.$this->type_id, array($this, '_add_information_email_detail_item'),10,2);

            /**
             * Delete Item Room
             *
             * @since 1.0
             * @author quandq
             */
            add_action('template_redirect', array($this, '_delete_cart_item_hotel_room'));

            /**
             * Show List Room in single Hotel
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_after_service_amenity',array($this,'_show_list_room'));

            /**
             * Show Start,End Information
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_review_after_address_'.$this->type_id,array($this,'_show_start_end_information'));


            /**
             * Show Order Info after Address
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_order_detail_after_address_'.$this->type_id,array($this,'_show_order_info_after_address'));

            /**
             * Show More Order Info for Email
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_email_order_after_address_'.$this->type_id,array($this,'_show_email_order_info_after_address'));

            add_action('wpbooking_order_history_after_service_name_'.$this->type_id,array($this,'_show_order_info_listing'));

        }

        public function _show_order_info_listing($order_data)
        {
            ?>

            <div class="item-form-to">
                <span><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date(get_option('date_format'),$order_data['check_in_timestamp']) ?> &nbsp
                <span><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date(get_option('date_format'),$order_data['check_out_timestamp']) ?> &nbsp
                <br>
                <?php
                $diff=$order_data['check_out_timestamp'] - $order_data['check_in_timestamp'];
                $diff = $diff / (60 * 60 * 24);
                if($diff > 1){
                    echo sprintf(esc_html__('(%s nights)','wpbooking'),$diff);
                }else{
                    echo sprintf(esc_html__('(%s night)','wpbooking'),$diff);
                }
                ?>

            </div>
            <?php
        }

        /**
         * Show Start,End Information
         *
         * @since 1.0
         * @author dungdt
         *
         * @param array $cart
         */
        public function _show_start_end_information($cart){
            $post_id=$cart['post_id'];
            ?>

            <div class="review-order-item-form-to">
                <span><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date_i18n(get_option('date_format'),$cart['check_in_timestamp']) ?> &nbsp
                <span><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date_i18n(get_option('date_format'),$cart['check_out_timestamp']) ?> &nbsp
                <?php
                $diff=$cart['check_out_timestamp'] - $cart['check_in_timestamp'];
                $diff = $diff / (60 * 60 * 24);
                if($diff > 1){
                    echo sprintf(esc_html__('(%s nights)','wpbooking'),$diff);
                }else{
                    echo sprintf(esc_html__('(%s night)','wpbooking'),$diff);
                }

                $url_change_date = add_query_arg(array(
                    'checkin_d'  => date("d",$cart['check_in_timestamp']),
                    'checkin_m'  => date("m",$cart['check_in_timestamp']),
                    'checkin_y'  => date("Y",$cart['check_in_timestamp']),

                    'checkout_d' => date("d",$cart['check_out_timestamp']),
                    'checkout_m' => date("m",$cart['check_out_timestamp']),
                    'checkout_y' => date("Y",$cart['check_out_timestamp']),
                ), get_permalink($post_id));
                ?>
                <small><a href="<?php echo esc_url($url_change_date) ?>"><?php esc_html_e("Change Date","wpbooking") ?></a></small>
            </div>
            <?php
        }

        /**
         * Show Order Info after Address
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_data
         */
        public function _show_order_info_after_address($order_data)
        {
            if(!empty($order_data['raw_data'])){
                $raw_data=json_decode($order_data['raw_data'],true);
                if($raw_data){
                    $this->_show_start_end_information($raw_data);
                }
            }
        }

        /**
         * Show More Order Info for Email
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_data
         */
        public function _show_email_order_info_after_address($order_data)
        {

            ?>
            <h4 class=color_black>
                <span class=bold><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date_i18n(get_option('date_format'),$order_data['check_in_timestamp']) ?>
                <span class=bold><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date_i18n(get_option('date_format'),$order_data['check_out_timestamp']) ?>
                <?php
                $diff=$order_data['check_out_timestamp'] - $order_data['check_in_timestamp'];
                $diff = $diff / (60 * 60 * 24);
                if($diff > 1){
                    echo sprintf(esc_html__('(%s nights)','wpbooking'),$diff);
                }else{
                    echo sprintf(esc_html__('(%s night)','wpbooking'),$diff);
                }
                ?>

            </h4>
            <?php
        }

        /**
         * Show List Room in single Hotel
         *
         * @since 1.0
         * @author dungdt
         */
        public function _show_list_room(){
            $service=wpbooking_get_service();
            if($service->get_type()==$this->type_id){
                echo wpbooking_load_view('single/hotel/room');
            }
        }


        /**
         * Init Action
         *
         * @since 1.0
         * @author dungdt
         */
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
                            'type'         => 'number',
                            'class'        => 'small',
                            'rules'=>'required',
                            'min' => 0,
                            'placeholder' => esc_html__('Phone number', 'wpbooking'),
                        ),
                        array(
                            'label'       => __('Contact Email', 'wpbooking'),
                            'id'          => 'contact_email',
                            'type'        => 'text',
                            'placeholder' => esc_html__('Example@domain.com', 'wpbooking'),
                            'class'       => 'small',
                            'rules'=>'required|valid_email'
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
                            'extra_rules'=>array(
                                'location_id'=>array('label'=>esc_html__('Location','wpbooking'),'rule'=>'required_integer'),
                                'address'=>array('label'=>esc_html__('Address','wpbooking'),'rule'=>'required'),
                            )
                        ),
                        array(
                            'label' => __('Map Lat & Long', 'wpbooking'),
                            'id'    => 'gmap',
                            'type'  => 'gmap',
                            'desc'  => esc_html__('This is the location we will provide guests. Click to move the marker if you need to move it', 'wpbooking')
                        ),
                        array(
                            'type'    => 'desc_section',
                            'title'   => esc_html__('Your address matters! ', 'wpbooking'),
                            'content' => esc_html__('Please make sure to enter your full address including building name, apartment number, etc.', 'wpbooking')
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                            'prev' => false,
                            'step'=>'first'
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
                            'rules'=>'required'
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
                    'label'  => esc_html__('3. Room Details', 'wpbooking'),
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
                            'desc'  => esc_html__("We display size guest room", "wpbooking")
                        ),
                        array(
                            'label' => __('What is your preferred  unit of measurement?', 'wpbooking'),
                            'id'    => 'room_measunit',
                            'type'  => 'radio',
                            'value' => array(
                                "metres" => esc_html__("Square metres", 'wpbooking'),
                                "feet"   => esc_html__("Square feet", 'wpbooking'),
                            ),
                            'std'   => 'metres',
                            'class' => 'radio_pro',
                            'desc'  => esc_html__("Select the preferred unit of measure your", "wpbooking")
                        ),
                        array(
                            'label'  => __('Room size', 'wpbooking'),
                            'id'     => 'room_size',
                            'type'   => 'room_size',
                            'rules'=>'required'
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
                            'taxonomy' => 'wb_hotel_room_facilities',
                            'rules'=>'required'
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
                            'label' => __("Payment information", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Specify the payment methods you accept at your accommodation as payment for the stay", "wpbooking")
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
                            'label' => __('Deposit payment amount', 'wpbooking'),
                            'id'    => 'deposit_payment_amount',
                            'type'  => 'number',
                            'desc'  => esc_html__("Leave empty for disallow deposit payment", "wpbooking"),
                            'class' => 'small',
                            'min'=>1,
                            'rules'=>'required|integer|greater_than[0]',
                            'rule_condition'=>'deposit_payment_status:not_empty'
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
                            ),
                            'extra_rules'=>array(
                                'vat_amount'=>array('label'=>esc_html__('VAT amount','wpbooking'),'rules'=>'required|integer|greater_than[0]','rule_condition'=>'vat_excluded:not_empty')
                            ),

                        ),
                        array(
                            'label'  => __('City Tax', 'wpbooking'),
                            'id'     => 'citytax_different',
                            'type'   => 'citytax_different',
                            'fields' => array(
                                'citytax_excluded',
                                'citytax_amount',
                                'citytax_unit',
                            ),
                            'extra_rules'=>array(
                                 'citytax_amount'=>array('label'=>esc_html__('City Tax amount','wpbooking'),'rules'=>'required|integer|greater_than[0]','rule_condition'=>'citytax_excluded:not_empty')
        ),
                        ),

                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Term & condition", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("Setting terms and condition for your property", "wpbooking")
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
                            'rules'=>'required'
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
                            'rules'=>'array_key_required[gallery]',
                            'error_message' => esc_html__('You must upload minimum one photo for your accommodation','wpbooking'),
                            'desc'  => __('Great photos invite guests to get the full experience of your property. Be sure to include high-resolution photos of the building, facilities, and amenities. We will display these photos on your property\'s page', 'wpbooking')
                        ),
                        array(
                            'type'       => 'section_navigation',
                            'next_label' => esc_html__('Save', 'wpbooking'),
                            'step' => 'finish'
                        ),
                    )
                ),
            ));

        }


        /**
         * Get Room by Hotel Metabox Fields
         *
         * @since 1.0
         * @author quandq
         *
         * @param $post_id
         * @return array|void|bool
         */
        function _get_room_by_hotel($post_id)
        {
            if (empty($post_id))
                return false;
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
                array(
                    'type' => 'breadcrumb',
                    'new_text' => esc_html__('Add new room','wpbooking'),
                ),
                array('type' => 'open_section','conner_button'=>'<a href="#" onclick="return false" class="wb-button wb-back-all-rooms"><i class="fa fa-chevron-circle-left fa-force-show" aria-hidden="true"></i> '.esc_html__('Back to All Rooms','wpbooking').'</a>'),
                array(
                    'label' => __("Room Name", 'wpbooking'),
                    'type'  => 'title',
                ),
                array(
                    'label' => esc_html__('Room name', 'wpbooking'),
                    'type'  => 'text',
                    'id'    => 'room_name',
                    'desc'  => __("Create an optional, custom name for your reference.", 'wpbooking'),
                    'rules'=>'required'
                ),
                array(
                    'label'    => esc_html__('Room Type', 'wpbooking'),
                    'type'     => 'dropdown',
                    'id'       => 'room_type',
                    'taxonomy' => 'wb_hotel_room_type',
                    'parent'   => 0,
                    'class'    => 'small',
                    'desc'  => __("Based on the amenities of room, select one type most accurate", 'wpbooking'),
                ),
                array(
                    'label' => esc_html__('Room Number', 'wpbooking'),
                    'type'  => 'number',
                    'id'    => 'room_number',
                    'class' => 'small',
                    'rules'=>'required|integer|greater_than[0]',
                    'min'=>1
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
                        0 ,1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
                    ),
                    'std' => 0,
                    'class' => 'small'
                ),
                array(
                    'label' => esc_html__('Living Rooms', 'wpbooking'),
                    'type'  => 'dropdown',
                    'id'    => 'living_rooms',
                    'value' => array(
                        0 ,1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
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
                array('type' => 'open_section'),
                array(
                    'type'  => 'title',
                    'label' => __('Extra Services', 'wpbooking'),
                    'desc'  => esc_html__('Set the extended services for your property', 'wpbooking')
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
            $hotel_id = trim($this->post('hotel_id'));


            if (!$room_id) {

                // Validate Permission
                if (!$hotel_id) {
                    $res['message'] = esc_html__('Please specify Property ID', 'wpbooking');
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

            $res['html'].='<div class="wb-back-all-rooms-wrap"><a href="#" onclick="return false" class="wb-button wb-back-all-rooms"><i class="fa fa-chevron-circle-left fa-force-show" aria-hidden="true"></i> '.esc_html__('Back to All Rooms','wpbooking').'</a></div>';
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

                $form_validate=new WPBooking_Form_Validator();
                $need_validate=false;
                $is_validated=true;
                foreach($fields as $field){

                    if(!empty($field['rules'])){
                        $need_validate=true;
                        $form_validate->set_rules($field['id'],$field['label'],$field['rules']);
                    }
                    if(!empty($field['extra_rules']) and is_array($field['extra_rules'])){
                        $need_validate=true;
                        foreach($field['extra_rules'] as $name=>$rule){
                            $form_validate->set_rules($name,$rule['label'],$rule['rule']);
                        }

                    }

                }

                if($need_validate){
                    $is_validated=$form_validate->run();

                    if(!$is_validated) $res['error_fields']=$form_validate->get_error_fields();
                }




                if($is_validated){
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
                    $updated_content=array(
                        '.wp-room-actions .room-count'=>$this->_get_room_count_text($hotel_id)
                    );
                    $res['updated_content'] = apply_filters('wpbooking_hotel_room_form_updated_content', $updated_content, $room_id, $hotel_id);

                    $res['status'] = 1;
                }

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
            $updated_content=array(
                '.wp-room-actions .room-count'=>$this->_get_room_count_text($hotel_id)
            );
            $res['updated_content'] = apply_filters('wpbooking_hotel_room_form_updated_content', $updated_content, $room_id, $hotel_id);
            echo json_encode($res);
            wp_die();
        }

        /**
         * Get Hotel Room Count HTML for List Room in Dashboard
         *
         * @since 1.0
         * @author dungft
         *
         * @param $hotel_id
         * @param bool @query
         * @return string
         *
         */
        public function _get_room_count_text($hotel_id,$query=false){
            if(!$query){
                $query = new WP_Query(array(
                    'post_parent'    => $hotel_id,
                    'posts_per_page' => 200,
                    'post_type'=>'wpbooking_hotel_room'
                ));
            }

            $total_room=0;
            while ($query->have_posts()){
                $query->the_post();
                $total_room+=get_post_meta(get_the_ID(),'room_number',true);
            }

            if($query->found_posts){
                $text_count=sprintf('<span class="n text-color">%d </span><b>%s</b> ',$query->found_posts,esc_html__('room type(s)','wpbooking'));
                if($total_room){
                    $text_count.=sprintf(esc_html__('with %s ','wpbooking'),sprintf('<span class="n text-color">%d </span><b>%s</b>',$total_room,esc_html__('room(s)')));
                }
                $html='<div class="room-count">'.sprintf(__('There are %s in your listing','wpbooking'),$text_count).'</div>';
            }else{
                $html='<div class="room-count">'.esc_html__('There is <b>no room</b> in your listing','wpbooking').'</div>';
            }

            wp_reset_postdata();
            return $html;
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
                if(empty($hotel_id))$hotel_id = WPBooking_Input::request('hotel_id');
                $query=$this->search_room($hotel_id);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $result['data'] .= wpbooking_load_view('single/loop-room', array('hotel_id' => $hotel_id));
                    }
                } else {
                    $result = array(
                        'status'  => 0,
                        'data'    => '',
                        'message' => __('Our system is not found any room from your searching. You can change search now.', 'wpbooking'),
                        'status_message' => 'default',
                    );
                    echo json_encode($result);
                    die;
                }
                $check_in = $this->request('checkin_y')."-".$this->request('checkin_m')."-".$this->request('checkin_d');
                $check_out = $this->request('checkout_y')."-".$this->request('checkout_m')."-".$this->request('checkout_d');
                if($check_in == '--')$check_in='';
                if($check_out == '--')$check_out='';
                // Validate Minimum Stay
                if ($check_in and $check_out) {
                    $service =  new WB_Service(WPBooking_Input::request('hotel_id'));
                    $check_in_timestamp = strtotime($check_in);
                    $check_out_timestamp = strtotime($check_out);
                    $minimum_stay = $service->get_minimum_stay();
                    $dDiff = wpbooking_timestamp_diff_day($check_in_timestamp, $check_out_timestamp);
                    if ($dDiff < $minimum_stay) {
                        $result['message'] = sprintf(esc_html__('This %s required minimum stay is %s night(s).', 'wpbooking'), $service->get_type(), $minimum_stay);
                        $result['status'] = 2;
                    }
                }

                wp_reset_query();
                echo json_encode($result);
                wp_die();
            }
        }

        function _add_default_query_hook()
        {
            global $wpdb;
            $injection = WPBooking_Query_Inject::inst();
            $tax_query = $injection->get_arg('tax_query');
            $rate_calculate = FALSE;

            //posts per page
            $posts_per_page = $this->get_option('posts_per_page',10);
            $injection->add_arg('posts_per_page', $posts_per_page);

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

            $check_in = $this->request('checkin_y')."-".$this->request('checkin_m')."-".$this->request('checkin_d');
            $check_out = $this->request('checkout_y')."-".$this->request('checkout_m')."-".$this->request('checkout_d');
            if($check_in == '--')$check_in='';
            if($check_out == '--')$check_out='';
            // Validate Minimum Stay
            if ($check_in and $check_out) {
                $check_in_timestamp = strtotime($check_in);
                $check_out_timestamp = strtotime($check_out);
                $dDiff = wpbooking_timestamp_diff_day($check_in_timestamp, $check_out_timestamp);
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'minimum_stay',
                        'type'    => 'NUMERIC',
                        'value'   => $dDiff,
                        'compare' => '<='
                    )
                );
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


            $injection->add_arg('post_status', 'publish');

            // Order By
            if ($sortby = $this->request('wb_sort_by')) {
                switch ($sortby) {
                    case "price_asc":
                        $injection->select('MIN(CAST(order_table.meta_value as double)) as min_price');
                        $injection->join('posts as post_table',"post_table.post_parent={$wpdb->posts}.ID");
                        $injection->join('postmeta as order_table',"order_table.post_ID=post_table.ID and order_table.meta_key='base_price' and order_table.meta_value>0");
                        $injection->orderby('min_price', 'asc');

                        break;
                    case "price_desc":
                        $injection->select('MIN(CAST(order_table.meta_value as double)) as min_price');
                        $injection->join('posts as post_table',"post_table.post_parent={$wpdb->posts}.ID");
                        $injection->join('postmeta as order_table',"order_table.post_ID=post_table.ID and order_table.meta_key='base_price' and order_table.meta_value>0");
                        $injection->orderby('min_price', 'desc');
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
                        $injection->select('avg(' . $wpdb->commentmeta . '.meta_value) as avg_rate')
                            ->join('comments', $wpdb->prefix . 'comments.comment_post_ID=' . $wpdb->posts . '.ID and  ' . $wpdb->comments . '.comment_approved=1', 'LEFT')
                            ->join('commentmeta', $wpdb->prefix . 'commentmeta.comment_id=' . $wpdb->prefix . 'comments.comment_ID and ' . $wpdb->commentmeta . ".meta_key='wpbooking_review'", 'LEFT');
                        if ($sortby == 'rate_asc') {
                                $injection->orderby('avg_rate', 'asc');
                            } else {
                                $injection->orderby('avg_rate', 'desc');
                            }

                        break;
                }
            }
            $sql = "
            {$wpdb->posts}.ID IN (
                    (
                        SELECT
                            hotel_id
                        FROM
                            (
                                SELECT
                                    {$wpdb->posts}.ID AS room_id,
                                    {$wpdb->posts}.post_parent AS hotel_id
                                FROM
                                    {$wpdb->posts}
                                JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                                AND {$wpdb->postmeta}.meta_key = 'room_number'
                                WHERE
                                    {$wpdb->posts}.post_type = 'wpbooking_hotel_room'
                                AND {$wpdb->postmeta}.meta_value > 0
                                AND {$wpdb->posts}.post_status = 'publish'
                                GROUP BY
                                    hotel_id
                            ) AS ID
                    )
                )
            ";



            $injection->where($sql,false,true);
            parent::_add_default_query_hook();

        }

        /**
         *  Query Room
         *
         * @since: 1.0
         * @author: quandq
         *
         * @param bool $hotel_id
         * @return WP_Query
         */
        function search_room($hotel_id = false)
        {
            if(empty($hotel_id)) $hotel_id = get_the_ID();
            $inject=WPBooking_Query_Inject::inst();
            $inject->inject();

            $check_in = $this->request('checkin_y')."-".$this->request('checkin_m')."-".$this->request('checkin_d');
            $check_out = $this->request('checkout_y')."-".$this->request('checkout_m')."-".$this->request('checkout_d');
            if($check_in == '--')$check_in='';
            if($check_out == '--')$check_out='';

            $number_room = $this->request('room_number',1);
            $is_minimum_stay = true;
            if ($check_in and $check_out) {
                $service =  new WB_Service(WPBooking_Input::request('hotel_id'));
                $check_in_timestamp = strtotime($check_in);
                $check_out_timestamp = strtotime($check_out);
                $minimum_stay = $service->get_minimum_stay();
                $dDiff = wpbooking_timestamp_diff_day($check_in_timestamp, $check_out_timestamp);
                if ($dDiff < $minimum_stay) {
                    $is_minimum_stay = false;
                }
            }
            if($is_minimum_stay){
                $ids_not_in = $this->get_unavailability_hotel_room($hotel_id,$check_in,$check_out,$number_room);
                $inject->where_not_in('ID',$ids_not_in);

            }

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
            $arg['meta_query'][] = array(
                'key'     => 'room_number',
                'value'   => $number_room,
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
            $query = new WP_Query($arg);


            $inject->clear();

            return $query;
        }

        /**
         * Get List Unavailability Room
         *
         * @since: 1.0
         * @author: quandq
         *
         * @param $hotel_id
         * @param $check_in
         * @param $check_out
         * @param int $number_room
         * @return array
         */
        function get_unavailability_hotel_room($hotel_id,$check_in, $check_out, $number_room=1){

            if(empty($hotel_id) or empty($check_in) or empty($check_out) or empty($number_room)){
                return array();
            }
            $check_in = strtotime($check_in);
            $check_out = strtotime($check_out);
            if(empty($hotel_id) or empty($check_in)){
                return array();
            }
            global $wpdb;
            $sql = "
            SELECT
                {$wpdb->posts}.ID
            FROM
                {$wpdb->posts}
            WHERE
                1 = 1
            AND {$wpdb->posts}.post_type = 'wpbooking_hotel_room'
            AND {$wpdb->posts}.post_parent = {$hotel_id}
            AND (
                 {$wpdb->posts}.ID IN (
                    SELECT
                        room_id
                    FROM
                        (
                            SELECT
                                {$wpdb->prefix}wpbooking_order_hotel_room.room_id,
                                count(id) AS total_booked,
                                SUM({$wpdb->prefix}wpbooking_order_hotel_room.number) as total_number,
                                {$wpdb->postmeta}.meta_value AS room_number
                            FROM
                                {$wpdb->prefix}wpbooking_order_hotel_room
                            JOIN {$wpdb->postmeta} ON {$wpdb->postmeta}.post_id = {$wpdb->prefix}wpbooking_order_hotel_room.room_id
                            AND {$wpdb->postmeta}.meta_key = 'room_number'
                            WHERE
                                1 = 1
                            AND (
                                (
                                    check_in_timestamp <= {$check_in}
                                    AND check_out_timestamp >= {$check_in}
                                )
                                OR (
                                    check_in_timestamp >= {$check_in}
                                    AND check_in_timestamp <= {$check_out}
                                )
                            )
                            GROUP BY
                                {$wpdb->prefix}wpbooking_order_hotel_room.room_id
                            HAVING
                                room_number - total_number < {$number_room}
                        ) AS table_booked
                )
                OR {$wpdb->posts}.ID IN (
                    SELECT
                        post_id
                    FROM
                        (
                            SELECT
                                post_id
                            FROM
                                {$wpdb->prefix}wpbooking_availability
                            WHERE
                                1 = 1
                            AND (
                                START >= {$check_in}
                                AND
                                END <= {$check_out}
                                AND `status` = 'not_available'
                            )
                            GROUP BY
                                post_id
                        )as table_availability
                )
            )";
            if($check_out <= $check_in){
                $sql = "
                        SELECT
                            {$wpdb->posts}.ID
                        FROM
                            {$wpdb->posts}
                        WHERE
                            1 = 1
                        AND {$wpdb->posts}.post_type = 'wpbooking_hotel_room'
                        AND {$wpdb->posts}.post_parent = {$hotel_id}";
            }
            $r=array();
            $res=$wpdb->get_results($sql,ARRAY_A);
            if(!is_wp_error($res))
            {
                foreach($res as $key=>$value)
                {
                    $r[]=$value['ID'];
                }
            }
            return $r;
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
                                    <span data-condition="room_measunit:is(metres)" class="input-group-addon wpbooking-condition wb-hidden">m<sup>2</sup></span>
                                    <span data-condition="room_measunit:is(feet)" class="input-group-addon wpbooking-condition">ft<sup>2</sup></span>
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
         * Get Fields Search Form
         *
         * @since: 1.0
         * @author: quandq
         *
         * @return array
         */
        public function get_search_fields()
        {
            $taxonomy = get_object_taxonomies('wpbooking_service', 'array');
            $wpbooking_taxonomy = get_option('wpbooking_taxonomies');
            $list_taxonomy = array();
            if (!empty($taxonomy)) {
                foreach ($taxonomy as $k => $v) {
                    if ($k == 'wpbooking_location') continue;
                    if ($k == 'wpbooking_extra_service') continue;
                    if ($k == 'wb_review_stats') continue;
                    if ($k == 'wb_tour_type') continue;
                    if(key_exists($k, $wpbooking_taxonomy)){
                        if(!empty($wpbooking_taxonomy[$k]['service_type']) && in_array('accommodation', $wpbooking_taxonomy[$k]['service_type'])){
                            $list_taxonomy[$k] = $v->label;
                        }
                    }else{
                        $list_taxonomy[$k] = $v->label;
                    }
                }
            }

            $search_fields = apply_filters('wpbooking_search_field_'.$this->type_id, array(
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

            ));

            // TODO: Implement get_search_fields() method.
            return $search_fields;
        }

        /**
         * Hook Callback Change Base Price
         *
         * @since 1.0
         * @author
         *
         * @param $base_price
         * @param $hotel_id
         * @param $service_type
         * @return mixed
         */
        public function _change_base_price($base_price, $hotel_id, $service_type)
        {
            $base_price = WPBooking_Meta_Model::inst()->get_price_accommodation($hotel_id);

            return $base_price;
        }

        /**
         * Hook Callback Change Base Price
         *
         * @since 1.0
         * @author quandq
         *
         * @param $price_html
         * @param $price
         * @param $post_id
         * @param $service_type
         * @return string
         */
        public function _change_base_price_html($price_html,$price,$post_id,$service_type)
        {
            if(!$post_id) return ;
            $check_in = WPBooking_Input::request('checkin_y')."-".WPBooking_Input::request('checkin_m')."-".WPBooking_Input::request('checkin_d');
            $check_out = WPBooking_Input::request('checkout_y')."-".WPBooking_Input::request('checkout_m')."-".WPBooking_Input::request('checkout_d');
            $price_html=WPBooking_Currency::format_money($price);
            $diff = strtotime($check_out) - strtotime($check_in);
            $diff = $diff / (60 * 60 * 24);
            if($diff > 1){
                $price_html=sprintf(__('from %s /%s nights','wpbooking'),'<br><span class="price">'.$price_html.'</span>',$diff);
            }else{
                $price_html=sprintf(__('from %s /night','wpbooking'),'<br><span class="price">'.$price_html.'</span>');
            }
            return $price_html;
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
            $data_rooms = $this->post('wpbooking_room');
            if(!empty($data_rooms)) {
                foreach( $data_rooms as $room_id => $data_room ) {
                    if(!empty($data_room['number_room'])){
                        $extra_service = array();
                        $my_extra_services = get_post_meta($room_id,'extra_services',true);
                        if(!empty($data_room['extra_service'])){
                            $post_extras = $data_room['extra_service'];
                            foreach($post_extras as $key=>$value){
                                $extra_service['title'] = esc_html__('Extra Service','wpbooking');
                                if(!empty($value['is_check'])){
                                    $price = 0;
                                    if(!empty($my_extra_services[$key]['money'])){
                                        $price = $my_extra_services[$key]['money'];
                                    }
                                    $extra_service['data'][$key] = array(
                                        'title'=>$value['is_check'],
                                        'quantity'=>$value['quantity'],
                                        'price'=>$price
                                    );
                                }

                            }
                        }
                        // Check require
                        if(!empty($my_extra_services)){
                            foreach($my_extra_services as $key=>$value){
                                if($value['require'] == 'yes' and empty($extra_service[$key])){
                                    $extra_service['data'][$key] = array(
                                        'title'=>$value['is_selected'],
                                        'quantity'=>1,
                                        'price'=>$value['money'],
                                    );
                                }
                            }
                        }
                        $cart_item['rooms'][$room_id] = array(
                            'room_id'=>$room_id,
                            'number'=>$data_room['number_room'],
                            'extra_fees'=>array(
                                'extra_service'=>$extra_service
                            )
                        );
                        if ($cart_item['check_in_timestamp'] and $cart_item['check_out_timestamp']) {
                            $cart_item['rooms'][$room_id]['calendar_prices'] = $calendar->get_prices( $room_id , $cart_item[ 'check_in_timestamp' ] , $cart_item[ 'check_out_timestamp' ] );
                        }

                        // add list date price
                        $price_base = get_post_meta($room_id,'base_price',true);
                        $check_in = $cart_item['check_in_timestamp'];
                        $check_out = $cart_item['check_out_timestamp'];
                        if(!empty($cart_item['rooms'][$room_id]['calendar_prices'])){
                            $custom_calendar = $cart_item['rooms'][$room_id]['calendar_prices'];
                        }
                        $groupday = $this->getGroupDay($check_in, $check_out);
                        if(is_array($groupday) && count($groupday)) {
                            foreach( $groupday as $date ) {
                                $price_tmp = $price_base;
                                if(!empty($custom_calendar)){
                                    foreach($custom_calendar as $date_calendar){
                                        if($date[0] >= $date_calendar['start'] && $date[0] <=  $date_calendar['end']){
                                            $price_tmp = $date_calendar['price'];
                                        }
                                    }
                                }
                                $cart_item['rooms'][$room_id]['list_date_price'][$date[0]] = $price_tmp;
                            }
                        }

                    }

                }
            }

            $wpbooking_adults = WPBooking_Input::post('wpbooking_adults',1);
            $cart_item['person'] = $wpbooking_adults;
            $wpbooking_children = WPBooking_Input::post('wpbooking_children');
            if(!empty($wpbooking_children)){
                $cart_item['person']+=$wpbooking_children;
            }

            return $cart_item;
        }

        /**
         * Validate checkout
         *
         * @author quandq
         * @since 1.0
         *
         * @param $is_validated
         * @param array $cart
         * @return bool
         */
        function _validate_checkout($is_validated, $cart = array())
        {
            if ($is_validated) {
                if (!empty($cart)) {
                    // Validate Availability last time
                    $cart_item = wp_parse_args($cart, array(
                        'check_out_timestamp' => '',
                        'check_in_timestamp'  => ''
                    ));
                    if ($cart_item['check_out_timestamp']) {
                        $cart_item['check_out_timestamp'] = $cart_item['check_in_timestamp'];
                    }
                    $check_in_timestamp = $cart_item['check_in_timestamp'];
                    $check_out_timestamp = $cart_item['check_out_timestamp'];

                    if(!empty( $cart_item['rooms'])){
                        $list_room = $cart_item['rooms'];
                        //check availability Calendar
                        foreach($list_room as $room_id => $data){
                            $res = $this->check_availability_room($room_id,$check_in_timestamp, $check_out_timestamp);
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
                                    $message = esc_html__('You can not book "%s" on: %s', 'wpbooking');
                                    $not_avai_string = FALSE;
                                    $not_avai_string .= date(get_option('date_format'), $res['unavailable_dates']);
                                    wpbooking_set_message(sprintf($message, get_the_title($room_id) , $not_avai_string), 'error');
                                }

                            }
                        }
                        //check availability Order
                        $ids_room_not_availability = array();
                        foreach($list_room as $room_id => $data){
                            $data_rs = $this->check_availability_order_hotel_room($room_id,$check_in_timestamp, $check_out_timestamp,$data['number']);
                            if(!empty($data_rs['total'])){
                                $number_room = get_post_meta($room_id,'room_number',true);
                                $availability_number = $number_room - $data_rs['total'];
                                $ids_room_not_availability[] = array('title'=>get_the_title($data_rs['id']),'number'=>$availability_number);
                            }
                        }
                        if(!empty($ids_room_not_availability)){
                            $is_validated = FALSE;
                            $message = '';
                            foreach($ids_room_not_availability as $k_not_availability=>$value_not_availability){
                                //$message .= sprintf(esc_html__('Bạn không thể book. Room Type "%s" chỉ còn %s phòng trống!', 'wpbooking','error'), $value_not_availability['title'], $value_not_availability['number'])."</br>";
                                $message = esc_html__("Number of room you booking is not enough, please change your search.","wpbooking");
                            }
                            wpbooking_set_message($message, 'error');
                            return $is_validated;
                        }
                    }
                }
            }

            return $is_validated;
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

            $check_in = $this->request('wpbooking_checkin_y')."-".$this->request('wpbooking_checkin_m')."-".$this->request('wpbooking_checkin_d');
            $check_out = $this->request('wpbooking_checkout_y')."-".$this->request('wpbooking_checkout_m')."-".$this->request('wpbooking_checkout_d');
            if($check_in == '--')$check_in='';
            if($check_out == '--')$check_out='';

            $wpbooking_room = $this->post('wpbooking_room');
            $check_number_room = false;
            $total_number_room = 0;
            if(!empty($wpbooking_room)) {
                foreach( $wpbooking_room as $k => $v ) {
                    if(!empty( $v['number_room'] )) {
                        $check_number_room = true;
                        $total_number_room += $v['number_room'];
                    }
                }
            }

            if(empty($check_in) and empty($check_out)){
                wpbooking_set_message(esc_html__("To see price details, please select check-in and check-out date.","wpbooking"),'error');
                $is_validated = FALSE;
                return $is_validated;
            }
            if(empty($check_in)){
                wpbooking_set_message(esc_html__("Please select check-in date.","wpbooking"),'error');
                $is_validated = FALSE;
                return $is_validated;
            }
            if(empty($check_out)){
                wpbooking_set_message(esc_html__("Please select check-out date.","wpbooking"),'error');
                $is_validated = FALSE;
                return $is_validated;
            }
            if(empty($check_number_room)){
                wpbooking_set_message(esc_html__("Please select number of room.","wpbooking"),'error');
                $is_validated = FALSE;
                return $is_validated;
            }
            //check number room and adult
            $adult = $this->post('wpbooking_adults');
            if($total_number_room > $adult){
                $is_validated = FALSE;
                $message = esc_html__('Number of rooms bookable can not more than number of adults.', 'wpbooking');
                wpbooking_set_message($message, 'error');
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
                    wpbooking_set_message(esc_html__("Day after day check out to check in","wpbooking"),'error');
                    $is_validated = FALSE;
                    return $is_validated;
                }

                // Validate Minimum Stay
                if ($check_in_timestamp and $check_out_timestamp) {
                    $minimum_stay = $service->get_minimum_stay();
                    $dDiff = wpbooking_timestamp_diff_day($check_in_timestamp, $check_out_timestamp);
                    if ($dDiff < $minimum_stay) {
                        $is_validated = FALSE;
                        wpbooking_set_message(sprintf(esc_html__('This %s required minimum stay is %s night(s)', 'wpbooking'),$service->get_type(), $minimum_stay), 'error');
                        return $is_validated;
                    }
                }


                if(!empty( $cart_params['rooms'])){
                    $list_room = $cart_params['rooms'];
                    //check availability Calendar
                    foreach($list_room as $room_id => $data){
                        $res = $this->check_availability_room($room_id,$check_in_timestamp, $check_out_timestamp);
                        if (!$res['status']) {
                            $is_validated = FALSE;
                            // If there are some day not available, return the message
                            if (!empty($res['can_not_check_in'])) {
                                wpbooking_set_message(sprintf(esc_html__("You can not check-in at: %s", 'wpbooking'), date_i18n(get_option('date_format'), $check_in_timestamp)),'error');
                            }
                            if (!empty($res['can_not_check_out'])) {
                                wpbooking_set_message(sprintf(esc_html__("You can not check-out at: %s", 'wpbooking'), date_i18n(get_option('date_format'), $check_out_timestamp)),'error');
                            }
                            if (!empty($res['unavailable_dates'])) {
                                $message = esc_html__('You can not book "%s" on: %s', 'wpbooking');
                                $not_avai_string = FALSE;
                                $not_avai_string .= date_i18n(get_option('date_format'), $res['unavailable_dates']);
                                wpbooking_set_message(sprintf($message, get_the_title($room_id) , $not_avai_string), 'error');
                            }

                        }
                    }
                    //check availability Order
                    $ids_room_not_availability = array();
                    foreach($list_room as $room_id => $data){
                        $data_rs = $this->check_availability_order_hotel_room($room_id,$check_in_timestamp, $check_out_timestamp,$data['number']);
                        if(!empty($data_rs['total'])){
                            $number_room = get_post_meta($room_id,'room_number',true);
                            $availability_number = $number_room - $data_rs['total'];
                            $ids_room_not_availability[] = array('title'=>get_the_title($data_rs['id']),'number'=>$availability_number);
                        }
                    }
                    if(!empty($ids_room_not_availability)){
                        $is_validated = FALSE;
                        $message = '';
                        foreach($ids_room_not_availability as $k_not_availability=>$value_not_availability){
                            //$message .= sprintf(esc_html__('Bạn không thể book. Room Type "%s" chỉ còn %s phòng trống!', 'wpbooking','error'), $value_not_availability['title'], $value_not_availability['number'])."</br>";
                            $message = esc_html__("Number of room you booking is not enough, please change your search.","wpbooking");
                        }
                        wpbooking_set_message($message, 'error');
                        return $is_validated;
                    }

                }


            }


            return $is_validated;
        }

        /**
         * Check Room availability Calendar
         *
         * @author quandq
         * @since 1.0
         *
         * @param $room_id
         * @param $start
         * @param $end
         * @return mixed|void
         */
        function check_availability_room($room_id,$start,$end){

            $return=array(
                'status'=>0,
                'unavailable_dates'=>array()
            );

            if($room_id){
                $calendar = WPBooking_Calendar_Model::inst();
                $calendar_prices = $calendar->calendar_months($room_id, $start, $end);
                if(!empty($calendar_prices)){
                    foreach($calendar_prices as $key=>$value){
                        $calendar_prices[date('d-m-Y',$value['start'])]=$value;
                    }
                }
                $is_available_for = get_post_meta($room_id,'property_available_for',true);
                switch($is_available_for){
                    case "specific_periods":
                        if(!empty($calendar_prices)){
                            $return['status']=1;
                            $check_in_temp=$start;
                            while ($check_in_temp <= $end) {
                                if(!array_key_exists(date('d-m-Y',$check_in_temp),$calendar_prices) or $calendar_prices[date('d-m-Y',$check_in_temp)]['status']=='not_available'){
                                    $return['unavailable_dates'] = $check_in_temp;
                                    $return['status']=0;
                                }
                                $check_in_temp = strtotime('+1 day', $check_in_temp);
                            }
                        }else{
                            $return['unavailable_dates'] = $start;
                            $return['status']=0;
                        }
                        break;
                    case "forever":
                    default:
                        $return['status']=1;
                        if(!empty($calendar_prices)){
                            $check_in_temp=$start;
                            while ($check_in_temp <= $end) {
                                if(array_key_exists(date('d-m-Y',$check_in_temp),$calendar_prices) and $calendar_prices[date('d-m-Y',$check_in_temp)]['status']=='not_available'){
                                    $return['unavailable_dates'] = $check_in_temp;
                                    $return['status']=0;
                                }
                                $check_in_temp = strtotime('+1 day', $check_in_temp);
                            }
                        }
                        break;
                }
            }
            return apply_filters('wpbooking_service_check_availability_room',$return,$this,$start,$end);

        }

        /**
         * Check Room availability Order
         *
         * @author quandq
         * @since 1.0
         *
         * @param $room_id
         * @param $check_in
         * @param $check_out
         * @param int $number_room
         * @return array|mixed|string
         */
        function check_availability_order_hotel_room($room_id,$check_in, $check_out, $number_room=1){

            if(empty($room_id) or empty($check_in) or empty($check_out) or empty($number_room)){
                return array();
            }
            $hotel_id = wp_get_post_parent_id($room_id);
            global $wpdb;
            $sql = "
            SELECT
                {$wpdb->posts}.ID
            FROM
                {$wpdb->posts}
            WHERE
                1 = 1
            AND {$wpdb->posts}.post_type = 'wpbooking_hotel_room'
            AND {$wpdb->posts}.post_parent = {$hotel_id}
            AND {$wpdb->posts}.ID = {$room_id}
            AND (
                 {$wpdb->posts}.ID IN (
                    SELECT
                        room_id
                    FROM
                        (
                            SELECT
                                {$wpdb->prefix}wpbooking_order_hotel_room.room_id,
                                count(id) AS total_booked,
                                SUM({$wpdb->prefix}wpbooking_order_hotel_room.number) as total_number,
                                {$wpdb->postmeta}.meta_value AS room_number
                            FROM
                                {$wpdb->prefix}wpbooking_order_hotel_room
                            JOIN {$wpdb->postmeta} ON {$wpdb->postmeta}.post_id = {$wpdb->prefix}wpbooking_order_hotel_room.room_id
                            AND {$wpdb->postmeta}.meta_key = 'room_number'
                            WHERE
                                1 = 1
                            AND (
                                (
                                    check_in_timestamp <= {$check_in}
                                    AND check_out_timestamp >= {$check_in}
                                )
                                OR (
                                    check_in_timestamp >= {$check_in}
                                    AND check_in_timestamp <= {$check_out}
                                )
                            )
                            GROUP BY
                                {$wpdb->prefix}wpbooking_order_hotel_room.room_id
                            HAVING
                                room_number - total_number < {$number_room}
                        ) AS table_booked
                )
            )";
            $r=array();

            $res=$wpdb->get_row($sql,ARRAY_A);
            if(!is_wp_error($res))
            {
                $room_id = array_shift($res);
                $r['total'] = 0;
                $r['id']=$room_id;
                if(!empty($room_id)){
                    $sql2 = "
                           SELECT
                                SUM({$wpdb->prefix}wpbooking_order_hotel_room.number) as total_number
                            FROM
                                {$wpdb->prefix}wpbooking_order_hotel_room
                            WHERE
                                1 = 1
                            	AND  {$wpdb->prefix}wpbooking_order_hotel_room.room_id = {$room_id}
                            AND (
                                (
                                    check_in_timestamp <= {$check_in}
                                    AND check_out_timestamp >= {$check_in}
                                )
                                OR (
                                    check_in_timestamp >= {$check_in}
                                    AND check_in_timestamp <= {$check_out}
                                )
                            )
                            GROUP BY
                                {$wpdb->prefix}wpbooking_order_hotel_room.room_id";
                    $res2=$wpdb->get_row($sql2,ARRAY_A);
                    if(!is_wp_error($res))
                    {
                        $total = array_shift($res2);
                        $r['total']=$total;
                    }
                }
            }
            return $r;
        }

        /**
         * Reload image list Room after save gallery
         *
         * @author quandq
         * @since 1.0
         *
         */
        function wpbooking_reload_image_list_room(){
            $post_id = $this->request('wb_post_id');
            $tab = $this->request('wb_meta_section');
            $service = new WB_Service($post_id);
            $list_room = array();
            if($service->get_type() == $this->type_id and $tab=='photo_tab'){
                $arg = array(
                    'post_type'      => 'wpbooking_hotel_room',
                    'posts_per_page' => '200',
                    'post_status'    => array('publish', 'draft', 'pending', 'future', 'private', 'inherit'),
                    'post_parent'    => $post_id
                );
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
         * Add Item Info Room for Page CheckOut
         * @author quandq
         * @since 1.0
         * @param $cart
         */
        function _add_info_checkout_item_room($cart){
            echo wpbooking_load_view('checkout/other/checkout-item-room',array('cart'=>$cart));
        }

        /**
         * Add Item Info Room for Page CheckOut
         *
         * @author quandq
         * @since 1.0
         * @param $cart
         */
        function _add_info_total_item_room($cart){
            if(!empty($cart['rooms'])) {
                $number =0;
                foreach($cart['rooms'] as $room){
                    $number += $room['number'];
                }
                if($number>1){
                    $html =  sprintf(esc_html__("%s rooms",'wpbooking'),$number);
                }else{
                    $html = sprintf(esc_html__("%s room",'wpbooking'),$number);
                }
                $price = $this->_get_total_price_all_room_in_cart($cart,false);
                echo '<span class="total-title">'.esc_html($html).'</span>
                      <span class="total-amount">'.WPBooking_Currency::format_money($price).'</span>';

                foreach($cart['rooms'] as $room_id=>$room){
                    $number_room = $room['number'];
                    if(!empty($room['extra_fees'])){
                        $extra_fees = $cart['rooms'][$room_id]['extra_fees'];
                        foreach($extra_fees as $extra_items){
                            $price = 0;
                            if(!empty($extra_items['data'])){
                                echo '<span class="total-title">'.esc_html($extra_items['title']).'</span>';
                                foreach($extra_items['data'] as $data){
                                    $price += ( $data['price'] * $data['quantity'] ) * $number_room;
                                }
                                echo '<span class="total-amount">'.WPBooking_Currency::format_money($price).'</span>';
                            }
                        }
                    }
                }
            }
        }

        /**
         * Add Item Info Room for Page Order Detail
         *
         * @author quandq
         * @since 1.0
         * @param $order_data
         */
        function _add_info_order_detail_item_room($order_data){
            $order = WPBooking_Order_Hotel_Order_Model::inst();
            $order_data['rooms']= $order->get_order($order_data['order_id']);
            echo wpbooking_load_view('order/other/order-item-room',array('order_data'=>$order_data));
        }

        /**
         * Add Item Info Room for Email Detail
         *
         * @author quandq
         * @since 1.0
         * @param $order_data
         */
        function _add_information_email_detail_item($order_data){
            $order = WPBooking_Order_Hotel_Order_Model::inst();
            $order_data['rooms']= $order->get_order($order_data['order_id']);
            echo wpbooking_load_view('emails/shortcodes/detail-item-room',array('order_data'=>$order_data));
        }
        /**
         * Add Item Info Room for Page Order Detail
         *
         * @author quandq
         * @since 1.0
         * @param $order_data
         */
        function _add_info_order_total_item_room($order_data){
            $order = WPBooking_Order_Hotel_Order_Model::inst();
            $rooms= $order->get_order($order_data['order_id']);
            if(!empty($rooms)) {
                $number =0;
                foreach($rooms as $room){
                    $number += $room['number'];
                }
                if($number>1){
                    $html =  sprintf(esc_html__("%s rooms",'wpbooking'),$number);
                }else{
                    $html = sprintf(esc_html__("%s room",'wpbooking'),$number);
                }
                $price = 0;
                foreach($rooms as $room){
                    $price += $room['price'];
                }
                echo '<span class="total-title">'.esc_html($html).'</span>
                      <span class="total-amount">'.WPBooking_Currency::format_money($price).'</span>';
                foreach($rooms as $room){
                    $number_room = $room['number'];
                    if(!empty($room['extra_fees'])){
                        $extra_fees = unserialize($room['extra_fees']);
                        if(!empty($extra_fees)){
                            foreach($extra_fees as $extra_items){
                                $price = 0;
                                if(!empty($extra_items['data'])){
                                    echo '<span class="total-title">'.esc_html($extra_items['title']).'</span>';
                                    foreach($extra_items['data'] as $data){
                                        $price += ( $data['price'] * $data['quantity'] ) * $number_room;
                                    }
                                    echo '<span class="total-amount">'.WPBooking_Currency::format_money($price).'</span>';
                                }
                            }
                        }

                    }
                }
            }
        }



        /**
         * Get Price Room In Cart
         *
         * @author quandq
         * @since 1.0
         *
         * @param $cart
         * @param $room_id
         * @return int
         */
        function _get_price_room_in_cart($cart,$room_id){
            if(empty($room_id)) return 0 ;
            $total_price = 0;
            if(!empty($cart['rooms'][$room_id])){
                $data_room = $cart['rooms'][$room_id];
                $service = new WB_Service($data_room['room_id']);
                $price_base = $service->get_meta('base_price');
                $check_in = $cart['check_in_timestamp'];
                $check_out = $cart['check_out_timestamp'];

                if(!empty($cart['rooms'][$room_id]['calendar_prices'])){
                    $custom_calendar = $cart['rooms'][$room_id]['calendar_prices'];
                }
                $groupday = self::getGroupDay($check_in, $check_out);
                if(is_array($groupday) && count($groupday)) {
                    foreach( $groupday as $date ) {
                        $price_tmp = $price_base;
                        if(!empty($custom_calendar)){
                            foreach($custom_calendar as $date_calendar){
                                if($date[0] >= $date_calendar['start'] && $date[0] <=  $date_calendar['end']){
                                    $price_tmp = $date_calendar['price'];
                                }
                            }
                        }
                        $total_price +=$price_tmp;
                    }
                }
            }
            return $total_price;
        }

        /**
         * Get Total Price Room In Cart
         *
         * @author quandq
         * @since 1.0
         *
         * @param $cart
         * @param $room_id
         * @param bool $include_price_extra
         * @return int|mixed
         */
        function _get_total_price_room_in_cart($cart,$room_id,$include_price_extra = true){
            if(empty($room_id)) return 0 ;
            $extra_price = 0;
            $price_room = 0;
            $number_room = 0;
            if(!empty($cart['rooms'][$room_id])){
                $data_room = $cart['rooms'][$room_id];
                $service = new WB_Service($data_room['room_id']);
                $price_base = $service->get_meta('base_price');
                $check_in = $cart['check_in_timestamp'];
                $check_out = $cart['check_out_timestamp'];
                $number_room = $cart['rooms'][$room_id]['number'];
                // Base Price
                if(!empty($cart['rooms'][$room_id]['calendar_prices'])){
                    $custom_calendar = $cart['rooms'][$room_id]['calendar_prices'];
                }
                $groupday = self::getGroupDay($check_in, $check_out);
                if(is_array($groupday) && count($groupday)) {
                    foreach( $groupday as $date ) {
                        $price_tmp = $price_base;
                        if(!empty($custom_calendar)){
                            foreach($custom_calendar as $date_calendar){
                                if($date[0] >= $date_calendar['start'] && $date[0] <=  $date_calendar['end']){
                                    $price_tmp = $date_calendar['price'];
                                }
                            }
                        }
                        $price_room +=$price_tmp;
                    }
                }
                // Extra Price
                if(!empty($cart['rooms'][$room_id]['extra_fees'])){
                    $extra_fees = $cart['rooms'][$room_id]['extra_fees'];
                    foreach($extra_fees as $extra_items){
                        if(!empty($extra_items['data'])){
                            foreach($extra_items['data'] as $data){
                                $extra_price += $data['price'] * $data['quantity'];
                            }
                        }
                    }
                }
            }
            if($include_price_extra){
                $total_price = ($price_room + $extra_price) * $number_room;
            }else{
                $total_price = ($price_room) * $number_room;
            }

            return $total_price;
        }

        /**
         * Get  Price Room with date
         *
         * @author quandq
         * @since 1.0
         *
         * @param $room_id
         * @param $check_in
         * @param $check_out
         * @return int|mixed
         */
        function _get_price_room_with_date($room_id,$check_in,$check_out){
            if(empty($room_id)) $room_id = get_the_ID();
            $calendar = WPBooking_Calendar_Model::inst();
            $check_in_timestamp = strtotime($check_in);
            $check_out_timestamp = strtotime($check_out);
            $price_room=0;

            $service = new WB_Service($room_id);
            $price_base = $service->get_meta('base_price');
            $custom_calendar = $calendar->get_prices( $room_id , $check_in_timestamp , $check_out_timestamp );

            $groupday = self::getGroupDay($check_in_timestamp, $check_out_timestamp);
            if(is_array($groupday) && count($groupday)) {
                foreach( $groupday as $date ) {
                    $price_tmp = $price_base;
                    if(!empty($custom_calendar)){
                        foreach($custom_calendar as $date_calendar){
                            if($date[0] >= $date_calendar['start'] && $date[0] <=  $date_calendar['end']){
                                $price_tmp = $date_calendar['price'];
                            }
                        }
                    }
                    $price_room +=$price_tmp;
                }
            }
            return $price_room;
        }

        /**
         * Get Total Price all Room in cart
         *
         * @author quandq
         * @since 1.0
         *
         * @param $cart
         * @param bool $include_price_extra
         * @return int|mixed
         */
        function _get_total_price_all_room_in_cart($cart,$include_price_extra = true){
            $price = 0;
            if(!empty($cart['rooms'])) {
                foreach($cart['rooms'] as $room_id=>$room){
                    $price += $this->_get_total_price_room_in_cart($cart,$room_id,$include_price_extra);
                }
            }
            return $price;
        }
        /**
         *  Get GroupDay
         *  @author quandq
         *  @since 1.0
         *
         * @param string $start
         * @param string $end
         * @return array
         */
        static function getGroupDay($start = '', $end = ''){
            $list = array();
            for($i = $start; $i <= $end; $i = strtotime('+1 day', $i)){
                $next = strtotime('+1 day', $i);
                if($next <= $end){
                    $list[] = array($i, $next);
                }
            }
            return $list;
        }

        /**
         * Get Total Price Room In Cart
         * @author quandq
         * @since 1.0
         *
         * @param $price
         * @param $cart
         * @return int
         */
        function _get_cart_total_price_hotel_room($price,$cart){
            if(!empty($cart['rooms'])) {
                foreach($cart['rooms'] as $room_id=>$room){
                    $price += $this->_get_total_price_room_in_cart($cart,$room_id);
                }
            }
            return $price;
        }

        /**
         * Save Order Hotel Room
         * @author quandq
         * @since 1.0
         *
         * @param $cart
         * @param $order_id
         */
        function _save_order_hotel_room($cart,$order_id){
            if(!empty($cart['rooms'])){
                $hotel_id = $cart['post_id'];
                foreach($cart['rooms'] as $room_id => $room ){
                    $order = WPBooking_Order_Hotel_Order_Model::inst();
                    $price_room = WPBooking_Accommodation_Service_Type::inst()->_get_price_room_in_cart($cart,$room_id);
                    $price_total_room = WPBooking_Accommodation_Service_Type::inst()->_get_total_price_room_in_cart($cart,$room_id);
                    $data = array(
                        'order_id'=> $order_id,
                        'hotel_id'=> $hotel_id,
                        'room_id'=> $room_id,
                        'price'=> $price_room,
                        'price_total'=> $price_total_room,
                        'number'=> $room['number'],
                        'extra_fees'=> serialize($room['extra_fees']),
                        'check_in_timestamp'=> $cart['check_in_timestamp'],
                        'check_out_timestamp'=> $cart['check_out_timestamp'],
                        'raw_data'=> serialize($room['list_date_price']),
                    );
                    $order->save_order_hotel_room($data, $room_id , $order_id);
                }
            }
        }

        /**
         * Handler Action Delete Cart Item Hotel Room
         *
         * @since 1.0
         * @author quandq
         */
        function _delete_cart_item_hotel_room()
        {
            if (isset($_GET['delete_item_hotel_room'])) {
                $index = WPBooking_Input::get('delete_item_hotel_room');
                $booking = WPBooking_Checkout_Controller::inst();
                $all = $booking->get_cart();
                if(!empty($all['service_type']) and $all['service_type'] = 'accommodation' ){
                    unset($all['rooms'][$index]);
                    if(empty($all['rooms'])){
                        $booking->set_cart(array());
                    }else{
                        $booking->set_cart($all);
                    }
                    wpbooking_set_message(__("Delete item successfully", 'wpbooking'), 'success');
                }
            }
        }

        /**
         * Change Tax Room CheckOut
         * @param $tax
         * @param $cart
         * @return mixed
         */
        function _change_tax_room_checkout($tax, $cart){

            $tax = array();
            $diff=$cart['check_out_timestamp'] - $cart['check_in_timestamp'];
            $date_diff = $diff / (60 * 60 * 24);

            $total_price = WPBooking_Checkout_Controller::inst()->get_cart_total(array('without_tax'=>false));
            $total_tax = 0;
            $tax_total = 0;

            if(!empty($cart['tax']) and !empty($cart['rooms'])){
                $number_room = 0;
                foreach($cart['rooms'] as $room){
                    $number_room += $room['number'];
                }
                foreach($cart['tax'] as $key => $value){
                    if($value['excluded'] != 'no'){
                        $unit = $value['unit'];
                        $tax[$key] = $value;
                        $price = 0;
                        switch($unit){
                            case "fixed":
                            case "stay":
                                $price = $value['amount'] * $number_room;
                                break;
                            case "percent":
                                $price = $total_price * ($value['amount'] / 100);
                                break;
                            case "night":
                                $price = $value['amount'] * $date_diff * $number_room;
                                break;
                            case "person_per_stay":
                                if(!empty($cart['person'] )){
                                    $person = $cart['person'];
                                    $price = $person *  $value['amount'] * $number_room;
                                }
                                break;
                            case "person_per_night":
                                if(!empty($cart['person'] )){
                                    $person = $cart['person'];
                                    $price =  ( $value['amount'] * $person ) * $date_diff * $number_room;
                                }
                                break;
                            default:
                        }
                        if($value['excluded'] == 'yes_not_included'){
                            $total_tax += $price;
                        }
                        $tax_total += $price;
                        $tax[$key]['price'] = floatval($price);
                    }
                }
            }
            $tax['total_price'] = $total_tax;
            $tax['tax_total'] = $tax_total;
            return $tax;
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