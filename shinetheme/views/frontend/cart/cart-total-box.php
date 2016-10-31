<?php
$booking=WPBooking_Checkout_Controller::inst();
$cart=$booking->get_cart();
$service_type = $cart['service_type'];
?>
<h5 class="checkout-form-title"><?php esc_html_e("Total","wpbooking") ?></h5>
<div class="review-cart-total">
    <div class="review-cart-item">

        <?php do_action('wpbooking_other_total_item_information_'.$service_type,$cart) ?>

        <?php if ($price = $booking->get_cart_extra_price()) { ?>
            <span class="total-title">
					<?php _e('Extra Price:', 'wpbooking') ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
        <?php } ?>

        <?php if (!empty($cart['tax']['vat']['vat_excluded']) and $cart['tax']['vat']['vat_excluded'] != 'no') {
            $vat_amount = $cart['tax']['vat']['vat_amount']."% ";
            $vat_unit = $cart['tax']['vat']['vat_unit'];
            if($vat_unit == 'fixed') $vat_amount = '';
            ?>
            <span class="total-title">
					<?php  echo sprintf(esc_html__("%s V.A.T",'wpbookng'),$vat_amount); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money(1000); ?></span>
        <?php } ?>
        <?php if (!empty($cart['tax']['citytax']['citytax_excluded']) and $cart['tax']['citytax']['citytax_excluded'] != 'no') {
            $citytax_amount = $cart['tax']['citytax']['citytax_amount']."% ";
            $citytax_unit = $cart['tax']['citytax']['citytax_unit'];
            ?>
            <span class="total-title">
					<?php  esc_html_e("City Tax",'wpbookng'); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money(1000); ?></span>
        <?php } ?>


    </div>
    <span class="total-line"></span>
    <div class="review-cart-item total">

        <span class="total-title text-up text-bold"><?php _e('Total Amount', 'wpbooking') ?></span>
        <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money(1000); ?></span>

        <span class="total-title text-color"> <?php _e('Deposit/Pay Now', 'wpbooking') ?></span>
        <span class="total-amount text-color"><?php echo WPBooking_Currency::format_money(1000); ?></span>

        <span class="total-title text-bold"><?php _e('You’ll pay at the property', 'wpbooking') ?></span>
        <span class="total-amount text-bold"><?php echo WPBooking_Currency::format_money(1000); ?></span>

    </div>
</div>