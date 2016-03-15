<?php
if(!class_exists('Traveler_Admin_Setting'))
{
    class Traveler_Admin_Setting extends Traveler_Controller
    {
        private static $_inst;

        function __construct()
        {
            //add_action('init',array($this,'_add_post_type'));
            add_action( 'admin_menu', array($this,"register_traveler_booking_sub_menu_page") );

            add_action( 'admin_init', array($this,"_save_settings") );

            // add script and style
            add_action('admin_enqueue_scripts',array($this,"_add_scripts"));
        }

        function _add_scripts()
        {
            wp_enqueue_media();
            wp_enqueue_script( 'traveler_admin.js' , traveler_admin_assets_url('js/admin.js') , array( 'jquery', ) , null , true );
            wp_enqueue_style('traveler_admin.css', traveler_admin_assets_url('css/admin.css'));
        }


        /*---------Begin Helper Functions----------------*/
        function get_option($option_id,$default=false){
            /* get the saved options */
            $options = get_option( 'traveler_booking_'.$option_id );
            /* look for the saved value */
            if ( isset( $options ) && '' != $options ) {
                return $options;
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
			echo $this->admin_load_view('settings');
        }
        function _save_settings(){
            if(!empty($_POST['traveler_booking_save_settings']) and wp_verify_nonce($_REQUEST[ 'traveler_booking_save_settings_field' ],"traveler_booking_action")){
                $full_settings = Traveler_Admin_Setting::inst()->_get_settings();
                if(!empty($full_settings)){
                    $is_tab = Traveler_Input::request('st_tab');
                    $is_section = Traveler_Input::request('st_section');
                    if(empty($is_section) and !empty($full_settings[$is_tab]['sections'])){
                        $tmp = $full_settings[$is_tab]['sections'];
                        $is_section = array_shift($tmp);
                        $is_section = $is_section['id'];
                    }
                    $custom_settings = $full_settings[$is_tab]['sections'][$is_section]['fields'];
                    foreach($custom_settings as $key=>$value){
                        $key = 'traveler_booking_'.$value['id'];
                        $value = Traveler_Input::request($key);
                        update_option($key,$value);
                    }
                }
            }
        }
        function _get_settings(){
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
                                    'std'     => 'check_2',
                                    'value' => array(
                                        'check_1'  => __( 'Check Box 1' , "traveler-booking" ) ,
                                        'check_2' => __( 'Check Box 2' , "traveler-booking" ) ,
                                    ) ,
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
                                ),
                                array(
                                    'id'      => 'page-select' ,
                                    'label'   => __( 'Page select' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Page select' , 'traveler-booking' )  ,
                                    'type'    => 'page-select' ,
                                    'std'     => '',
                                ),
                                array(
                                    'id'      => 'post-select' ,
                                    'label'   => __( 'Post select' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Post select' , 'traveler-booking' )  ,
                                    'type'    => 'post-select' ,
                                    'std'     => '',
                                ),
                                array(
                                    'id'      => 'taxonomy-select' ,
                                    'label'   => __( 'Taxonomy select' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Taxonomy select' , 'traveler-booking' )  ,
                                    'type'    => 'taxonomy-select' ,
                                    'std'     => '',
                                    'taxonomy'=> 'category'
                                ),
                                array(
                                    'id'      => 'gallery' ,
                                    'label'   => __( 'Gallery' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Gallery' , 'traveler-booking' )  ,
                                    'type'    => 'gallery' ,
                                    'std'     => '',
                                    'taxonomy'=> ''
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