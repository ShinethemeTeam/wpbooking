<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/17/2016
 * Time: 2:45 PM
 */
global $current_user;
$inbox=WPBooking_Inbox::inst();
$users=$inbox->get_latest_message();
$users=$inbox->filter_latest_message($users);
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

<?php if(!empty($users)) {?>
<div class="wb-profile-panel">
	<div class="panel-heading">
		<h5 class="panel-title"><?php esc_html_e('Messages','wpbooking') ?></h5>
	</div>

	<div class="panel-body">
		<ul class="wb-list-new-messages">
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
					<li class="inbox-user-item <?php echo ($user_id==WPBooking_Input::get('user_id'))?'active':FALSE ?>">
						<a href="<?php echo esc_url($url) ?>">
							<div class="avatar"><?php echo get_avatar($user_id) ?></div>
							<div class="info">
								<h4 class="user-displayname"><?php echo esc_html($user_info->display_name)?></h4>
								<div class="message"><?php echo wpbooking_cutnchar(stripcslashes($user['content']),60) ?></div>
								<p class="time"><?php printf(esc_html__('%s ago','wpbooking'),human_time_diff($user['created_at'],time())) ?></p>
								<?php if(!empty($user['unread_number'])){
									printf('<p class="unread_number">%s</p>',sprintf(esc_html__('%d new message(s)','wpbooking'),$user['unread_number']));
								} ?>
							</div>
						</a>
					</li>
					<?php
				}
			}
			?>
			<li class="go-view-all"><a href="<?php echo get_permalink(wpbooking_get_option('myaccount-page')).'tab/inbox'; ?>" class="view-all"><?php esc_html_e('View all','wpbooking')?></a></li>
		</ul>
	</div>
</div>
<?php } ?>