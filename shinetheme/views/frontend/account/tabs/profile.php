<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/7/2016
 * Time: 9:41 AM
 */
if(get_query_var('update-profile')){
	echo wpbooking_load_view('account/update-profile');
	return;
}
$update_profile=get_permalink(wpbooking_get_option('myaccount-page')).'update-profile/'.get_current_user_id();
global $current_user;
get_currentuserinfo();
?>
<h3 class="tab-page-title">
	<?php
	echo esc_html__('Your Profile','wpbooking');
	?>
	<ul class="information">
		<li><strong><?php esc_html_e('Avatar:','wpbooking') ?></strong> <?php if($avatar=get_user_meta(get_current_user_id(),'avatar',true)){ printf('<img class="avatar-preview" src="%s">',$avatar);}  ?></li>
		<li><strong><?php esc_html_e('Display Name:','wpbooking') ?></strong> <?php echo esc_attr($current_user->display_name)  ?></li>
		<li><strong><?php esc_html_e('Email:','wpbooking') ?></strong> <?php echo esc_attr($current_user->user_email)  ?></li>
		<li><strong><?php esc_html_e('Phone Number:','wpbooking') ?></strong> <?php echo get_user_meta(get_current_user_id(),'phone_number',true)  ?></li>
	</ul>
</h3>
<a href="<?php echo esc_url($update_profile)?>"><?php esc_html_e('Update Profile','wpbooking')?></a>