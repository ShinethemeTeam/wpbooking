<?php
if(!function_exists('wpbooking_email_name_customer_func'))
{
	function wpbooking_email_name_customer_func($attr=array(),$content=FALSE)
	{
		$order_id=WPBooking()->get('order_id');
		$f_name=get_post_meta($order_id,'wpbooking_user_first_name',true);
        $l_name=get_post_meta($order_id,'wpbooking_user_last_name',true);
        $full = $f_name.' '.$l_name;
		if(empty($full)){
            return wpbooking_load_view('emails/shortcodes/order_name_customer');
		}
        return $full;
	}

	add_shortcode('wpbooking_email_name_customer','wpbooking_email_name_customer_func');
}