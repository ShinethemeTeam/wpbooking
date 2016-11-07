<?php
global $current_user;
?>
<div class="wpbooking-bootstrap profile ">
	<h3 class="tab-page-title"> <?php esc_html_e("My Dashboard","wpbooking") ?></h3>
	<div class="content">
		<div class="row">
			<?php
			$user_id = $current_user->ID;
			$full_name = $current_user->display_name;
			if(empty($full_name)){
				$fist_name = $current_user->first_name;
				$last_name = $current_user->last_name;
				$full_name = $fist_name.' '.$last_name;
			}
			if(!empty($full_name)){?>
				<div class="col-md-12 full_name">
					<p><?php echo esc_html($full_name) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($email = $current_user->user_email)){ ?>
				<div class="col-md-6">
					<label><?php esc_html_e("Email Address:","wpbooking") ?> </label>
					<p><?php echo esc_html($email) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($phone = get_user_meta($user_id,'phone',true))){ ?>
				<div class="col-md-6">
					<label><?php esc_html_e("Telephone:","wpbooking") ?> </label>
					<p><?php echo esc_html($phone) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($address = get_user_meta($user_id,'address',true))){ ?>
				<div class="col-md-12">
					<label><?php esc_html_e("Address:","wpbooking") ?> </label>
					<p><?php echo esc_html($address) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($apt_unit = get_user_meta($user_id,'apt_unit',true))){ ?>
				<div class="col-md-6">
					<label><?php esc_html_e("Apt/ Unit","wpbooking") ?> </label>
					<p><?php echo esc_html($apt_unit) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($postcode_zip = get_user_meta($user_id,'postcode',true))){ ?>
				<div class="col-md-6">
					<label><?php esc_html_e("Postcode / Zip","wpbooking") ?> </label>
					<p><?php echo esc_html($postcode_zip) ?></p>
				</div>
			<?php } ?>
			<?php  ?>
			<div class="col-md-12">
				<a class="change_pass" href="<?php echo get_the_permalink(get_the_ID()) ?>tab/change_password/"><?php esc_html_e("Change Password","wpbooking") ?></a>

			</div>
		</div>
	</div>
</div>

	<!--<div class="profile-avatar">
		<?php /*echo get_avatar(get_current_user_id(),90) */?>
		<a class="edit_profile" href="<?php /*echo get_permalink(wpbooking_get_option('myaccount-page')).'tab/edit_profile'; */?>"><?php /*esc_html_e('Edit','wpbooking') */?></a>
	</div>
	<div class="profile-info">
		<h5 class="user-display-name"><?php /*echo esc_attr($current_user->display_name)  */?></h5>
		<?php /*if(current_user_can('publish_posts')){*/?>
			<a href="<?php /*echo get_permalink(wpbooking_get_option('myaccount-page')).'tab/profile'; */?>" class="wb-btn wb-btn-success"><?php /*esc_html_e('View Profile','wpbooking') */?></a>
		<?php /*} */?>
	</div>-->





