<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/1/2016
 * Time: 10:01 AM
 */
$inbox=WPBooking_Inbox::inst();
$users=$inbox->get_latest_message();
echo WPBooking_Inbox_Model::inst()->last_query();
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
	<div class="wpbooking-inbox-user">
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
				<div class="inbox-user-item ">
					<a href="<?php echo esc_url($url) ?>">
						<div class="avatar"><?php echo get_avatar($user['from_user']) ?></div>
						<div class="info">
							<h4 class="user-displayname"><?php echo esc_html($user_info->display_name)?></h4>
							<div class="message"><?php echo stripcslashes($user['content']) ?></div>
							<p class="time"><?php printf(esc_html__('%s ago','wpbooking'),human_time_diff($user['created_at'],time())) ?></p>
						</div>
					</a>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>