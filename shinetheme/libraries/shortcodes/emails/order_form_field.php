<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_order_form_field_func'))
{
	function wpbooking_email_order_form_field_func($attr=array(),$content=FALSE)
	{
		$attr=wp_parse_args($attr,array(
			'key'=>FALSE
		));
		if(!$attr['key']) return FALSE;

		$order_id=WPBooking()->get('order_id');

	}

	add_shortcode('wpbooking_email_order_form_field','wpbooking_email_order_form_field_func');
}