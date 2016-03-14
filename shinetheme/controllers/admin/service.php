<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 12:44 PM
 */
if(!class_exists('Traveler_Admin_Service'))
{
	class Traveler_Admin_Service
	{
		private static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_add_post_type'));
		}

		function _add_post_type()
		{
			$labels = array(
				'name'               => _x( 'Books', 'post type general name', 'your-plugin-textdomain' ),
				'singular_name'      => _x( 'Book', 'post type singular name', 'your-plugin-textdomain' ),
				'menu_name'          => _x( 'Books', 'admin menu', 'your-plugin-textdomain' ),
				'name_admin_bar'     => _x( 'Book', 'add new on admin bar', 'your-plugin-textdomain' ),
				'add_new'            => _x( 'Add New', 'book', 'your-plugin-textdomain' ),
				'add_new_item'       => __( 'Add New Book', 'your-plugin-textdomain' ),
				'new_item'           => __( 'New Book', 'your-pluQgin-textdomain' ),
				'edit_item'          => __( 'Edit Book', 'your-plugin-textdomain' ),
				'view_item'          => __( 'View Book', 'your-plugin-textdomain' ),
				'all_items'          => __( 'All Books', 'your-plugin-textdomain' ),
				'search_items'       => __( 'Search Books', 'your-plugin-textdomain' ),
				'parent_item_colon'  => __( 'Parent Books:', 'your-plugin-textdomain' ),
				'not_found'          => __( 'No books found.', 'your-plugin-textdomain' ),
				'not_found_in_trash' => __( 'No books found in Trash.', 'your-plugin-textdomain' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'your-plugin-textdomain' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'book' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			register_post_type( 'book', $args );
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