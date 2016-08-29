<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 8/26/16
 * Time: 12:41 PM
 */
$user=get_user_by('id',WPBooking_Input::get('user_id'));
if(empty($user) or is_wp_error($user)){
	printf('<div class="alert alert-danger">%s</div>',esc_html__('User is not valid','wpbooking'));
	return;
}

$inbox=WPBooking_Inbox::inst();
global $wpdb;
?>
<div class="reply-inbox">
<h3 class="tab-page-title">
	<?php
	echo sprintf(esc_html__('Reply - %s','wpbooking'),$user->display_name);
	?>
</h3>
<div class="inbox-messages">
	<?php
	$user_id=WPBooking_Input::get('user_id');
	if(!$user_id and !empty($users[0])){
		if($users[0]['from_user']!=get_current_user_id())$user_id=$users[0]['from_user'];
		else $user_id=$users[0]['to_user'];
	}

	if($user_id){
		$user_info = get_userdata($user_id);

		?>
		<div class="old-messages wb-scroll-bottom" data-user-id="<?php echo esc_attr($user_id) ?>">
			<?php $old_messages=$inbox->get_user_message($user_id);
			$total=$inbox->count_user_message($user_id);
			if($total>10){
				printf('<div class="text-center"><a class="wb-btn wb-btn-default wb-btn-md wb-load-more-reply" data-user-id="%s" data-offset="0">%s</a></div>',$user_id,esc_html__('Old Message','wpbooking').' <i class=" loading fa fa-spinner fa-pulse"></i>');
			}
			if(!empty($old_messages)){
				for($i=count($old_messages)-1;$i>=0;$i--){
					$message=$old_messages[$i];
					echo wpbooking_load_view('account/inbox/loop-message',array('message'=>$message));
				}
			}
			?>
		</div>
		<form action="<?php echo home_url('/') ?>" method="post" onsubmit="return false" class="wb-send-message-form" data-reload="1">
			<input type="hidden" name="wpbooking_action" value="send_message">
			<input type="hidden" name="to_user" value="<?php echo esc_attr($user_id)?>">
			<div class="message-input">
				<div class="form-group">
					<textarea name="wb-message-input"  id="wb-message-input" cols="30" placeholder="<?php esc_html_e('Your Message','wpbooking') ?>" rows="5"></textarea>
				</div>
				<div class="text-right">
					<button type="submit" class="wb-btn wb-btn-md wb-btn-blue" type="submit"><?php esc_html_e('Send','wpbooking') ?> <i class=" loading fa fa-spinner fa-pulse"></i></button>
				</div>
				<div class="message-box text-left"></div>
			</div>
			<div class="user-avatar">
				<?php echo get_avatar(get_current_user_id(),100) ?>
			</div>
		</form>

		<?php
	}
	?>
</div>
</div>
