<?php
global $current_user;
?>
<div class="wpbooking-bootstrap profile ">
	<h3 class="tab-page-title"> <?php echo esc_html__("My Dashboard","wp-booking-management-system") ?></h3>
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
					<label><?php echo esc_html__("Email Address:","wp-booking-management-system") ?> </label>
					<p><?php echo esc_html($email) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($phone = get_user_meta($user_id,'phone',true))){ ?>
				<div class="col-md-6">
					<label><?php echo esc_html__("Telephone:","wp-booking-management-system") ?> </label>
					<p><?php echo esc_html($phone) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($address = get_user_meta($user_id,'address',true))){ ?>
				<div class="col-md-12">
					<label><?php echo esc_html__("Address:","wp-booking-management-system") ?> </label>
					<p><?php echo esc_html($address) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($apt_unit = get_user_meta($user_id,'apt_unit',true))){ ?>
				<div class="col-md-6">
					<label><?php echo esc_html__("Apt / Unit:","wp-booking-management-system") ?> </label>
					<p><?php echo esc_html($apt_unit) ?></p>
				</div>
			<?php } ?>
			<?php if(!empty($postcode_zip = get_user_meta($user_id,'postcode',true))){ ?>
				<div class="col-md-6">
					<label><?php echo esc_html__("Postcode / Zip:","wp-booking-management-system") ?> </label>
					<p><?php echo esc_html($postcode_zip) ?></p>
				</div>
			<?php } ?>
			<?php  ?>
			<div class="col-md-12">
				<a class="change_pass" href="<?php echo esc_attr(get_the_permalink(get_the_ID())) ?>tab/change_password/"><?php echo esc_html__("Change Password","wp-booking-management-system") ?></a>

			</div>
		</div>
	</div>
</div>






