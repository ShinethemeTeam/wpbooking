<?php
$booking=WPBooking_Checkout_Controller::inst();
$cart=$booking->get_cart();
$service_type = $cart['service_type'];
?>
<h5 class="checkout-form-title"><?php esc_html_e("Total","wpbooking") ?></h5>
<div class="review-cart-total">
    <div class="review-cart-item">
        <?php do_action('wpbooking_other_total_item_information_'.$service_type,$cart) ?>
        <?php
        $tax = $booking->get_cart_tax_price();
        if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != 'no') {
            $amount = $tax['vat']['amount']."% ";
            $unit = $tax['vat']['unit'];
            if($unit == 'fixed') $vat_amount = '';
            ?>
            <span class="total-title">
					<?php  echo sprintf(esc_html__("%s V.A.T",'wpbookng'),$vat_amount); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
        <?php } ?>
        <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != 'no') {
            ?>
            <span class="total-title">
					<?php  esc_html_e("City Tax",'wpbookng'); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
        <?php } ?>
    </div>
    <span class="total-line"></span>
    <div class="review-cart-item total">

        <?php
        $price = $booking->get_cart_total_with_tax(array());
        ?>
        <span class="total-title text-up text-bold"><?php _e('Total Amount', 'wpbooking') ?></span>
        <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money($price); ?></span>

        <span class="total-title text-color"> <?php _e('Deposit/Pay Now', 'wpbooking') ?></span>
        <span class="total-amount text-color"><?php echo WPBooking_Currency::format_money(1000); ?></span>

        <span class="total-title text-bold"><?php _e('You’ll pay at the property', 'wpbooking') ?></span>
        <span class="total-amount text-bold"><?php echo WPBooking_Currency::format_money(1000); ?></span>

    </div>
</div>