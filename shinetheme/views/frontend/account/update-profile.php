<?php
    global $current_user;
?>
<div class="row">

    <div class="col-sm-6">
        <form action="" method="post">
            <input type="hidden" name="action" value="wpbooking_update_profile">
            <h4 class="form-title"><?php echo esc_html__( 'Update Profile', 'wp-booking-management-system' ) ?></h4>

            <div class="form-group">
                <label><?php echo esc_html__( 'Avatar', 'wp-booking-management-system' ) ?></label>
                <input type="hidden" class="form-control" id="u_avatar" name="u_avatar"
                       placeholder="<?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?>"
                       value="<?php echo esc_attr( WPBooking_Input::post( 'u_avatar', get_user_meta( get_current_user_id(), 'u_avatar', true ) ) ) ?>">
                <div class="upload-avatar">
                    <div class="input-group">
							<span class="input-group-btn">
								<span class="btn btn-primary btn-file">
									<?php echo esc_html__( 'Browse...', 'wp-booking-management-system' ) ?> <input type="file"
                                                                                           class="upload_input">
								</span>
							</span>
                        <input type="text" class="form-control image_url "
                               value="<?php echo esc_attr( WPBooking_Input::post( 'u_avatar', get_user_meta( get_current_user_id(), 'avatar', true ) ) ) ?>"
                               readonly="" name="u_avatar">
                    </div>
                    <p class="help-block"><?php echo esc_html__( 'Image formats : jpg, png, gif . Image size is 800x600 and max file size is 2MB', 'wp-booking-management-system' ) ?></p>
                    <div class="upload-message"></div>
                    <?php if ( $avatar = WPBooking_Input::post( 'u_avatar', get_user_meta( get_current_user_id(), 'avatar', true ) ) ) {
                        printf( '<img alt="avatar" src="%s" class="uploaded_image_preview">', $avatar );
                    } ?>
                </div>
            </div>
            <div class="form-group">
                <label for="u_display_name"><?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?></label>
                <input type="text" class="form-control" id="u_display_name" name="u_display_name"
                       placeholder="<?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?>"
                       value="<?php echo esc_attr( WPBooking_Input::post( 'u_display_name', $current_user->display_name ) ) ?>">
            </div>

            <div class="form-group">
                <label for="u_email"><?php echo esc_html__( 'Email', 'wp-booking-management-system' ) ?></label>
                <input type="text" class="form-control" id="u_email" name="u_email"
                       placeholder="<?php echo esc_html__( 'Email', 'wp-booking-management-system' ) ?>"
                       value="<?php echo esc_attr( WPBooking_Input::post( 'u_email', $current_user->user_email ) ) ?>">
            </div>
            <div class="form-group">
                <label for="u_phone"><?php echo esc_html__( 'Phone Number', 'wp-booking-management-system' ) ?></label>
                <input type="text" class="form-control" id="u_phone" name="u_phone"
                       placeholder="<?php echo esc_html__( 'Phone Number', 'wp-booking-management-system' ) ?>"
                       value="<?php echo esc_attr( WPBooking_Input::post( 'u_phone', get_user_meta( get_current_user_id(), 'phone_number', true ) ) ) ?>">
            </div>
            <?php if ( WPBooking_Input::post( 'action' ) == 'wpbooking_update_profile' ) echo wpbooking_get_message(); ?>
            <button type="submit" class="btn btn-default"><?php echo esc_html__( 'Submit', 'wp-booking-management-system' ) ?></button>

        </form>
    </div>
    <div class="col-sm-6">
        <form action="" method="post">
            <input type="hidden" name="action" value="wpbooking_change_password">
            <h4 class="form-title"><?php echo esc_html__( 'Change Password', 'wp-booking-management-system' ) ?></h4>

            <div class="form-group">
                <label for="u_password"><?php echo esc_html__( 'Old Password', 'wp-booking-management-system' ) ?></label>
                <input type="password" class="form-control" id="u_password" name="u_password"
                       placeholder="<?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?>">
            </div>
            <div class="form-group">
                <label for="u_new_password"><?php echo esc_html__( 'New Password', 'wp-booking-management-system' ) ?></label>
                <input type="password" class="form-control" id="u_new_password" name="u_new_password"
                       placeholder="<?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?>">
            </div>
            <div class="form-group">
                <label for="u_re_new_password"><?php echo esc_html__( 'Confirm New Password', 'wp-booking-management-system' ) ?></label>
                <input type="password" class="form-control" id="u_re_new_password" name="u_re_new_password"
                       placeholder="<?php echo esc_html__( 'Display Name', 'wp-booking-management-system' ) ?>">
            </div>
            <?php if ( WPBooking_Input::post( 'action' ) == 'wpbooking_change_password' ) echo wpbooking_get_message(); ?>
            <button type="submit" class="btn btn-default"><?php echo esc_html__( 'Submit', 'wp-booking-management-system' ) ?></button>

        </form>
    </div>

</div>
