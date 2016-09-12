<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_order_date_func'))
{
	function wpbooking_email_order_date_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
			return date(get_option('date_format'));
		}
        $order=new WB_Order($order_id);

        return $order->get_booking_date();
	}

	add_shortcode('wpbooking_email_order_date','wpbooking_email_order_date_func');
}