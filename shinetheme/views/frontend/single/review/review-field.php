<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 4:01 PM
 */
$wpbooking_review_stats=apply_filters('wpbooking_review_stats',array(),get_the_ID());
?>
<div class="wpbooking-comment-form-rating">
	<p class="comment-form-review">
		<label for="wpbooking_review" class="form-label"><strong><?php _e("Review Stars",'wpbooking') ?></strong></label>
		<input type="hidden" class="" name="wpbooking_review" value="0">

		<div class="review-stats">
			<?php if(!empty($wpbooking_review_stats)){
				foreach($wpbooking_review_stats as $key=> $value){
					?>
					<label class="wpbooking-rating-review">
						<span class="rating-title">
							<?php echo esc_attr($value['title'])?>
						</span>
						<input type="hidden" class="wpbooking_review_detail_rate" name="wpbooking_review_detail[<?php echo esc_attr($key)?>][rate]" value="0">
						<input type="hidden" class="" name="wpbooking_review_detail[<?php echo esc_attr($key)?>][title]" value="<?php echo esc_attr($value['title']) ?>">
						<span class="rating-stars">
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
						</span>
					</label>
					<?php
				}
			}else{
				?>
				<label class="wpbooking-rating-review">
						<span class="rating-stars">
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
							<a ><i class="fa fa-star-o icon-star"></i></a>
						</span>
				</label>
				<?php
			}?>
		</div>

	</p>
</div>