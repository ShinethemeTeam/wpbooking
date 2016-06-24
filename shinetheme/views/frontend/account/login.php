<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
?>
<form action="" method="post" id="wpbooking-login-form">
	<input type="hidden"  name="action" value="wpbooking_do_login">
	<?php
	if(WPBooking_Input::post('action')=='wpbooking_do_login')
	echo wpbooking_get_message()
	?>
	<h3 class="form-title"><?php esc_html_e('Login','wpbooking') ?></h3>
	<div class="form-group">
		<label for="input-login"><?php esc_html_e('Username','wpbooking') ?></label>
		<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="input-login"  placeholder="<?php esc_html_e('Your Username','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="input-password"><?php esc_html_e('Password','wpbooking') ?></label>
		<input type="password" class="form-control" id="input-password" name="password" placeholder="<?php esc_html_e('Your Password','wpbooking') ?>">
	</div>
	<div class="checkbox">
		<label>
			<input type="checkbox" <?php checked(WPBooking_Input::post('remember'),1) ?> name="remember" value="1"><?php esc_html_e('Remember Me','wpbooking') ?>
		</label>
	</div>
	<button type="submit" class="btn btn-default"><?php esc_html_e('Submit','wpbooking') ?></button>
</form>
