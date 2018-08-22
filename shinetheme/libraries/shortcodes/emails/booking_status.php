<?php
if(!function_exists('wpbooking_email_order_status_func'))
{
    function wpbooking_email_order_status_func($attr=array(),$content=FALSE)
    {
        $order_id=WPBooking()->get('order_id');
        if(!$order_id){
            return wpbooking_load_view('emails/shortcodes/booking_status');
        }
        $order=new WB_Order($order_id);
        return $order->get_status_email_html();
    }

    add_shortcode('wpbooking_email_order_status','wpbooking_email_order_status_func');
}