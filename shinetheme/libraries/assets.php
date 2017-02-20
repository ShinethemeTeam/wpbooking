<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('WPBooking_Assets'))
{
	class WPBooking_Assets
	{
		static $css=FALSE;
		static $last_string_id;

		static function init()
		{
			self::$last_string_id=time();
			add_action('wp_head',array(__CLASS__,'_show_head_css'));
			add_action('admin_head',array(__CLASS__,'_show_head_css'));
			add_action('wp_footer',array(__CLASS__,'_show_footer_css'));
			add_action('admin_footer',array(__CLASS__,'_show_footer_css'));
		}
		static function _show_head_css()
		{
			printf("<style id='wpbooking_head_css'>%s</style>",self::$css);
			self::$css=FALSE;
		}
		static function _show_footer_css()
		{
			printf("<style id='wpbooking_footer_css'>%s</style>",self::$css);
			self::$css=FALSE;
		}
		static function add_css($string)
		{
			self::$css.=$string;
		}

		static function build_css_class($css,$need='')
		{
			$class='wpbooking_'.(self::$last_string_id+rand());

			self::add_css(sprintf('.%s%s{
				%s
			}',$class,$need,$css));

			return $class;
		}

		static function url($url)
		{
			return WPBooking()->get_url('assets/'.$url);
		}

		static function admin_url($url){

			return WPBooking()->get_url('assets/admin/'.$url);
		}

	}

	WPBooking_Assets::init();
}