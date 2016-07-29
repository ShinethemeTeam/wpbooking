<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/27/2016
 * Time: 4:29 PM
 */
if(!$my_query->have_posts()) return;
?>

<div class="wpbooking-loop-header">
	<div class="row">
		<div class="col-sm-7">
			<h2 class="post-found-count"><?php printf(esc_html__('Found %d room(s)','wpbooking'),$my_query->found_posts) ?></h2>
			<p class="post-query-desc">
				<?php
					echo wpbooking_post_query_desc();
				?>
			</p>
		</div>
		<div class="col-sm-5">
			<div class="wpbooking-loop-order">
				<form method="get" >
					<?php if(!empty($_GET)){
						foreach($_GET as $key=>$get){
							if(!in_array($key,array('wb_sort_by')))
							{
								if(is_array($get)){
									if(!empty($get)){
										foreach($get as $key2=>$val2){
											printf('<input type="hidden" name="%s[%s]" value="%s">',$key,$key2,$val2);
										}
									}
								}else{
									printf('<input type="hidden" name="%s" value="%s">',$key,$get);
								}

							}
						}
					}
					$sortby=array(
						'date_asc'=>esc_html__('Date ASC','wpbooking'),
						'date_desc'=>esc_html__('Date DESC','wpbooking'),
						'price_asc'=>esc_html__('Price ASC','wpbooking'),
						'price_desc'=>esc_html__('Price DESC','wpbooking'),
					)
					?>
					<select name="wb_sort_by" class="wpbooking-loop-sort-by">
						<option value=""><?php esc_html_e('Sort by','wpbooking') ?></option>
						<?php if(!empty($sortby)){
							foreach($sortby as $key=>$sort){
								printf('<option value="%s" %s >%s</option>',$key,selected(WPBooking_Input::get('wb_sort_by'),$key,FALSE),$sort);
							}
						} ?>
					</select>
				</form>
			</div>
			<div class="wpbooking-view-switch">
				<a href="#" class="<?php if(empty($_COOKIE['wpbooking_view_type']) or $_COOKIE['wpbooking_view_type']=='grid') echo 'active'; ?>" data-view="grid"><i class="fa fa-th"></i></a>
				<a href="#" class="<?php if(!empty($_COOKIE['wpbooking_view_type']) and $_COOKIE['wpbooking_view_type']=='list') echo 'active'; ?>" data-view="list"><i class="fa fa-list-ul"></i></a>
			</div>
		</div>
	</div>
</div>
