<?php
    $config[ 'settings' ] = [
        "general" => [
            "name"     => esc_html__( 'General', 'wp-booking-management-system' ),
            "sections" => [
                "general_option" => [
                    'id'     => 'general_option',
                    'label'  => esc_html__( 'General Options', 'wp-booking-management-system' ),
                    'fields' => [
                        [
                            'id'    => 'currency',
                            'label' => esc_html__( 'Currency Options', 'wp-booking-management-system' ),
                            'desc'  => esc_html__( 'Currency', 'wp-booking-management-system' ),
                            'type'  => 'currency',
                            'std'   => '',
                            'value' => [
                                [
                                    'id'    => 'currency',
                                    'label' => esc_html__( 'Currency', 'wp-booking-management-system' ),
                                    'type'  => 'dropdown',
                                    'value' => apply_filters( 'wpbooking_get_all_currency', [] ),
                                ],
                                [
                                    'id'    => 'symbol',
                                    'label' => esc_html__( 'Symbol', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Symbol of currency. For example: $', 'wp-booking-management-system' ),
                                    'type'  => 'text',
                                    'std'   => ''
                                ],
                                [
                                    'id'    => 'position',
                                    'label' => esc_html__( 'Position', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Position of Symbol', 'wp-booking-management-system' ),
                                    'type'  => 'dropdown',
                                    'std'   => 'left',
                                    'value' => [
                                        'left'             => esc_html__( '$99', "wp-booking-management-system" ),
                                        'right'            => esc_html__( '99$', "wp-booking-management-system" ),
                                        'left_with_space'  => esc_html__( '$ 99', "wp-booking-management-system" ),
                                        'right_with_space' => esc_html__( '99 $', "wp-booking-management-system" ),
                                    ]
                                ],
                                [
                                    'id'    => 'thousand_sep',
                                    'label' => esc_html__( 'Thousand Separator', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Thousand Separator', 'wp-booking-management-system' ),
                                    'type'  => 'text',
                                    'std'   => ',',
                                ],
                                [
                                    'id'    => 'decimal_sep',
                                    'label' => esc_html__( 'Decimal Separator', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Decimal Separator', 'wp-booking-management-system' ),
                                    'type'  => 'text',
                                    'std'   => '.',
                                ],
                                [
                                    'id'    => 'decimal',
                                    'label' => esc_html__( 'Decimal', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Decimal', 'wp-booking-management-system' ),
                                    'type'  => 'number',
                                    'std'   => 2,
                                    'attr'  => [
                                        'min' => 0,
                                        'max' => 3
                                    ]
                                ],
                                [
                                    'id'    => 'rate',
                                    'label' => esc_html__( 'Exchange Rate', 'wp-booking-management-system' ),
                                    'desc'  => esc_html__( 'Exchange Rate vs Main Currency', 'wp-booking-management-system' ),
                                    'type'  => 'text',
                                    'value' => 1
                                ],

                            ]
                        ],
                        [
                            'label' => esc_html__( 'Archive Page', 'wp-booking-management-system' ),
                            'type'  => 'page-select',
                            'id'    => 'archive-page'
                        ],
                        [
                            'label' => esc_html__( 'Term & Condition Page', 'wp-booking-management-system' ),
                            'type'  => 'page-select',
                            'id'    => 'term-page'
                        ],
                        [
                            'label' => esc_html__( 'Accounts', 'wp-booking-management-system' ),
                            'type'  => 'title'
                        ],
                        [
                            'label' => esc_html__( 'My Account Page', 'wp-booking-management-system' ),
                            'type'  => 'page-select',
                            'id'    => 'myaccount-page'
                        ],
                        [
                            'label' => esc_html__( 'Google Map', 'wp-booking-management-system' ),
                            'type'  => 'title'
                        ],
                        [
                            'id'    => 'google_api_key',
                            'label' => esc_html__( "Google API key", 'wp-booking-management-system' ),
                            'type'  => 'text',
                            'desc'  => sprintf( esc_html__( 'You can get Google API %s', 'wp-booking-management-system' ), '<a href="' . esc_url( 'https://developers.google.com/maps/documentation/javascript/get-api-key' ) . '">here</a>' ),
                        ],
                    ]
                ],
            ],
        ],
        'booking' => [
            'name'     => esc_html__( "Booking", 'wp-booking-management-system' ),
            'sections' => [
                'general_booking' => [
                    'id'     => 'general_booking',
                    'label'  => esc_html__( "General Options", 'wp-booking-management-system' ),
                    'fields' => [

                        [
                            'id'    => 'checkout_page',
                            'label' => esc_html__( "Checkout Page", 'wp-booking-management-system' ),
                            'type'  => 'page-select',
                        ],
                        [
                            'id'    => 'allow_guest_checkout',
                            'label' => esc_html__( "Allow Guest to Checkout?", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                        ],
                        [
                            'id'    => 'allow_passenger_information_checkout',
                            'label' => esc_html__( "Passengers information on the checkout form", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                        ],
                        [
                            'label' => esc_html__( 'Captcha Google', 'wp-booking-management-system' ),
                            'type'  => 'title'
                        ],
                        [
                            'id'    => 'allow_captcha_google_checkout',
                            'label' => esc_html__( "Allow Captcha Google to be Checked out", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                            'desc'  => wp_kses( __( 'To use this feature, you must create a google key. See more at <a href="https://www.google.com/recaptcha/intro/android.html" target="_blank">Google Captcha</a>', 'wp-booking-management-system' ), [ 'a' => [ 'href' => [], 'target' => [] ] ] )
                        ],
                        [
                            'id'          => 'google_key_captcha',
                            'label'       => esc_html__( "Google key", 'wp-booking-management-system' ),
                            'type'        => 'text',
                            'placeholder' => '',
                            'condition' => 'allow_captcha_google_checkout:is(1)'
                        ],
                        [
                            'id'          => 'google_secret_key_captcha',
                            'label'       => esc_html__( "Google secret key", 'wp-booking-management-system' ),
                            'type'        => 'text',
                            'placeholder' => '',
                            'condition' => 'allow_captcha_google_checkout:is(1)'
                        ],
                    ]
                ]

            ]
        ],
        'email'   => [
            'name'     => esc_html__( "Email", 'wp-booking-management-system' ),
            'sections' => [
                'general_booking' => [
                    'id'     => 'general_booking',
                    'label'  => esc_html__( "General Options", 'wp-booking-management-system' ),
                    'fields' => [

                        [
                            'id'    => 'email_from',
                            'label' => esc_html__( "Email From Name", 'wp-booking-management-system' ),
                            'type'  => 'text',
                            'std'   => esc_html__( "WPBooking Plugin", 'wp-booking-management-system' )
                        ],
                        [
                            'id'          => 'email_from_address',
                            'label'       => esc_html__( "Email From Address", 'wp-booking-management-system' ),
                            'type'        => 'text',
                            'placeholder' => 'no-reply@domain.com'
                        ],
                        [
                            'id'          => 'system_email',
                            'label'       => esc_html__( "Email  System to get Booking, Registration Notifications...etc", 'wp-booking-management-system' ),
                            'type'        => 'text',
                            'placeholder' => 'system@domain.com'
                        ],
                        [
                            'id'          => 'email_header',
                            'label'       => esc_html__( "Email Header", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                        ],
                        [
                            'id'          => 'email_footer',
                            'label'       => esc_html__( "Email Footer", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                        ],

                        [
                            'id'    => 'email_stylesheet',
                            'label' => esc_html__( "Email CSS Code", 'wp-booking-management-system' ),
                            'type'  => 'ace-editor',
                            'desc'  => esc_html__( 'We will use this to transmogrify your Email HTML by parsing the CSS and inserting the CSS definitions into tags within your Email HTML based on the CSS selectors', 'wp-booking-management-system' ),
                        ],
                    ]
                ],
                'booking_email'   => [
                    'id'     => 'booking_email',
                    'label'  => esc_html__( "Booking Email", 'wp-booking-management-system' ),
                    'fields' => [

                        [
                            'id'    => 'on_booking_email_customer',
                            'label' => esc_html__( "Enable Email To Customer", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'email_to_customer',
                            'label'       => esc_html__( "For Customer", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/document' ),
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_booking_email_preview', 'email' => 'email_to_customer' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wp-booking-management-system' ) . '</a>',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'condition'   => 'on_booking_email_customer:is(1)'
                        ],

                        [
                            'id'    => 'on_booking_email_admin',
                            'label' => esc_html__( "Enable Email To Admin", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'email_to_admin',
                            'label'       => esc_html__( "For Administrator", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/document' ),
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_booking_email_preview', 'email' => 'email_to_admin' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wp-booking-management-system' ) . '</a>',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'condition'   => 'on_booking_email_admin:is(1)'
                        ],
                    ]
                ],
                'partner_email'   => [
                    'id'     => 'partner_email',
                    'label'  => esc_html__( "Registration Email", 'wp-booking-management-system' ),
                    'fields' => [

                        [
                            'type'  => 'title',
                            'label' => esc_html__( 'Customer Registration', 'wp-booking-management-system' )
                        ],
                        [
                            'id'    => 'on_registration_email_customer',
                            'label' => esc_html__( "Enable Email To Customer", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'registration_email_customer',
                            'label'       => esc_html__( "Email To Customer", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/registration_document' ),
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_register_email_preview', 'email' => 'registration_email_customer' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wp-booking-management-system' ) . '</a>',
                            'condition'   => 'on_registration_email_customer:is(1)'
                        ],
                        [
                            'id'    => 'on_registration_email_admin',
                            'label' => esc_html__( "Enable Email To Admin", 'wp-booking-management-system' ),
                            'type'  => 'checkbox',
                        ],
                        [
                            'id'          => 'registration_email_admin',
                            'label'       => esc_html__( "Email To Admin", 'wp-booking-management-system' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/registration_document' ),
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_register_email_preview', 'email' => 'registration_email_admin' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wp-booking-management-system' ) . '</a>',

                            'condition' => 'on_registration_email_admin:is(1)'
                        ],
                        [
                            'type' => 'hr',
                        ],
                    ]
                ]

            ]
        ],
    ];
