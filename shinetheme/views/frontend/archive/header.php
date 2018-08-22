<?php
global $wp_query;
if(!$wp_query->have_posts()) return;
?>

<div class="wpbooking-loop-header">
    <div class="search-title">
        <?php
        $action = WPBooking_Input::get('wpbooking_action');
        if($action == 'archive_filter'){
            echo '<h2>'.esc_html__('Search Result','wp-booking-management-system').'</h2>';
        }?>
    </div>
	<div class="col-post-found">
		<h2 class="post-found-count"><?php
            $service_type = WPBooking_Input::get('service_type');
            $service = WPBooking_Service_Controller::inst()->get_service_type($service_type);
			if($service_type and !empty($service)){
                printf(_n('Found %d ','Found %d ',$wp_query->found_posts, 'wp-booking-management-system').$service->get_info('label').esc_html__('(s)','wp-booking-management-system'), $wp_query->found_posts);
            }else{
                printf(_n('Found %d service','Found %d services',$wp_query->found_posts, 'wp-booking-management-system'), $wp_query->found_posts);
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
                $current_url = $_SERVER['REQUEST_URI'];
                $grid_url = add_query_arg(array(
                    'layout' => 'grid'
                ), $current_url);
                $list_url = add_query_arg(array(
                    'layout' => 'list'
                ), $current_url);
                $layout = wpbooking_get_layout_archive();
                ?>
                <a class="wb-btn wb-btn-grid <?php echo ($layout == 'grid'?'active':'')?>" href="<?php echo esc_url($grid_url); ?>"><i class="fa fa-th"></i></a>
                <a class="wb-btn wb-btn-list <?php echo ($layout == 'list'?'active':'')?>" href="<?php echo esc_url($list_url); ?>"><i class="fa fa-list"></i></a>
                <?php
				$sortby=array(
					'date_asc'=>esc_html__('ASC Date ','wp-booking-management-system'),
					'date_desc'=>esc_html__('DESC Date','wp-booking-management-system'),
					'price_asc'=>esc_html__('ASC Price ','wp-booking-management-system'),
					'price_desc'=>esc_html__('DESC Price','wp-booking-management-system'),
				);
                apply_filters('wpbooking_filter_list_sortby', $sortby, $service_type);
                if(WPBooking_Input::get('service_type')){
				?>
				<select name="wb_sort_by" class="wpbooking-loop-sort-by">
					<option value=""><?php echo esc_html__('Sort by','wp-booking-management-system') ?></option>
					<?php if(!empty($sortby)){
						foreach($sortby as $key=>$sort){
							printf('<option value="%s" %s >%s</option>',$key,selected(WPBooking_Input::get('wb_sort_by'),$key,FALSE),$sort);
						}
					} ?>
				</select>
                <?php } ?>
			</form>
		</div>
	</div>
</div>
