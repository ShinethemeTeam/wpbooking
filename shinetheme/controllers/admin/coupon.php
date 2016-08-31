<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/31/2016
 * Time: 4:27 PM
 */
if(!defined( 'ABSPATH' )) {
    exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_Admin_Coupon'))
{
    class WPBooking_Admin_Coupon extends WPBooking_Controller {

        private static $_inst;
        function __construct()
        {

            add_action('init', array($this, '_register_post_type'));
            add_action('admin_menu', array($this, '_add_booking_menupage'));
        }

        /**
         * Register Submenu Page to Create/Edit Coupon
         *
         * @since 1.0
         * @author dungdt
         *
         */
        function _add_booking_menupage()
        {
            $menu_page = $this->get_menu_page();
            add_submenu_page(
                $menu_page['parent_slug'],
                $menu_page['page_title'],
                $menu_page['menu_title'],
                $menu_page['capability'],
                $menu_page['menu_slug'],
                $menu_page['function']
            );
        }

        /**
         * Return menu page args
         *
         * @since 1.0
         * @author dungdt
         *
         * @return mixed|void
         */
        function get_menu_page()
        {
            $menu_page = WPBooking()->get_menu_page();
            $page = array(
                'parent_slug' => $menu_page['menu_slug'],
                'page_title'  => __('Coupon', 'wpbooking'),
                'menu_title'  => __('Coupon', 'wpbooking'),
                'capability'  => 'manage_options',
                'menu_slug'   => 'wpbooking_page_coupon',
                'function'    => array($this, 'callback_menu_page')
            );

            return apply_filters('wpbooking_admin_coupon_menu_args', $page);
        }

        /**
         * Function Callback to display page content
         *
         * @since 1.0
         * @author dungdt
         */
        function callback_menu_page()
        {
            if ($id = WPBooking_Input::get('coupon_id')) {
                $order_item = WPBooking_Order_Model::inst()->find($id);

                if ($order_item) {

                    $data['order_id'] = $order_item['order_id'];
                    $data['order_item'] = $order_item;

                    echo($this->admin_load_view('coupon/detail', $data));

                    return;
                }


            }

            // Listing Page
            echo($this->admin_load_view('coupon/index'));
        }

        /**
         * Register Coupon POst Type
         *
         * @since 1.0
         * @author dungdt
         */
        function _register_post_type()
        {
            $menu_page = WPBooking()->get_menu_page();
            $labels = array(
                'name'               => _x('Coupon', 'post type general name', 'wpbooking'),
                'singular_name'      => _x('Coupon', 'post type singular name', 'wpbooking'),
                'menu_name'          => _x('Coupon', 'admin menu', 'wpbooking'),
                'name_admin_bar'     => _x('Coupon', 'add new on admin bar', 'wpbooking'),
                'add_new'            => _x('Add New', 'Coupon', 'wpbooking'),
                'add_new_item'       => __('Add New Coupon', 'wpbooking'),
                'new_item'           => __('New Coupon', 'your-plugin-textdomain'),
                'edit_item'          => __('Edit Coupon', 'wpbooking'),
                'view_item'          => __('View Coupon', 'wpbooking'),
                'all_items'          => __('All Coupon', 'wpbooking'),
                'search_items'       => __('Search Coupon', 'wpbooking'),
                'parent_item_colon'  => __('Parent Coupon:', 'wpbooking'),
                'not_found'          => __('No Coupon found.', 'wpbooking'),
                'not_found_in_trash' => __('No Coupon found in Trash.', 'wpbooking')
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __('Description.', 'wpbooking'),
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => false,
                'show_in_menu'       => $menu_page['menu_slug'],
                'query_var'          => false,
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => FALSE,
                //'menu_position'      => '59.9',
                'supports'           => array('title', 'author')
            );

            register_post_type('wpbooking_coupon', $args);
        }

        static function inst()
        {
            if(!self::$_inst) self::$_inst=new self();

            return self::$_inst;
        }
    }

    WPBooking_Admin_Coupon::inst();
}