<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:31 PM
 */
$booking=Traveler_Booking::inst();
$form=$booking->get_order_form_by_post_id();
echo do_shortcode($form);