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
                    'label' => __('Location', 'traveler-booking'),
                    'id'    => 'location_tab',
                    'type'  => 'tab',
                ),
				array(
					'id'=>'service_type',
					'label'=>__("Service Type",'traveler-booking'),
					'location'=>'hndle-tag',
					'type'=>'service-type-select',
				),
                array(
                    'id'      => 'location' ,
                    'label'   => __( 'Location' , 'traveler-booking' ) ,
                    'desc'    => __( 'Location' , 'traveler-booking' )  ,
                    'type'    => 'location' ,
                ),
                array(
                    'id'      => 'taxonomies' ,
                    'label'   => __( 'Taxonomies' , 'traveler-booking' ) ,
                    'desc'    => __( 'taxonomies' , 'traveler-booking' )  ,
                    'type'    => 'taxonomies' ,
                ),
                array(
                    'id'      => 'check_box' ,
                    'label'   => __( 'Check Box' , 'traveler-booking' ) ,
                    'desc'    => __( 'Check Box' , 'traveler-booking' )  ,
                    'type'    => 'checkbox' ,
                    'std'     => 'check_2',
                    'value' => array(
                        'check_1'  => __( 'Check Box 1' , "traveler-booking" ) ,
                        'check_2' => __( 'Check Box 2' , "traveler-booking" ) ,
                    ) ,
                ),
                array(
                    'id' => 'list-item',
                    'label' => 'List Item',
                    'desc' => 'List Item',
                    'type' => 'list-item',
                    'value' => array(
                        array(
                            'id'    => 'symbol',
                            'label' => __('Symbol', 'traveler-booking'),
                            'desc'  => __('Symbol of currency. Example: $', 'traveler-booking'),
                            'type'  => 'text',
                            'std'   => ''
                        ),
                        array(
                            'id'      => 'textarea' ,
                            'label'   => __( 'Text Area' , 'traveler-booking' ) ,
                            'desc'    => __( 'Text Area' , 'traveler-booking' )  ,
                            'type'    => 'textarea' ,
                            'std'     => 'textarea',
                        ),
                        array(
                            'id'      => 'dropdown' ,
                            'label'   => __( 'Dropdown' , 'traveler-booking' ) ,
                            'desc'    => __( 'Dropdown' , 'traveler-booking' )  ,
                            'type'    => 'dropdown' ,
                            'std'     => 'yes',
                            'value' => array(
                                'no'  => __( 'No' , "traveler-booking" ) ,
                                'yes' => __( 'Yes' , "traveler-booking" ) ,
                            ) ,
                        ),
                    )
                ),
                array(
                    'id' => 'price',
                    'label' => 'Text Box',
                    'desc' => 'Text Box',
                    'type' => 'text',
                    'std' => 'textbox',
                ),
                array(
                    'id' => 'gmap',
                    'label' => 'Gmap',
                    'desc' => 'Gmap',
                    'type' => 'gmap',
                    'std' => 'Gmap',
                    'map_zoom' => 13
                ),
                array(
                    'id'      => 'radio' ,
                    'label'   => __( 'Radio' , 'traveler-booking' ) ,
                    'desc'    => __( 'Radio' , 'traveler-booking' )  ,
                    'type'    => 'radio' ,
                    'std'     => 'no',
                    'value' => array(
                        'no'  => __( 'No' , "traveler-booking" ) ,
                        'yes' => __( 'Yes' , "traveler-booking" ) ,
                    ) ,
                ),
                array(
                    'id'      => 'textarea' ,
                    'label'   => __( 'Text Area' , 'traveler-booking' ) ,
                    'desc'    => __( 'Text Area' , 'traveler-booking' )  ,
                    'type'    => 'textarea' ,
                    'std'     => 'textarea',
                    'condition'=>'check_box:is(check_1)'
                ),
                array(
                    'id'      => 'texteditor' ,
                    'label'   => __( 'Text Editor' , 'traveler-booking' ) ,
                    'desc'    => __( 'Text Editor' , 'traveler-booking' )  ,
                    'type'    => 'texteditor' ,
                    'std'     => 'texteditor',
                ),
                array(
                    'id'      => 'dropdown' ,
                    'label'   => __( 'Dropdown' , 'traveler-booking' ) ,
                    'desc'    => __( 'Dropdown' , 'traveler-booking' )  ,
                    'type'    => 'dropdown' ,
                    'std'     => 'no',
                    'value' => array(
                        'no'  => __( 'No' , "traveler-booking" ) ,
                        'yes' => __( 'Yes' , "traveler-booking" ) ,
                    ) ,
                ),

                array(
                    'label' => __('Location 1', 'traveler_booking'),
                    'id'    => 'location_tab1',
                    'type'  => 'tab'
                ),
                array(
                    'id'      => 'check_box_1' ,
                    'label'   => __( 'Check Box 1' , 'traveler-booking' ) ,
                    'desc'    => __( 'Check Box 1' , 'traveler-booking' )  ,
                    'type'    => 'checkbox' ,
                    'std'     => 'check_2',
                    'value' => array(
                        'check_1'  => __( 'Check Box 1.1' , "traveler-booking" ) ,
                        'check_2' => __( 'Check Box 2.1' , "traveler-booking" ) ,
                    ) ,
                ),
                array(
                    'label' => __('Location', 'traveler-booking'),
                    'id'    => 'location_tab_2',
                    'type'  => 'tab',
                ),
                array(
                    'id' => 'address',
                    'label' => __("Address",'traveler-booking'),
                    'desc' => __("Address",'traveler-booking'),
                    'type' => 'text',
                    'std' => '',
                ),
                array(
                    'id' => 'google_map',
                    'label' => __("Google map",'traveler-booking'),
                    'desc' => __("Google map",'traveler-booking'),
                    'type' => 'gmap',
                    'std' => '',
                    'map_zoom' => 13
                ),
                array(
                    'label' => __('Info Service', 'traveler-booking'),
                    'id'    => 'info_service_tab',
                    'type'  => 'tab',
                ),
                array(
                    'id'      => 'gallery' ,
                    'label'   => __( 'Gallery' , 'traveler-booking' ) ,
                    'desc'    => __( 'Gallery' , 'traveler-booking' )  ,
                    'type'    => 'gallery' ,
                    'std'     => '',
                ),
            )
		);

		$metabox->register_meta_box( $settings );
	}
}
new Test_Metabox();