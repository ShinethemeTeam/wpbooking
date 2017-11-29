<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 10/28/2016
 * Version: 1.0
 */
?>
<form class="wb-form-lost-password" action="<?php echo wp_lostpassword_url(); ?>" method="post">
    <h3 class="form-title"><?php esc_html_e('Lost password','wpbooking'); ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="username_email" class="control-label"><?php echo esc_html__('Username or email address','wpbooking'); ?> <span class="required">*</span></label>
            <input class="form-control <?php echo (WPBooking_Input::get('errors')?'wb-error':'')?>" id="user_login" required name="user_login" value="">
        </div>
        <div class="form-group">
            <button type="submit" name="submit" class="wb-btn wb-btn-default wb-btn-lg" value="reset_pass_submit"><?php echo esc_html__('Reset Password','wpbooking'); ?></button>
        </div>
        <a class="login-url" href="<?php echo esc_url(wp_login_url()); ?>"><?php echo esc_html__('Login','wpbooking'); ?></a>
    </div>
    <?php
    if(WPBooking_Input::get('errors') || WPBooking_Input::get('checkemail') == 'confirm'){
        echo wpbooking_get_message();
    }?>
</form>
