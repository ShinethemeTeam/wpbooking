<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_order_payment_gateway_func'))
{
	function wpbooking_email_order_payment_gateway_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
			return '<span class=bold>Paypal</span>';
		}
		$order=new WB_Order($order_id);
        return '<span class=bold>'.$order->get_payment_gateway().'</span>';
	}

	add_shortcode('wpbooking_email_order_payment_gateway','wpbooking_email_order_payment_gateway_func');
}