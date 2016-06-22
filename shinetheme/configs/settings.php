<?php
$config['settings'] = array(
	"general"      => array(
		"name"     => __('General', 'wpbooking'),
		"sections" => array(
			"general_option" => array(
				'id'     => 'general_option',
				'label'  => __('General Options', 'wpbooking'),
				'fields' => array(

					array(
						'id'    => 'currency',
						'label' => __('Currency', 'wpbooking'),
						'desc'  => __('Currency', 'wpbooking'),
						'type'  => 'list-item',
						'std'   => '',
						'value' => array(
							array(
								'id'    => 'currency',
								'label' => __('Currency', 'wpbooking'),
								'type'  => 'dropdown',
								'value'   => apply_filters('wpbooking_get_all_currency', array()),
							),
							array(
								'id'    => 'symbol',
								'label' => __('Symbol', 'wpbooking'),
								'desc'  => __('Symbol of currency. Example: $', 'wpbooking'),
								'type'  => 'text',
								'std'   => ''
							),
							array(
								'id'    => 'position',
								'label' => __('Position', 'wpbooking'),
								'desc'  => __('Position of Symbol', 'wpbooking'),
								'type'  => 'dropdown',
								'std'   => 'left',
								'value' => array(
									'left'             => __('$99', "wpbooking"),
									'right'            => __('99$', "wpbooking"),
									'left_with_space'  => __('$ 99', "wpbooking"),
									'right_with_space' => __('99 $', "wpbooking"),
								)
							),
							array(
								'id'    => 'thousand_sep',
								'label' => __('Thousand Separator', 'wpbooking'),
								'desc'  => __('Thousand Separator', 'wpbooking'),
								'type'  => 'text',
								'std'   => ',',
							),
							array(
								'id'    => 'decimal_sep',
								'label' => __('Decimal Separator', 'wpbooking'),
								'desc'  => __('Decimal Separator', 'wpbooking'),
								'type'  => 'text',
								'std'   => '.',
							),
							array(
								'id'    => 'decimal',
								'label' => __('Decimal', 'wpbooking'),
								'desc'  => __('Decimal', 'wpbooking'),
								'type'  => 'number',
								'std'   => 2,
								'attr'  => array(
									'min' => 0,
									'max' => 3
								)
							),
							array(
								'id'    => 'rate',
								'label' => __('Exchange Rate', 'wpbooking'),
								'desc'  => __('Exchange Rate vs Main Currency', 'wpbooking'),
								'type'  => 'text',
								'value'=>1
							),

						)
					),
					array(
						'label'=>esc_html__('Accounts','wpbooking'),
						'type'=>'title'
					),
					array(
						'label'=>esc_html__('My Account Page','wpbooking'),
						'type'=>'page-select',
						'id'=>'myaccount-page'
					),
				)
			),
		),
	),
	'booking'      => array(
		'name'     => __("Booking", 'wpbooking'),
		'sections' => array(
			'general_booking' => array(
				'id'     => 'general_booking',
				'label'  => __("General Options", 'wpbooking'),
				'fields' => array(

					array(
						'id'        => 'cart_page',
						'label'     => __("Cart Page", 'wpbooking'),
						'type'      => 'page-select',
					),
					array(
						'id'        => 'checkout_page',
						'label'     => __("Checkout Page", 'wpbooking'),
						'type'      => 'page-select',
					),
					array(
						'id'        => 'checkout_form',
						'label'     => __("Checkout Form", 'wpbooking'),
						'type'      => 'post-select',
						'post_type' => array('wpbooking_form')
					),
					array(
						'id'        => 'allow_guest_checkout',
						'label'     => __("Allow Guest Checkout?", 'wpbooking'),
						'type'      => 'checkbox',
					),
				)
			)

		)
	),
	'email'      => array(
		'name'     => __("Email", 'wpbooking'),
		'sections' => array(
			'general_booking' => array(
				'id'     => 'general_booking',
				'label'  => __("General Options", 'wpbooking'),
				'fields' => array(

					array(
						'id'        => 'email_from',
						'label'     => __("Email From Name", 'wpbooking'),
						'type'      => 'text',
						'std'		=>__("WPBooking Plugin",'wpbooking')
					),
					array(
						'id'        => 'email_from_address',
						'label'     => __("Email From Address", 'wpbooking'),
						'type'      => 'text',
						'placeholder'=>'contact@domain.com'
					),
				)
			),
//			'confirm_email' => array(
//				'id'     => 'confirm_email',
//				'label'  => __("Confirmation Email", 'wpbooking'),
//				'fields' => array(
//
//					array(
//						'id'        => 'confirm_to_customer',
//						'label'     => __("For Customer", 'wpbooking'),
//						'type'      => 'texteditor',
//						'desc'=>wpbooking_admin_load_view('email/document')
//					),
//					array(
//						'id'        => 'confirm_to_partner',
//						'label'     => __("For Partner", 'wpbooking'),
//						'type'      => 'texteditor',
//					),
//				)
//			),
			'booking_email' => array(
				'id'     => 'booking_email',
				'label'  => __("Email Template", 'wpbooking'),
				'fields' => array(

					array(
						'id'        => 'email_to_customer',
						'label'     => __("For Customer", 'wpbooking'),
						'type'      => 'texteditor',
						'desc'=>wpbooking_admin_load_view('email/document'),
						'editor_args'=>array(
							'tinymce'=>FALSE
						)
					),
					array(
						'id'        => 'email_to_partner',
						'label'     => __("For Author and Administrator", 'wpbooking'),
						'type'      => 'texteditor',
						'desc'=>wpbooking_admin_load_view('email/document'),
						'editor_args'=>array(
							'tinymce'=>FALSE
						)
					),
					array(
						'id'        => 'email_stylesheet',
						'label'     => __("Email CSS Code", 'wpbooking'),
						'type'      => 'ace-editor',
						'desc'=>esc_html__('We will use this to transmogrifies your Email HTML by parsing the CSS and inserting the CSS definitions into tags within your Email HTML based on the CSS selectors'),
					),
				)
			),

		)
	),
);
