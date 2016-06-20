<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/20/2016
 * Time: 3:50 PM
 */
if(!function_exists('WPBooking_Helpers'))
{
	class WPBooking_Helpers{

		/**
		 * Check if current is ajax request
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return bool
		 */
		static function is_ajax()
		{
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				return true;
			}
			return false;
		}

	}
}