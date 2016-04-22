<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/21/2016
 * Time: 4:43 PM
 */
if(!function_exists('traveler_email_order_table_func'))
{
	function traveler_email_order_table_func($attr=array(),$content=FALSE)
	{
		return traveler_load_view('email/shortcodes/order-table',$attr);
	}

	add_shortcode('traveler_email_order_table','traveler_email_order_table_func');
}