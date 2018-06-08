<?php
/**
 * @hooked wpbooking_content_wrapper_start_html - 15
 */
do_action('wpbooking_after_main_header');
?>
	<?php
	global $wp_query;
    if(isset($my_query)) $wp_query = $my_query;

	if ($wp_query->have_posts()) {
		while ($wp_query->have_posts()) {
            $wp_query->the_post();
            /**
             * wpbooking_loop_item_content
             *
             * @hooked wpbooking_get_item_content_html
             */
            do_action('wpbooking_loop_item_content');

		}
	} else {
		printf('<h3>%s</h3>', esc_html__('Not found anything related to search conditions.', 'wp-booking-management-system'));
	}
	?>
<?php
/**
 * @hooked wpbooking_content_wrapper_end_html - 15
 */
do_action('wpbooking_before_main_footer');
?>
