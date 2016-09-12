<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:20 PM
 */
get_header();
    echo wpbooking_load_view('wrap/start');
        echo wpbooking_load_view('single/content');
    echo wpbooking_load_view('wrap/end');

    get_sidebar('wpbooking');
get_footer();