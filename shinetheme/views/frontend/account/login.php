<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
$full_url =  $current_url="//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<form action="" method="post" id="wpbooking-login-form" class="login-register-form">
	<input type="hidden"  name="action" value="wpbooking_do_login">
	<input type="hidden" name="url" value="<?php echo esc_url($full_url) ?>">
	<h3 class="form-title"><?php esc_html_e('Login','wpbooking') ?></h3>
	<div class="form-group-wrap">
		<div class="form-group">
			<label for="input-login" class="control-label"><?php esc_html_e('Username','wpbooking') ?> <span class="required">*</span></label>
			<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="input-login"  placeholder="<?php esc_html_e('Your Username','wpbooking') ?>">
		</div>
		<div class="form-group">
			<label for="input-password" class="control-label"><?php esc_html_e('Password','wpbooking') ?> <span class="required">*</span></label>
			<input type="password" class="form-control" id="input-password" name="password" placeholder="<?php esc_html_e('Your Password','wpbooking') ?>">
		</div>
		<div class="form-group">
			<button type="submit" class="wb-btn wb-btn-blue wb-btn-md"><?php esc_html_e('Login','wpbooking') ?></button>
			<label class="remember-me">
				<input type="checkbox" <?php checked(WPBooking_Input::post('remember'),1) ?> name="remember" value="1"><?php esc_html_e('Remember Me','wpbooking') ?>
			</label>
		</div>
		<a href="<?php ?>" class="lost-password"><?php esc_html_e('Lost your password?','wpbooking') ?></a>
	</div>
	<?php
	if(WPBooking_Input::post('action')=='wpbooking_do_login')
		echo wpbooking_get_message()
	?>
</form>
