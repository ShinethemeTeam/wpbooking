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
		echo '<div id="primary" class="content-area">
				<main id="main" class="site-main twentyfifteen" role="main">';
		break;
	default :
		echo '<div id="primary" class="content-area">
				<main id="main" class="site-main twentyfifteen" role="main">';
		break;
}