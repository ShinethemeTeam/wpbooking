<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 12:44 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('Traveler_Admin_Service'))
{
	class Traveler_Admin_Service extends Traveler_Controller
	{
		private static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_add_taxonomy'));
			add_action('init',array($this,'_add_post_type'),5);
			add_action('save_post',array($this,'_save_extra_field'));
			add_filter('traveler_booking_settings',array($this,'_add_settings'));
		}


		function _save_extra_field($post_id=FALSE)
		{
			if(get_post_type($post_id)!='traveler_service') return false;

			$service_model=Traveler_Service_Model::inst();

			$data=array(
				'map_lat'=>get_post_meta($post_id,'map_lat',true)
			);

			if(!$service_model->find_by('post_id',$post_id)){
				$service_model->insert($data);
			}else{
				$service_model->where('post_id',$post_id)->update($data);
			}
		}

		function _add_settings($settings)
		{
			$settings['services']=array(
				'name'=>__("Services",'wpbooking'),
				'sections'=>apply_filters('traveler_service_setting_sections',array())
			);
			return $settings;
		}

		function _add_taxonomy(){

		}
		function _add_post_type()
		{
			$labels = array(
				'name'               => _x( 'Service', 'post type general name', 'wpbooking' ),
				'singular_name'      => _x( 'Service', 'post type singular name', 'wpbooking' ),
				'menu_name'          => _x( 'Services', 'admin menu', 'wpbooking' ),
				'name_admin_bar'     => _x( 'Service', 'add new on admin bar', 'wpbooking' ),
				'add_new'            => _x( 'Add New', 'service', 'wpbooking' ),
				'add_new_item'       => __( 'Add New Service', 'wpbooking' ),
				'new_item'           => __( 'New Service', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Service', 'wpbooking' ),
				'view_item'          => __( 'View Service', 'wpbooking' ),
				'all_items'          => __( 'All Services', 'wpbooking' ),
				'search_items'       => __( 'Search Services', 'wpbooking' ),
				'parent_item_colon'  => __( 'Parent Services:', 'wpbooking' ),
				'not_found'          => __( 'No services found.', 'wpbooking' ),
				'not_found_in_trash' => __( 'No services found in Trash.', 'wpbooking' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wpbooking' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'service' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				//'menu_position'      => '59.9',
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			register_post_type( 'traveler_service', $args );
		}


		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}


	}

	Traveler_Admin_Service::inst();
}