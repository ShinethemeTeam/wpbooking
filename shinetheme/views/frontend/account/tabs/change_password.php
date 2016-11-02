<h3 class="tab-page-title">
    <?php esc_html_e("Change Password",'wpbooking') ?>
</h3>
<form action="" method="post">
    <input type="hidden" name="action" value="wpbooking_change_password">
    <div class="container-fluid">
        <div class="row border">

            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_password"><?php esc_html_e('Old Password','wpbooking') ?></label>
                    <input type="password" class="form-control" id="u_password" name="u_password" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_new_password"><?php esc_html_e('New Password','wpbooking') ?></label>
                    <input type="password" class="form-control" id="u_new_password" name="u_new_password" >
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="u_re_new_password"><?php esc_html_e('New Password Again','wpbooking') ?></label>
                    <input type="password" class="form-control" id="u_re_new_password" name="u_re_new_password" >
                </div>
            </div>
            <div class="col-md-9 text-info">
                <?php esc_html_e("Tips: Use at least 8 characters. Don’t re-use passwords from other websites or include obvious words like your name or email.","wpbooking") ?>
            </div>
            <div class="col-md-12">
                <?php if(WPBooking_Input::post('action')=='wpbooking_change_password') echo wpbooking_get_message(); ?>
                <button type="submit" class="btn wb-btn-default"><?php esc_html_e('Update password','wpbooking') ?></button>
            </div>
        </div>
    </div>
</form>