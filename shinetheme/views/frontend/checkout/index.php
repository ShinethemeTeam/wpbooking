<?php
$booking = WPBooking_Checkout_Controller::inst();
$cart = $booking->get_cart();
if (empty($cart)) {
    wpbooking_set_message(esc_html__('Sorry! Your cart is currently empty', 'wp-booking-management-system'), 'danger');
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
            <form id="wpbooking-checkout-form" action="<?php echo home_url('/') ?>" onsubmit="return false" method="post" novalidate enctype="multipart/form-data">
                <input class="wpbooking_check_empty_cart" type="hidden" value="true">
                <div class="row">
                    <div class="col-md-7">
                        <div class="checkout-form-wrap">
                            <h5 class="checkout-form-title"><?php echo esc_html__('Billing Information', 'wp-booking-management-system') ?></h5>
                            <h5 class="checkout-form-sub-title">
                                <?php
                                if(is_user_logged_in()){
                                    echo esc_html__('Billing Information', 'wp-booking-management-system');
                                }else{
                                    echo esc_html__("Already have an account?","wp-booking-management-system");
                                    $url_check_out = get_permalink(wpbooking_get_option('checkout_page'));
                                ?>
                                     <a href="<?php echo esc_url(add_query_arg(array("redirect_to"=>$url_check_out),wp_login_url())); ?>" class="text-color"><?php echo esc_html__('Login',"wp-booking-management-system") ?></a>
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
                        <h5 class="checkout-form-title"><?php echo esc_html__('Method of Payment', 'wp-booking-management-system') ?></h5>
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
                                $link = '<a target="_blank" class="term_condition" href="'.esc_url(get_permalink($page_term_condition)).'">'.esc_attr__("terms and conditions", 'wp-booking-management-system').'</a>';
                                printf(esc_html__("I have read and accepted the %s","wp-booking-management-system"),$link);
                                ?>
                            </label>
                        </div>
                        <div class="checkout-submit-button">
                            <button type="submit" class="wb-btn wb-btn-primary wb-btn-md submit-button" disabled><?php echo esc_html__('CHECKOUT', 'wp-booking-management-system') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
