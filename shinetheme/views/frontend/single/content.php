<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:23 PM
 */

if (post_password_required()) {
	echo get_the_password_form();
	return;
}
$service = wpbooking_get_service();
$service_type=$service->get_type();
if(!$template=wpbooking_load_view('single/types/'.$service_type)){
	$template=wpbooking_load_view('single/types/default');
}
$template=apply_filters('wpbooking_single_content_template',$template,get_the_ID(),$service);

echo do_shortcode($template);