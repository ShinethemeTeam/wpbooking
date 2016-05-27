<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/21/2016
 * Time: 4:43 PM
 */
if(!function_exists('wpbooking_email_order_table_func'))
{
	function wpbooking_email_order_table_func($attr=array(),$content=FALSE)
	{
		return wpbooking_load_view('email/shortcodes/order-table',$attr);
	}

	add_shortcode('wpbooking_email_order_table','wpbooking_email_order_table_func');
}