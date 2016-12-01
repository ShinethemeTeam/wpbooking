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

        static function inst(){
            if(!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_About::inst();
}