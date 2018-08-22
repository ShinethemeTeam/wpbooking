<?php
if(!function_exists('wpbooking_email_order_total_func'))
{
	function wpbooking_email_order_total_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
            return wpbooking_load_view('emails/shortcodes/order_total');
		}
		$order=new WB_Order($order_id);
        return WPBooking_Currency::format_money($order->get_total(array('without_deposit'=>false)));
	}

	add_shortcode('wpbooking_email_order_total','wpbooking_email_order_total_func');
}