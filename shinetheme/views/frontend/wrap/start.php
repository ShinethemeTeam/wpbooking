<?php
$template = get_option( 'template' );
switch($template){

	case 'twentyfifteen' :
		echo '<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">';
		break;
    case "twentythirteen":
        echo '<div id="primary" class="content-area">
				<main id="content" class="entry-content" role="main">';
        break;
    case "twentytwelve":
        echo '<div id="primary" class="site-content">
				<main id="content" role="main">';
        break;
	default :
		echo '<div id="primary" class="content-area">
				<main id="main" class="site-main '.esc_attr($template).'" role="main">';
		break;
}