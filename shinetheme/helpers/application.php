<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 2:07 PM
 */
if(!function_exists('wpbooking_admin_load_view')) {
	function wpbooking_admin_load_view($view, $data = array())
	{
		$file=WPBooking()->get_dir('shinetheme/views/admin/'.$view.'.php');
		if(file_exists($file)){

			extract($data);
			ob_start();
			include($file);
			return @ob_get_clean();
		}
	}
}
if(!function_exists('wpbooking_load_view')) {
	function wpbooking_load_view($view, $data = array())
	{
		$file=locate_template(array(
			'wpbooking/'.$view.'.php'
		),FALSE);

		if(!file_exists($file)){

			$file=WPBooking()->get_dir('shinetheme/views/frontend/'.$view.'.php');
		}

		if(file_exists($file)){

			if(is_array($data))
			extract($data);

			ob_start();
			include($file);
			return @ob_get_clean();
		}
	}
}
if(!function_exists('wpbooking_view_path')) {
	function wpbooking_view_path($view)
	{
		// Try to find overided file in theme_name/wpbooking/file-name.php
		$file=locate_template(array(
			'wpbooking/'.$view.'.php'
		),FALSE);

		if(!file_exists($file)){

			$file=WPBooking()->get_dir('shinetheme/views/frontend/'.$view.'.php');
		}

		if(file_exists($file)){

			return $file;
		}
	}
}

if(!function_exists('wpbooking_get_admin_message'))
{
	function wpbooking_get_admin_message($clear_message=true){
		$message=WPBooking()->get_admin_message($clear_message);

		if($message){
			$type=$message['type'];
			switch($type){
				case "error":
					$type='error';
					break;

				case "success":
					$type='updated';
					break;
				default:
					$type='notice-warning';
					break;
			}
			return sprintf('<div class="notice %s" ><p>%s</p></div>',$type,$message['content']);
		}
	}
}
if(!function_exists('wpbooking_get_message'))
{
	function wpbooking_get_message($clear_message=true){
		$message=WPBooking()->get_message($clear_message);

		if($message){
			$type=$message['type'];
			switch($type){
				case "error":
					$type='danger';
					break;

			}
			return sprintf('<div class="alert alert-%s" >%s</div>',$type,$message['content']);
		}
	}
}
if(!function_exists('wpbooking_set_admin_message'))
{
	function wpbooking_set_admin_message($message,$type='information'){
		WPBooking()->set_admin_message($message,$type);
	}
}
if(!function_exists('wpbooking_set_message'))
{
	function wpbooking_set_message($message,$type='information'){
		WPBooking()->set_message($message,$type);
	}
}

if( !function_exists('wpbooking_encrypt') ){
	function wpbooking_encrypt( $string = '' ){
		return md5( md5( WPBooking_Config::inst()->item('encrypr_key') ) . md5( $string ) );
	}
}
if( !function_exists('wpbooking_encrypt_compare') ){
	function wpbooking_encrypt_compare( $string = '', $encrypt = ''){
		$string = md5( md5( WPBooking_Config::inst()->item('encrypr_key') ) . md5( $string ) );

		if( $string == $encrypt ){
			return true;
		}
		return false;
	}
}
if( !function_exists('wpbooking_origin_id') ){
	function wpbooking_origin_id( $post_id , $post_type = 'post', $return_origin ){
		if(function_exists('icl_object_id')) {
		    return wpml_object_id_filter( $post_id, $post_type, true );
		} else {
		    return $post_id;
		}
	}
}


if( !function_exists('wpbooking_show_tree_terms') ){
	function wpbooking_show_tree_terms( array &$terms, array &$returns, $parent = 0 , $deep = 0){
		if( count( $terms ) == 0 ){
			return $returns;
		}

		$list_tmp = array();
		foreach ( $terms as $i => $term ) {
	        if ( $term->parent == $parent ) {
	            $list_tmp[] = $term;
	            unset( $terms[ $i ] );
	        }
	    }
	    	
	    $deep += 15;


	    if( $list_tmp ){
		    foreach ( $list_tmp as $child ) {
		    	$returns[] = array(
	            	'id' => $child->term_id,
	            	'name' => $child->name,
	            	'deep' => $deep,
	            	'parent_name' => wpbooking_get_term( 'term_id', $term->parent, 'wpbooking_location', 'name', $child->name)
	            );
		        wpbooking_show_tree_terms( $terms, $returns, $child->term_id, $deep );
		    	
		    }
		}
	}
}
if( !function_exists('wpbooking_get_term') ){
	function wpbooking_get_term( $field, $value, $term, $field_return, $default ){
		$term = get_term_by( $field, $value, $term );

		if( !empty( $term ) ){
			return $term->$field_return;
		}
		return $default;
	}
}
if( !function_exists('wpbooking_timestamp_diff_day') ){
	function wpbooking_timestamp_diff_day( $date1, $date2 ){
		$total_time= $date2-$date1;

		$day   = floor($total_time /(3600*24));

		return $day;
	}
}
if(!function_exists('wpbooking_get_translated_string')){
	function wpbooking_get_translated_string($string,$name=FALSE){

		if(!$name) $name=$string;

		do_action( 'wpml_register_single_string', 'wpbooking', $name, $string );

		return $string;
	}
}