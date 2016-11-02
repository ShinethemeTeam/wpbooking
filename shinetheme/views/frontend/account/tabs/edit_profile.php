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

    <div class="wb-edit-profile-wrap">
        <h3 class="tab-page-title">
            <?php esc_html_e("Information",'wpbooking') ?>
        </h3>
        <div class="container-fluid item_profile">
            <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_fist_name"><?php esc_html_e('First Name','wpbooking') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_fist_name" name="u_fist_name" value="<?php echo WPBooking_Input::post('u_fist_name',$current_user->first_name) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_last_name"><?php esc_html_e('Last Name','wpbooking') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_last_name" name="u_last_name" value="<?php echo WPBooking_Input::post('u_last_name',$current_user->last_name) ?>"  >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_email"><?php esc_html_e('Email Address','wpbooking') ?> <span class="required">*</span></label>
                            <input type="text"  class="form-control" id="u_email" name="u_email" value="<?php echo WPBooking_Input::post('u_email',$current_user->user_email) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_phone"><?php esc_html_e('Phone','wpbooking') ?> <span class="required">*</span></label>
                            <input type="tel"  class="form-control" id="u_phone" name="u_phone" value="<?php echo WPBooking_Input::post('u_phone',get_user_meta(get_current_user_id(),'phone',true)) ?>" >
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
                            <input type="number"  class="form-control" id="u_postcode" name="u_postcode" value="<?php echo WPBooking_Input::post('u_postcode',get_user_meta(get_current_user_id(),'postcode',true)) ?>" >
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="u_apt_unit"><?php esc_html_e('Apt/ Unit','wpbooking') ?></label>
                            <input type="number"  class="form-control" id="u_apt_unit" name="u_apt_unit" value="<?php echo WPBooking_Input::post('u_apt_unit',get_user_meta(get_current_user_id(),'apt_unit',true)) ?>"  >
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php if(WPBooking_Input::post('action')=='wpbooking_update_profile') echo wpbooking_get_message(); ?>
                        <button type="submit" class="btn wb-btn-default"><?php esc_html_e('Save','wpbooking') ?></button>
                    </div>
                </div>
        </div>
    </div>
</form>
