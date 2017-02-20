<?php
$booking = WPBooking_Checkout_Controller::inst();
$cart = $booking->get_cart();
if (empty($cart)) {
    wpbooking_set_message(__('Sorry! Your cart is currently empty', 'wpbooking'), 'danger');
}
echo wpbooking_get_message();
if (empty($cart)) {
    return;
}
$allow_guest_checkout=wpbooking_get_option('allow_guest_checkout');
if(!$allow_guest_checkout and !is_user_logged_in()){
    $checkout_url=$booking->get_checkout_url();
    ?>
    <script type="text/javascript">
        window.location.href='<?php echo esc_url($checkout_url) ?>';
    </script>
    <?php
    return false;
}
?>
<div class="wpbooking-checkout-wrap">
    <div class="wpbooking_checkout_form">
        <div class="wpbooking-checkout-review-order">
            <div class="wpbooking-review-order">
                <?php echo wpbooking_load_view('checkout/review') ?>
            </div>
        </div>
        <div class="wpbooking-checkout-form wpbooking-bootstrap">
            <form action="<?php echo home_url('/') ?>" onsubmit="return false" method="post" novalidate enctype="multipart/form-data">
                <input class="wpbooking_check_empty_cart" type="hidden" value="true">
                <div class="row">
                    <div class="col-md-7">
                        <div class="checkout-form-wrap">
                            <h5 class="checkout-form-title"><?php esc_html_e('Billing Information', 'wpbooking') ?></h5>
                            <h5 class="checkout-form-sub-title">
                                <?php
                                if(is_user_logged_in()){
                                    esc_html_e('Billing Information', 'wpbooking');
                                }else{
                                    esc_html_e("Already have an account?","wpbooking");
                                    $url_check_out = get_permalink(wpbooking_get_option('checkout_page'));
                                ?>
                                     <a href="<?php echo esc_url(add_query_arg(array("redirect_to"=>$url_check_out),wp_login_url())); ?>" class="text-color"><?php esc_html_e('Login',"wpbooking") ?></a>
                                <?php } ?>
                            </h5>
                            <input name="action" value="wpbooking_do_checkout" type="hidden">
                            <?php
                            do_action('wpbooking_billing_information_form');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <?php echo wpbooking_load_view('checkout/cart-total-box') ?>
                        <h5 class="checkout-form-title"><?php esc_html_e('Method of Payment', 'wpbooking') ?></h5>
                        <div class="wpbooking-gateways">
                            <?php echo wpbooking_load_view('checkout/gateways') ?>
                        </div>
                        <div class="wpbooking-captcha">
                            <?php echo wpbooking_load_view('checkout/captcha') ?>
                        </div>
                        <div class="form-group">
                            <label for="term_condition">
                                <input type="checkbox" id="term_condition" name="term_condition"  value="1">
                                <?php
                                $page_term_condition = wpbooking_get_option('term-page');
                                $link = '<a class="term_condition" href="'.esc_url(get_permalink($page_term_condition)).'">'.esc_attr__("terms and conditions").'</a>';
                                printf(esc_html__("I have read and accepted the %s","wpbooking"),$link);
                                ?>
                            </label>
                        </div>
                        <div class="checkout-submit-button">
                            <button type="submit" class="wb-btn wb-btn-primary wb-btn-md submit-button" disabled><?php _e('CHECKOUT', 'wpbooking') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
