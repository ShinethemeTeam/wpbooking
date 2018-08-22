
<?php
    $old_pass = ''; $error_fields = array();
    if(WPBooking_Session::get('old_pass')){
        $old_pass = WPBooking_Session::get('old_pass');
        WPBooking_Session::destroy('old_pass');
    }elseif(WPBooking_Session::get('error_c_fields')){
        $error_fields = WPBooking_Session::get('error_c_fields');
        WPBooking_Session::destroy('error_c_fields');
    }
?>
<h3 class="tab-page-title">
    <?php echo esc_html__("Change Password",'wp-booking-management-system') ?>
</h3>
<form class="wb-form-change-password" action="" method="post">
    <input type="hidden" name="action" value="wpbooking_change_password">
    <div class="container-fluid">
        <div class="row border">

            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_password"><?php echo esc_html__('Old Password','wp-booking-management-system') ?></label>
                    <input type="password" class="form-control <?php echo esc_attr($old_pass); ?>" required id="u_password" name="u_password" value="<?php echo esc_attr(WPBooking_Input::post('u_password')); ?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_new_password"><?php echo esc_html__('New Password','wp-booking-management-system') ?></label>
                    <input type="password" class="form-control <?php echo (array_key_exists('u_new_password',$error_fields)?'wb-error':'')?>" required id="u_new_password" name="u_new_password" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_re_new_password"><?php echo esc_html__('Confirm New Password','wp-booking-management-system') ?></label>
                    <input type="password" class="form-control <?php echo (array_key_exists('u_re_new_password',$error_fields)?'wb-error':'')?>" required id="u_re_new_password" name="u_re_new_password" >
                </div>
            </div>
            <div class="col-md-9 text-info">
                <?php echo esc_html__("Tips: Use 8 characters at least. Don't re-use passwords from other websites or include obvious words like your name or email.","wp-booking-management-system") ?>
            </div>
            <div class="col-md-12">
                <?php if(WPBooking_Input::post('action')=='wpbooking_change_password') echo wpbooking_get_message(); ?>
                <button type="submit" class="btn wb-btn-default"><?php echo esc_html__('Update password','wp-booking-management-system') ?></button>
            </div>
        </div>
    </div>
</form>