<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/7/2016
 * Time: 11:11 AM
 */
global $current_user;
$user_id = $current_user->ID;
?>
<form action="" method="post">
	<input type="hidden" name="action" value="wpbooking_update_profile">
	<h3 class="tab-page-title">
		<?php esc_html_e("Edit Profile",'wpbooking') ?>
	</h3>
	<div class="container-fluid item_avatar item_profile">
		<div class="row dark_bg">
			<div class="col-sm-3">
				<div class="avatar">
					<input type="hidden"  class="form-control image_url" value="<?php echo WPBooking_Input::post('u_avatar',get_user_meta(get_current_user_id(),'avatar',true)) ?>" readonly="" name="u_avatar">
					<?php echo get_avatar( $user_id , 123 ); ?>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="text-info">
					<?php esc_html_e("Clear frontal face photos are an important way for hosts and guests to learn about each other.
					It’s not much fun to host a landscape! Please upload a photo that clearly shows your face.","wpbooking") ?>
				</div>
				<div class="avatar-control">
					<div class="upload-avatar">
						<span class="btn btn-default btn-file">
							<?php esc_html_e('Change Avatar','wpbooking')?> <input type="file" class="upload_input">
						</span>
					</div>
					<?php
					$link_my_profile=get_permalink(wpbooking_get_option('myaccount-page')).'/tab/profile/';
					?>
					<a href="<?php echo esc_url($link_my_profile) ?>" class="text-color"><?php esc_html_e("View profile","wpbooking") ?></a>
				</div>
			</div>
		</div>
	</div>

	<h3 class="tab-page-title">
		<?php esc_html_e("Informations",'wpbooking') ?>
	</h3>
	<div class="container-fluid item_profile">
		<div class="row dark_bg">
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_fist_name"><?php esc_html_e('Fist name','wpbooking') ?> <span class="required">*</span></label>
						<input type="text"  class="form-control" id="u_fist_name" name="u_fist_name" value="<?php echo WPBooking_Input::post('u_fist_name',$current_user->first_name) ?>" >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_last_name"><?php esc_html_e('Last name','wpbooking') ?> <span class="required">*</span></label>
						<input type="text"  class="form-control" id="u_last_name" name="u_last_name" value="<?php echo WPBooking_Input::post('u_last_name',$current_user->last_name) ?>"  >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_gender"><?php esc_html_e('I am','wpbooking') ?></label>
						<?php $gender =  WPBooking_Input::post('u_gender',get_user_meta(get_current_user_id(),'gender',true)) ?>
						<select name="u_gender" id="u_gender" class="form-control">
							<option <?php echo selected($gender,'male') ?> value="male"><?php esc_html_e('Male','wpbooking') ?></option>
							<option <?php echo selected($gender,'female') ?> value="female"><?php esc_html_e('Female','wpbooking') ?></option>
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<?php
						$birth_date =  get_user_meta(get_current_user_id(),'birth_date',true);
						$day = date('d',strtotime($birth_date));
						$month = date('m',strtotime($birth_date));
						$year = date('Y',strtotime($birth_date));
						?>
						<label for="u_birth_date"><?php esc_html_e('Birth Date','wpbooking') ?></label>
						<div class="birthday">
							<?php $u_month =  WPBooking_Input::request('u_birth_date_month',$month); ?>
							<select name="u_birth_date_month" class="form-control">
								<option <?php selected($u_month,'01') ?> value="01"><?php esc_html_e('January','wpbooking') ?></option>
								<option <?php selected($u_month,'02') ?> value="02"><?php esc_html_e('February','wpbooking') ?></option>
								<option <?php selected($u_month,'03') ?> value="03"><?php esc_html_e('March','wpbooking') ?></option>
								<option <?php selected($u_month,'04') ?> value="04"><?php esc_html_e('April','wpbooking') ?></option>
								<option <?php selected($u_month,'05') ?> value="05"><?php esc_html_e('May','wpbooking') ?></option>
								<option <?php selected($u_month,'06') ?> value="06"><?php esc_html_e('June','wpbooking') ?></option>
								<option <?php selected($u_month,'07') ?> value="07"><?php esc_html_e('July','wpbooking') ?></option>
								<option <?php selected($u_month,'08') ?> value="08"><?php esc_html_e('August','wpbooking') ?></option>
								<option <?php selected($u_month,'09') ?> value="09"><?php esc_html_e('September','wpbooking') ?></option>
								<option <?php selected($u_month,'10') ?> value="10"><?php esc_html_e('October','wpbooking') ?></option>
								<option <?php selected($u_month,'11') ?> value="11"><?php esc_html_e('November','wpbooking') ?></option>
								<option <?php selected($u_month,'12') ?> value="12"><?php esc_html_e('December','wpbooking') ?></option>
							</select>
							<select name="u_birth_date_day"  class="form-control">
								<?php $u_day =  WPBooking_Input::request('u_birth_date_day',$day); ?>
								<?php
								for( $i = 1 ; $i <= 31 ; $i++ ){
									echo "<option ".selected($u_day,$i,false)." value={$i}>{$i}</option>";
								}
								?>
							</select>
							<select name="u_birth_date_year"  class="form-control">
								<?php $u_year =  WPBooking_Input::request('u_birth_date_year',$year); ?>
								<?php
								$year_now = Date('Y');
								$year_old = $year_now - 100;
								for( $i = $year_now ; $i >= $year_old ; $i-- ){
									echo "<option ".selected($u_year,$i,false)." value={$i}>{$i}</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_company_name"><?php esc_html_e('Company name','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_company_name" name="u_company_name" value="<?php echo WPBooking_Input::post('u_company_name',get_user_meta(get_current_user_id(),'company_name',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_email"><?php esc_html_e('Email address','wpbooking') ?> <span class="required">*</span></label>
						<input type="text"  class="form-control" id="u_email" name="u_email" value="<?php echo WPBooking_Input::post('u_email',$current_user->user_email) ?>" >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_phone"><?php esc_html_e('Phone','wpbooking') ?> <span class="required">*</span></label>
						<input type="text"  class="form-control" id="u_phone" name="u_phone" value="<?php echo WPBooking_Input::post('u_phone',get_user_meta(get_current_user_id(),'phone',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_country"><?php esc_html_e('Country','wpbooking') ?> <span class="required">*</span></label>
						<?php $list = wpbooking_get_country_list(); ?>
						<?php $country =  WPBooking_Input::post('u_country',get_user_meta(get_current_user_id(),'country',true)) ?>
						<select name="u_country" class="form-control country">
							<?php
							if(!empty($list)){
								foreach($list as $k=>$v){
									echo "<option ".selected($k,$country,false)." value='{$k}'>{$v}</option>";
								}
							}?>
						</select>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_address"><?php esc_html_e('Address','wpbooking') ?> <span class="required">*</span></label>
						<input type="text"  class="form-control" id="u_address" name="u_address" value="<?php echo WPBooking_Input::post('u_address',get_user_meta(get_current_user_id(),'address',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_postcode"><?php esc_html_e('Postcode / ZIP','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_postcode" name="u_postcode" value="<?php echo WPBooking_Input::post('u_postcode',get_user_meta(get_current_user_id(),'postcode',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_apt_unit"><?php esc_html_e('Apt/ Unit','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_apt_unit" name="u_apt_unit" value="<?php echo WPBooking_Input::post('u_apt_unit',get_user_meta(get_current_user_id(),'apt_unit',true)) ?>"  >
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_preferred_language"><?php esc_html_e('Preferred Language','wpbooking') ?></label>
						<?php $list = WPBooking_User::_get_list_preferred_language();?>
						<?php $preferred_language =  WPBooking_Input::post('u_preferred_language',get_user_meta(get_current_user_id(),'preferred_language',true)) ?>
						<select name="u_preferred_language" class="form-control">
							<?php if(!empty($list)){
								foreach($list as $k=>$v){
									echo "<option ".selected($k,$preferred_language,false)." value='{$k}'>{$v}</option>";
								}
							} ?>
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label for="u_preferred_currency"><?php esc_html_e('Preferred Currency','wpbooking') ?></label>
						<?php $list = WPBooking_Currency::get_added_currencies();?>
						<?php $preferred_currency =  WPBooking_Input::post('u_preferred_currency',get_user_meta(get_current_user_id(),'preferred_currency',true)) ?>
						<select name="u_preferred_currency" class="form-control">
							<?php if(!empty($list)){
								foreach($list as $k=>$v){
									echo "<option ".selected($v['currency'],$preferred_currency,false)." value='{$v['currency']}'>{$v['symbol']}</option>";
								}
							} ?>
						</select>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_facebook"><?php esc_html_e('Facebook','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_facebook" name="u_facebook" value="<?php echo WPBooking_Input::post('u_facebook',get_user_meta(get_current_user_id(),'profile_facebook',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_twitter"><?php esc_html_e('Twitter','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_twitter" name="u_twitter" value="<?php echo WPBooking_Input::post('u_twitter',get_user_meta(get_current_user_id(),'profile_twitter',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_google_plus"><?php esc_html_e('Google +','wpbooking') ?></label>
						<input type="text"  class="form-control" id="u_google_plus" name="u_google_plus" value="<?php echo WPBooking_Input::post('u_google_plus',get_user_meta(get_current_user_id(),'profile_google_plus',true)) ?>" >
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<label for="u_about_me"><?php esc_html_e('About me','wpbooking') ?></label>
						<textarea name="u_about_me"  class="form-control" id=u_about_me" cols="30" rows="4" placeholder="<?php esc_html_e("Notes about your property or yourself",'wpbooking') ?>"><?php echo WPBooking_Input::post('u_about_me',get_user_meta(get_current_user_id(),'description',true)) ?></textarea>
					</div>
				</div>
				<div class="col-md-12">
					<?php if(WPBooking_Input::post('action')=='wpbooking_update_profile') echo wpbooking_get_message(); ?>
					<button type="submit" class="btn btn-primary"><?php esc_html_e('Save','wpbooking') ?></button>
				</div>
			</div>
	</div>
</form>
