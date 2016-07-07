<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 9:47 AM
 */
$booking=WPBooking_Order::inst();
$form_id=$booking->get_checkout_form();

$cart=$booking->get_cart();
if(empty($cart))
{
	wpbooking_set_message(__('Sorry! Your cart is currently empty','wpbooking'),'danger');
}

echo wpbooking_get_message();

if(empty($cart))
{
	return;
}

?>
<div class="wpbooking-checkout-wrap">
	<form class="wpbooking_checkout_form" action="<?php echo home_url('/') ?>" onsubmit="return false" method="post" novalidate>
		<div class="wpbooking-checkout-form">
			<input name="action" value="wpbooking_do_checkout" type="hidden">
			<?php echo do_shortcode($form_id)?>
			<?php if(!is_user_logged_in()): ?>
			<label ><input type="checkbox" name="wpbooking_create_account" value="1"><?php printf(esc_html__('Create %s account','wpbooking'),get_bloginfo('name')) ?></label>
			<?php endif;?>
		</div>
		<div class="wpbooking-checkout-review-order">
			<div class="wpbooking-review-order">
				<?php echo wpbooking_load_view('checkout/review') ?>

			</div>
			<div class="wpbooking-gateways">
				<?php echo wpbooking_load_view('checkout/gateways') ?>
			</div>
			<div class="checkout-submit-button">
				<button type="submit" class="button button-primary submit-button"><?php _e('Place Your Order','wpbooking') ?></button>
			</div>
		</div>
	</form>

</div>
