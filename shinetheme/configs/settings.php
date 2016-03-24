<?php
$config['settings'] = array(
	"general"   => array(
		"name"     => __('General', 'traveler-booking'),
		"sections" => array(
			"general_option"         => array(
				'id'     => 'general_option',
				'label'  => __('General Options', 'traveler-booking'),
				'fields' => array(
                    array(
                        'id'        => 'title',
                        'label'     => __('Title Tab', 'traveler-booking'),
                        'desc'      => '',
                        'type'      => 'title',
                        'std'       => '',
                        'condition' => ''
                    ),
                    array(
                        'id'        => 'hr',
                        'label'     => '',
                        'desc'      => '',
                        'type'      => 'hr',
                        'std'       => '',
                        'condition' => ''
                    ),
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
								'std'   => apply_filters('traveler_get_all_currency', array()),
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
									'left'  => __('$99', "traveler-booking"),
									'right' => __('99$', "traveler-booking"),
									'left_with_space' => __('$ 99', "traveler-booking"),
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
								'attr'=>array(
									'min'=>0,
									'max'=>3
								)
							),
							array(
								'id'    => 'textarea',
								'label' => __('Text Area', 'traveler-booking'),
								'desc'  => __('Text Area', 'traveler-booking'),
								'type'  => 'textarea',
								'std'   => '',
							),
							array(
								'id'    => 'texteditor',
								'label' => __('Text Editor', 'traveler-booking'),
								'desc'  => __('Text Editor', 'traveler-booking'),
								'type'  => 'texteditor',
								'std'   => '',
							),
							array(
								'id'    => 'texteditor2',
								'label' => __('Text Editor 2', 'traveler-booking'),
								'desc'  => __('Text Editor 2', 'traveler-booking'),
								'type'  => 'texteditor',
								'std'   => '',
							),
							array(
								'id'    => 'upload',
								'label' => __('Upload', 'traveler-booking'),
								'desc'  => __('Upload', 'traveler-booking'),
								'type'  => 'upload',
								'std'   => '',
							),
							array(
								'id'       => 'gallery',
								'label'    => __('Gallery', 'traveler-booking'),
								'desc'     => __('Gallery', 'traveler-booking'),
								'type'     => 'gallery',
								'std'      => '',
								'taxonomy' => ''
							),
							array(
								'id'    => 'page-select',
								'label' => __('Page select', 'traveler-booking'),
								'desc'  => __('Page select', 'traveler-booking'),
								'type'  => 'page-select',
								'std'   => '',
							),
							array(
								'id'    => 'post-select',
								'label' => __('Post select', 'traveler-booking'),
								'desc'  => __('Post select', 'traveler-booking'),
								'type'  => 'post-select',
								'std'   => '',
							),
							array(
								'id'       => 'taxonomy-select',
								'label'    => __('Taxonomy select', 'traveler-booking'),
								'desc'     => __('Taxonomy select', 'traveler-booking'),
								'type'     => 'taxonomy-select',
								'std'      => '',
								'taxonomy' => 'category'
							),
							array(
								'id'    => 'image-thumb',
								'label' => __('Image Thumb', 'traveler-booking'),
								'desc'  => __('Image Thumb', 'traveler-booking'),
								'type'  => 'image-thumb',
								'std'   => '',
							),
						)
					),
					array(
						'id'        => 'text_box',
						'label'     => __('Text Box', 'traveler-booking'),
						'desc'      => __('Text Box', 'traveler-booking'),
						'type'      => 'text',
						'std'       => '',
						'condition' => ''
					),
					array(
						'id'    => 'check_box',
						'label' => __('Check Box', 'traveler-booking'),
						'desc'  => __('Check Box click', 'traveler-booking'),
						'type'  => 'checkbox',
						'std'   => '',
						'value' => '',
					),
					array(
						'id'        => 'text_box_3',
						'label'     => __('Text Box 3', 'traveler-booking'),
						'desc'      => __('Text Box 3', 'traveler-booking'),
						'type'      => 'text',
						'std'       => '',
						'condition' => 'check_box:is(on)'
					),
					array(
						'id'    => 'muti_check_box',
						'label' => __('Check Box Array', 'traveler-booking'),
						'desc'  => __('Check Box Array', 'traveler-booking'),
						'type'  => 'muti-checkbox',
						'std'   => '',
						'value' => array(
							array(
								'id'    => 'check_muti_1',
								'label' => 'Check Muti 1',
								'std'   => '',
							),
							array(
								'id'    => 'check_muti_2',
								'label' => 'Check Muti 2',
								'std'   => '',
							),
						),
					),
					array(
						'id'    => 'radio',
						'label' => __('Radio', 'traveler-booking'),
						'desc'  => __('Radio', 'traveler-booking'),
						'type'  => 'radio',
						'std'   => 'no',
						'value' => array(
							'off' => __('No', "traveler-booking"),
							'on'  => __('Yes', "traveler-booking"),
						),
					),
					array(
						'id'    => 'dropdown',
						'label' => __('Dropdown', 'traveler-booking'),
						'desc'  => __('Dropdown', 'traveler-booking'),
						'type'  => 'dropdown',
						'std'   => 'no',
						'value' => array(
							'no'  => __('No', "traveler-booking"),
							'yes' => __('Yes', "traveler-booking"),
						),
					),
					array(
						'id'    => 'textarea',
						'label' => __('Text Area', 'traveler-booking'),
						'desc'  => __('Text Area', 'traveler-booking'),
						'type'  => 'textarea',
						'std'   => '',
					),
					array(
						'id'    => 'texteditor',
						'label' => __('Text Editor', 'traveler-booking'),
						'desc'  => __('Text Editor', 'traveler-booking'),
						'type'  => 'texteditor',
						'std'   => '',
					),
					array(
						'id'    => 'upload',
						'label' => __('Upload', 'traveler-booking'),
						'desc'  => __('Upload', 'traveler-booking'),
						'type'  => 'upload',
						'std'   => '',
					),
					array(
						'id'    => 'upload2',
						'label' => __('Upload2', 'traveler-booking'),
						'desc'  => __('Upload2', 'traveler-booking'),
						'type'  => 'upload',
						'std'   => '',
					),
					array(
						'id'    => 'page-select',
						'label' => __('Page select', 'traveler-booking'),
						'desc'  => __('Page select', 'traveler-booking'),
						'type'  => 'page-select',
						'std'   => '',
					),
					array(
						'id'    => 'post-select',
						'label' => __('Post select', 'traveler-booking'),
						'desc'  => __('Post select', 'traveler-booking'),
						'type'  => 'post-select',
						'std'   => '',
					),
					array(
						'id'       => 'taxonomy-select',
						'label'    => __('Taxonomy select', 'traveler-booking'),
						'desc'     => __('Taxonomy select', 'traveler-booking'),
						'type'     => 'taxonomy-select',
						'std'      => '',
						'taxonomy' => 'category'
					),
					array(
						'id'       => 'gallery',
						'label'    => __('Gallery', 'traveler-booking'),
						'desc'     => __('Gallery', 'traveler-booking'),
						'type'     => 'gallery',
						'std'      => '',
						'taxonomy' => ''
					),
					array(
						'id'       => 'gallery2',
						'label'    => __('Gallery2', 'traveler-booking'),
						'desc'     => __('Gallery2', 'traveler-booking'),
						'type'     => 'gallery',
						'std'      => '',
						'taxonomy' => ''
					),
					array(
						'id'    => 'image-thumb',
						'label' => __('Image Thumb', 'traveler-booking'),
						'desc'  => __('Image Thumb', 'traveler-booking'),
						'type'  => 'image-thumb',
						'std'   => '',
					),
					array(
						'id'    => 'gallery-thumb',
						'label' => __('Gallery Thumb', 'traveler-booking'),
						'desc'  => __('Gallery Thumb', 'traveler-booking'),
						'type'  => 'image-thumb',
						'std'   => '',
					),
					array(
						'id'    => 'form-build',
						'label' => __('Form Build', 'traveler-booking'),
						'desc'  => __('Form Build', 'traveler-booking'),
						'type'  => 'form-build',
						'std'   => '',
						'value' => array(
							array(
								'id'    => 'field_name',
								'label' => __('Field Name', 'traveler-booking'),
								'desc'  => __('Field Name', 'traveler-booking'),
								'type'  => 'text',
								'std'   => ''
							),
							array(
								'id'    => 'field_email',
								'label' => __('Email', 'traveler-booking'),
								'desc'  => __('Email', 'traveler-booking'),
								'type'  => 'email',
								'std'   => ''
							),
						),
					),

				)
			),
			"pages2_setting_section" => array(
				'id'     => 'pages2_setting_section',
				'label'  => __('Page 2 Option', 'traveler-booking'),
				'fields' => array(
					array(
						'id'    => 'setting_one',
						'label' => __('Settings One', 'traveler-booking'),
						'desc'  => __('Settings One', 'traveler-booking'),
						'type'  => 'text',
						'std'   => ''
					),
					array(
						'id'    => 'setting_two',
						'label' => __('Settings Two', 'traveler-booking'),
						'desc'  => __('Settings Two', 'traveler-booking'),
						'type'  => 'text',
						'std'   => ''
					)
				)
			),
		),
	),
	"setting_2" => array(
		"name"     => "Settings two",
		"sections" => array(
			"blog_setting_section" => array(
				'id'     => 'blog_setting_section',
				'label'  => __('Blog Option', 'traveler-booking'),
				'fields' => array(
					array(
						'id'    => 'blog_one',
						'label' => __('Settings One', 'traveler-booking'),
						'desc'  => __('Settings One', 'traveler-booking'),
						'type'  => 'text',
						'std'   => ''
					),
					array(
						'id'    => 'blog_two',
						'label' => __('Settings Two', 'traveler-booking'),
						'desc'  => __('Settings Two', 'traveler-booking'),
						'type'  => 'text',
						'std'   => ''
					)
				)
			),
		),
	),
);
//$config['settings']= array(
//    "general"=>array(
//        "name"=>__('General','traveler-booking'),
//        "sections"=>array(
//            "general_option" => array(
//                'id'      => 'general_option' ,
//                'label'   => __( 'General Options' , 'traveler-booking' ) ,
//                'fields'     => array(
//                    array(
//                        'id'      => 'currency' ,
//                        'label'   => __( 'Currency' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Currency' , 'traveler-booking' )  ,
//                        'type'    => 'list-item' ,
//                        'std'     => '',
//                        'value'   => array(
//                            array(
//                                'id'      => 'text_box' ,
//                                'label'   => __( 'Text Box' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Text Box' , 'traveler-booking' )  ,
//                                'type'    => 'text' ,
//                                'std'     => ''
//                            ),
//                            array(
//                                'id'      => 'text_box_2' ,
//                                'label'   => __( 'Text Box 2' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Text Box 2' , 'traveler-booking' )  ,
//                                'type'    => 'text' ,
//                                'std'     => ''
//                            ),
//                            array(
//                                'id'      => 'checkbox' ,
//                                'label'   => __( 'Yes' , 'traveler-booking' ) ,
//                                'desc'    => __( 'this is desc' , 'traveler-booking' )  ,
//                                'type'    => 'checkbox' ,
//                                'std'     => ''
//                            ),
//                            array(
//                                'id'      => 'radio' ,
//                                'label'   => __( 'Radio' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Radio' , 'traveler-booking' )  ,
//                                'type'    => 'radio' ,
//                                'std'     => 'no',
//                                'value' => array(
//                                    'no'  => __( 'No' , "traveler-booking" ) ,
//                                    'yes' => __( 'Yes' , "traveler-booking" ) ,
//                                ) ,
//                            ),
//                            array(
//                                'id'      => 'dropdown' ,
//                                'label'   => __( 'Dropdown' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Dropdown' , 'traveler-booking' )  ,
//                                'type'    => 'dropdown' ,
//                                'std'     => 'no',
//                                'value' => array(
//                                    'no'  => __( 'No' , "traveler-booking" ) ,
//                                    'yes' => __( 'Yes' , "traveler-booking" ) ,
//                                ) ,
//                            ),
//                            array(
//                                'id'      => 'textarea' ,
//                                'label'   => __( 'Text Area' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Text Area' , 'traveler-booking' )  ,
//                                'type'    => 'textarea' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'texteditor' ,
//                                'label'   => __( 'Text Editor' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Text Editor' , 'traveler-booking' )  ,
//                                'type'    => 'texteditor' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'texteditor2' ,
//                                'label'   => __( 'Text Editor 2' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Text Editor 2' , 'traveler-booking' )  ,
//                                'type'    => 'texteditor' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'upload' ,
//                                'label'   => __( 'Upload' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Upload' , 'traveler-booking' )  ,
//                                'type'    => 'upload' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'gallery' ,
//                                'label'   => __( 'Gallery' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Gallery' , 'traveler-booking' )  ,
//                                'type'    => 'gallery' ,
//                                'std'     => '',
//                                'taxonomy'=> ''
//                            ),
//                            array(
//                                'id'      => 'page-select' ,
//                                'label'   => __( 'Page select' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Page select' , 'traveler-booking' )  ,
//                                'type'    => 'page-select' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'post-select' ,
//                                'label'   => __( 'Post select' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Post select' , 'traveler-booking' )  ,
//                                'type'    => 'post-select' ,
//                                'std'     => '',
//                            ),
//                            array(
//                                'id'      => 'taxonomy-select' ,
//                                'label'   => __( 'Taxonomy select' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Taxonomy select' , 'traveler-booking' )  ,
//                                'type'    => 'taxonomy-select' ,
//                                'std'     => '',
//                                'taxonomy'=> 'category'
//                            ),
//                            array(
//                                'id'      => 'image-thumb' ,
//                                'label'   => __( 'Image Thumb' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Image Thumb' , 'traveler-booking' )  ,
//                                'type'    => 'image-thumb' ,
//                                'std'     => '',
//                            ),
//                        )
//                    ),
//                    array(
//                        'id'      => 'text_box' ,
//                        'label'   => __( 'Text Box' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Text Box' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => '',
//                        'condition'=>''
//                    ),
//                    array(
//                        'id'      => 'check_box' ,
//                        'label'   => __( 'Check Box' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Check Box' , 'traveler-booking' )  ,
//                        'type'    => 'checkbox' ,
//                        'std'     => '',
//                        'value' =>  '',
//                    ),
//                    array(
//                        'id'      => 'text_box_3' ,
//                        'label'   => __( 'Text Box 3' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Text Box 3' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => '',
//                        'condition'=>'check_box:is(on)'
//                    ),
//                    array(
//                        'id'      => 'muti_check_box' ,
//                        'label'   => __( 'Check Box Array' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Check Box Array' , 'traveler-booking' )  ,
//                        'type'    => 'muti-checkbox' ,
//                        'std'     => '',
//                        'value' => array(
//                            array(
//                                'id' => 'check_muti_1',
//                                'label' => 'Check Muti 1',
//                                'std'     => '',
//                            ),
//                            array(
//                                'id' => 'check_muti_2',
//                                'label' => 'Check Muti 2',
//                                'std'     => '',
//                            ),
//                        ) ,
//                    ),
//                    array(
//                        'id'      => 'radio' ,
//                        'label'   => __( 'Radio' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Radio' , 'traveler-booking' )  ,
//                        'type'    => 'radio' ,
//                        'std'     => 'no',
//                        'value' => array(
//                            'off'  => __( 'No' , "traveler-booking" ) ,
//                            'on' => __( 'Yes' , "traveler-booking" ) ,
//                        ) ,
//                    ),
//                    array(
//                        'id'      => 'dropdown' ,
//                        'label'   => __( 'Dropdown' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Dropdown' , 'traveler-booking' )  ,
//                        'type'    => 'dropdown' ,
//                        'std'     => 'no',
//                        'value' => array(
//                            'no'  => __( 'No' , "traveler-booking" ) ,
//                            'yes' => __( 'Yes' , "traveler-booking" ) ,
//                        ) ,
//                    ),
//                    array(
//                        'id'      => 'textarea' ,
//                        'label'   => __( 'Text Area' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Text Area' , 'traveler-booking' )  ,
//                        'type'    => 'textarea' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'texteditor' ,
//                        'label'   => __( 'Text Editor' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Text Editor' , 'traveler-booking' )  ,
//                        'type'    => 'texteditor' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'upload' ,
//                        'label'   => __( 'Upload' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Upload' , 'traveler-booking' )  ,
//                        'type'    => 'upload' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'upload2' ,
//                        'label'   => __( 'Upload2' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Upload2' , 'traveler-booking' )  ,
//                        'type'    => 'upload' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'page-select' ,
//                        'label'   => __( 'Page select' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Page select' , 'traveler-booking' )  ,
//                        'type'    => 'page-select' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'post-select' ,
//                        'label'   => __( 'Post select' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Post select' , 'traveler-booking' )  ,
//                        'type'    => 'post-select' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'taxonomy-select' ,
//                        'label'   => __( 'Taxonomy select' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Taxonomy select' , 'traveler-booking' )  ,
//                        'type'    => 'taxonomy-select' ,
//                        'std'     => '',
//                        'taxonomy'=> 'category'
//                    ),
//                    array(
//                        'id'      => 'gallery' ,
//                        'label'   => __( 'Gallery' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Gallery' , 'traveler-booking' )  ,
//                        'type'    => 'gallery' ,
//                        'std'     => '',
//                        'taxonomy'=> ''
//                    ),
//                    array(
//                        'id'      => 'gallery2' ,
//                        'label'   => __( 'Gallery2' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Gallery2' , 'traveler-booking' )  ,
//                        'type'    => 'gallery' ,
//                        'std'     => '',
//                        'taxonomy'=> ''
//                    ),
//                    array(
//                        'id'      => 'image-thumb' ,
//                        'label'   => __( 'Image Thumb' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Image Thumb' , 'traveler-booking' )  ,
//                        'type'    => 'image-thumb' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'gallery-thumb' ,
//                        'label'   => __( 'Gallery Thumb' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Gallery Thumb' , 'traveler-booking' )  ,
//                        'type'    => 'image-thumb' ,
//                        'std'     => '',
//                    ),
//                    array(
//                        'id'      => 'form-build' ,
//                        'label'   => __( 'Form Build' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Form Build' , 'traveler-booking' )  ,
//                        'type'    => 'form-build' ,
//                        'std'     => '',
//                        'value'   => array(
//                            array(
//                                'id'      => 'field_name' ,
//                                'label'   => __( 'Field Name' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Field Name' , 'traveler-booking' )  ,
//                                'type'    => 'text' ,
//                                'std'     => ''
//                            ),
//                            array(
//                                'id'      => 'field_email' ,
//                                'label'   => __( 'Email' , 'traveler-booking' ) ,
//                                'desc'    => __( 'Email' , 'traveler-booking' )  ,
//                                'type'    => 'email' ,
//                                'std'     => ''
//                            ),
//                        ),
//                    ),
//
//                )
//            ),
//            "pages2_setting_section" => array(
//                'id'      => 'pages2_setting_section' ,
//                'label'   => __( 'Page 2 Option' , 'traveler-booking' ) ,
//                'fields'     => array(
//                    array(
//                        'id'      => 'setting_one' ,
//                        'label'   => __( 'Settings One' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Settings One' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => ''
//                    ),
//                    array(
//                        'id'      => 'setting_two' ,
//                        'label'   => __( 'Settings Two' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Settings Two' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => ''
//                    )
//                )
//            ),
//        ),
//    ),
//    "setting_2"=>array(
//        "name"=>"Settings two",
//        "sections"=>array(
//            "blog_setting_section" => array(
//                'id'      => 'blog_setting_section' ,
//                'label'   => __( 'Blog Option' , 'traveler-booking' ) ,
//                'fields'     => array(
//                    array(
//                        'id'      => 'blog_one' ,
//                        'label'   => __( 'Settings One' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Settings One' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => ''
//                    ),
//                    array(
//                        'id'      => 'blog_two' ,
//                        'label'   => __( 'Settings Two' , 'traveler-booking' ) ,
//                        'desc'    => __( 'Settings Two' , 'traveler-booking' )  ,
//                        'type'    => 'text' ,
//                        'std'     => ''
//                    )
//                )
//            ),
//        ),
//    ),
//);
