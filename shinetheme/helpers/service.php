<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/20/2016
 * Time: 6:00 PM
 */
if(!function_exists('wpbooking_service_price'))
{
	function wpbooking_service_price($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$base_price= get_post_meta($post_id,'price',true);
		$service_type= get_post_meta($post_id,'service_type',true);

		$base_price= apply_filters('wpbooking_service_base_price',$base_price,$post_id,$service_type);
		$base_price= apply_filters('wpbooking_service_base_price_'.$service_type,$base_price,$post_id,$service_type);

		return $base_price;
	}
}
if(!function_exists('wpbooking_service_price_html'))
{
	function wpbooking_service_price_html($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();

		$price=wpbooking_service_price($post_id);
		//$currency=get_post_meta($post_id,'currency',TRUE);
		$service_type= get_post_meta($post_id,'service_type',true);

		$price_html=WPBooking_Currency::format_money($price);
		switch(get_post_meta($post_id,'price_type',true)){
			case "per_night":
				$price_html=sprintf(__('%s Per Night','wpbooking'),$price_html);
				break;
		}

		$price_html= apply_filters('wpbooking_service_base_price',$price_html,$post_id,$service_type);
		$price_html= apply_filters('wpbooking_service_base_price_'.$service_type,$price_html,$post_id,$service_type);

		return $price_html;
	}
}
if(!function_exists('wpbooking_service_rate_to_html'))
{
	function wpbooking_service_rate_to_html($post_id=FALSE)
	{
		if(!$post_id) $post_id=get_the_ID();
		$rate=WPBooking_Comment_Model::inst()->get_avg_review($post_id);

		$html= '
		<span class="rating-stars">';
		for($i=1;$i<=5;$i++){
			$active=FALSE;
			if($rate>=$i) $active='active';
			$html.=sprintf('<a class="%s"><i class="fa fa-star-o icon-star"></i></a>',$active);
		}
		$html.='</span>';

		return $html;

	}
}
