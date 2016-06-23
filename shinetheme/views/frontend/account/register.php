<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
?>
<form action="" method="post" id="wpbooking-register-form">
	<input type="hidden"  name="action" value="wpbooking_do_register">
	<?php echo wpbooking_get_message() ?>
	<h3 class="form-title"><?php esc_html_e('Register','wpbooking') ?></h3>
	<div class="form-group">
		<label for="reg-login"><?php esc_html_e('Username','wpbooking') ?></label>
		<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="reg-login"  placeholder="<?php esc_html_e('Your Username','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="input-email"><?php esc_html_e('Email','wpbooking') ?></label>
		<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('email') ?>" name="email" id="input-email"  placeholder="<?php esc_html_e('Your Email','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="input-password"><?php esc_html_e('Password','wpbooking') ?></label>
		<input type="password" class="form-control" id="input-password" name="password" placeholder="<?php esc_html_e('Your Password','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="input-repassword"><?php esc_html_e('Re-type Password','wpbooking') ?></label>
		<input type="password" class="form-control" id="input-repassword" name="repassword" placeholder="<?php esc_html_e('Re-type Your Password','wpbooking') ?>">
	</div>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="term_condition" <?php checked(WPBooking_Input::post('term_condition'),1) ?> value="1"><?php esc_html_e('Accept Term & Condition','wpbooking') ?>
		</label>
	</div>
	<button type="submit" class="btn btn-default"><?php esc_html_e('Submit','wpbooking') ?></button>
</form>

