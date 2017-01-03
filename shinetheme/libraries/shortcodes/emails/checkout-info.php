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
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
			return apply_filters('wpbooking_email_preview_checkout_info_html', wpbooking_load_view('emails/shortcodes/preview/checkout_info'));
		}
		return apply_filters('wpbooking_email_checkout_info_html', wpbooking_load_view('emails/shortcodes/checkout_info',$attr), $attr);
	}

	add_shortcode('wpbooking_email_checkout_info','wpbooking_email_checkout_info_func');
}