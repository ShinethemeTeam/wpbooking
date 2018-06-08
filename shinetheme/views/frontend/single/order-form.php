<?php
// External Booking
if($url=get_post_meta(get_the_ID(),'external_booking_url',true) ){
	printf('<a href="%s" class="mt20 wb-btn wb-btn-blue" target="_blank">%s</a>',$url,esc_html__('Book Now','wp-booking-management-system'));
	return;
}
$booking=WPBooking_Order::inst();
$extra_price=wpbooking_load_view('single/extra-price');
$form=$booking->get_order_form_by_post_id();
printf('<form onsubmit="return false" class="wpbooking_order_form">
			<input name="action" value="wpbooking_add_to_cart" type="hidden">
			<input name="post_id" value="%d" type="hidden">
			<input type="hidden" class="minimum_stay" value="%s">
		%s
		</form>',get_the_ID(),get_post_meta(get_the_ID(),'minimum_stay',true),$form);
