<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 2:07 PM
 */
if(!function_exists('traveler_admin_load_view')) {
	function traveler_admin_load_view($view, $data = array())
	{
		$file=Traveler()->get_dir('shinetheme/views/admin/'.$view.'.php');
		if(file_exists($file)){

			extract($data);
			ob_start();
			include($file);
			return @ob_get_clean();
		}
	}
}
if(!function_exists('traveler_load_view')) {
	function traveler_load_view($view, $data = array())
	{
		// Try to find overided file in theme_name/traveler-booking/file-name.php
		$file=locate_template(array(
			'traveler-booking/'.$view.'.php'
		),FALSE);

		if(!file_exists($file)){

			$file=Traveler()->get_dir('shinetheme/views/frontend/'.$view.'.php');
		}

		if(file_exists($file)){

			extract($data);
			ob_start();
			include($file);
			return @ob_get_clean();
		}
	}
}
if(!function_exists('traveler_view_path')) {
	function traveler_view_path($view)
	{
		// Try to find overided file in theme_name/traveler-booking/file-name.php
		$file=locate_template(array(
			'traveler-booking/'.$view.'.php'
		),FALSE);

		if(!file_exists($file)){

			$file=Traveler()->get_dir('shinetheme/views/frontend/'.$view.'.php');
		}

		if(file_exists($file)){

			return $file;
		}
	}
}

if(!function_exists('traveler_get_admin_message'))
{
	function traveler_get_admin_message($clear_message=true){
		$message=Traveler()->get_admin_message($clear_message);

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
if(!function_exists('traveler_get_message'))
{
	function traveler_get_message($clear_message=true){
		$message=Traveler()->get_message($clear_message);

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
if(!function_exists('traveler_set_admin_message'))
{
	function traveler_set_admin_message($message,$type='information'){
		Traveler()->set_admin_message($message,$type);
	}
}
if(!function_exists('traveler_set_message'))
{
	function traveler_set_message($message,$type='information'){
		Traveler()->set_message($message,$type);
	}
}

if( !function_exists('traveler_encrypt') ){
	function traveler_encrypt( $string = '' ){
		return md5( md5( Traveler_Config::inst()->item('encrypr_key') ) . md5( $string ) );
	}
}
if( !function_exists('traveler_encrypt_compare') ){
	function traveler_encrypt_compare( $string = '', $encrypt = ''){
		$string = md5( md5( Traveler_Config::inst()->item('encrypr_key') ) . md5( $string ) );

		if( $string == $encrypt ){
			return true;
		}
		return false;
	}
}
if( !function_exists('traveler_origin_id') ){
	function traveler_origin_id( $post_id , $post_type = 'post', $return_origin ){
		if(function_exists('icl_object_id')) {
		    return icl_object_id( $post_id, $post_type, true );
		} else {
		    return $post_id;
		}
	}
}
if( !function_exists('dateDiff') ){
	function dateDiff( $start, $end ){
        $start = strtotime( $start );
        $end = strtotime( $end );
        return ($end - $start) / (60 * 60 * 24);
    }
}

if( !function_exists('traveler_show_tree_terms') ){
	function traveler_show_tree_terms( array &$terms, array &$returns, $parent = 0 , $deep = 0){
		if( count( $terms ) == 0 ){
			return $returns;
		}
		$list_tmp = array();
		foreach ( $terms as $i => $term ) {
	        if ( $term->parent == $parent ) {
	            $returns[] = array(
	            	'id' => $term->term_id,
	            	'name' => $term->name,
	            	'deep' => $deep,
	            	'parent_name' => traveler_get_term( 'term_id', $parent, 'traveler_location', 'name', $term->name)
	            );
	            $list_tmp[] = $term;
	            unset( $terms[ $i ] );
	        }
	    }
	    if( $list_tmp ){
		    foreach ( $list_tmp as $child ) {
		    	$deep += 15;
		        traveler_show_tree_terms( $terms, $returns, $child->term_id, $deep );
		    }
		}
	}
}
if( !function_exists('traveler_get_term') ){
	function traveler_get_term( $field, $value, $term, $field_return, $default ){
		$term = get_term_by( $field, $value, $term );

		if( !empty( $term ) ){
			return $term->$field_return;
		}
		return $default;
	}
}