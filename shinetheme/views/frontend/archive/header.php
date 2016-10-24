<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/27/2016
 * Time: 4:29 PM
 */
global $wp_query;
if(!$wp_query->have_posts()) return;
?>

<div class="wpbooking-loop-header">
    <div class="search-title">
        <?php
        $action = WPBooking_Input::get('wpbooking_action');
        if($action == 'archive_filter'){
            echo '<h2>'.__('Search Result','wpbooking').'</h2>';
        }?>
    </div>
	<div class="col-post-found">
		<h2 class="post-found-count"><?php
            $service = new WB_Service();
            if($service == 'room') {
                printf(_n('Found %d room','Found %d rooms',$wp_query->found_posts, 'wpbooking'), $wp_query->found_posts);
            }else{
                printf(_n('Found %d hotel','Found %d hotels',$wp_query->found_posts, 'wpbooking'), $wp_query->found_posts);
            }
            ?></h2>
		<p class="post-query-desc">
			<?php
				echo wpbooking_post_query_desc();
			?>
		</p>
	</div>
	<div class="col-loop-order">
		<div class="wpbooking-loop-order">
			<form method="get" >
				<?php if(!empty($_GET)){
					foreach($_GET as $key=>$get){
						if(!in_array($key,array('wb_sort_by')))
						{
							if(is_array($get)){
								if(!empty($get)){
									foreach($get as $key2=>$val2){
										if($val2)
										printf('<input type="hidden" name="%s[%s]" value="%s">',$key,$key2,$val2);
									}
								}
							}elseif($get){
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
					'rate_asc'=>esc_html__('Rate ASC','wpbooking'),
					'rate_desc'=>esc_html__('Rate DESC','wpbooking'),
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
	</div>
</div>
