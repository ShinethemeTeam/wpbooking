<?php
global $current_user;
$user_id = $current_user->ID;
$error_fields = array();
if(!empty(WPBooking()->get('error_ed_fields'))){
    $error_fields = WPBooking()->get('error_ed_fields');
    WPBooking()->set('error_ed_fields','');
}
?>
<form class="wb-form-edit-profile" action="" method="post">
	<input type="hidden" name="action" value="wpbooking_update_profile">
	<h3 class="tab-page-title">
		<?php echo esc_html__("Edit Profile",'wp-booking-management-system') ?>
	</h3>

    <div class="wb-edit-profile-wrap">
        <h3 class="tab-page-title">
            <?php echo esc_html__("Information",'wp-booking-management-system') ?>
        </h3>
        <div class="container-fluid item_profile">
            <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_fist_name"><?php echo esc_html__('First Name','wp-booking-management-system') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_fist_name" required name="u_fist_name" value="<?php echo esc_attr(WPBooking_Input::post('u_fist_name',$current_user->first_name)) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_last_name"><?php echo esc_html__('Last Name','wp-booking-management-system') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_last_name" required name="u_last_name" value="<?php echo esc_attr(WPBooking_Input::post('u_last_name',$current_user->last_name)) ?>"  >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_email"><?php echo esc_html__('Email Address','wp-booking-management-system') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control <?php echo (array_key_exists('u_email',$error_fields)?'wb-error':'')?>" id="u_email" required name="u_email" value="<?php echo esc_attr(WPBooking_Input::post('u_email',$current_user->user_email)) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_phone"><?php echo esc_html__('Phone','wp-booking-management-system') ?> <span class="required">*</span></label>
                            <input type="tel"  class="form-control" id="u_phone" required name="u_phone" value="<?php echo esc_attr(WPBooking_Input::post('u_phone',get_user_meta(get_current_user_id(),'phone',true))) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="u_address"><?php echo esc_html__('Address','wp-booking-management-system') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_address" required name="u_address" value="<?php echo esc_attr(WPBooking_Input::post('u_address',get_user_meta(get_current_user_id(),'address',true))) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_postcode"><?php echo esc_html__('Postcode / ZIP','wp-booking-management-system') ?></label>
                            <input type="text"  class="form-control" id="u_postcode" name="u_postcode" value="<?php echo esc_attr(WPBooking_Input::post('u_postcode',get_user_meta(get_current_user_id(),'postcode',true))) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_apt_unit"><?php echo esc_html__('Apt / Unit','wp-booking-management-system') ?></label>
                            <input type="text"  class="form-control" id="u_apt_unit" name="u_apt_unit" value="<?php echo esc_attr(WPBooking_Input::post('u_apt_unit',get_user_meta(get_current_user_id(),'apt_unit',true))) ?>"  >
                        </div>
                    </div>
            </div>
            <?php do_action('wpbooking_after_field_update_profile',$user_id) ?>
            <div class="row">
                <div class="col-md-12">
                    <?php if(WPBooking_Input::post('action')=='wpbooking_update_profile') echo wpbooking_get_message(); ?>
                    <button type="submit" class="wb-btn wb-btn-default"><?php echo esc_html__('Save','wp-booking-management-system') ?></button>
                </div>
            </div>

        </div>
    </div>
</form>
