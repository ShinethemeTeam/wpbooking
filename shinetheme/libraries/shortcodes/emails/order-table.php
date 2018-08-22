<?php
if(!function_exists('wpbooking_email_order_table_func'))
{
    function wpbooking_email_order_table_func($attr=array(),$content=FALSE)
    {
        $order_id=WPBooking()->get('order_id');
        if(!$order_id){
            return wpbooking_load_view('emails/shortcodes/preview/order-table');
        }
        return wpbooking_load_view('emails/shortcodes/order-table',$attr);
    }

    add_shortcode('wpbooking_email_order_table','wpbooking_email_order_table_func');
}