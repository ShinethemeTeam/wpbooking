<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/20/2016
 * Time: 6:00 PM
 */
if(!function_exists('traveler_service_price'))
{
	function traveler_service_price($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$base_price= get_post_meta($post_id,'price',true);
		$service_type= get_post_meta($post_id,'service_type',true);

		$base_price= apply_filters('traveler_service_base_price',$base_price,$post_id,$service_type);
		$base_price= apply_filters('traveler_service_base_price_'.$service_type,$base_price,$post_id,$service_type);

		return $base_price;
	}
}
if(!function_exists('traveler_service_price_html'))
{
	function traveler_service_price_html($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$price=traveler_service_price($post_id);
		$currency=get_post_meta($post_id,'currency',TRUE);
		$service_type= get_post_meta($post_id,'service_type',true);

		$price_html=Traveler_Currency::format_money($price,array('currency'=>$currency));

		$price_html= apply_filters('traveler_service_base_price',$price_html,$post_id,$service_type);
		$price_html= apply_filters('traveler_service_base_price_'.$service_type,$price_html,$post_id,$service_type);

		return $price_html;
	}
}