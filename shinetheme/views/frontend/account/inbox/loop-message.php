<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/4/2016
 * Time: 11:15 AM
 */

if($message['from_user']!=get_current_user_id())$user_id=$message['from_user'];
else $user_id=$message['to_user'];
$user_info = get_userdata($user_id);
?>
	<div class="message">
		<div class="avatar"><?php echo get_avatar($user_id) ?></div>
		<div class="info">
			<h4 class="user-displayname"><?php echo esc_html($user_info->display_name)?></h4>
			<div class="message-content">
				<?php echo do_shortcode(nl2br($message['content'])) ?>
			</div>

			<p class="time"><?php echo date(get_option('date_format'),$message['created_at']) ?></p>
		</div>
	</div>