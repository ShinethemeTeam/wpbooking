<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/30/2016
 * Time: 5:41 PM
 */
$traveler_review_details=get_comment_meta(get_comment_ID(),'traveler_review_detail',true);
$traveler_review=get_comment_meta(get_comment_ID(),'traveler_review',true);
?>

<?php if(!empty($traveler_review_details)){
	foreach($traveler_review_details as $key=> $value){
		if(!isset($value['title'])) return;
		if(!isset($value['rate'])) return;
		?>
		<label class="traveler-rating-review-result">
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
	<label class="traveler-rating-review-result">
		<span class="rating-stars">
			<a class="<?php if($traveler_review>=1) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
			<a class="<?php if($traveler_review>=2) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
			<a class="<?php if($traveler_review>=3) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
			<a class="<?php if($traveler_review>=4) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
			<a class="<?php if($traveler_review>=5) echo 'active'; ?>"><i class="fa fa-star-o icon-star"></i></a>
		</span>
	</label>
	<?php
}?>