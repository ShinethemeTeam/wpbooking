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
$my_id = get_the_author_meta( 'ID' );
$user_id = WPBooking_Input::request('user_id',get_the_author_meta( 'ID' ));
$current_user = get_userdata( $user_id );
?>
<h3 class="tab-page-title">
	<?php
	echo sprintf(esc_html__("Hello I'm %s",'wpbooking'),$current_user->first_name);
	?>
</h3>
<div class="user-detail">
	<div class="avatar">
		<div class="overlay">
			<?php echo get_avatar( $user_id , 123 ); ?>
		</div>
	</div>
	<div class="info">
		<h5 class="user-name"><?php echo esc_html($current_user->display_name) ?></h5>
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
		<?php if(!empty($profile_address = get_user_meta($user_id,'profile_address',true))){ ?>
			<div class="text-info"><?php echo esc_html($profile_address) ?></div>
		<?php } ?>

		<div class="text-info"><?php esc_html_e("Member since",'wpbooking') ?> <?php echo date_i18n(' Y M',strtotime($current_user->data->user_registered)) ?></div>
		<div class="quote">
			<?php if(!empty($description = get_user_meta($user_id,'description',true))){ ?>
				<div class="icon"> <i class="fa fa-quote-left"></i></div>
				<div class="text">
					<?php echo esc_html($description) ?>
				</div>
			<?php } ?>

			<div class="contact">
				<?php
				if($my_id != $user_id){
				$url=WPBooking_User::inst()->account_page_url().'tab/inbox/';
				$contact_now_url=add_query_arg(array('user_id'=>$user_id),$url);
				?>
				<a href="<?php echo esc_html($contact_now_url) ?>" class="btn btn-default">Contact</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="user-listing">
	<h3 class="tab-page-title">
		<?php esc_html_e("Listing",'wpbooking') ?>
	</h3>
	<div class="list-item">
		<?php
		$args = array(
			'post_type'=> 'wpbooking_service',
			'author' => $user_id,
		);

		query_posts( $args );
		while(have_posts()){
			the_post();
			$service=new WB_Service();

			?>
			<div class="item">
				<div class="item_thumbnail">
					<?php echo balanceTags($service->get_featured_image('thumb'))?>
				</div>
				<div class="item_title">
					<?php the_title() ?>
				</div>
				<div class="item_control">
					<a href="<?php the_permalink() ?>" class="wb-btn wb-btn-blue">View </a>
				</div>
			</div>
		<?php
		}
		wp_reset_query();
		?>

	</div>
</div>


<!--<h3 class="tab-page-title">
	<?php
/*	echo esc_html__('Your Profile','wpbooking');
	*/?>
	<ul class="information">
		<li><strong><?php /*esc_html_e('Avatar:','wpbooking') */?></strong> <?php /*if($avatar=get_user_meta(get_current_user_id(),'avatar',true)){ printf('<img class="avatar-preview" src="%s">',$avatar);}  */?></li>
		<li><strong><?php /*esc_html_e('Display Name:','wpbooking') */?></strong> <?php /*echo esc_attr($current_user->display_name)  */?></li>
		<li><strong><?php /*esc_html_e('Email:','wpbooking') */?></strong> <?php /*echo esc_attr($current_user->user_email)  */?></li>
		<li><strong><?php /*esc_html_e('Phone Number:','wpbooking') */?></strong> <?php /*echo get_user_meta(get_current_user_id(),'phone_number',true)  */?></li>
	</ul>
</h3>
<a href="<?php /*echo esc_url($update_profile)*/?>"><?php /*esc_html_e('Update Profile','wpbooking')*/?></a>-->