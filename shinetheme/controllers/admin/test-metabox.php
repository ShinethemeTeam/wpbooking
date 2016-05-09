<?php 
/**
*@since 1.0.0
*	Test metabox
**/
class Test_Metabox extends Traveler_Controller{

	public function __construct(){
		parent::__construct();

		$metabox = new Traveler_Metabox();

		$settings = array(
			'id'       => 'st_post_metabox',
            'title'    => __('Information', 'wpbooking'),
            'desc'     => '',
            'pages'    => array('post','traveler_service'),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => array(
                array(
                    'label' => __('General', 'wpbooking'),
                    'id' => 'general_tab',
                    'type' => 'tab',
                ),array(
                     'id'=>'service_type',
                     'label'=>__("Service Type",'wpbooking'),
                     'location'=>'hndle-tag',
                    'type'=>'service-type-select',
                 ),

                array(
                    'label' => __('Location', 'wpbooking'),
                    'id' => 'location',
                    'type' => 'location'
                ),
                array(
                    'label' => __('Map', 'wpbooking'),
                    'id' => 'gmap',
                    'type' => 'gmap'
                ),
                array(
                    'label' => __('Gallery', 'wpbooking'),
                    'id' => 'gallery',
                    'type' => 'gallery'
                ),
                array(
                    'label' => __('Accommodates', 'wpbooking'),
                    'id' => 'accommodates',
                    'type' => 'text'
                ),
                array(
                    'label' => __('Bathrooms', 'wpbooking'),
                    'id' => 'bathrooms',
                    'type' => 'text'
                ),
                array(
                    'label' => __('Check-in Time', 'wpbooking'),
                    'id' => 'check_in_time',
                    'type' => 'text',
                    'class' => 'time-picker'
                ),
                array(
                    'label' => __('Check-out Time', 'wpbooking'),
                    'id' => 'check_out_time',
                    'type' => 'text',
                    'class' => 'time-picker'
                ),
                array(
                    'label' => __('No. Adult', 'wpbooking'),
                    'id' => 'number_adult',
                    'type' => 'number',
                ),
                array(
                    'label' => __('No. Children', 'wpbooking'),
                    'id' => 'number_children',
                    'type' => 'number',
                ),
                array(
                    'label' => __('External Booking?', 'wpbooking'),
                    'id' => 'external_booking',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'wpbooking')
                    ),
                ),
                array(
                    'label' => __('External URL', 'wpbooking'),
                    'id' => 'external_url',
                    'type' => 'text',
                    'condition' => 'external_booking:is(yes)'
                ),
//                array(
//                    'label' => __('Instant Booking?', 'wpbooking'),
//                    'id' => 'instant_booking',
//                    'type' => 'checkbox',
//                    'value' => array(
//                        'yes' => __('Yes', 'wpbooking')
//                    ),
//                ),
                array(
                    'label' => __('Day not available from - to days', 'wpbooking'),
                    'id' => 'day_not_available',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Preparetions', 'wpbooking'),
                    'id' => 'preparetions',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Amelities', 'wpbooking'),
                    'id'    => 'amelities_tab',
                    'type'  => 'tab',
                ),
                array(
                    'label' => __('Taxonomy', 'wpbooking'),
                    'id' => 'taxonomy',
                    'type' => 'taxonomies',
                ),
                array(
                    'label' => __('Pricing', 'wpbooking'),
                    'id'    => 'price_tab',
                    'type'  => 'tab',
                ),

                array(
                    'label' => __('Base Price', 'wpbooking'),
                    'id' => 'price',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Currency', 'wpbooking'),
                    'id' => 'currency',
                    'type' => 'dropdown',
                    'std' => 'usd',
                    'value' =>Traveler_Currency::get_added_currency_array()
                ),
				array(
					'label' => __('Price Type', 'wpbooking'),
					'id' => 'price_type',
					'type' => 'dropdown',
					'value' => array(
						'fixed' => __('Fixed', 'wpbooking'),
						'per_night' => __('Per Night', 'wpbooking'),
					),
				),
                array(
                    'label' => __('Long Terms?', 'wpbooking'),
                    'id' => 'long_terms',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'wpbooking')
                    ),
                ),
                array(
                    'label' => __('Weekly Discount', 'wpbooking'),
                    'id' => 'weekly_discount',
                    'type' => 'text',
                    'condition' => 'long_terms:is(yes)'
                ),
                array(
                    'label' => __('Monthly Discount', 'wpbooking'),
                    'id' => 'monthly_discount',
                    'type' => 'text',
                    'condition' => 'long_terms:is(yes)'
                ),
                array(
                    'label' => __('Extra Price', 'wpbooking'),
                    'id' => 'extra_price',
                    'type' => 'list-item',
                    'value' => array(
                        array(
                            'id'    => 'name',
                            'label' => __('Name Of Item', 'wpbooking'),
                            'type'  => 'text',
                        ),array(
                            'id'    => 'price',
                            'label' => __('Price', 'wpbooking'),
                            'type'  => 'text',
                        ),
                        array(
                            'label' => __('Type', 'wpbooking'),
                            'id' => 'currency',
                            'type' => 'dropdown',
                            'value' => array(
                                'fixed' => __('Fixed', 'wpbooking'),
                                'per_night' => __('Per Night', 'wpbooking'),
                            ),
                        ),
                    )
                ),
                array(
                    'label' => __('Deposit?', 'wpbooking'),
                    'id' => 'deposit',
                    'type' => 'dropdown',
                    'value' => array(
                        'none' => __('None', 'wpbooking'),
                        'percent' => __('Percent', 'wpbooking'),
                        'fixed' => __('Fixed', 'wpbooking'),
                    ),
                ),
                array(
                    'label' => __('Amount', 'wpbooking'),
                    'id' => 'amount',
                    'type' => 'text',
					''
                ),
                array(
                    'label' => __('Calendar', 'wpbooking'),
                    'id'    => 'calendar_tab',
                    'type'  => 'tab',
                ),
                array(
                    'label' => __('Availability', 'wpbooking'),
                    'id'    => 'availability',
                    'type'  => 'calendar',
                    'service_type' => 'room'
                ),

            )
		);

		$metabox->register_meta_box( $settings );
	}
}
new Test_Metabox();