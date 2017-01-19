<?php
/**
 * Created by wpbooking.
 * Developer: nasanji
 * Date: 1/6/2017
 * Version: 1.0
 */

/**
 * Get content wrapper in page archive
 */
add_action('wpbooking_after_main_header', 'wpbooking_content_wrapper_start_html', 15);
add_action('wpbooking_before_main_footer', 'wpbooking_content_wrapper_end_html', 15);


add_action('wpbooking_loop_item_content', 'wpbooking_get_item_content_html', 15);