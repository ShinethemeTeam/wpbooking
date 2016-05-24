<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/28/2016
 * Time: 2:32 PM
 */
$extra_price=get_post_meta(get_the_ID(),'extra_price',true);
if(empty($extra_price) or !is_array($extra_price)) return;
?>
<div class="traveler-extra-price-wrap mb20">
	<h4 ><?php _e('Extra Price:','wpbooking') ?></h4>
	<div class="traveler-extra-price-list">
		<?php
		foreach($extra_price as $key=>$value){
			?>
			<div class="extra-item">
				<label >
					<input type="checkbox" name="">
					<span class="item-title"><?php echo esc_html($value['title']) ?></span>
					<span class="item-price"><?php echo Traveler_Currency::format_money($value['price']) ?></span>
				</label>

			</div>
			<?php
		}
		?>
	</div>
</div>

