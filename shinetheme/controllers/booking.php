<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/25/2016
 * Time: 11:52 AM
 */
if(!class_exists('Traveler_Booking'))
{
	class Traveler_Booking
	{
		static $_inst;

		function __construct()
		{
			add_action('wp_ajax_traveler_do_checkout',array($this,'do_checkout'));
			add_action('wp_ajax_nopriv_traveler_do_checkout',array($this,'do_checkout'));
		}

		function add_to_cart()
		{

		}

		/**
		 * Ajax Checkout Hanlder
		 * @since 1.0
		 */
		function do_checkout()
		{
			$res=array();


			echo json_encode($res);
			die;
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

	Traveler_Booking::inst();
}