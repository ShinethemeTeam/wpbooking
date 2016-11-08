<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/7/2016
 * Time: 11:11 AM
 */
global $current_user;
?>
<div class="row">

	<div class="col-sm-6">
		<form action="" method="post">
			<input type="hidden" name="action" value="wpbooking_update_profile">
			<h4 class="form-title"><?php esc_html_e('Update Profile','wpbooking') ?></h4>

			<div class="form-group">
				<label ><?php esc_html_e('Avatar','wpbooking') ?></label>
				<input type="hidden" class="form-control" id="u_avatar" name="u_avatar" placeholder="<?php esc_html_e('Display Name','wpbooking') ?>" value="<?php echo WPBooking_Input::post('u_avatar',get_user_meta(get_current_user_id(),'u_avatar',true)) ?>">
				<div class="upload-avatar">
					<div class="input-group">
							<span class="input-group-btn">
								<span class="btn btn-primary btn-file">
									<?php esc_html_e('Browse...','wpbooking')?> <input type="file" class="upload_input" >
								</span>
							</span>
					<input  type="text"  class="form-control image_url " value="<?php echo WPBooking_Input::post('u_avatar',get_user_meta(get_current_user_id(),'avatar',true)) ?>" readonly="" name="u_avatar">
					</div>
					<p class="help-block"><?php esc_html_e('Image format : jpg, png, gif . Image size 800x600 and max file size 2MB','wpbooking') ?></p>
					<div class="upload-message"></div>
					<?php if($avatar=WPBooking_Input::post('u_avatar',get_user_meta(get_current_user_id(),'avatar',true))){
						printf('<img alt="avatar" src="%s" class="uploaded_image_preview">',$avatar);
					} ?>
				</div>
			</div>
			<div class="form-group">
				<label for="u_display_name"><?php esc_html_e('Display Name','wpbooking') ?></label>
				<input type="text" class="form-control" id="u_display_name" name="u_display_name" placeholder="<?php esc_html_e('Display Name','wpbooking') ?>" value="<?php echo WPBooking_Input::post('u_display_name',$current_user->display_name) ?>">
			</div>

			<div class="form-group">
				<label for="u_email"><?php esc_html_e('Email','wpbooking') ?></label>
				<input type="text" class="form-control" id="u_email" name="u_email" placeholder="<?php esc_html_e('Email','wpbooking') ?>" value="<?php echo WPBooking_Input::post('u_email',$current_user->user_email) ?>">
			</div>
			<div class="form-group">
				<label for="u_phone"><?php esc_html_e('Phone Number','wpbooking') ?></label>
				<input type="text" class="form-control" id="u_phone" name="u_phone" placeholder="<?php esc_html_e('Phone Number','wpbooking') ?>" value="<?php echo WPBooking_Input::post('u_phone',get_user_meta(get_current_user_id(),'phone_number',true)) ?>">
			</div>
			<?php if(WPBooking_Input::post('action')=='wpbooking_update_profile') echo wpbooking_get_message(); ?>
			<button type="submit" class="btn btn-default"><?php esc_html_e('Submit','wpbooking') ?></button>

		</form>
	</div>
	<div class="col-sm-6">
		<form action="" method="post">
			<input type="hidden" name="action" value="wpbooking_change_password">
			<h4 class="form-title"><?php esc_html_e('Change Password','wpbooking') ?></h4>

			<div class="form-group">
				<label for="u_password"><?php esc_html_e('Old Password','wpbooking') ?></label>
				<input type="password" class="form-control" id="u_password" name="u_password" placeholder="<?php esc_html_e('Display Name','wpbooking') ?>" >
			</div>
			<div class="form-group">
				<label for="u_new_password"><?php esc_html_e('New Password','wpbooking') ?></label>
				<input type="password" class="form-control" id="u_new_password" name="u_new_password" placeholder="<?php esc_html_e('Display Name','wpbooking') ?>" >
			</div>
			<div class="form-group">
				<label for="u_re_new_password"><?php esc_html_e('Confirm New Password','wpbooking') ?></label>
				<input type="password" class="form-control" id="u_re_new_password" name="u_re_new_password" placeholder="<?php esc_html_e('Display Name','wpbooking') ?>" >
			</div>
			<?php if(WPBooking_Input::post('action')=='wpbooking_change_password') echo wpbooking_get_message(); ?>
			<button type="submit" class="btn btn-default"><?php esc_html_e('Submit','wpbooking') ?></button>

		</form>
	</div>

</div>
