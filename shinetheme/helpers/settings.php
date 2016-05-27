<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 5:34 PM
 */

if(!function_exists('wpbooking_get_option'))
{
	function wpbooking_get_option($key,$default=FALSE){
		return WPBooking_Admin_Setting::inst()->get_option($key,$default);
	}
}