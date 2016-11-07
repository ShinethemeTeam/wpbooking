<?php
$captcha = WPBooking_Captcha::inst();
if($captcha->_is_check_allow_captcha()){
?>
    <h5 class="checkout-form-title"><?php _e('Captcha','wpbooking')?></h5>
    <div class="content-captcha">
        <?php echo balanceTags($captcha->get_recaptcha()); ?>
    </div>
<?php } ?>