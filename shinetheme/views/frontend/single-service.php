<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:20 PM
 */
get_header();
?>

<?php
	echo traveler_load_view('single/content');

	do_action('traveler_booking_sidebar');

get_footer();