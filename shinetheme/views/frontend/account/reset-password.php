<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 10/31/2016
 * Version: 1.0
 */

$error_fields = array();
if(WPBooking_Session::get('error_rs_field')){
    $error_fields = WPBooking_Session::get('error_rs_field');
    WPBooking_Session::destroy('error_rs_field');
}
?>
<form class="wb-form-reset-password" method="post" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>">
    <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( WPBooking_Input::get('login') ); ?>" autocomplete="off" />
    <input type="hidden" name="rp_key" value="<?php echo esc_attr( WPBooking_Input::get('key') ); ?>" />
    <h3 class="form-title"><?php echo esc_html__('Reset Password','wp-booking-management-system'); ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="new_password" class="control-label"><?php echo esc_html__('New Password', 'wp-booking-management-system')?> <span class="required">*</span></label>
            <input type="password" required name="new_password" id="new_password" class="form-control <?php echo (array_key_exists('new_password',$error_fields)?'wb-error':''); ?>" value="">
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label"><?php echo esc_html__('Confirm Password','wp-booking-management-system'); ?> <span class="required">*</span></label>
            <input type="password" required name="confirm_password" id="confirm_password" class="form-control <?php echo (array_key_exists('confirm_password',$error_fields)?'wb-error':''); ?>" value="">
        </div>
        <div class="form-group">
            <p class="note"><?php echo esc_html__('Tips: Use 8 characters at least. Don\'t re-use passwords from other websites or include obvious words like your name or email.','wp-booking-management-system')?></p>
        </div>
        <div class="form-group">
            <button class="wb-btn wb-btn-default wb-btn-lg" type="submit"><?php echo esc_html__('Reset Password', 'wp-booking-management-system'); ?></button>
        </div>
    </div>
    <?php
    if(!empty(WPBooking_Input::get('reset') == 'error'))
        echo wpbooking_get_message();
    ?>
</form>
