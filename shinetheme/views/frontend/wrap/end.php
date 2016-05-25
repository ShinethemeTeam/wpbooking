<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 5/25/2016
 * Time: 2:59 PM
 */
$template = get_option( 'template' );
switch($template){

	case 'twentyfifteen' :
		echo '</main></div>';
		break;
	default :
		echo '</main></div>';
		break;
}