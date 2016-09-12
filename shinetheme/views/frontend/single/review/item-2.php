<?php
if(empty($data['comment_ID'])) return;
$service=new WB_Service($data['comment_post_ID']);
$reply_allow=wpbooking_review_allow_reply($data['comment_ID']);
?>
	<div class="content_comment_profile">
		<div class="comment_container" id="comment-<?php echo esc_attr($data['comment_ID']) ?>">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php echo get_avatar( $data['comment_author_email'], 42 ); ?>
					<?php printf( '<b class="review-author-name">%s</b>', get_comment_author_link($data['comment_ID'] ) ); ?>

					<?php $count=WPBooking_User::inst()->count_reviews($data['comment_author_email']);
					if($count){
						printf('<span class="review-count">'._n('1 review','%d reviews',$count,'wpbooking').'</span>',$count);
					}
					?>
				</div><!-- .comment-author -->
			</footer><!-- .comment-meta -->
			<div class="comment-content-wrap">
				<?php if ( '0' == $data['comment_approved'] ) : ?>
					<p class="comment-awaiting-moderation"><?php esc_html_e( 'This review is waiting for approval.' ,'wpbooking'); ?></p>
				<?php else:
					$comment_title=get_comment_meta($data['comment_ID'],'wpbooking_title',true);
					if(!$comment_title)$comment_title='&nbsp;';
					if(empty($data['children']))
						printf('<span class="comnent-title">%s</span>',$comment_title);

					if($wpbooking_review=get_comment_meta($data['comment_ID'],'wpbooking_review',true)){
						?>
						<div class="wpbooking-review-summary">
							<label class="wpbooking-rating-review-result">
											<span class="rating-stars">
												<a class="<?php if($wpbooking_review>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
												<a class="<?php if($wpbooking_review>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
												<a class="<?php if($wpbooking_review>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
												<a class="<?php if($wpbooking_review>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
												<a class="<?php if($wpbooking_review>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
											</span>
							</label>
						</div>
						<?php
					}
					echo "<div class='comment-text'>";
					comment_text($data['comment_ID']);
					echo "</div>";
					$wpbooking_review_details=get_comment_meta($data['comment_ID'],'wpbooking_review_detail',true);
					if(!empty($wpbooking_review_details)){
						echo "<div class='wpbooking-more-review-detail'>";
						echo "<div class='review-stats'>";
						foreach($wpbooking_review_details as $key=> $value){
							if(!isset($value['title'])) return;
							if(!isset($value['rate'])) return;
							?>
							<label class="wpbooking-rating-review-result">
												<span class="rating-title">
													<?php echo esc_attr($value['title'])?>
												</span>
												<span class="rating-stars">
													<a class="<?php if($value['rate']>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
													<a class="<?php if($value['rate']>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
													<a class="<?php if($value['rate']>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
													<a class="<?php if($value['rate']>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
													<a class="<?php if($value['rate']>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
												</span>
							</label>
							<?php
						}
						echo "</div>";
						echo "<span class='wp-show-detail-review'><span class='more'>".esc_html__('More','wpbooking')." <i class='fa fa-angle-double-down'></i></span><span class='less'>".esc_html__('Less','wpbooking')." <i class='fa fa-angle-double-up'></i></span></span>";
						echo "</div>";
					}
					if($service->enable_vote_for_review($data['comment_ID']) and !$data['comment_parent'] AND $data['user_id'] == $data['my_user_id']){
						$count=wpbooking_count_review_vote($data['comment_ID']);
						$liked=wpbooking_user_liked_review($data['comment_ID'])?'active':FALSE;
						?>

						<div class="item_count_like <?php if($count == 0) echo 'hide'; ?>">
							<span class="icon-like active"><i class="fa fa-thumbs-o-up"></i></span>
											<span class="count_like">
													<?php echo esc_html($count) ?>
													<?php if($count > 1) esc_html_e('likes','wpbooking'); else esc_html_e('like','wpbooking') ?>
											</span>
						</div>

						<?php
						//printf('<div class="wpbooking-vote-for-review">%s <span class="review-vote-count">%s</span> <a data-review-id="%s" class="review-do-vote %s"><i class="fa fa-thumbs-o-up"></i></a></div>',esc_html__('Was this review helpful?','wpbooking'),($count)?sprintf(esc_html__('%d like this','wpbooking'),$count):FALSE,$data['comment_ID'],$liked);
					}
					?>
					<?php if(empty($data['children']) AND $data['user_id'] == $data['my_user_id']){ ?>
					<hr>
					<div class="comment-control">
						<?php if($service->enable_vote_for_review($data['comment_ID']) and !$data['comment_parent']){
							$liked=wpbooking_user_liked_review($data['comment_ID'])?'active':FALSE;
							?>
							<div data-review-id="<?php echo esc_html($data['comment_ID']) ?>" class="review-do-vote <?php echo esc_html($liked) ?>"> <i class="fa fa-thumbs-up "></i> <?php esc_html_e('Like','wpbooking'); ?></div>
						<?php } ?>

						<?php if($reply_allow AND $data['user_id'] == $data['my_user_id']){ ?>
							<div onclick="return false" class="wb-btn-reply-comment"> <i class="fa fa-comment "></i> <?php esc_html_e('Reply','wpbooking'); ?></div>
						<?php } ?>
					</div>
				<?php } ?>
					<?php
				endif; ?>
			</div><!-- .comment-content -->
		</div>
		<?php if($reply_allow AND $data['user_id'] == $data['my_user_id']) {?>
			<ul>
				<li class="reply-comment-form">
					<div class="comment_container">
						<footer class="comment-meta">
							<div class="comment-author vcard">
								<?php echo ($service->get_author('avatar')) ?>
								<?php printf( '<b class="review-author-name">%s</b>', $service->get_author('name') ); ?>
								<?php $count=WPBooking_User::inst()->count_reviews($service->get_author('email'));
								if($count){
									printf('<span class="review-count">'._n('1 review','%d reviews',$count,'wpbooking').'</span>',$count);
								}
								?>
							</div><!-- .comment-author -->
						</footer><!-- .comment-meta -->

						<div class="comment-content-wrap">
							<div class="wpbooking-add-reply">
								<div class="reply-input">
									<textarea name="reply_content" class="reply_content" id="" cols="30" rows="10"></textarea>
								</div>
								<div class="reply-submit">
									<a href="#" data-review-id="<?php echo esc_attr($data['comment_ID']) ?>" onclick="return false" class="wb-btn wb-btn-primary"><?php esc_html_e('Send','wpbooking')?> <i class="fa fa fa-spinner fa-spin"></i></a>
								</div>
							</div>
						</div><!-- .comment-content -->
					</div>
				</li>
			</ul>
		<?php } ?>
	</div>

