<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/15/2016
 * Time: 10:51 AM
 */
if(!function_exists('wpbooking_assets_url'))
{
	function wpbooking_assets_url($url)
	{
		return WPBooking()->get_url('assets/'.$url);
	}
}
if(!function_exists('wpbooking_admin_assets_url'))
{
	function wpbooking_admin_assets_url($url)
	{
		return WPBooking()->get_url('assets/admin/'.$url);
	}
}
if(!function_exists('wpbooking_get_image_size'))
{
	function wpbooking_get_image_size($url)
	{

	}
}