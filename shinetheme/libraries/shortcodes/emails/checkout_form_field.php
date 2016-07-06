<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/3/2016
 * Time: 3:15 PM
 */
if(!function_exists('wpbooking_email_checkout_form_field_func'))
{
	function wpbooking_email_checkout_form_field_func($attr=array(),$content=FALSE)
	{
		$attr=wp_parse_args($attr,array(
			'name'=>FALSE
		));
		if(!$attr['name']) return FALSE;

		$order_id=WPBooking()->get('order_id');
		if($order_id){
			$form_data=WPBooking_Order::inst()->get_order_form_datas($order_id);

			if(array_key_exists($attr['name'],$form_data)){
				return WPBooking_Admin_Form_Build::inst()->get_form_field_data($form_data[$attr['name']]);
			}

		}else{
			// Preview Email
			return esc_html__('Example Value','wpbooking');
		}
	}

	add_shortcode('wpbooking_email_checkout_form_field','wpbooking_email_checkout_form_field_func');
}