<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 9:47 AM
 */
$booking=Traveler_Booking::inst();
$form_id=$booking->get_checkout_form();

$cart=$booking->get_cart();
if(empty($cart))
{
	traveler_set_message(__('Sorry! Your cart is currently empty','traveler-booking'),'danger');
}

echo traveler_get_message();

if(empty($cart))
{
	return;
}
?>
<div class="traveler-checkout-wrap">
	<form class="traveler_checkout_form" action="<?php echo home_url('/') ?>" onsubmit="return false" method="post">
		<div class="traveler-checkout-form">
			<input name="action" value="traveler_do_checkout" type="hidden">
			<?php echo do_shortcode($form_id)?>
		</div>
		<div class="traveler-checkout-review-order">
			<div class="traveler-review-order">
				<?php echo traveler_load_view('checkout/review') ?>

			</div>
			<div class="traveler-gateways">
				<?php echo traveler_load_view('checkout/gateways') ?>
			</div>
			<div class="checkout-submit-button">
				<button type="submit" class="button button-primary submit-button"><?php _e('Place Your Order','traveler-booking') ?></button>
			</div>
		</div>
	</form>

</div>
