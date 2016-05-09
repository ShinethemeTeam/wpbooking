<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/7/2016
 * Time: 11:16 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('Traveler_Admin_Order'))
{
	class Traveler_Admin_Order extends Traveler_Controller
	{
		static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_register_post_type'));
		}

		function _register_post_type()
		{
			$menu_page = Traveler()->get_menu_page();
			$labels = array(
				'name'               => _x( 'Booking', 'post type general name', 'wpbooking' ),
				'singular_name'      => _x( 'Booking', 'post type singular name', 'wpbooking' ),
				'menu_name'          => _x( 'Booking', 'admin menu', 'wpbooking' ),
				'name_admin_bar'     => _x( 'Booking', 'add new on admin bar', 'wpbooking' ),
				'add_new'            => _x( 'Add New', 'Booking', 'wpbooking' ),
				'add_new_item'       => __( 'Add New Booking', 'wpbooking' ),
				'new_item'           => __( 'New Booking', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Booking', 'wpbooking' ),
				'view_item'          => __( 'View Booking', 'wpbooking' ),
				'all_items'          => __( 'All Booking', 'wpbooking' ),
				'search_items'       => __( 'Search Booking', 'wpbooking' ),
				'parent_item_colon'  => __( 'Parent Booking:', 'wpbooking' ),
				'not_found'          => __( 'No Booking found.', 'wpbooking' ),
				'not_found_in_trash' => __( 'No Booking found in Trash.', 'wpbooking' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wpbooking' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => $menu_page['menu_slug'],
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'booking' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				//'menu_position'      => '59.9',
				'supports'           => array( 'title',  'author' )
			);

			register_post_type( 'traveler_order', $args );
		}

		static function inst()
		{
			if(!self::$_inst)
			{
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	Traveler_Admin_Order::inst();
}