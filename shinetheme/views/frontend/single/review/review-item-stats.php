<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 5:41 PM
 */
$wpbooking_review_details=get_comment_meta(get_comment_ID(),'wpbooking_review_detail',true);
$wpbooking_review=get_comment_meta(get_comment_ID(),'wpbooking_review',true);
?>
<div class="wpbooking-rating-result-wrap">
	<?php if(!empty($wpbooking_review_details)){
		foreach($wpbooking_review_details as $key=> $value){
			if(!isset($value['title'])) return;
			if(!isset($value['rate'])) return;
			?>
			<label class="wpbooking-rating-review-result">
				<?php echo esc_attr($value['title'])?>
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
	}else{
		?>
		<label class="wpbooking-rating-review-result">
			<span class="rating-stars">
				<a class="<?php if($wpbooking_review>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
				<a class="<?php if($wpbooking_review>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
				<a class="<?php if($wpbooking_review>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
				<a class="<?php if($wpbooking_review>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
				<a class="<?php if($wpbooking_review>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
			</span>
		</label>
		<?php
	}?>
</div>
