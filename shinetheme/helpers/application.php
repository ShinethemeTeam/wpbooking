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
		$file=Traveler()->get_dir($view.'.php');
		if(is_file($file)){

			extract($data);
			ob_start();
			include($file);
			return @ob_get_clean();
		}
	}
}