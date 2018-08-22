<?php
global $current_user;
if(get_query_var('update-profile')){
	echo wpbooking_load_view('account/update-profile');
	return;
}
$update_profile=get_permalink(wpbooking_get_option('myaccount-page')).'update-profile/'.get_current_user_id();
$link_my_profile=get_permalink(wpbooking_get_option('myaccount-page')).'tab/profile/';
$my_user_id = $current_user->ID;
if($user_query = get_query_var('profile')){
	$user_id=$user_query;
	$link_my_profile = get_permalink(wpbooking_get_option('myaccount-page'))."profile/".$user_id;

}else $user_id=$my_user_id;
$data_current_user = get_userdata( $user_id );
?>
<h3 class="tab-page-title">
	<?php
	$name = get_user_meta($user_id,"last_name",true);
	if(empty($name)) $name = $data_current_user->data->display_name;
	if(!empty($name))
	echo sprintf(esc_html__("Hello I'm %s",'wp-booking-management-system'),$name);
	?>
</h3>
<div class="user-detail">
	<div class="avatar">
		<div class="overlay">
			<?php echo get_avatar( $user_id , 123 ); ?>
		</div>
	</div>
	<div class="info">
		<h5 class="user-name">
			<?php
			if(!empty($data_current_user->data->display_name)) $name_full = $data_current_user->data->display_name;
			if(!empty($name_full))
				 echo esc_html($name_full) ?>
		</h5>
		<div class="user-share">
			<?php if(!empty($facebook = get_user_meta($user_id,'profile_facebook',true))){ ?>
				<a href="<?php echo esc_url($facebook) ?>"><span class="fa fa-facebook"></span></a>
			<?php } ?>
			<?php if(!empty($twitter = get_user_meta($user_id,'profile_twitter',true))){ ?>
				<a href="<?php echo esc_url($twitter) ?>"><span class="fa fa-twitter"></span></a>
			<?php } ?>
			<?php if(!empty($google = get_user_meta($user_id,'profile_google_plus',true))){ ?>
				<a href="<?php echo esc_url($google) ?>"><span class="fa fa-google-plus"></span></a>
			<?php } ?>
		</div>
		<?php if(!empty($profile_address = get_user_meta($user_id,'address',true))){ ?>
			<div class="text-info"><?php echo esc_html($profile_address) ?></div>
		<?php } ?>

		<div class="text-info"><?php echo esc_html__("Member since",'wp-booking-management-system') ?> <?php echo date_i18n(' Y M',strtotime($data_current_user->data->user_registered)) ?></div>
		<div class="quote">
			<?php if(!empty($description = get_user_meta($user_id,'description',true))){ ?>
				<div class="icon"> <i class="fa fa-quote-left"></i></div>
				<div class="text">
					<?php echo esc_html($description) ?>
				</div>
			<?php } ?>

			<div class="contact">
				<?php
				if($my_user_id != $user_id){
				$url=WPBooking_User::inst()->account_page_url().'tab/inbox/';
				$contact_now_url=add_query_arg(array('user_id'=>$user_id),$url);
				?>
				<a href="<?php echo esc_html($contact_now_url) ?>" class="wb-btn wb-btn-default wb-btn-md"><?php echo esc_html__("Contact","wp-booking-management-system") ?></a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="user-listing">
	<?php
	$args = array(
		'post_type'=> 'wpbooking_service',
		'author' => $user_id,
	);
	if($user_id != $my_user_id){
		$args['meta_key'] = 'enable_property';
		$args['meta_value'] = "on";
	}
	query_posts( $args );
	?>
	<?php if(have_posts()): ?>
		<h3 class="tab-page-title">
			<?php echo esc_html__("Listing",'wp-booking-management-system') ?>
		</h3>
		<div class="list-item">
			<?php

			while(have_posts()){
				the_post();
				$service=new WB_Service();

				?>
				<div class="item">
					<div class="item_thumbnail">
						<?php echo do_shortcode($service->get_featured_image('thumb'))?>
					</div>
					<div class="item_title">
						<?php the_title() ?>
					</div>
					<div class="item_control">
						<a href="<?php the_permalink() ?>" class="wb-btn wb-btn-blue"><?php echo esc_html__("View","wp-booking-management-system") ?> </a>
					</div>
				</div>
				<?php
			}
			wp_reset_query();
			?>
		</div>
	<?php endif; ?>
</div>
<?php
do_action('wpbooking_after_user_listing');
?>
