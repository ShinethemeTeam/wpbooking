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
			echo $this->admin_load_view('settings');
        }
        function _save_settings(){
            if(!empty($_POST['traveler_booking_save_settings']) and wp_verify_nonce($_REQUEST[ 'shb_save_field' ],"shb_action")){
                $data = $_REQUEST['st_traveler_booking_settings'];
                $option = get_option('st_traveler_booking_settings');
                foreach($data as $k=>$v){
                    $option[$k] = $v;
                }
                var_dump($option);
                update_option('st_traveler_booking_settings',$option);
                die();
            }
        }
        function _init_settings(){
            $custom_settings = array(
                "email"=>array(
                    "name"=>"Settings One",
                    "sections"=>array(
                        "pages_setting_section" => array(
                            'id'      => 'pages_setting_section' ,
                            'label'   => __( 'Page Option' , 'traveler-booking' ) ,
                            'fields'     => array(
                                array(
                                    'id'      => 'setting_one' ,
                                    'label'   => __( 'Settings One' , 'traveler-booking' ) ,
                                    'desc'    => __( 'Settings One' , 'traveler-booking' )  ,
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