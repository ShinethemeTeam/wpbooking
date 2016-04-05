<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:31 PM
 */
$booking=Traveler_Booking::inst();
$form=$booking->get_order_form_by_post_id();
printf('<form onsubmit="return false" class="traveler_order_form">
			<input name="action" value="traveler_add_to_cart" type="hidden">
			<input name="post_id" value="%d" type="hidden">
		%s
		</form>',get_the_ID(),$form);