<?php
    $config[ 'settings' ] = [
        "general" => [
            "name"     => esc_html__( 'General', 'wpbooking' ),
            "sections" => [
                "general_option" => [
                    'id'     => 'general_option',
                    'label'  => esc_html__( 'General Options', 'wpbooking' ),
                    'fields' => [
                        [
                            'id'    => 'currency',
                            'label' => esc_html__( 'Currency Options', 'wpbooking' ),
                            'desc'  => esc_html__( 'Currency', 'wpbooking' ),
                            'type'  => 'currency',
                            'std'   => '',
                            'value' => [
                                [
                                    'id'    => 'currency',
                                    'label' => esc_html__( 'Currency', 'wpbooking' ),
                                    'type'  => 'dropdown',
                                    'value' => apply_filters( 'wpbooking_get_all_currency', [] ),
                                ],
                                [
                                    'id'    => 'symbol',
                                    'label' => esc_html__( 'Symbol', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Symbol of currency. For example: $', 'wpbooking' ),
                                    'type'  => 'text',
                                    'std'   => ''
                                ],
                                [
                                    'id'    => 'position',
                                    'label' => esc_html__( 'Position', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Position of Symbol', 'wpbooking' ),
                                    'type'  => 'dropdown',
                                    'std'   => 'left',
                                    'value' => [
                                        'left'             => esc_html__( '$99', "wpbooking" ),
                                        'right'            => esc_html__( '99$', "wpbooking" ),
                                        'left_with_space'  => esc_html__( '$ 99', "wpbooking" ),
                                        'right_with_space' => esc_html__( '99 $', "wpbooking" ),
                                    ]
                                ],
                                [
                                    'id'    => 'thousand_sep',
                                    'label' => esc_html__( 'Thousand Separator', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Thousand Separator', 'wpbooking' ),
                                    'type'  => 'text',
                                    'std'   => ',',
                                ],
                                [
                                    'id'    => 'decimal_sep',
                                    'label' => esc_html__( 'Decimal Separator', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Decimal Separator', 'wpbooking' ),
                                    'type'  => 'text',
                                    'std'   => '.',
                                ],
                                [
                                    'id'    => 'decimal',
                                    'label' => esc_html__( 'Decimal', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Decimal', 'wpbooking' ),
                                    'type'  => 'number',
                                    'std'   => 2,
                                    'attr'  => [
                                        'min' => 0,
                                        'max' => 3
                                    ]
                                ],
                                [
                                    'id'    => 'rate',
                                    'label' => esc_html__( 'Exchange Rate', 'wpbooking' ),
                                    'desc'  => esc_html__( 'Exchange Rate vs Main Currency', 'wpbooking' ),
                                    'type'  => 'text',
                                    'value' => 1
                                ],

                            ]
                        ],
                        [
                            'label' => esc_html__( 'Archive Page', 'wpbooking' ),
                            'type'  => 'page-select',
                            'id'    => 'archive-page'
                        ],
                        [
                            'label' => esc_html__( 'Term & Condition Page', 'wpbooking' ),
                            'type'  => 'page-select',
                            'id'    => 'term-page'
                        ],
                        [
                            'label' => esc_html__( 'Accounts', 'wpbooking' ),
                            'type'  => 'title'
                        ],
                        [
                            'label' => esc_html__( 'My Account Page', 'wpbooking' ),
                            'type'  => 'page-select',
                            'id'    => 'myaccount-page'
                        ],
                        [
                            'label' => esc_html__( 'Google Map', 'wpbooking' ),
                            'type'  => 'title'
                        ],
                        [
                            'id'    => 'google_api_key',
                            'label' => esc_html__( "Google API key", 'wpbooking' ),
                            'type'  => 'text',
                            'desc'  => sprintf( esc_html__( 'You can get Google API %s', 'wpbooking' ), '<a href="' . esc_url( 'https://developers.google.com/maps/documentation/javascript/get-api-key' ) . '">here</a>' ),
                        ],
                    ]
                ],
            ],
        ],
        'booking' => [
            'name'     => esc_html__( "Booking", 'wpbooking' ),
            'sections' => [
                'general_booking' => [
                    'id'     => 'general_booking',
                    'label'  => esc_html__( "General Options", 'wpbooking' ),
                    'fields' => [

                        [
                            'id'    => 'checkout_page',
                            'label' => esc_html__( "Checkout Page", 'wpbooking' ),
                            'type'  => 'page-select',
                        ],

                        [
                            'id'    => 'allow_guest_checkout',
                            'label' => esc_html__( "Allow Guest to Checkout?", 'wpbooking' ),
                            'type'  => 'checkbox',
                        ],
                        [
                            'label' => esc_html__( 'Captcha Google', 'wpbooking' ),
                            'type'  => 'title'
                        ],
                        [
                            'id'    => 'allow_captcha_google_checkout',
                            'label' => esc_html__( "Allow Captcha Google to be Checked out", 'wpbooking' ),
                            'type'  => 'checkbox',
                            'desc'  => wp_kses( __( 'To use this feature, you must create a google key. See more at <a href="https://www.google.com/recaptcha/intro/android.html" target="_blank">Google Captcha</a>', 'wpbooking' ), [ 'a' => [ 'href' => [], 'target' => [] ] ] )
                        ],
                        [
                            'id'          => 'google_key_captcha',
                            'label'       => esc_html__( "Google key", 'wpbooking' ),
                            'type'        => 'text',
                            'placeholder' => '',
                            'condition' => 'allow_captcha_google_checkout:is(1)'
                        ],
                        [
                            'id'          => 'google_secret_key_captcha',
                            'label'       => esc_html__( "Google secret key", 'wpbooking' ),
                            'type'        => 'text',
                            'placeholder' => '',
                            'condition' => 'allow_captcha_google_checkout:is(1)'
                        ],
                    ]
                ]

            ]
        ],
        'email'   => [
            'name'     => esc_html__( "Email", 'wpbooking' ),
            'sections' => [
                'general_booking' => [
                    'id'     => 'general_booking',
                    'label'  => esc_html__( "General Options", 'wpbooking' ),
                    'fields' => [

                        [
                            'id'    => 'email_from',
                            'label' => esc_html__( "Email From Name", 'wpbooking' ),
                            'type'  => 'text',
                            'std'   => esc_html__( "WPBooking Plugin", 'wpbooking' )
                        ],
                        [
                            'id'          => 'email_from_address',
                            'label'       => esc_html__( "Email From Address", 'wpbooking' ),
                            'type'        => 'text',
                            'placeholder' => 'no-reply@domain.com'
                        ],
                        [
                            'id'          => 'system_email',
                            'label'       => esc_html__( "Email  System to get Booking, Registration Notifications...etc", 'wpbooking' ),
                            'type'        => 'text',
                            'placeholder' => 'system@domain.com'
                        ],
                        [
                            'id'          => 'email_header',
                            'label'       => esc_html__( "Email Header", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                        ],
                        [
                            'id'          => 'email_footer',
                            'label'       => esc_html__( "Email Footer", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                        ],

                        [
                            'id'    => 'email_stylesheet',
                            'label' => esc_html__( "Email CSS Code", 'wpbooking' ),
                            'type'  => 'ace-editor',
                            'desc'  => esc_html__( 'We will use this to transmogrify your Email HTML by parsing the CSS and inserting the CSS definitions into tags within your Email HTML based on the CSS selectors', 'wpbooking' ),
                        ],
                    ]
                ],
                'booking_email'   => [
                    'id'     => 'booking_email',
                    'label'  => esc_html__( "Booking Email", 'wpbooking' ),
                    'fields' => [

                        [
                            'id'    => 'on_booking_email_customer',
                            'label' => esc_html__( "Enable Email To Customer", 'wpbooking' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'email_to_customer',
                            'label'       => esc_html__( "For Customer", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/document' ),
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_booking_email_preview', 'email' => 'email_to_customer' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wpbooking' ) . '</a>',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'condition'   => 'on_booking_email_customer:is(1)'
                        ],

                        [
                            'id'    => 'on_booking_email_admin',
                            'label' => esc_html__( "Enable Email To Admin", 'wpbooking' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'email_to_admin',
                            'label'       => esc_html__( "For Administrator", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/document' ),
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_booking_email_preview', 'email' => 'email_to_admin' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wpbooking' ) . '</a>',
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'condition'   => 'on_booking_email_admin:is(1)'
                        ],
                    ]
                ],
                'partner_email'   => [
                    'id'     => 'partner_email',
                    'label'  => esc_html__( "Registration Email", 'wpbooking' ),
                    'fields' => [

                        [
                            'type'  => 'title',
                            'label' => esc_html__( 'Customer Registration', 'wpbooking' )
                        ],
                        [
                            'id'    => 'on_registration_email_customer',
                            'label' => esc_html__( "Enable Email To Customer", 'wpbooking' ),
                            'type'  => 'checkbox',
                            'std'   => '1'
                        ],
                        [
                            'id'          => 'registration_email_customer',
                            'label'       => esc_html__( "Email To Customer", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/registration_document' ),
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_register_email_preview', 'email' => 'registration_email_customer' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wpbooking' ) . '</a>',
                            'condition'   => 'on_registration_email_customer:is(1)'
                        ],
                        [
                            'id'    => 'on_registration_email_admin',
                            'label' => esc_html__( "Enable Email To Admin", 'wpbooking' ),
                            'type'  => 'checkbox',
                        ],
                        [
                            'id'          => 'registration_email_admin',
                            'label'       => esc_html__( "Email To Admin", 'wpbooking' ),
                            'type'        => 'texteditor',
                            'desc'        => wpbooking_admin_load_view( 'email/registration_document' ),
                            'editor_args' => [
                                'tinymce' => false
                            ],
                            'extra_html'  => '<a class="wpbooking-preview-modal thickbox button button-primary" href="' . add_query_arg( [ 'action' => 'wpbooking_register_email_preview', 'email' => 'registration_email_admin' ], 'admin-ajax.php' ) . '">' . esc_html__( 'Preview', 'wpbooking' ) . '</a>',

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
