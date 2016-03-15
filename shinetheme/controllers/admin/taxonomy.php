<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/15/2016
 * Time: 2:47 PM
 */
if(!class_exists('Traveler_Admin_Taxonomy_Controller'))
{
	class Traveler_Admin_Taxonomy_Controller extends Traveler_Controller
	{

		static $_inst;

		protected $_option_name = 'traveler_taxonomies';

		function __construct()
		{
			parent::__construct();

			add_action('admin_menu', array($this, '_add_taxonomy_page'));
		}

		function _show_taxonomy_page()
		{
			$tax=$this->get_taxonomies();
			echo $this->admin_load_view('taxonomy/index',array(
				'rows'=>$tax
			));
		}
		function get_taxonomies()
		{
			return get_option($this->_option_name);
		}
		function _add_taxonomy_page()
		{
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
				'page_title'=>__('Taxonomies','traveler-booking'),
				'menu_title'=>__('Taxonomies','traveler-booking'),
				'capability'=>'manage_options',
				'menu_slug'=>'traveler_booking_page_taxonomy',
				'function'=> array($this,'_show_taxonomy_page')
			);

			return apply_filters('traveler_setting_menu_args',$page);
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}

			return self::$_inst;
		}


	}

	Traveler_Admin_Taxonomy_Controller::inst();
}