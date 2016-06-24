<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/23/2016
 * Time: 10:34 AM
 */
if(is_user_logged_in()) return;

$types=WPBooking_Service::inst()->get_service_types();
?>
<form action="" method="post" id="wpbooking-register-form" class="wpbooking-register-form">
	<input type="hidden"  name="action" value="wpbooking_do_partner_register">
	<?php
	if(WPBooking_Input::post('action')=='wpbooking_do_partner_register')
	echo wpbooking_get_message()
	?>
	<h3 class="form-title"><?php esc_html_e('Register as Partner','wpbooking') ?></h3>
	<div class="form-group">
		<label for="preg-login"><?php esc_html_e('Username','wpbooking') ?></label>
		<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('login') ?>" name="login" id="preg-login"  placeholder="<?php esc_html_e('Your Username','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="preg-email"><?php esc_html_e('Email','wpbooking') ?></label>
		<input type="text" class="form-control" value="<?php echo WPBooking_Input::post('email') ?>" name="email" id="preg-email"  placeholder="<?php esc_html_e('Your Email','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="preg-password"><?php esc_html_e('Password','wpbooking') ?></label>
		<input type="password" class="form-control" id="preg-password" name="password" placeholder="<?php esc_html_e('Your Password','wpbooking') ?>">
	</div>
	<div class="form-group">
		<label for="preg-repassword"><?php esc_html_e('Re-type Password','wpbooking') ?></label>
		<input type="password" class="form-control" id="preg-repassword" name="repassword" placeholder="<?php esc_html_e('Re-type Your Password','wpbooking') ?>">
	</div>
	<?php if(!empty($types)){
	?>
		<div class="form-group select-service-type">
			<label ><?php esc_html_e('Select Service Type','wpbooking')?></label>
			<?php
			foreach($types as $k=>$v){
				$value=WPBooking_Input::post('service_type');
				$old=isset($value[$k]['name'])?isset($value[$k]['name']):FALSE;
				?>
					<div class="service-type-item">
						<label ><input type="checkbox" <?php checked($old,1)?> class="service_type_checkbox" name="service_type[<?php echo esc_attr($k) ?>][name]" value="<?php esc_attr_e($k) ?>"> <?php echo ($v['label']) ?></label>
						<div class="upload-certificate <?php echo ($old)?'active':FALSE ?>">
							<div class="input-group">
								<span class="input-group-btn">
									<span class="btn btn-primary btn-file">
										<?php esc_html_e('Browse...','wpbooking')?> <input type="file" class="upload_input" >
									</span>
								</span>
								<input  type="text"  class="form-control image_url " value="<?php echo (!empty($value[$k]['certificate']))?$value[$k]['certificate']:false ?>" readonly="" name="service_type[<?php echo esc_attr($k) ?>][certificate]">

							</div>
							<p class="help-block"><?php esc_html_e('Image format : jpg, png, gif . Image size 800x600 and max file size 2MB','wpbooking') ?></p>
							<div class="upload-message"></div>
							<?php
							if(!empty($value[$k]['certificate'])){
								printf('<img src="%s" class="uploaded_image_preview">',$value[$k]['certificate']);
							}
							?>
						</div>
					</div>
				<?php
			}?>

		</div>
	<?php }?>
	<div class="checkbox">
		<label>
			<input type="checkbox" name="term_condition" <?php checked(WPBooking_Input::post('term_condition'),1) ?> value="1"><?php esc_html_e('Accept Term & Condition','wpbooking') ?>
		</label>
	</div>

	<button type="submit" class="btn btn-default"><?php esc_html_e('Submit','wpbooking') ?></button>
</form>

