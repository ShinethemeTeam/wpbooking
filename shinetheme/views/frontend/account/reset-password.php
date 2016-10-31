<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 10/31/2016
 * Version: 1.0
 */
?>
<form class="wb-form-reset-password" method="post" action="">
    <input type="hidden" name="action" value="wpbooking_reset_pass">
    <h3 class="form-title"><?php esc_html_e('Reset Password','wpbooking'); ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="new_password" class="control-label"><?php esc_html_e('New Password')?> <span class="required">*</span></label>
            <input type="password" name="new_password" id="new_password" class="form-control" value="">
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label"><?php echo esc_html__('Confirm Password','wpbooking'); ?> <span class="required">*</span></label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="">
        </div>
        <div class="form-group">
            <p class="note"><?php echo esc_html__('Tips: Use at least 8 characters. Don’t re-use passwords from other websites or include obvious words like your name or email.','wpbooking')?></p>
        </div>
        <div class="form-group">
            <button class="wb-btn wb-btn-default wb-btn-lg" type="submit"><?php echo esc_html__('Reset Password'); ?></button>
        </div>
    </div>
    <?php
        wp_nonce_field('wb-reset-password');
        if(WPBooking_Input::post('action') == 'wpbooking_reset_pass'){
            echo wpbooking_get_message();
        }
    ?>
</form>
