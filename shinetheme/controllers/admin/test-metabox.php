<?php 
/**
*@since 1.0.0
*	Test metabox
**/
class Test_Metabox extends Traveler_Controller{

	public function __construct(){
		parent::__construct();

		$metabox = new Traveler_Admin_Metabox();

		$settings = array(
			'id'       => 'st_post_metabox',
            'title'    => __('Information', 'traveler_booking'),
            'desc'     => '',
            'pages'    => array('post'),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => array(
            	array(
                    'label' => __('Location', 'traveler_booking'),
                    'id'    => 'location_tab',
                    'type'  => 'tab'
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
                    'id' => 'text_box',
                    'label' => 'Text Box',
                    'desc' => 'Text Box',
                    'type' => 'text',
                    'std' => 'textbox',
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
            )
		);

		$metabox->register_meta_box( $settings );
	}
}
new Test_Metabox();