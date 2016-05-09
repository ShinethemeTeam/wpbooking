<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/17/2016
 * Time: 2:13 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('Traveler_Admin_Location'))
{
	class Traveler_Admin_Location extends Traveler_Controller
	{
		static $_inst;

		function __construct()
		{
			parent::__construct();

			add_action('init',array($this,'_register_taxonomy'));
		}

		function _register_taxonomy()
		{
			$labels = array(
				'name'              => _x( 'Locations', 'taxonomy general name','wpbooking' ),
				'singular_name'     => _x( 'Location', 'taxonomy singular name','wpbooking' ),
				'search_items'      => __( 'Search Locations','wpbooking' ),
				'all_items'         => __( 'All Locations','wpbooking' ),
				'parent_item'       => __( 'Parent Location' ,'wpbooking'),
				'parent_item_colon' => __( 'Parent Location:' ,'wpbooking'),
				'edit_item'         => __( 'Edit Location' ,'wpbooking'),
				'update_item'       => __( 'Update Location' ,'wpbooking'),
				'add_new_item'      => __( 'Add New Location' ,'wpbooking'),
				'new_item_name'     => __( 'New Location Name' ,'wpbooking'),
				'menu_name'         => __( 'Location' ,'wpbooking'),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'location' ),
			);
			$args=apply_filters('traveler_register_location_taxonomy',$args);

			register_taxonomy( 'traveler_location', array( 'traveler_service' ), $args );

			$hide=apply_filters('traveler_hide_locaton_select_box',false);
			if($hide)
				Traveler_Assets::add_css("#traveler_locationdiv{display:none!important}");
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	Traveler_Admin_Location::inst();
}