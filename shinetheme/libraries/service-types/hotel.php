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

            add_action('init',array($this,'_add_init_action'));
        }


        public function _add_init_action(){
            // Metabox
            $this->set_metabox(array(
                'general_tab'=>array(
                    'label'=>esc_html__('1. About','wpbooking'),
                    'fields'=>array(
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("About Your Hotel", 'wpbooking'),
                            'type'  => 'title',
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
                            'type'  => 'text',
                            'value' => '1',
                            'class' => 'small'
                        ),
                        array(
                            'label' => __('Website', 'wpbooking'),
                            'id'    => 'website',
                            'type'  => 'text',
                            'class' => 'small'
                        ),
                        array(
                            'type'  => 'close_section',
                        ),
                        array(
                            'type'  => 'open_section',
                        ),
                        array(
                            'label' => __("Hotel Location", 'wpbooking'),
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
                            'type'  => 'close_section',
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
                            'type' => 'section_navigation',
                            'prev' => FALSE
                        ),

                    )
                ),
                'detail_tab'=>array(
                    'label' => __('2. Details', 'wpbooking'),
                    'fields'=>array(

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
                            'label' => __("Extra Services:", 'wpbooking'),
                            'type'  => 'title',
                        ),
                        array(
                            'label' => __("Extra Services:", 'wpbooking'),
                            'type'  => 'extra_services',
                            'id'    => 'extra_services'
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
                'photo_tab'=>array(
                    'label' => __('4. Photos /', 'wpbooking'),
                    'fields'=>array(
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
                            'type' => 'section_navigation',
                        ),
                    )
                ),
                'calendar_tab'=>array(
                    'label' => __('5. Calendar', 'wpbooking'),
                    'fields'=>array(

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
        }
        static function inst()
        {
            if (!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_Hotel_Service_Type::inst();
}