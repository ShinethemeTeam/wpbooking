<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 3:36 PM
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="wb-reviews-section">
	
	<?php if ( have_comments() ) : ?>
		<h5 class="service-info-title">
			<?php
			printf(esc_html__("%s's Reviews",'wpbooking'),get_the_title());
			?>
		</h5>

		
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
				'callback'=>'wpbooking_comment_item'
			) );
			?>
		</ol><!-- .comment-list -->
		
		<?php wpbooking_comment_nav(); ?>

		<?php
		$count=get_comments_number();
		$limit=get_option('comments_per_page');
		if($count){
			$page = get_query_var('cpage');
			if ( !$page )
				$page = 1;

			$page--;

			$to=($page+1)*$limit;

			if($count<$to)
			{
				$to=$count;
			}
			printf('<div class="wpbooking-review-total"><span class="count-total">%s</span><span class="show-from">%s</span></div>',_n('1 review on this room','% reviews on this room',$count,'wpbooking'),sprintf(esc_html__('Showing %d to %d','wpbooking'),($limit*$page)+1,$to));
		}
		?>
	<?php endif; // have_comments() ?>
	
	<?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'wpbooking' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- .comments-area -->
