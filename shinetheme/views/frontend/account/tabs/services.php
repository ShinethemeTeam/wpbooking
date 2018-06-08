<?php
$args = array(
	'posts_per_page' => 10,
	'post_type'      => 'wpbooking_service',
	'paged'          => WPBooking_Input::get('page_number',1),
	'author'         => get_current_user_id()

);
if ($service_type = WPBooking_Input::get('service_type')) {
	$args['meta_key'] = 'service_type';
	$args['meta_value'] = $service_type;
}
$query = new WP_Query($args);

$types = WPBooking_Service_Controller::inst()->get_service_types();
?>
	<h3 class="tab-page-title">
		<?php
		echo esc_html__('Your Listing', 'wp-booking-management-system');
		?>
	</h3>
<?php if (!empty($types) and count($types) > 1) { ?>
	<ul class="service-filters">
		<?php
		$class = FALSE;
		if (!WPBooking_Input::get('service_type')) $class = 'active';
		printf('<li class="%s"><a href="%s">%s</a></li>', $class, get_permalink(wpbooking_get_option('myaccount-page') ). 'tab/services', esc_html__('All', 'wp-booking-management-system'));
		foreach ($types as $type_id => $type) {
			$class = FALSE;
			if(WPBooking_Input::get('service_type')==$type_id) $class='active';
			$url = esc_url(add_query_arg(array('service_type' => $type_id), get_permalink(wpbooking_get_option('myaccount-page')).'tab/services'));
			printf('<li class="%s"><a href="%s">%s</a></li>', $class, $url, $type->get_info('label'));
		}
		?>
	</ul>
<?php } ?>
	<div class="wpbooking-account-services">
		<?php if ($query->have_posts()) {
			$title = sprintf(esc_html__('You have %d service(s),','wp-booking-management-system'),$query->found_posts);
			if($service_type and $service_type_object=WPBooking_Service_Controller::inst()->get_service_type($service_type)){
				$title=sprintf(esc_html__('You have %d %s(s)','wp-booking-management-system'),$query->found_posts,strtolower($service_type_object->get_info('label')));
			}

			while($query->have_posts()){
				$query->the_post();
				$service=new WB_Service();
				?>
				<div class="service-item">
					<div class="service-img">
						<?php echo ($service->get_featured_image('thumb')) ?>
					</div>
					<div class="service-info">
						<h5 class="service-title">
							<a href="<?php the_permalink()?>" target="_blank"><?php the_title()?></a>
						</h5>
						<p class="service-price"><?php $service->get_price_html(TRUE) ?></p>
						<div class="service-status">
							<label class="wpbooking-switch-wrap">
								<select data-id="<?php the_ID() ?>"   class="checkbox wpbooking_service_change_status">
									<option <?php selected($service->get_meta('enable_property'),'on') ?>  value="on">on</option>
									<option <?php selected($service->get_meta('enable_property'),'off') ?> value="off">off</option>
								</select>
								<div class="wpbooking-switch <?php echo ($service->get_meta('enable_property')=='on')?'switchOn':FALSE ?>"></div>
							</label>
						</div>
					</div>
				</div>
				<?php
			}
		} else {
			printf('<div class="alert alert-danger">%s</div>', esc_html__('Not Found Service(s)', 'wp-booking-management-system'));
		}


		?>
		<div class="wpbooking-pagination">
			<?php  echo paginate_links(array(
						'total'=>$query->max_num_pages,
						'current'  => WPBooking_Input::get('page_number', 1),
						'format'   => '?page_number=%#%',
						'add_args' => array()
					));?>
		</div>

	</div>
<?php wp_reset_postdata(); ?>