<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 10/28/2016
 * Version: 1.0
 */
?>
<form class="wb-form-lost-password" action="" method="post">
    <input type="hidden" name="action" value="wpbooking_lost_pass">
    <h3 class="form-title"><?php esc_html_e('Lost password','wpbooking'); ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="username_email" class="control-label"><?php echo esc_html__('Username or email address','wpbooking'); ?> <span class="required">*</span></label>
            <input class="form-control" id="user_login" name="user_login" value="">
        </div>
        <div class="form-group">
            <button type="submit" class="wb-btn wb-btn-default wb-btn-lg"><?php echo esc_html__('Reset Password','wpbooking'); ?></button>
        </div>
        <a class="login-url" href="<?php echo esc_url(wp_login_url()); ?>"><?php echo esc_html__('Login','wpbooking'); ?></a>
    </div>
    <?php wp_nonce_field('wb_lost_password'); ?>
    <?php if(WPBooking_Input::post('action') == 'wpbooking_lost_password'){
        echo wpbooking_get_message();
    }?>
</form>
