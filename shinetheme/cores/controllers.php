<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_Controller'))
{
	class WPBooking_Controller
	{

		function __construct()
		{
		}

		function load_view($view,$data=array())
		{
			return wpbooking_load_view($view,$data);
		}

		function admin_load_view($view,$data=array())
		{
			return wpbooking_admin_load_view($view,$data);
		}

		/**
		 * Return $_GET Item
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $key
		 * @return bool
		 */
		function get($key=FALSE){
			return WPBooking_Input::get($key);
		}

		/**
		 * Return $_POST item
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param bool|FALSE $key
		 * @return bool
		 */
		function post($key=FALSE){
			return WPBooking_Input::post($key);
		}

		/**
		 * Get IP address of Client
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return mixed|void
		 */
		function ip_address(){
			return WPBooking_Input::ip_address();
		}
	}
}