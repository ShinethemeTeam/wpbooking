<?php
global $current_user;
if(get_query_var('update-profile')){
	echo wpbooking_load_view('account/update-profile');
	return;
}
$update_profile=get_permalink(wpbooking_get_option('myaccount-page')).'update-profile/'.get_current_user_id();
$link_my_profile=get_permalink(wpbooking_get_option('myaccount-page')).'/tab/profile/';
$my_user_id = $current_user->ID;
$user_id = WPBooking_Input::request('user_id',$my_user_id);
$data_current_user = get_userdata( $user_id );
?>
<h3 class="tab-page-title">
	<?php
	$name = $data_current_user->last_name;
	if(empty($name)) $name = $data_current_user->display_name;
	if(!empty($name))
	echo sprintf(esc_html__("Hello I'm %s",'wpbooking'),$name);
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
			$name_full = $data_current_user->first_name.' '.$data_current_user->last_name;
			if(empty($name_full)) $name_full = $data_current_user->display_name;
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

		<div class="text-info"><?php esc_html_e("Member since",'wpbooking') ?> <?php echo date_i18n(' Y M',strtotime($data_current_user->data->user_registered)) ?></div>
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
				<a href="<?php echo esc_html($contact_now_url) ?>" class="btn btn-default"><?php esc_html_e("Contact","wpbooking") ?></a>
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
					<a href="<?php the_permalink() ?>" class="wb-btn wb-btn-blue"><?php esc_html_e("View","wpbooking") ?> </a>
				</div>
			</div>
		<?php
		}
		wp_reset_query();
		?>
	</div>
</div>
<div class="user-reviews">
	<?php
	global $wpdb;
	$page=WPBooking_Input::request('page_number',1);
	$limit=5;
	$offset=($page-1)*$limit;
	$comment=WPBooking_Comment_Model::inst();
	$res=$comment->select('SQL_CALC_FOUND_ROWS *');
	$res=$comment->join('posts','posts.ID=comments.comment_post_ID')
		->where(array(
			$wpdb->prefix.'posts.post_author'=>$user_id,
			$wpdb->prefix.'posts.post_type'=>'wpbooking_service',
			$wpdb->prefix.'comments.comment_parent'=>0,
		))
		->limit($limit,$offset)
		->orderby($wpdb->prefix.'comments.comment_ID',"DESC")
		->get()
		->result();
	$total_item=$wpdb->get_var('SELECT FOUND_ROWS()');
	//echo $comment->last_query();
	$total=ceil($total_item/$limit);
	$paging=array();
	$paging['base']=$link_my_profile.'%_%';
	$paging['format']='?page_number=%#%';
	$paging['total']=$total;
	$paging['current']=$page;
	?>
	<h3 class="tab-page-title">
		<?php
		if($total_item > 1)
			echo sprintf(esc_html__("Reviews (%d)",'wpbooking'),$total_item);
		else
			echo sprintf(esc_html__("Review (%d)",'wpbooking'),$total_item)
		?>
	</h3>
	<ol class="comment-list">
		<?php
		if(!empty($res)){
			foreach($res as $k=>$v){
				echo '<li id="'.$v['comment_ID'].'">';
				$v['my_user_id'] = $my_user_id;
				$v['user_id'] = $user_id;
				echo wpbooking_load_view('/single/review/item-2',array('data'=>$v));
				$children_comment=$comment->join('posts','posts.ID=comments.comment_post_ID')
					->where(array(
						$wpdb->prefix.'posts.post_author'=>$user_id,
						$wpdb->prefix.'posts.post_type'=>'wpbooking_service',
						$wpdb->prefix.'comments.comment_parent'=>$v['comment_ID'],
					))
					->orderby($wpdb->prefix.'comments.comment_ID',"DESC")
					->get()
					->result();
				if(!empty($children_comment)){
					foreach($children_comment as $key=>$value){
						echo '<ol class="comment-list children">';
						$value['children'] = $v['comment_ID'];
						$value['my_user_id'] = $my_user_id;
						$value['user_id'] = $user_id;
						echo wpbooking_load_view('/single/review/item-2',array('data'=>$value));
						echo "</ol>";
					}
				}
				echo "</li>";
			}
		}
		?>
 	</ol>
	<div class="user-paginate">
		<?php echo paginate_links($paging); ?>
	</div>
</div>
