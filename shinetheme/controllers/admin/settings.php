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
        }

        function register_traveler_booking_sub_menu_page() {
            add_submenu_page( 'tools.php', 'Settings', 'Settings', 'manage_options', 'traveler_booking_page_settings', array($this,'callback_traveler_booking_sub_menu') );
        }
        function callback_traveler_booking_sub_menu() {
            echo traveler_admin_load_view('admin/settings');
            echo 'xxxx';
        }
        function _save_settings(){
            if(!empty($_POST['save_settings']) and wp_verify_nonce($_REQUEST[ 'shb_save_field' ],"shb_action")){
                $data = $_REQUEST['st_traveler_booking_settings'];
                $option = get_option('st_traveler_booking_settings');
                foreach($data as $k=>$v){
                    $option[$k] = $v;
                }
                update_option('st_traveler_booking_settings',$option);
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