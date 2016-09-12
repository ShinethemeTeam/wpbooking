<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/5/2016
 * Time: 9:47 AM
 */
$booking = WPBooking_Order::inst();
$form_id = $booking->get_checkout_form();

$cart = $booking->get_cart();
if (empty($cart)) {
    wpbooking_set_message(__('Sorry! Your cart is currently empty', 'wpbooking'), 'danger');
}

echo wpbooking_get_message();

if (empty($cart)) {
    return;
}

?>
<div class="wpbooking-checkout-wrap">
    <div class="wpbooking_checkout_form">

        <div class="wpbooking-checkout-form">
            <form action="<?php echo home_url('/') ?>" onsubmit="return false" method="post" novalidate>
                <div class="checkout-form-wrap">
                    <h5 class="checkout-form-title"><?php esc_html_e('Customer Information', 'wpbooking') ?></h5>
                    <input name="action" value="wpbooking_do_checkout" type="hidden">
                    <?php echo do_shortcode($form_id) ?>
                    <?php if (!is_user_logged_in()): ?>
                        <p>
                            <label><input type="checkbox" name="wpbooking_create_account"
                                          value="1"> <?php printf(esc_html__('Create %s account', 'wpbooking'), get_bloginfo('name')) ?>
                            </label>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="wpbooking-gateways">
                    <h5 class="checkout-form-title"><?php esc_html_e('Payment method', 'wpbooking') ?></h5>
                    <?php echo wpbooking_load_view('checkout/gateways') ?>
                </div>
                <div class="checkout-submit-button">
                    <button type="submit"
                            class="wb-btn wb-btn-blue wb-btn-md submit-button"><?php _e('Checkout Now', 'wpbooking') ?></button>
                </div>
            </form>
        </div>
        <div class="wpbooking-checkout-review-order">
            <div class="wpbooking-review-order">
                <?php echo wpbooking_load_view('checkout/review') ?>
            </div>
            <div class="wpbooking-coupon-form">
                <div class="wb-message"></div>
                <input type="text" class="form-control wb-coupon-code"
                       placeholder="<?php esc_html_e('Enter coupon code', 'wpbooking') ?>">
                <a type="button" class="wb-btn wb-btn-default wb-coupon-apply"><?php esc_html_e('Apply', 'wpbooking') ?>
                    <i class="loading fa fa-spinner fa-pulse fa-fw"></i></a>
            </div>

            <?php echo wpbooking_load_view('cart/cart-total-box') ?>
        </div>
    </div>

</div>
