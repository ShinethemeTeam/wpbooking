<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/15/2016
 * Time: 10:51 AM
 */
if(!function_exists('traveler_assets_url'))
{
	function traveler_assets_url($url)
	{
		return Traveler()->get_url('assets/'.$url);
	}
}
if(!function_exists('traveler_admin_assets_url'))
{
	function traveler_admin_assets_url($url)
	{
		return Traveler()->get_url('assets/admin/'.$url);
	}
}
if(!function_exists('traveler_get_image_size'))
{
	function traveler_get_image_size($url)
	{

	}
}