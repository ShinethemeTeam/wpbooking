<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 4:01 PM
 */
$wpbooking_review_stats=apply_filters('wpbooking_review_stats',array(),get_the_ID());
?>
<p class="comment-form-review">
	<label for="wpbooking_review"><?php _e("Your Rating",'wpbooking') ?></label>
	<input type="hidden" class="" name="wpbooking_review">

	<?php if(!empty($wpbooking_review_stats)){
		foreach($wpbooking_review_stats as $key=> $value){
			?>
			<label class="wpbooking-rating-review">
				<?php echo esc_attr($value['title'])?>
				<input type="hidden" class="wpbooking_review_detail_rate" name="wpbooking_review_detail[<?php echo esc_attr($key)?>][rate]">
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

</p>