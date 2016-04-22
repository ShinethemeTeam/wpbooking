<?php
$config['settings'] = array(
	"general"      => array(
		"name"     => __('General', 'traveler-booking'),
		"sections" => array(
			"general_option" => array(
				'id'     => 'general_option',
				'label'  => __('General Options', 'traveler-booking'),
				'fields' => array(

					array(
						'id'    => 'currency',
						'label' => __('Currency', 'traveler-booking'),
						'desc'  => __('Currency', 'traveler-booking'),
						'type'  => 'list-item',
						'std'   => '',
						'value' => array(
							array(
								'id'    => 'currency',
								'label' => __('Currency', 'traveler-booking'),
								'type'  => 'dropdown',
								'value'   => apply_filters('traveler_get_all_currency', array()),
							),
							array(
								'id'    => 'symbol',
								'label' => __('Symbol', 'traveler-booking'),
								'desc'  => __('Symbol of currency. Example: $', 'traveler-booking'),
								'type'  => 'text',
								'std'   => ''
							),
							array(
								'id'    => 'position',
								'label' => __('Position', 'traveler-booking'),
								'desc'  => __('Position of Symbol', 'traveler-booking'),
								'type'  => 'dropdown',
								'std'   => 'left',
								'value' => array(
									'left'             => __('$99', "traveler-booking"),
									'right'            => __('99$', "traveler-booking"),
									'left_with_space'  => __('$ 99', "traveler-booking"),
									'right_with_space' => __('99 $', "traveler-booking"),
								)
							),
							array(
								'id'    => 'thousand_sep',
								'label' => __('Thousand Separator', 'traveler-booking'),
								'desc'  => __('Thousand Separator', 'traveler-booking'),
								'type'  => 'text',
								'std'   => ',',
							),
							array(
								'id'    => 'decimal_sep',
								'label' => __('Decimal Separator', 'traveler-booking'),
								'desc'  => __('Decimal Separator', 'traveler-booking'),
								'type'  => 'text',
								'std'   => '.',
							),
							array(
								'id'    => 'decimal',
								'label' => __('Decimal', 'traveler-booking'),
								'desc'  => __('Decimal', 'traveler-booking'),
								'type'  => 'number',
								'std'   => 2,
								'attr'  => array(
									'min' => 0,
									'max' => 3
								)
							),
							array(
								'id'    => 'rate',
								'label' => __('Exchange Rate', 'traveler-booking'),
								'desc'  => __('Exchange Rate vs Main Currency', 'traveler-booking'),
								'type'  => 'text',
								'value'=>1
							),

						)
					),
				)
			),
		),
	),
	'booking'      => array(
		'name'     => __("Booking", 'traveler-booking'),
		'sections' => array(
			'general_booking' => array(
				'id'     => 'general_booking',
				'label'  => __("General Options", 'traveler-booking'),
				'fields' => array(

					array(
						'id'        => 'cart_page',
						'label'     => __("Cart Page", 'traveler-booking'),
						'type'      => 'page-select',
					),
					array(
						'id'        => 'checkout_page',
						'label'     => __("Checkout Page", 'traveler-booking'),
						'type'      => 'page-select',
					),
					array(
						'id'        => 'checkout_form',
						'label'     => __("Checkout Form", 'traveler-booking'),
						'type'      => 'post-select',
						'post_type' => array('traveler_form')
					),
					array(
						'id'        => 'allow_guest_checkout',
						'label'     => __("Allow Guest Checkout?", 'traveler-booking'),
						'type'      => 'checkbox',
					),
				)
			)

		)
	),
	'email'      => array(
		'name'     => __("Email", 'traveler-booking'),
		'sections' => array(
			'general_booking' => array(
				'id'     => 'general_booking',
				'label'  => __("General Options", 'traveler-booking'),
				'fields' => array(

					array(
						'id'        => 'email_from',
						'label'     => __("Email From Name", 'traveler-booking'),
						'type'      => 'text',
						'std'		=>__("Traveler Booking Plugin",'traveler-booking')
					),
					array(
						'id'        => 'email_from_address',
						'label'     => __("Email From Address", 'traveler-booking'),
						'type'      => 'text',
						'placeholder'=>'contact@domain.com'
					),
				)
			),
			'confirm_email' => array(
				'id'     => 'confirm_email',
				'label'  => __("Confirmation Email", 'traveler-booking'),
				'fields' => array(

					array(
						'id'        => 'confirm_to_customer',
						'label'     => __("For Customer", 'traveler-booking'),
						'type'      => 'texteditor',
						'desc'=>traveler_admin_load_view('email/document')
					),
					array(
						'id'        => 'confirm_to_partner',
						'label'     => __("For Partner", 'traveler-booking'),
						'type'      => 'texteditor',
					),
				)
			),
			'booking_email' => array(
				'id'     => 'booking_email',
				'label'  => __("Booking Email", 'traveler-booking'),
				'fields' => array(

					array(
						'id'        => 'email_to_customer',
						'label'     => __("For Customer", 'traveler-booking'),
						'type'      => 'texteditor',
						'desc'=>traveler_admin_load_view('email/document')
					),
					array(
						'id'        => 'email_to_partner',
						'label'     => __("For Item's Host", 'traveler-booking'),
						'type'      => 'texteditor',
						'desc'=>traveler_admin_load_view('email/document')
					),
				)
			),

		)
	),
);
