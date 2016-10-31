<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/17/2016
 * Time: 2:45 PM
 */
global $current_user;
$inbox=WPBooking_Inbox::inst();
?>
<div class="wb-profile-box">
	<div class="profile-avatar">
		<?php echo get_avatar(get_current_user_id(),90) ?>
		<a class="edit_profile" href="<?php echo get_permalink(wpbooking_get_option('myaccount-page')).'tab/edit_profile'; ?>"><?php esc_html_e('Edit','wpbooking') ?></a>
	</div>
	<div class="profile-info">
		<h5 class="user-display-name"><?php echo esc_attr($current_user->display_name)  ?></h5>
		<?php if(current_user_can('publish_posts')){?>
			<a href="<?php echo get_permalink(wpbooking_get_option('myaccount-page')).'tab/profile'; ?>" class="wb-btn wb-btn-success"><?php esc_html_e('View Profile','wpbooking') ?></a>
		<?php } ?>
	</div>
</div>
