<?php
$booking=WPBooking_Checkout_Controller::inst();
$cart=$booking->get_cart();
$service_type = $cart['service_type'];
$price_total=$cart['price']=$booking->get_cart_total();
$tax_total=0;
?>
<h5 class="checkout-form-title"><?php esc_html_e("Total","wpbooking") ?></h5>
<div class="review-cart-total">
    <div class="review-cart-item">
        <?php do_action('wpbooking_check_out_total_item_information_'.$service_type,$cart) ?>
        <?php
        $tax = $booking->get_cart_tax_price($price_total);
        $tax_total=!empty($tax['total_price'])?$tax['total_price']:0;
        if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != '' and $tax['vat']['price']) {
            $vat_amount = $tax['vat']['amount']."% ";
            $unit = $tax['vat']['unit'];
            if($unit == 'fixed') $vat_amount = '';
            ?>
            <span class="total-title">
					<?php  echo sprintf(esc_html__("%s V.A.T",'wpbooking'),$vat_amount); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
        <?php } ?>
        <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != '' and $tax['citytax']['price']) {
            ?>
            <span class="total-title">
					<?php  esc_html_e("City Tax",'wpbooking'); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
        <?php } ?>
    </div>
    <?php do_action("wpbooking_after_check_out_total_price") ?>
    <?php if(!empty($tax['total_price'])) echo '<span class="total-line"></span>' ?>
    <div class="review-cart-item total">
        <span class="total-title text-up text-bold"><?php esc_html_e('Total Amount', 'wpbooking') ?></span>
        <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money($booking->get_total_price_cart_with_tax()); ?></span>
        <?php

        if(!empty($cart['deposit']['status'])){
            $price_deposit = $booking->get_cart_deposit();

            $property = $booking->get_total_price_cart_with_tax() - $price_deposit;
            ?>
            <span class="total-title text-color"> <?php esc_html_e('Deposit/Pay Now', 'wpbooking') ?></span>
            <span class="total-amount text-color"><?php echo WPBooking_Currency::format_money($price_deposit); ?></span>

            <span class="total-title text-bold"><?php esc_html_e('You’ll pay at the property', 'wpbooking') ?></span>
            <span class="total-amount text-bold"><?php echo WPBooking_Currency::format_money($property); ?></span>
        <?php } ?>
    </div>
</div>