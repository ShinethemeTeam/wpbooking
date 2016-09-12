<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_order_total_func'))
{
	function wpbooking_email_order_total_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
			return '1000$';
		}
		$order=new WB_Order($order_id);
        return $order->get_total(array('without_deposit'=>true));
	}

	add_shortcode('wpbooking_email_order_total','wpbooking_email_order_total_func');
}