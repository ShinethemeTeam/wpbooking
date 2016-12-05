<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 12/1/2016
 * Version: 1.0
 */
if(! defined('ABSPATH')){
    exit;
}

if(!class_exists('WPBooking_About')){
    class WPBooking_About{

        static $_inst;

        function __construct()
        {
            add_action('wpbooking_default_menu_page', array($this, '_wpbooking_about_page'));

            add_action( 'admin_menu', array($this,'register_wpbooking_extensions_menu_page') );

            add_action('admin_enqueue_scripts', array($this, '_enqueue_scripts'));
        }

        function _wpbooking_about_page(){
            echo wpbooking_admin_load_view('about/index');
        }

        function _enqueue_scripts(){
            if(WPBooking_Input::get('page') == 'wpbooking') {
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_media();
            }
        }

        function register_wpbooking_extensions_menu_page(){
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

            $menu_page=WPBooking()->get_menu_page();
            $page=array(
                'parent_slug'=>$menu_page['menu_slug'],
                'page_title'=>__('Extensions','wpbooking'),
                'menu_title'=>__('Extensions','wpbooking'),
                'capability'=>'manage_options',
                'menu_slug'=>'wpbooking_page_extensions',
                'function'=> array($this,'callback_wpbooking_extensions_sub_menu')
            );

            return apply_filters('wpbooking_extensions_menu_args',$page);

        }

        function callback_wpbooking_extensions_sub_menu(){
            echo wpbooking_admin_load_view('about/tab-extensions');
        }

        static function inst(){
            if(!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_About::inst();
}