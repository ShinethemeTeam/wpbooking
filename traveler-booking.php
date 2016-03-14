<?php
/**
 * Plugin Name: Traveler Booking System
 * Plugin URI: #
 * Description: All in one Booking System
 * Version: 1.0
 * Author: shinetheme
 * Author URI: http://www.shinetheme.com
 * Requires at least: 4.1
 * Tested up to: 4.3
 *
 * Text Domain: traveler-booking
 * Domain Path: /i18n/languages/
 *
 * @package BravoBooking
 * @author Bravotheme
 * @since 1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('Traveler_Booking_System') and !function_exists('Traveler')) {
	class Traveler_Booking_System
	{
		static $_inst=FALSE;

		private $_version = 1.0;

		private $_dir = FALSE;

		private $_url = FALSE;

		/**
		 * @since 1.0
		 */
		function __construct()
		{
			do_action('traveler_before_plugin_init');

			$this->_dir = plugin_dir_path(__FILE__);
			$this->_url = plugin_dir_url(__FILE__);

			add_action('init', array($this, '_init'));
			add_action('admin_menu', array($this, '_admin_init_menu_page'));
			add_action('plugins_loaded',array($this,'_load_cores'));
			//$this->_load_cores();

			do_action('traveler_after_plugin_init');
		}

		function _load_cores()
		{
			$files = array(
				'cores/config',
				'cores/model',
				'cores/loader',
			);
			$this->load($files);
		}

		/**
		 * @since 1.0
		 */

		function _init()
		{
			load_plugin_textdomain('traveler_booking', FALSE, plugin_basename(dirname(__FILE__)) . '/languages');
		}

		/**
		 * @since 1.0
		 */
		function _admin_init()
		{
			$plugin = get_plugin_data(__FILE__);
			$this->_version = $plugin['Version'];

		}

		function _admin_init_menu_page()
		{

			$menu_page=$this->get_menu_page();
			add_menu_page(
				$menu_page['page_title'],
				$menu_page['menu_title'],
				$menu_page['capability'],
				$menu_page['menu_slug'],
				$menu_page['function'],
				$menu_page['icon_url'],
				$menu_page['position']
			);
		}

		/**
		 * @since 1.0
		 * @param $file
		 * @param bool|FALSE $include_once
		 */
		function load($file, $include_once = FALSE)
		{
			if (is_array($file)) {
				if (!empty($file)) {
					foreach ($file as $value) {
						$this->load($value, $include_once);
					}
				}
			} else {
				$file = $this->_dir . 'shinetheme/' . $file . '.php';
				if (!$file) {

				}
				if (file_exists($file)) {
					if ($include_once) include_once($file);
					include($file);
				}
			}

		}

		/**
		 * @since 1.0
		 * @param bool|FALSE $file
		 * @return string
		 */
		function get_dir($file = FALSE)
		{
			return $this->_dir . $file;
		}

		/**
		 * @since 1.0
		 * @param bool|FALSE $file
		 * @return string
		 */
		function get_url($file = FALSE)
		{
			return $this->_url . $file;
		}

		function get_menu_page()
		{
			$page = apply_filters('traveler_menu_page_args', array(
				'page_title' => __("Traveler", 'traveler-booking'),
				'menu_title' => __("Traveler", 'traveler-booking'),
				'capability' => 'manage_options',
				'menu_slug'  => 'traveler_booking',
				'function'   => array($this, '_show_default_page'),
				'icon_url'   => FALSE,
				'position'   =>55
			));

			return $page;

		}
		function _show_default_page()
		{
			do_action('traveler_default_menu_page');
		}

		/**
		 * @return Traveler_Booking_System
		 */
		static function inst()
		{

			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}

	/**
	 * @since 1.0
	 */
	function Traveler()
	{
		return Traveler_Booking_System::inst();
	}

	Traveler();
}