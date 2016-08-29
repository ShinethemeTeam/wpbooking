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
?>
	<div class="message <?php if($message['from_user']==get_current_user_id())echo 'current'; ?>">
		<div class="avatar"><?php echo get_avatar($message['from_user']) ?></div>
		<div class="info">
			<h4 class="author"><?php echo esc_html($user_info->display_name)?></h4>
			<div class="message-content">
				<?php echo do_shortcode(nl2br(stripslashes($message['content']))) ?>
			</div>

			<p class="time"><?php printf(esc_html__('%s ago','wpbooking'),human_time_diff($message['created_at'],time())) ?></p>
		</div>
	</div>