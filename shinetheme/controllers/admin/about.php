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

            /**
             * Action to Register Dashboard Widgets
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_dashboard_setup',array($this,'add_dashboard_widgets'));
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

        /**
         * Register Widget in Dashboard Page
         *
         * @since 1.0
         * @author dungdt
         */
        public function add_dashboard_widgets(){
            wp_add_dashboard_widget('wpbooking_report',esc_html__('WPBooking Sales Summary','wpbooking'),array($this,'_report_widget_callback'));
        }

        /**
         * Callback function to show Report Widget
         *
         * @since 1.0
         * @author dungdt
         */
        public function _report_widget_callback()
        {
            $data=$this->get_widget_sale_data();
            $html= wpbooking_admin_load_view('report-widget',$data);
            $html=apply_filters('wpbooking_report_widget_content',$html);
            echo do_shortcode($html);
        }

        public function get_widget_sale_data(){
            $data=array(
                'current_month_earning'=>0,
                'current_month_sale'=>0,
                'today_earning'=>0,
                'today_sale'=>0,
                'last_month_earning'=>0,
                'last_month_sale'=>0,
                'total_earning'=>0,
                'total_sale'=>0,
            );



            return apply_filters('wpbooking_report_widget_sale_data',$data);
        }

        static function inst(){
            if(!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_About::inst();
}