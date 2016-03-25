<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!class_exists('Traveler_Admin_Form_Build'))
{
    class Traveler_Admin_Form_Build extends Traveler_Controller
    {
        private static $_inst;

        function __construct()
        {
            add_action( 'admin_menu', array($this,"register_traveler_booking_sub_menu_page") );

            //add_action('init',array($this,'_add_post_type'),5);
            // add script and style
            add_action('admin_enqueue_scripts',array($this,"_add_scripts"));
        }

        function _add_scripts()
        {

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
                'page_title'=>__('Form Builder','traveler-booking'),
                'menu_title'=>__('Form Builder','traveler-booking'),
                'capability'=>'manage_options',
                'menu_slug'=>'traveler_booking_page_form_builder',
                'function'=> array($this,'callback_traveler_booking_sub_menu_form_builder')
            );

            return apply_filters('traveler_setting_menu_args',$page);

        }
        function callback_traveler_booking_sub_menu_form_builder() {
            echo $this->admin_load_view('form-builder');
        }

        function _get_all_shortcode_in_content($content=false){
            $shortcode  = array();
            if(!empty($content)){
                $pattern = get_shortcode_regex();
                preg_match_all('/'.$pattern.'/s', $content , $matches2);
                if(!empty($matches2[0])){
                    $shortcode = $matches2[0];
                }
            }
            return $shortcode;
        }

        function _add_post_type()
        {
            $labels = array(
                'name'               => _x( 'Form Builder', 'post type general name', 'traveler-booking' ),
                'singular_name'      => _x( 'Form Builder', 'post type singular name', 'traveler-booking' ),
                'menu_name'          => _x( 'Form Builder', 'admin menu', 'traveler-booking' ),
                'name_admin_bar'     => _x( 'Form Builder', 'add new on admin bar', 'traveler-booking' ),
                'add_new'            => _x( 'Add New', 'service', 'traveler-booking' ),
                'add_new_item'       => __( 'Add New Form Builder', 'traveler-booking' ),
                'new_item'           => __( 'New Form Builder', 'your-plugin-textdomain' ),
                'edit_item'          => __( 'Edit Form Builder', 'traveler-booking' ),
                'view_item'          => __( 'View Form Builder', 'traveler-booking' ),
                'all_items'          => __( 'All Form Builders', 'traveler-booking' ),
                'search_items'       => __( 'Search Form Builders', 'traveler-booking' ),
                'parent_item_colon'  => __( 'Parent Form Builders:', 'traveler-booking' ),
                'not_found'          => __( 'No form builder found.', 'traveler-booking' ),
                'not_found_in_trash' => __( 'No form builder found in Trash.', 'traveler-booking' )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'traveler-booking' ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'form_builder' ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                //'menu_position'      => '59.9',
                'supports'           => array( 'title', 'editor' )
            );

            register_post_type( 'traveler_form_build', $args );
        }

        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }
            return self::$_inst;
        }

    }

    Traveler_Admin_Form_Build::inst();
}