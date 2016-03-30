<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

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
            wp_enqueue_script ('jquery-ui-tabs');
            wp_enqueue_media();
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
                $full_settings =$this->_get_settings();

                if(!empty($full_settings)){
                    $is_tab = Traveler_Input::request('st_tab');
                    $is_section = Traveler_Input::request('st_section');
                    if(empty($is_tab) and !empty($full_settings)){
                        $tmp_tab = $full_settings;
                        if(!empty($tmp_tab)){
                            $tmp_key = array_keys($tmp_tab);
                            $is_tab = array_shift($tmp_key);
                        }
                    }
                    if(empty($is_section) and !empty($full_settings[$is_tab]['sections'])){
                        $tmp_section = $full_settings[$is_tab]['sections'];
                        if(!empty($tmp_section)){
                            $tmp_key = array_keys($tmp_section);
                            $is_section = array_shift($tmp_key);
                        }
                    }
                    $custom_settings = $full_settings[$is_tab]['sections'][$is_section]['fields'];


                    foreach($custom_settings as $key=>$value){
                        switch($value['type']){
                            case "multi-checkbox":
                                $custom_multi_checkbox = $value['value'];
                                foreach($custom_multi_checkbox as $key_multi=>$value_multi){
                                    $key_request = 'traveler_booking_'.$value_multi['id'];
                                    $value_request = Traveler_Input::request($key_request);
                                    update_option($key_request,$value_request);
                                }
                                break;
                            case "list-item":
                                $id_list_item = $value['id'];
                                $data = array();
                                $key_request = 'traveler_booking_list_item';
                                $value_request = Traveler_Input::request($key_request);
                                if(!empty($value_request[$id_list_item])){
                                    $data = $value_request[$id_list_item];
                                }
                                unset($data['__number_list__']);
                                $id_save = 'traveler_booking_'.$value['id'];
                                update_option($id_save,$data);
                                break;
                            default:
                                $key_request = 'traveler_booking_'.$value['id'];
                                $value_request = Traveler_Input::request($key_request);
                                update_option($key_request,$value_request);
                        }

                    }
                }
            }
        }
        function _get_settings(){
            $custom_settings=Traveler_Config::inst()->item('settings');
            $custom_settings = apply_filters( 'traveler_booking_settings' , $custom_settings );
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