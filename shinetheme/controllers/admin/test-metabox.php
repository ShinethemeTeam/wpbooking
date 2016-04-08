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
            'title'    => __('Information', 'traveler-booking'),
            'desc'     => '',
            'pages'    => array('post','traveler_service'),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => array(
                array(
                    'label' => __('General', 'traveler-booking'),
                    'id' => 'general_tab',
                    'type' => 'tab',
                ),
                array(
                    'label' => __('Location', 'traveler-booking'),
                    'id' => 'location',
                    'type' => 'location'
                ),
                array(
                    'label' => __('Map', 'traveler-booking'),
                    'id' => 'gmap',
                    'type' => 'gmap'
                ),
                array(
                    'label' => __('Gallery', 'traveler-booking'),
                    'id' => 'gallery',
                    'type' => 'gallery'
                ),
                array(
                    'label' => __('Accommodates', 'traveler-booking'),
                    'id' => 'accommodates',
                    'type' => 'text'
                ),
                array(
                    'label' => __('Bathrooms', 'traveler-booking'),
                    'id' => 'bathrooms',
                    'type' => 'text'
                ),
                array(
                    'label' => __('Check in Time', 'traveler-booking'),
                    'id' => 'check_in_time',
                    'type' => 'text',
                    'class' => 'time-picker'
                ),
                array(
                    'label' => __('Check out Time', 'traveler-booking'),
                    'id' => 'check_out_time',
                    'type' => 'text',
                    'class' => 'time-picker'
                ),
                array(
                    'label' => __('No. Adult', 'traveler-booking'),
                    'id' => 'number_adult',
                    'type' => 'number',
                ),
                array(
                    'label' => __('No. Children', 'traveler-booking'),
                    'id' => 'number_children',
                    'type' => 'number',
                ),
                array(
                    'label' => __('External Booking?', 'traveler-booking'),
                    'id' => 'external_booking',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('External URL', 'traveler-booking'),
                    'id' => 'external_url',
                    'type' => 'text',
                    'condition' => 'external_booking:is(yes)'
                ),
                array(
                    'label' => __('Instant Booking?', 'traveler-booking'),
                    'id' => 'instant_booking',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('Day not available from - to days', 'traveler-booking'),
                    'id' => 'day_not_available',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Preparetions', 'traveler-booking'),
                    'id' => 'preparetions',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Pricing', 'traveler-booking'),
                    'id'    => 'price_tab',
                    'type'  => 'tab',
                ),
                array(
                    'label' => __('Base Price', 'traveler-booking'),
                    'id' => 'price',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Currency', 'traveler-booking'),
                    'id' => 'currency',
                    'type' => 'dropdown',
                    'std' => 'usd',
                    'value' => array(
                        'usd' => __('USD - $', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('Long Terms?', 'traveler-booking'),
                    'id' => 'long_terms',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('Weekly Discount', 'traveler-booking'),
                    'id' => 'weekly_discount',
                    'type' => 'text',
                    'condition' => 'long_terms:is(yes)'
                ),
                array(
                    'label' => __('Monthly Discount', 'traveler-booking'),
                    'id' => 'monthly_discount',
                    'type' => 'text',
                    'condition' => 'long_terms:is(yes)'
                ),
                array(
                    'label' => __('Extra Price', 'traveler-booking'),
                    'id' => 'extra_price',
                    'type' => 'list-item',
                    'value' => array(
                        array(
                            'id'    => 'name',
                            'label' => __('Name Of Item', 'traveler-booking'),
                            'type'  => 'text',
                        ),array(
                            'id'    => 'price',
                            'label' => __('Price', 'traveler-booking'),
                            'type'  => 'text',
                        ),
                        array(
                            'label' => __('Type', 'traveler-booking'),
                            'id' => 'currency',
                            'type' => 'dropdown',
                            'value' => array(
                                'fixed' => __('Fixed', 'traveler-booking'),
                                'per_day' => __('per Day', 'traveler-booking'),
                                'per_night' => __('per Night', 'traveler-booking'),
                            ),
                        ),
                    )
                ),
                array(
                    'label' => __('Price Type', 'traveler-booking'),
                    'id' => 'price_type',
                    'type' => 'dropdown',
                    'value' => array(
                        'fixed' => __('Fixed', 'traveler-booking'),
                        'per_night' => __('per Night', 'traveler-booking'),
                    ),
                ),
                array(
                    'label' => __('Deposit?', 'traveler-booking'),
                    'id' => 'deposit',
                    'type' => 'dropdown',
                    'value' => array(
                        'none' => __('None', 'traveler-booking'),
                        'percent' => __('Percent', 'traveler-booking'),
                        'fixed' => __('Fixed', 'traveler-booking'),
                    ),
                ),
                array(
                    'label' => __('Amount', 'traveler-booking'),
                    'id' => 'amount',
                    'type' => 'text',
                ),
                array(
                    'label' => __('Calendar', 'traveler-booking'),
                    'id'    => 'calendar_tab',
                    'type'  => 'tab',
                ),
                array(
                    'label' => __('Availability', 'traveler-booking'),
                    'id'    => 'availability',
                    'type'  => 'calendar',
                    'service_type' => 'room'
                ),
                array(
                    'label' => __('Gateways', 'traveler-booking'),
                    'id'    => 'gateways',
                    'type'  => 'tab',
                ),
                array(
                    'label' => __('Cheque', 'traveler-booking'),
                    'id' => 'gateway_cheque',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('Paypal', 'traveler-booking'),
                    'id' => 'gateway_paypal',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),

                array(
                    'label' => __('Stripe', 'traveler-booking'),
                    'id' => 'gateway_stripe',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
                array(
                    'label' => __('Payfast', 'traveler-booking'),
                    'id' => 'gateway_payfast',
                    'type' => 'checkbox',
                    'value' => array(
                        'yes' => __('Yes', 'traveler-booking')
                    ),
                ),
            )
		);

		$metabox->register_meta_box( $settings );
	}
}
new Test_Metabox();