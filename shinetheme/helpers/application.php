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

