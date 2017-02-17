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
if(!function_exists('wpbooking_handle_icon'))
{
    function wpbooking_handle_icon($string)
    {
        if(strpos($string,'im-')===0)
        {
            $icon= "im ".$string;
        }elseif(strpos($string,'fa-')===0)
        {
            $icon= "fa ".$string;
        }elseif(strpos($string,'ion-')===0)
        {
            $icon= "ion ".$string;
        }
        else{
            $icon=$string;
        }
        return $icon;
    }
}
