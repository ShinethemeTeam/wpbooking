<?php
if(!function_exists('wpbooking_email_order_id_func'))
{
	function wpbooking_email_order_id_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		if(!$order_id){
            return wpbooking_load_view('emails/shortcodes/order_id');
		}
		return '#'.$order_id;
	}

	add_shortcode('wpbooking_email_order_id','wpbooking_email_order_id_func');
}