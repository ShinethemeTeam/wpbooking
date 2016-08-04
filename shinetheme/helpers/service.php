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
		$price_html=sprintf(__('from %s/night','wpbooking'),'<br><span class="price">'.$price_html.'</span>');

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

		$count=get_comments_number($post_id);
		$html.='<span class="rating-count">';
		if($count==0){
			$html.=esc_html__('0 review','wpbooking');
		}elseif($count>1){
			$html.=sprintf(esc_html__('%d reviews','wpbooking'),$count);
		}else{
			$html.=esc_html__('1 review','wpbooking');
		}
		$html.='</span>';

		return $html;

	}
}

if(!function_exists('wpbooking_order_item_status_html')){
	function wpbooking_order_item_status_html($status){
		$all_status=WPBooking_Config::inst()->item('order_item_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "on-hold":
					return sprintf('<label class="label label-warning">%s</label>',$all_status[$status]['label']);
				break;
				case "completed":
					return sprintf('<label class="label label-success">%s</label>',$all_status[$status]['label']);
				break;
				case "cancelled":
				case "refunded":
					return sprintf('<label class="label label-danger">%s</label>',$all_status[$status]['label']);
				break;

				default:
					return sprintf('<label class="label label-default">%s</label>',$all_status[$status]['label']);
					break;
			}
		}else{
			return sprintf('<label class="label label-default">%s</label>',esc_html__('Unknown','wpbooking'));
		}
	}
}
if(!function_exists('wpbooking_order_item_status_color')){
	function wpbooking_order_item_status_color($status){
		$all_status=WPBooking_Config::inst()->item('order_item_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "on-hold":
					return '#f0ad4e';
				break;
				case "completed":
					return '#5cb85c';
				break;
				case "cancelled":
				case "refunded":
					return '#d9534f';
				break;

				default:
					return '#5e5e5e';
					break;
			}
		}else{
			return '#5e5e5e';
		}
	}
}
if(!function_exists('wpbooking_payment_status_html')){
	function wpbooking_payment_status_html($status){

		// Pre-handle for old
		if($status=='on-paying') $status='processing';

		$all_status=WPBooking_Config::inst()->item('order_item_status');
		if(array_key_exists($status,$all_status)){
			switch($status){
				case "processing":
					return sprintf('<label class="label label-info">%s</label>',$all_status[$status]['label']);
				break;
				case "completed":
					return sprintf('<label class="label label-success">%s</label>',$all_status[$status]['label']);
				break;
				case "failed":
					return sprintf('<label class="label label-danger">%s</label>',$all_status[$status]['label']);
				break;
			}
		}else{
			return sprintf('<label class="label label-default">%s</label>',esc_html__('Unknown','wpbooking'));
		}
	}
}
if(!function_exists('wpbooking_get_order_item_used_gateway')){
	function wpbooking_get_order_item_used_gateway($payment_id=FALSE){

		$payment=WPBooking_Payment_Model::inst()->find($payment_id);
		if($payment and !empty($payment['gateway'])){
			$gateway=WPBooking_Payment_Gateways::inst()->get_gateway($payment['gateway']);
			if($gateway){
				return $gateway->get_info('label');
			}else{
				return esc_html__('Unknown Gateway','wpbooking');
			}

		}
	}
}

if(!function_exists('wpbooking_post_query_desc'))
{
	function wpbooking_post_query_desc($input=FALSE)
	{
		if(!$input) $input=WPBooking_Input::get();

		$q=array();
		if(!empty($input['location_id']) and $location_id=$input['location_id']){
			$location=get_term($location_id,'wpbooking_location');
			if(!is_wp_error($location) and $location)
			$q[]=sprintf(esc_html__('in %s','wpbooking'),$location->name);
		}
		if(!empty($input['check_in']) and $check_in=$input['check_in']){
			$q[]=sprintf(esc_html__('from %s','wpbooking'),$check_in);

			if(!empty($input['check_out']) and $check_out=$input['check_out']){
				$q[]=sprintf(esc_html__('to %s','wpbooking'),$check_out);
			}
		}

		if(!empty($input['guest']) and $guest=$input['guest']){
			$q[]=sprintf(esc_html__('%d guest(s)','wpbooking'),$guest);
		}
		$query_desc=FALSE;
		if(!empty($q)){
			foreach($q as $key=>$val){
				if($key==count($q)-1 && count($q)>1){
					$query_desc.='and ';
				}
				$query_desc.=$val.' ';
			}
		}

		return  apply_filters('wpbooking_service_post_query_desc',$query_desc,$q,$input);
	}
}
if(!function_exists('wpbooking_comment_nav')){
	function wpbooking_comment_nav() {
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<nav class="wb-reviews-pagination">';
			paginate_comments_links( apply_filters( 'wpbooking_comment_pagination_args', array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
			) ) );
			echo '</nav>';
		endif;
	}
}
if(!function_exists('wpbooking_comment_item')){
	function wpbooking_comment_item( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		echo wpbooking_load_view( 'single/review/item', array( 'comment' => $comment, 'args' => $args, 'depth' => $depth ) );
	}
}