<?php
    /**
     * Created by wpbooking.
     * Developer: nasanji
     * Date: 1/6/2017
     * Version: 1.0
     */

    if ( !function_exists( 'wpbooking_content_wrapper_start_html' ) ) {
        function wpbooking_content_wrapper_start_html()
        {
            $layout = wpbooking_get_layout_archive();
            echo wpbooking_load_view( 'archive/wrapper-start', [ 'layout' => $layout ] );
        }
    }

    if ( !function_exists( 'wpbooking_content_wrapper_end_html' ) ) {
        function wpbooking_content_wrapper_end_html()
        {
            echo wpbooking_load_view( 'archive/wrapper-end' );
        }
    }

    if ( !function_exists( 'wpbooking_get_item_content_html' ) ) {
        function wpbooking_get_item_content_html()
        {
            echo apply_filters( 'wpbooking_archive_loop_item', wpbooking_load_view( 'archive/item' ) );
        }
    }