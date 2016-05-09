<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 4:01 PM
 */
$traveler_review_stats=apply_filters('traveler_review_stats',array(),get_the_ID());
?>
<p class="comment-form-review">
	<label for="traveler_review"><?php _e("Your Rating",'wpbooking') ?></label>
	<input type="hidden" class="" name="traveler_review">

	<?php if(!empty($traveler_review_stats)){
		foreach($traveler_review_stats as $key=> $value){
			?>
			<label class="traveler-rating-review">
				<?php echo esc_attr($value['title'])?>
				<input type="hidden" class="traveler_review_detail_rate" name="traveler_review_detail[<?php echo esc_attr($key)?>][rate]">
				<input type="hidden" class="" name="traveler_review_detail[<?php echo esc_attr($key)?>][title]" value="<?php echo esc_attr($value['title']) ?>">
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
		<label class="traveler-rating-review">
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