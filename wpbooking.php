<?php
/**
 * Plugin Name: WpBooking
 * Plugin URI: #
 * Description: All in one Booking System
 * Version: 1.0
 * Author: shinetheme
 * Author URI: http://www.shinetheme.com
 * Requires at least: 4.1
 * Tested up to: 4.3
 *
 * Text Domain: wpbooking
 * Domain Path: /languages/
 *
 * @package wpbooking
 * @author shinetheme
 * @since 1.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_System') and !function_exists('WPBooking')) {
	class WPBooking_System
	{
		static $_inst=FALSE;

		private $_version = 1.0;

		/**
		 * Get and Access Global Variable
		 * @var array
		 */
		protected $global_values=array();

		protected $_dir_path=FALSE;
		protected $_dir_url=FALSE;


		/**
		 * @since 1.0
		 */
		function __construct()
		{
			do_action('wpbooking_before_plugin_init');

			$this->_dir_path=plugin_dir_path(__FILE__);
			$this->_dir_url=plugin_dir_url(__FILE__);

			add_action('init', array($this, '_init'));
			add_action('admin_menu', array($this, '_admin_init_menu_page'));
			add_action('plugins_loaded',array($this,'_load_cores'));

			add_action('admin_enqueue_scripts',array($this,'_admin_default_scripts'));
			add_action('wp_enqueue_scripts',array($this,'_frontend_scripts'));

			do_action('wpbooking_after_plugin_init');
		}

		function _frontend_scripts()
		{
			/**
			 * Css
			 */
            wp_enqueue_style('wpbooking-bootstrap-css',wpbooking_assets_url('bootstrap/css/bootstrap.min.css'));
			wp_enqueue_style('font-awesome',wpbooking_assets_url('fa4.5/css/font-awesome.min.css'),FALSE,'4.5.0');
            wp_enqueue_style('fotorama',wpbooking_assets_url('fotorama4.6.4/fotorama.css'));
//			wp_enqueue_style('plugin_name-admin-ui-css',
//				'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css',
//				false,
//				1.0,
//				false);



			/**
			 * Select2 CSS
			 */
			wp_enqueue_style('wpbooking-select2',wpbooking_assets_url('select2/css/select2.min.css'));
			wp_enqueue_style('jquery-ui-datepicker',wpbooking_assets_url('css/datepicker.css'));
			wp_enqueue_style('wpbooking-booking',wpbooking_assets_url('css/wpbooking-booking.css'));

			/**
			 * Ion RangeSlider for Price Search Field
			 * @author dungdt
			 * @since 1.0
			 */
			wp_register_script('ion-range-slider',wpbooking_assets_url('ion-range-slider/js/ion.rangeSlider.min.js'),array('jquery'),null,true);
			wp_register_style('ion-range-slider',wpbooking_assets_url('ion-range-slider/css/ion.rangeSlider.css'));
			wp_register_style('ion-range-slider-flatui',wpbooking_assets_url('ion-range-slider/css/ion.rangeSlider.skinFlat.css'));
			wp_register_style('ion-range-slider-html5',wpbooking_assets_url('ion-range-slider/css/ion.rangeSlider.skinHTML5.css'));



			/**
			 * Javascripts
			 */
			wp_enqueue_script('bootstrap-js',wpbooking_assets_url('bootstrap/js/bootstrap.js'),array('jquery'),null,true);
            wp_enqueue_script('fotorama-js',wpbooking_assets_url('fotorama4.6.4/fotorama.js'),array('jquery'),null,true);
			wp_enqueue_script('google-map-js','//maps.googleapis.com/maps/api/js?libraries=places&sensor=false',array('jquery'),null,true);
			wp_enqueue_script('gmap3.min-js',wpbooking_assets_url('js/gmap3.min.js'),array('jquery'),null,true);

			/**
			 * Chartjs - For Price Slider
			 */
			wp_register_script('chartjs',wpbooking_assets_url('js/Chart.min.js'),array(),null,true);

			/**
			 * Select2 Jquery
			 */
			wp_enqueue_script('wpbooking-select2',wpbooking_assets_url('select2/js/select2.full.min.js'),array('jquery'),null,true);

			wp_enqueue_script('wpbooking-booking',wpbooking_assets_url('js/wpbooking-booking.js'),array('jquery','jquery-ui-datepicker'),null,true);


			wp_localize_script('jquery','wpbooking_params',array(
				'ajax_url'=>admin_url('admin-ajax.php'),
				'wpbooking_security' => wp_create_nonce( 'wpbooking-nonce-field' )
			));
		}
		/**
		 * Load default CSS and Javascript for admin
		 * @since 1.0
		 */
		function _admin_default_scripts()
		{
			/**
			 * Ace Editor
			 */
			wp_register_script('acejs',wpbooking_assets_url('ace/ace.js'),array(),null,true);
			wp_register_script('bootstrap',wpbooking_assets_url('bootstrap/js/bootstrap.min.js'),array('jquery'),null,true);

			/**
			 * Icon Picker
			 */
			wp_register_script('iconpicker',wpbooking_assets_url('iconpicker/js/fontawesome-iconpicker.min.js'),array('jquery'),null,true);

			/**
			 * Icheck
			 */
			wp_register_script('icheck',wpbooking_assets_url('icheck/icheck.min.js'),array('jquery'),null,true);
			wp_register_style('icheck',wpbooking_assets_url('icheck/skins/square/_all.css'));

			wp_enqueue_script('wpbooking-admin',wpbooking_admin_assets_url('js/wpbooking-admin.js'),array('jquery','bootstrap','icheck', 'jquery-ui-core','iconpicker', 'jquery-ui-datepicker','jquery-ui-accordion'),null,true);
			wp_enqueue_script('wpbooking-admin-form-build',wpbooking_admin_assets_url('js/wpbooking-admin-form-build.js'),array('jquery'),null,true);
			
			wp_enqueue_script('moment-js',wpbooking_admin_assets_url('js/moment.min.js'),array('jquery'),null,true);

			//wp_enqueue_script('full-calendar',wpbooking_admin_assets_url('js/fullcalendar-yearview.js'),array('jquery', 'moment-js'),null,true);
			wp_enqueue_script('full-calendar',wpbooking_admin_assets_url('js/fullcalendar.min.js'),array('jquery', 'moment-js'),null,true);

			wp_enqueue_script('fullcalendar-lang', wpbooking_admin_assets_url('/js/lang-all.js'), array('jquery'), null, true);

			wp_enqueue_script('wpbooking-calendar-room',wpbooking_admin_assets_url('js/wpbooking-calendar-room.js'),array('jquery','jquery-ui-datepicker'),null,true);


			//Popover
			wp_register_style('popover',wpbooking_assets_url('bootstrap/less/popovers.css'));


			// Admin Fonts
			$fonts=add_query_arg(array(
				'family'=>'Open+Sans:700,800',
				'subset'=>'vietnamese',
			),'https://fonts.googleapis.com/css');

			wp_enqueue_style('open-sans-bold',$fonts);
			wp_enqueue_style('iconpicker',wpbooking_assets_url('iconpicker/css/fontawesome-iconpicker.min.css'));
			//wp_enqueue_style('full-calendar',wpbooking_admin_assets_url('/css/fullcalendar-yearview.css'),FALSE,'1.1.6');
			wp_enqueue_style('full-calendar',wpbooking_admin_assets_url('/css/fullcalendar.min.css'),FALSE,'1.1.6');
			//wp_enqueue_style('full-calendar-print',wpbooking_admin_assets_url('/css/fullcalendar.print.css'),FALSE,'1.1.6');

			wp_enqueue_style('font-awesome',wpbooking_assets_url('fa4.5/css/font-awesome.min.css'),FALSE,'4.5.0');
			wp_enqueue_style('wpbooking-admin',wpbooking_admin_assets_url('css/admin.css'),array('icheck'));
			wp_enqueue_style('wpbooking-admin-form-build',wpbooking_admin_assets_url('css/wpbooking-admin-form-build.css'));




			wp_localize_script('jquery','wpbooking_params',array(
				'ajax_url'=>admin_url('admin-ajax.php'),
				'wpbooking_security' => wp_create_nonce( 'wpbooking-nonce-field' ),
				'delete_confirm'=>esc_html__('Are you want to delete?','wpbooking')
			));
		}
		function _load_cores()
		{
			$files = array(
				'cores/config',
				'cores/model',
				'cores/controllers',
				'cores/loader',
			);
			$this->load($files);
		}

		/**
		 * @since 1.0
		 */

		function _init()
		{
			load_plugin_textdomain('wpbooking', FALSE, plugin_basename(dirname(__FILE__)) . '/languages');

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
				$file = $this->get_dir( 'shinetheme/' . $file . '.php');
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
			return $this->_dir_path . $file;
		}

		/**
		 * @since 1.0
		 * @param bool|FALSE $file
		 * @return string
		 */
		function get_url($file = FALSE)
		{
			return $this->_dir_url . $file;
		}

		function get_menu_page()
		{
			$page = apply_filters('wpbooking_menu_page_args', array(
				'page_title' => __("WPBooking", 'wpbooking'),
				'menu_title' => __("WPBooking", 'wpbooking'),
				'capability' => 'manage_options',
				'menu_slug'  => 'wpbooking',
				'function'   => array($this, '_show_default_page'),
				'icon_url'   => FALSE,
				'position'   =>55
			));

			return $page;

		}
		function _show_default_page()
		{
			do_action('wpbooking_default_menu_page');
		}

		function set_admin_message($message,$type='information')
		{
			$_SESSION['message']['admin']=array(
				'content'=>$message,
				'type'=>$type
			);
		}
		function set_message($message,$type='information'){
			$_SESSION['message']['frontend']=array(
				'content'=>$message,
				'type'=>$type
			);
		}

		function get_message($clear_message=TRUE)
		{
			$message= isset($_SESSION['message']['frontend'])?$_SESSION['message']['frontend']:FALSE;
			if($clear_message) $_SESSION['message']['frontend']=array();

			return $message;
		}
		function get_admin_message($clear_message=TRUE)
		{
			$message= isset($_SESSION['message']['admin'])?$_SESSION['message']['admin']:FALSE;
			if($clear_message) $_SESSION['message']['admin']=array();

			return $message;
		}

		/**
		 * Set Global Variable
		 *
		 * @since 1.0
		 * @param $name
		 * @param $value
		 */
		function set($name,$value){
			$this->global_values[$name]=$value;
		}

		/**
		 * Get Global Variable
		 *
		 * @since 1.0
		 * @param $name
		 * @param bool|FALSE $default
		 * @return bool
		 */
		function get($name,$default=FALSE){
			return isset($this->global_values[$name])?$this->global_values[$name]:$default;
		}

		/**
		 * @return WPBooking_System
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
	function WPBooking()
	{
		return WPBooking_System::inst();
	}

	WPBooking();
}