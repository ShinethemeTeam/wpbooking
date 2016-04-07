<?php
$booking=Traveler_Booking::inst();
$form=$booking->get_order_form_by_post_id();
echo do_shortcode($form);