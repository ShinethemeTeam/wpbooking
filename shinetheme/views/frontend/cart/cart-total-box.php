<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 9/9/2016
 * Time: 9:30 AM
 */
$booking=WPBooking_Order::inst();
?>
<div class="review-cart-total">
    <span class="total-title">
        <?php _e('Total Price:', 'wpbooking') ?>
    </span>
    <span class="total-amount"><?php echo WPBooking_Currency::format_money($booking->get_cart_total(array(
            'without_deposit'        => true,
            'without_tax'            => true,
            'without_extra_price'    => true,
            'without_addition_price' => false,
            'without_discount'=>true

        ))); ?></span>

    <?php if ($price = $booking->get_cart_extra_price()) { ?>
        <span class="total-title">
					<?php _e('Extra Price:', 'wpbooking') ?>
				</span>
        <span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
    <?php } ?>

    <?php if ($price = $booking->get_cart_addition_price()) { ?>
        <span class="total-title">
					<?php _e('Addition:', 'wpbooking') ?>
				</span>
        <span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
    <?php } ?>


    <?php if ($price = $booking->get_cart_tax_price()) { ?>
        <span class="total-title">
					<?php _e('Tax:', 'wpbooking') ?>
				</span>
        <span class="total-amount"><?php echo WPBooking_Currency::format_money($price); ?></span>
    <?php } ?>

    <?php if ($price = $booking->get_cart_discount_price()) { ?>
        <span class="total-title">
					<?php _e('Discount:', 'wpbooking') ?>
				</span>
        <span class="total-amount">-<?php echo WPBooking_Currency::format_money($price); ?> <a href="#" onclick="return false" class="wpbooking-remove-coupon" title="<?php esc_html_e('Remove the coupon','wpbooking') ?>">(x)</a></span>
    <?php } ?>

    <span class="total-line"></span>
    <?php $total_amount = $booking->get_cart_total(array('without_deposit'=>true)); $discount=$booking->get_cart_discount_price();
    $total_amount-=$discount;
    ?>
    <span class="total-title">
					<?php _e('Total Amount:', 'wpbooking') ?>
				</span>
    <span class="total-amount big"><?php echo WPBooking_Currency::format_money($total_amount); ?></span>

    <?php if ($price = $booking->get_cart_paynow_price()) { ?>
        <span class="total-title">
						<?php _e('Deposit/Pay Now:', 'wpbooking') ?>
					</span>
        <span class="total-amount big"><?php echo WPBooking_Currency::format_money($price); ?></span>

        <?php if ($total_amount - $price > 0) {
            ?>
            <span class="total-title">
							<?php _e('Remain:', 'wpbooking') ?>
						</span>
            <span
                class="total-amount big"><?php echo WPBooking_Currency::format_money($total_amount - $price); ?></span>
            <?php
        } ?>
    <?php } ?>
</div>