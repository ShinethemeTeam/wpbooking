<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_order_status_func'))
{
	function wpbooking_email_order_status_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
			return '<label class="alert alert-information">'.esc_html__('Status','wpbooking').'</label>';
		}
		$order=new WB_Order($order_id);
        return $order->get_status_html();
	}

	add_shortcode('wpbooking_email_order_status','wpbooking_email_order_status_func');
}