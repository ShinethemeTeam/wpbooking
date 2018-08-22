<?php
$captcha = WPBooking_Captcha::inst();
if($captcha->_is_check_allow_captcha()){
?>
    <h5 class="checkout-form-title"><?php echo esc_html__('Captcha','wp-booking-management-system')?></h5>
    <div class="content-captcha">
        <?php echo do_shortcode($captcha->get_recaptcha()); ?>
    </div>
<?php } ?>