<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:32 PM
 */
$full_url =  $current_url="//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$error_field = array('u'=>'','p'=>'');
if(!WPBooking_Input::post('action'))
    WPBooking()->set('error_code','');

if(!empty(WPBooking()->get('error_code'))){
    if(strpos(WPBooking()->get('error_code'),'username')){
        $error_field['u'] = 'wb-error';
    }else{
        $error_field['p'] = 'wb-error';
    }
}
?>
<form action="" method="post" id="wpbooking-login-form" class="login-register-form">
	<input type="hidden"  name="action" value="wpbooking_do_login">
	<input type="hidden" name="url" value="<?php echo esc_url($full_url) ?>">
	<h3 class="form-title"><?php esc_html_e('Login','wpbooking') ?></h3>
	<div class="form-group-wrap">
		<div class="form-group">
			<label for="input-login" class="control-label"><?php esc_html_e('Username or email address','wpbooking') ?> <span class="required">*</span></label>
			<input type="text" class="form-control <?php echo esc_attr($error_field['u']); ?>" required value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="input-login">
		</div>
		<div class="form-group">
			<label for="input-password" class="control-label"><?php esc_html_e('Password','wpbooking') ?> <span class="required">*</span></label>
			<input type="password" class="form-control <?php echo esc_attr($error_field['p']); ?>" required id="input-password" name="password">
		</div>
		<div class="form-group">
			<button type="submit" class="wb-btn wb-btn-default"><?php esc_html_e('Login','wpbooking') ?></button>
			<label class="remember-me">
				<input type="checkbox" <?php checked(WPBooking_Input::post('remember'),1) ?> name="remember" value="1"><?php esc_html_e('Remember Me','wpbooking') ?>
			</label>
		</div>
		<a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="lost-password"><?php esc_html_e('Is your password lost?','wpbooking') ?></a>
        <?php if(wpbooking_is_any_register()){ ?>
            <hr>
            <p class="register-url"><?php echo esc_html__('Don\'t have an account yet? ');?><a href="<?php echo WPBooking_User::inst()->get_register_url(); ?>"><?php echo esc_html__('Create an account','wpbooking'); ?></a></p>
        	<?php do_action("wpbooking_after_register_user_link") ?>
		<?php } ?>
	</div>
	<?php
	if(WPBooking_Input::post('action')=='wpbooking_do_login' || WPBooking_Input::get('checkemail') == 'confirm' || WPBooking_Input::get('password') == 'changed' )
		echo wpbooking_get_message()
	?>
</form>
