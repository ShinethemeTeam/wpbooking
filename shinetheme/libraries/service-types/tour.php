<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/10/2016
 * Time: 3:47 PM
 */
if (!class_exists('WPBooking_Tour_Service_Type') and class_exists('WPBooking_Abstract_Service_Type')) {
    class WPBooking_Tour_Service_Type extends WPBooking_Abstract_Service_Type
    {
        static $_inst = false;

        protected $type_id = 'tour';

        function __construct()
        {
            $this->type_info = array(
                'label' => __("Tour", 'wpbooking'),
                'desc'  => esc_html__('Tour Booking', 'wpbooking')
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


            add_filter('wpbooking_archive_loop_image_size', array($this, '_apply_thumb_size'), 10, 3);


            /**
             * Register metabox fields
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('init',array($this,'_register_meta_fields'));


            /**
             * Register Tour Type
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('init',array($this,'_register_tour_type'));

            /**
             * Change Base Price Format
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_service_base_price_'.$this->type_id,array($this,'_edit_price'),10,2);


        }

        /**
         * Register Tour Type
         *
         * @since 1.0
         * @author dungdt
         */
        public function _register_tour_type(){
            // Register Taxonomy
            $labels = array(
                'name'              => _x('Tour Type', 'taxonomy general name', 'wpbooking'),
                'singular_name'     => _x('Tour Type', 'taxonomy singular name', 'wpbooking'),
                'search_items'      => __('Search Tour Type', 'wpbooking'),
                'all_items'         => __('All Tour Type', 'wpbooking'),
                'parent_item'       => __('Parent Tour Type', 'wpbooking'),
                'parent_item_colon' => __('Parent Tour Type:', 'wpbooking'),
                'edit_item'         => __('Edit Tour Type', 'wpbooking'),
                'update_item'       => __('Update Tour Type', 'wpbooking'),
                'add_new_item'      => __('Add New Tour Type', 'wpbooking'),
                'new_item_name'     => __('New Tour Type Name', 'wpbooking'),
                'menu_name'         => __('Tour Type', 'wpbooking'),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => false,
                'query_var'         => true,
                'meta_box_cb'       => false,
                'rewrite'           => array('slug' => 'tour-type'),
            );
            register_taxonomy('wb_tour_type', array('wpbooking_service'), $args);
        }

        /**
         * Query Minimum Price for Tour
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $price
         * @param $post_id
         * @return $this
         */
        public function _edit_price($price,$post_id)
        {
            $calendar=WPBooking_Calendar_Model::inst();

            $query=$calendar->select('MIN(calendar_price) as min_price')->where(array(
                'post_id'=>$post_id,
                'status'=>'available',
                'calendar_price >'=>0,
                'start >='=>strtotime(date('d-m-Y'))

            ))->get(1)->row();

            if($query){
                $price=$query['min_price'];
            }

            return $price;
        }

        /**
         * Register metabox fields
         *
         * @since 1.0
         * @author dungdt
         */
        public function _register_meta_fields()
        {
            // Metabox
            $this->set_metabox(array(
                'general_tab'     => array(
                    'label'  => esc_html__('1. Basic Information', 'wpbooking'),
                    'fields' => array(
                        array(
                            'type' => 'open_section',
                        ),
                        array(
                            'label' => __("About Your Tour", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__('Basic information', 'wpbooking'),
                        ),
                        array(
                            'id'    => 'enable_property',
                            'label' => __("Enable Tour", 'wpbooking'),
                            'type'  => 'on-off',
                            'std'   => 'on',
                            'desc'  => esc_html__('Listing will appear in search results.', 'wpbooking'),
                        ),
                        array(
                            'id'    => 'tour_type',
                            'label' => __("Tour Type", 'wpbooking'),
                            'type'  => 'dropdown',
                            'taxonomy'=>'wb_tour_type',
                            'class' => 'small'
                        ),
                        array(
                            'id'    => 'star_rating',
                            'label' => __("Star Rating", 'wpbooking'),
                            'type'  => 'star-select',
                            'desc'  => esc_html__('Standard of tour from 1 to 5 star.', 'wpbooking'),
                            'class' => 'small'
                        ),
                        array(
                            'id'    => 'duration',
                            'label' => __("Duration", 'wpbooking'),
                            'type'  => 'text',
                            'placeholder'=>esc_html__('Example: 10 days','wpbooking'),
                            'class' => 'small'
                        ),
                        array(
                            'label'        => __('Contact Number', 'wpbooking'),
                            'id'           => 'contact_number',
                            'desc'         => esc_html__('The contact phone', 'wpbooking'),
                            'type'         => 'text',
                            'class'        => 'small',
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
                            'label' => __("Tour Destination", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label'           => __('Address', 'wpbooking'),
                            'id'              => 'address',
                            'type'            => 'address',
                            'container_class' => 'mb35',
                            'exclude'=>array('apt_unit')
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
                            'content' => esc_html__('Please make sure to enter your full address ', 'wpbooking')
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                            'prev' => false
                        ),

                    )
                ),
                'important_info' => array(
                    'label' => esc_html__('2. Important Info','wpbooking'),
                    'fields' => array(
                        array(
                            'type' => 'open_section'
                        ),
                        array(
                            'type' => 'texteditor',
                            'id' => 'tour_detail',
                            'label' => esc_html__('Details','wpbooking'),
                            'desc' => esc_html__('Enter details of your tour','wpbooking')
                        ),
                        array(
                            'type' => 'close_section'
                        ),
                        array(
                            'type' => 'open_section'
                        ),
                        array(
                            'label' => esc_html__('Itinerary','wpbooking'),
                            'id' => 'itinerary',
                            'type' => 'itinerary_tour',
                            'desc' => esc_html__('Add to the itinerary of the tour','wpbooking'),
                        ),
                        array(
                            'type' => 'close_section'
                        ),
                        array(
                            'type' => 'section_navigation',
                        ),

                    )
                ),
                'detail_tab'      => array(
                    'label'  => __('3. Booking Details', 'wpbooking'),
                    'fields' => array(
                        array('type' => 'open_section'),
                        array(
                            'label' => __("Pricing type", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label'  => esc_html__('Pricing Type', 'wpbooking'),
                            'type'   => 'dropdown',
                            'id'     => 'pricing_type',
                            'value'=>array(
                                'per_person'=>esc_html__('Per person','wpbooking'),
                                'per_unit'=>esc_html__('Per unit','wpbooking'),
                            ),
                            'class' => 'small'
                        ),
                        array(
                            'label'=>esc_html__('Maximum people per booking','wpbooking'),
                            'id'=>'max_guests',
                            'type'=>'number',
                            'condition'=>'pricing_type:is(per_person)',
                            'std'=>1,
                            'class' => 'small'
                        ),
                        array(
                            'label'=>esc_html__('Age Options','wpbooking'),
                            'desc'=>esc_html__('Provide your requirements for what age defines a child vs. adult.','wpbooking'),
                            'id'=>'age_options',
                            'type'=>'age_options',
                            'condition'=>'pricing_type:is(per_person)'
                        ),
                        array(
                            'label'=>esc_html__('This tour is available','wpbooking'),
                            'id'=>'property_available_for',
                            'type'=>'dropdown',
                            'value'=>array(
                                'forever'=>esc_html__('Forever','wpbooking'),
                                'specific_periods'=>esc_html__('For specific periods','wpbooking'),
                            ),
                            'class' => 'small'
                        ),

                        array(
                            'label' => __("Availability", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'type'  => 'calendar',
                            'id'=>'calendar',
                            'service_type'=>'tour'
                        ),
                        array('type' => 'close_section'),
                        array(
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'policies_tab'    => array(
                    'label'  => __('4. Policies & Checkout', 'wpbooking'),
                    'fields' => array(

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
                            'desc'  => esc_html__("Set your local VAT, so guests know what is included in the price of their stay.", "wpbooking")
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
                        array('type' => 'close_section'),

                        array('type' => 'open_section'),
                        array(
                            'label' => __("Term & condition", 'wpbooking'),
                            'type'  => 'title',
                            'desc'  => esc_html__("We will show this information in checkout step.", "wpbooking")
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
                    'label'  => __('5. Photos', 'wpbooking'),
                    'fields' => array(
                        array(
                            'label' => __("Pictures", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Gallery", 'wpbooking'),
                            'id'    => 'tour_gallery',
                            'type'  => 'gallery',
                            'desc'  => __('Picture recommendations', 'wpbooking')
                        ),

                        array(
                            'type'       => 'section_navigation',
                            'next_label' => esc_html__('Save', 'wpbooking')
                        ),
                    )
                ),

            ));
        }

        /**
         * Change Thumb Size of Gallery
         *
         * @since 1.0
         * @author dungdt
         *
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
         * Get Search Fields
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed|void
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
//                        "review_rate" => __("Review Rate", "wpbooking"),
                        "star_rating" => __("Star Of Tour", "wpbooking"),
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

            return $search_fields;
            // TODO: Implement get_search_fields() method.
        }


        /**
         * Get Available Days by Month, Years
         *
         * @since 1.0
         * @author dungdt
         *
         *
         * @param $month
         * @param $year
         * @return array
         */
        public function get_available_days($post_id,$month,$year){

            $calendar=WPBooking_Calendar_Model::inst();

            $start=strtotime(date('1-'.$month.'-'.$year));
            if($start<strtotime(date('d-m-Y'))) $start=strtotime(date('d-m-Y'));

            $query=$calendar->where('post_id',$post_id)
                        ->where('status','available')
                        ->where('calendar_price >',0)
                        ->where('start >',$start)
                        ->orderby('start','asc')
                        ->get(100)
                        ->result();
            return $query;
        }
        /**
         * Get Next Available 10 Month
         *
         * @since 1.0
         * @author dungdt
         *
         * @param bool $post_id
         * @return array
         */
        public function getNext10MonthAvailable($post_id=false)
        {
            if(!$post_id) $post_id=get_the_ID();

            $calendar=WPBooking_Calendar_Model::inst();

            global $wpdb;
            switch (get_post_meta($post_id,'pricing_type',true)){
                case "per_unit":
                    $from_query=$calendar->select($wpdb->prefix.'wpbooking_availability.id,calendar_maximum,(adult_number + children_number + infant_number) AS total_people,start,calendar_price')
                                        ->join('wpbooking_order',"wpbooking_order.post_id = wpbooking_availability.post_id and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')",'left')
                                        ->where(array(
                                            $wpdb->prefix.'wpbooking_availability.post_id'=>$post_id,
                                            $wpdb->prefix.'wpbooking_availability.status'=>'available',
                                            'calendar_price >'=>0,
                                            'start >='=>strtotime(date('d-m-Y')),
                                        ))
                                        ->groupby($wpdb->prefix.'wpbooking_availability.id')
                                        ->having(' total_people IS NULL OR total_people < calendar_maximum')
                                        ->_get_query();
                    $calendar->_clear_query();

                    $query=$wpdb->get_results("
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
                    
                    ",ARRAY_A );
                    break;
                case "per_person":
                    $from_query=$calendar->select($wpdb->prefix.'wpbooking_availability.id,calendar_maximum,(adult_number + children_number + infant_number) AS total_people,start,calendar_price')
                        ->join('wpbooking_order',"wpbooking_order.post_id = wpbooking_availability.post_id and wpbooking_order. STATUS NOT IN ('cancelled','refunded','trash','payment_failed')",'left')
                        ->where(array(
                            $wpdb->prefix.'wpbooking_availability.post_id'=>$post_id,
                            $wpdb->prefix.'wpbooking_availability.status'=>'available',
                            'calendar_price >'=>0,
                            'start >='=>strtotime(date('d-m-Y')),
                        ))
                        ->groupby($wpdb->prefix.'wpbooking_availability.id')
                        ->having(' total_people IS NULL OR total_people < calendar_maximum')
                        ->_get_query();
                    $calendar->_clear_query();
                default:
                    break;
            }
            $res=array();

            if(!empty($query)){
                foreach($query as $item){
                    $res[$item['month_year']]=array(
                        'days'=>$this->get_available_days($post_id,date('m',$item['start']),date('Y',$item['start'])),
                        'label'=>date_i18n('M Y',$item['start'])
                    );
                }
            }

            return $res;
        }

        static function inst()
        {
            if (!self::$_inst)
                self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Tour_Service_Type::inst();
}