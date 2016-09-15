<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/4/2016
 * Time: 11:15 AM
 */

if($message['from_user']!=get_current_user_id())$user_id=$message['from_user'];
else $user_id=$message['to_user'];
$user_info = get_userdata($message['from_user']);

$user_page=WPBooking_User::inst()->account_page_url();
?>
	<div class="message <?php if($message['from_user']==get_current_user_id())echo 'current'; ?>">
		<div class="avatar">
			<a href="<?php echo esc_url($user_page.'profile/'.$message['from_user'].'/') ?>">
				<?php echo get_avatar($message['from_user'],45) ?>
			</a>
		</div>
		<div class="info">
			<h4 class="author">
				<a href="<?php echo esc_url($user_page.'profile/'.$message['from_user'].'/') ?>"><?php echo esc_html($user_info->display_name)?></a></h4>
			<div class="message-content">
				<?php echo do_shortcode(nl2br(stripslashes($message['content']))) ?>
			</div>

			<p class="time"><?php printf(esc_html__('%s ago','wpbooking'),human_time_diff($message['created_at'],time())) ?></p>
		</div>
	</div>