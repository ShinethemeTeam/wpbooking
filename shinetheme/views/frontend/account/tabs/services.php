<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/27/2016
 * Time: 10:45 AM
 */
if(get_query_var('service')){
	echo wpbooking_load_view('account/service/update');
	return;
}
$query=new WP_Query(array(
	'posts_per_page'=>10,
	'post_type'=>'wpbooking_service',
	'author'=>get_current_user_id(),
	'paged'=>get_query_var('paged')
));
?>
<div class="wpbooking-account-services">
	<form action="" method="get">
		<table class="wpbooking-service-table" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="select-all"><input type="checkbox" class="wpbooking-check-all"></th>
					<th class="service-image"><?php esc_html_e('Image','wpbooking') ?></th>
					<th class="service-name"><?php esc_html_e('Name','wpbooking') ?></th>
					<th class="service-price"><?php esc_html_e('Price','wpbooking') ?></th>
					<th class="service-status"><?php esc_html_e('Status','wpbooking') ?></th>
				</tr>
			</thead>
			<tbody>

			<?php
			if(!$query->have_posts()){
				?>
				<tr>
					<td colspan="10"><div class="alert alert-danger"><?php esc_html_e('No Items Found','wpbooking') ?></div></td>
				</tr>

				<?php
				return;
			}
			else{
				while($query->have_posts()){
					$query->the_post();
					$service_type=get_post_meta(get_the_ID(),'service_type',true);
					$edit_url=get_permalink(wpbooking_get_option('myaccount-page')).'service/'.get_the_ID();
					?>
					<tr class="wpbooking-account-service">
						<td class="select-all"><input type="checkbox" name="service_ids[]" value="<?php get_the_ID() ?>"></td>
						<td><?php if(has_post_thumbnail() and get_the_post_thumbnail()){
								the_post_thumbnail( array( 200, 150 ) );
							}?></td>
						<td>
							<h5 class="booking-item-title"><a href="<?php echo esc_url($edit_url) ?>" class=""><?php the_title(); ?></a></h5>
						</td>
						<td>
							<?php echo wpbooking_service_price_html() ?>
						</td>
						<td>
							<?php echo get_post_status() ?>
						</td>
					</tr>

					<?php

				}
				?>
				<?php
			}


			?>
			</tbody>
		</table>
		<div class="row">
			<div class="col-sm-6">
					<select class="wpbooking_action">
						<option name=""><?php esc_html_e('Bulk Edit','wpbooking') ?></option>
						<option name="trash"><?php esc_html_e('Trash','wpbooking') ?></option>
						<option name="draft"><?php esc_html_e('Draft','wpbooking') ?></option>
					</select>
					<button type="submit" class="btn btn-primary"><?php esc_html_e('Apply','wpbooking') ?></button>
				</label>
			</div>
			<div class="col-sm-6">
				<?php echo wpbooking_load_view('archive/pagination',array('my_query'=>$query)) ?>
			</div>
		</div>
	</form>
</div>
<?php wp_reset_postdata();?>