<?php
$booking=WPBooking_Checkout_Controller::inst();
$cart=$booking->get_cart();
$service_type = $cart['service_type'];
?>
<h5 class="checkout-form-title"><?php esc_html_e("Total","wpbooking") ?></h5>
<div class="review-cart-total">
    <div class="review-cart-item">
        <?php do_action('wpbooking_check_total_item_information_'.$service_type,$cart) ?>
        <?php
        $tax = $booking->get_cart_tax_price();
        if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != 'no' and $tax['vat']['price']) {
            $vat_amount = $tax['vat']['amount']."% ";
            $unit = $tax['vat']['unit'];
            if($unit == 'fixed') $vat_amount = '';
            ?>
            <span class="total-title">
					<?php  echo sprintf(esc_html__("%s V.A.T",'wpbooking'),$vat_amount); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
        <?php } ?>
        <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != 'no' and $tax['citytax']['price']) {
            ?>
            <span class="total-title">
					<?php  esc_html_e("City Tax",'wpbookng'); ?>
				</span>
            <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
        <?php } ?>
    </div>
    <?php if(!empty($tax['total_price'])) echo '<span class="total-line"></span>' ?>
    <div class="review-cart-item total">
        <?php
        $price_total = $booking->get_cart_total(array(
            'without_tax'        => true
        ));
        ?>
        <span class="total-title text-up text-bold"><?php _e('Total Amount', 'wpbooking') ?></span>
        <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money($price_total); ?></span>
        <?php
        if(!empty($cart['deposit']['status'])){
            $price_deposit = $booking->get_cart_total(array(
                'without_tax'        => true,
                'without_deposit'        => true
            ));

            $property = $price_total - $price_deposit;
            ?>
            <span class="total-title text-color"> <?php _e('Deposit/Pay Now', 'wpbooking') ?></span>
            <span class="total-amount text-color"><?php echo WPBooking_Currency::format_money($price_deposit); ?></span>

            <span class="total-title text-bold"><?php _e('You’ll pay at the property', 'wpbooking') ?></span>
            <span class="total-amount text-bold"><?php echo WPBooking_Currency::format_money($property); ?></span>
        <?php } ?>
    </div>
</div>