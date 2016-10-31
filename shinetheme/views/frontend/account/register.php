<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
$term_link=WPBooking_User::inst()->get_term_condition_link();
?>
<form action="" method="post" id="wpbooking-register-form" class="login-register-form">
	<input type="hidden"  name="action" value="wpbooking_do_register">

	<h3 class="form-title"><?php esc_html_e('Register','wpbooking') ?></h3>
	<div class="form-group-wrap">
		<div class="form-group">
			<label for="reg-login" class="control-label"><?php esc_html_e('Username','wpbooking') ?> <span class="required">*</span></label>
			<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="reg-login" ">
		</div>
		<div class="form-group">
			<label for="input-email" class="control-label"><?php esc_html_e('Email','wpbooking') ?> <span class="required">*</span></label>
			<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('email') ?>" name="email" id="input-email" ">
		</div>
		<div class="form-group">
			<label for="input-password" class="control-label"><?php esc_html_e('Password','wpbooking') ?> <span class="required">*</span></label>
			<input type="password" class="form-control" id="input-password" name="password" ">
		</div>
		<div class="form-group">
			<label for="input-repassword" class="control-label"><?php esc_html_e('Re-type Password','wpbooking') ?> <span class="required">*</span></label>
			<input type="password" class="form-control" id="input-repassword" name="repassword" ">
		</div>
		<div class="form-group">
			<label class="accept-term">
					<input type="checkbox" name="term_condition" <?php checked(WPBooking_Input::post('term_condition'),1) ?> value="1"><?php printf(esc_html__('Accept %s','wpbooking'),sprintf('<a href="%s" target="_blank">%s</a>',$term_link,esc_html__('Term & Condition','wpbooking'))); ?>
			</label>
		</div>
		<button type="submit" class="wb-btn wb-btn-default wb-btn-md"><?php esc_html_e('Register','wpbooking') ?></button>
	</div>
	<?php
	if(WPBooking_Input::post('action')=='wpbooking_do_register')
		echo wpbooking_get_message();
	?>
</form>

