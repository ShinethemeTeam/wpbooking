<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_checkout_info_func'))
{
	function wpbooking_email_checkout_info_func($attr=array(),$content=FALSE)
	{
		return wpbooking_load_view('emails/shortcodes/checkout_info',$attr);
	}

	add_shortcode('wpbooking_email_checkout_info','wpbooking_email_checkout_info_func');
}