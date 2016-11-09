<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 10/31/2016
 * Version: 1.0
 */

?>
<form class="wb-form-reset-password" method="post" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>">
    <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( WPBooking_Input::get('login') ); ?>" autocomplete="off" />
    <input type="hidden" name="rp_key" value="<?php echo esc_attr( WPBooking_Input::get('key') ); ?>" />
    <h3 class="form-title"><?php esc_html_e('Reset Password','wpbooking'); ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="new_password" class="control-label"><?php esc_html_e('New Password')?> <span class="required">*</span></label>
            <input type="password" required name="new_password" id="new_password" class="form-control" value="">
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label"><?php echo esc_html__('Confirm Password','wpbooking'); ?> <span class="required">*</span></label>
            <input type="password" required name="confirm_password" id="confirm_password" class="form-control" value="">
        </div>
        <div class="form-group">
            <p class="note"><?php echo esc_html__('Tips: Use at least 8 characters. Don’t re-use passwords from other websites or include obvious words like your name or email.','wpbooking')?></p>
        </div>
        <div class="form-group">
            <button class="wb-btn wb-btn-default wb-btn-lg" type="submit"><?php echo esc_html__('Reset Password'); ?></button>
        </div>
    </div>
    <?php
    if(!empty(WPBooking_Input::get('reset') == 'error'))
        echo wpbooking_get_message();
    ?>
</form>
