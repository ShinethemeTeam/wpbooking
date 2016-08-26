<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/1/2016
 * Time: 10:01 AM
 */
$inbox=WPBooking_Inbox::inst();
$users=$inbox->get_latest_message();
if(WPBooking_Input::get('user_id')){
	echo wpbooking_load_view('account/inbox/reply');
	return;
}
?>
<h3 class="tab-page-title">
	<?php
	echo esc_html__('Inbox','wpbooking');
	?>
</h3>
<div class="inbox-wrap">
	<div class="wpbooking-inbox-user inbox-user">
		<?php
		if(!empty($users)){
			foreach($users as $key=>$user){
				if($user['from_user']!=get_current_user_id())$user_id=$user['from_user'];
				else $user_id=$user['to_user'];

				$myaccount_page=get_permalink(wpbooking_get_option('myaccount-page'));
				$url=$myaccount_page.'tab/inbox/';
				$url=add_query_arg(array('user_id'=>$user_id),$url);

				$user_info = get_userdata($user_id);
				?>
				<div class="inbox-user-item <?php echo ($user_id==WPBooking_Input::get('user_id'))?'active':FALSE ?>">
					<a href="<?php echo esc_url($url) ?>">
						<div class="avatar"><?php echo get_avatar($user_id) ?></div>
						<div class="info">
							<h4 class="user-displayname"><?php echo esc_html($user_info->display_name)?></h4>

							<p class="time"><?php echo date(get_option('date_format'),$user['created_at']) ?></p>
						</div>
					</a>
				</div>
				<?php
			}
		}
		?>
	</div>
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
			<h3 class="send-messag-to"><?php printf(esc_html__('Send Message To: %s','wpbooking'),$user_info->display_name) ?></h3>
			<form action="<?php echo home_url('/') ?>" method="post" onsubmit="return false" class="wb-send-message-form" data-reload="1">
				<input type="hidden" name="wpbooking_action" value="send_message">
				<input type="hidden" name="to_user" value="<?php echo esc_attr($user_id)?>">
				<div class="form-group">
					<label for="wb-message-input"><?php esc_html_e('Your Message','wpbooking') ?></label>
					<textarea name="wb-message-input"  id="wb-message-input" cols="30" placeholder="<?php esc_html_e('Your Message','wpbooking') ?>" rows="5"></textarea>
				</div>
				<button type="submit" class="btn btn-primary" type="submit"><?php esc_html_e('Send Message','wpbooking') ?></button>
				<div class="message-box text-left"></div>
			</form>
			<div class="old-messages" data-user-id="<?php echo esc_attr($user_id) ?>">
				<?php $old_messages=$inbox->get_user_message($user_id);
				if(!empty($old_messages)){
					foreach($old_messages as $message){
						echo wpbooking_load_view('account/inbox/loop-message',array('message'=>$message));
					}
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
</div>