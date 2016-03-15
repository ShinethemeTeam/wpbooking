<?php
if(!class_exists('Traveler_Admin_Setting'))
{
    class Traveler_Admin_Setting
    {
        private static $_inst;

        function __construct()
        {
            //add_action('init',array($this,'_add_post_type'));
            add_action( 'admin_menu', array($this,"register_traveler_booking_sub_menu_page") );

            add_action( 'admin_init', array($this,"_save_settings") );
        }

        /*---------Begin Helper Functions----------------*/
        function get_option($option_id,$default=false){
            /* get the saved options */
            $options = get_option( 'st_traveler_booking_settings' );
            /* look for the saved value */
            if ( isset( $options[$option_id] ) && '' != $options[$option_id] ) {
                return $options[$option_id];
            }
            return $default;
        }

        function register_traveler_booking_sub_menu_page() {

			$menu_page=$this->get_menu_page();
            add_submenu_page(
				$menu_page['parent_slug'],
				$menu_page['page_title'],
				$menu_page['menu_title'],
				$menu_page['capability'],
				$menu_page['menu_slug'],
				$menu_page['function']
			);
        }
		function get_menu_page()
		{

			$menu_page=Traveler()->get_menu_page();
			$page=array(
				'parent_slug'=>$menu_page['menu_slug'],
				'page_title'=>__('Settings','traveler-booking'),
				'menu_title'=>__('Settings','traveler-booking'),
				'capability'=>'manage_options',
				'menu_slug'=>'traveler_booking_page_settings',
				'function'=> array($this,'callback_traveler_booking_sub_menu')
			);

			return apply_filters('traveler_setting_menu_args',$page);

		}
        function callback_traveler_booking_sub_menu() {
            echo traveler_admin_load_view('admin/settings');
        }
        function _save_settings(){
            if(!empty($_POST['traveler_booking_save_settings']) and wp_verify_nonce($_REQUEST[ 'shb_save_field' ],"shb_action")){
                $data = $_REQUEST['st_traveler_booking_settings'];
                $option = get_option('st_traveler_booking_settings');
                /*var_dump($data);
                die();*/
                foreach($data as $k=>$v){
                    $option[$k] = $v;
                }
                update_option('st_traveler_booking_settings',$option);
            }
        }
        function _init_settings(){
            $custom_settings = array(
                "setting_1"=>array(
                    "name"=>"Settings One",
                    "sections"=>array(
                        "pages_setting_section" => array(
                            'id'      => 'pages_setting_section' ,
                            'label'   => __( 'Page Option' , 'traveler-booking' ) ,
                            'fields'     => array(
                                array(
                                    'id'      => 'text_box' ,
                                    'label'   => __( 'Text Box' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Text Box' , 'traveler-booking' )  ,
                                    'type'    => 'text' ,
                                    'std'     => ''
                                ),
                                array(
                                    'id'      => 'check_box' ,
                                    'label'   => __( 'Check Box' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Check Box' , 'traveler-booking' )  ,
                                    'type'    => 'checkbox' ,
                                    'std'     => 'no',
                                    'value' => array(
                                        'no'  => __( 'No' , "st_membership" ) ,
                                        'yes' => __( 'Yes' , "st_membership" ) ,
                                    ) ,
                                ),
                                array(
                                    'id'      => 'dropdown' ,
                                    'label'   => __( 'Dropdown' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Dropdown' , 'traveler-booking' )  ,
                                    'type'    => 'dropdown' ,
                                    'std'     => 'no',
                                    'value' => array(
                                        'no'  => __( 'No' , "st_membership" ) ,
                                        'yes' => __( 'Yes' , "st_membership" ) ,
                                    ) ,
                                ),
                                array(
                                    'id'      => 'textarea' ,
                                    'label'   => __( 'Text Area' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Text Area' , 'traveler-booking' )  ,
                                    'type'    => 'textarea' ,
                                    'std'     => '',
                                ),
                                array(
                                    'id'      => 'texteditor' ,
                                    'label'   => __( 'Text Editor' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Text Editor' , 'traveler-booking' )  ,
                                    'type'    => 'texteditor' ,
                                    'std'     => '',
                                ),
                                array(
                                    'id'      => 'upload' ,
                                    'label'   => __( 'Upload' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Upload' , 'traveler-booking' )  ,
                                    'type'    => 'upload' ,
                                    'std'     => '',
                                )
                            )
                        ),
                        "pages2_setting_section" => array(
                            'id'      => 'pages2_setting_section' ,
                            'label'   => __( 'Page 2 Option' , 'traveler-booking' ) ,
                            'fields'     => array(
                                array(
                                    'id'      => 'setting_one' ,
                                    'label'   => __( 'Settings One' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Settings One' , 'traveler-booking' )  ,
                                    'type'    => 'text' ,
                                    'std'     => ''
                                ),
                                array(
                                    'id'      => 'setting_two' ,
                                    'label'   => __( 'Settings Two' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Settings Two' , 'traveler-booking' )  ,
                                    'type'    => 'text' ,
                                    'std'     => ''
                                )
                            )
                        ),
                    ),
                ),
                "setting_2"=>array(
                    "name"=>"Settings two",
                    "sections"=>array(
                        "blog_setting_section" => array(
                            'id'      => 'blog_setting_section' ,
                            'label'   => __( 'Blog Option' , 'traveler-booking' ) ,
                            'fields'     => array(
                                array(
                                    'id'      => 'blog_one' ,
                                    'label'   => __( 'Settings One' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Settings One' , 'traveler-booking' )  ,
                                    'type'    => 'text' ,
                                    'std'     => ''
                                ),
                                array(
                                    'id'      => 'blog_two' ,
                                    'label'   => __( 'Settings Two' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Settings Two' , 'traveler-booking' )  ,
                                    'type'    => 'text' ,
                                    'std'     => ''
                                )
                            )
                        ),
                    ),
                ),
            );
            $custom_settings = apply_filters( 'st_traveler_booking_settings' , $custom_settings );
            return $custom_settings;
        }

        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }
            return self::$_inst;
        }


    }

    Traveler_Admin_Setting::inst();
}