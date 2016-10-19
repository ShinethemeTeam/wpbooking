<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:23 PM
 */
global $post;
if (post_password_required()) {
	echo get_the_password_form();

	return;
}
$service_type = get_post_meta(get_the_ID(), 'service_type', TRUE);
$service = new WB_Service();
?>
<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<meta itemprop="url" content="<?php the_permalink(); ?>"/>
	<div class="container-fluid wpbooking-single-content entry-header">
		<?php if (has_post_thumbnail() and get_the_post_thumbnail()) {
			echo "<div class=single-thumbnai>";
			the_post_thumbnail("full");
			echo "</div>";

		} ?>

		<div class="service-title-gallery">
			<h1 class="service-title" itemprop="name"><?php the_title(); ?></h1>

			<div class="service-address-rate">
				<?php $address = $service->get_address();
				if ($address) {
					?>
					<div class="service-address">
						<i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
					</div>
				<?php } ?>
				<div class="service-rate">
					<?php
					$service->get_rate_html();
					?>
				</div>
			</div>
			<?php do_action('wpbooking_after_service_address_rate', get_the_ID(), $service->get_type(), $service) ?>

		</div>
		<div class="row-service-order-form">
			<div class="col-service-title">
				<div class="service-title-gallery">


					<div class="service-gallery-single">
						<?php
						$gallery = $service->get_gallery();
						if (!empty($gallery)) {
							foreach ($gallery as $media) {
								printf('<div class="gallery-item">%s<a class="hover-tag" data-effect="mfp-zoom-out" href="%s"><i class="fa fa-plus"></i></a></div>', $media['gallery'], $media['gallery_url']);
							}
						}
						?>
					</div>
				</div>
			</div>
			<div class="col-order-form">
				<div class="service-order-form">
					<div class="service-price"><?php $service->get_price_html(); ?></div>
					<div class="order-form-content">
						<?php echo wpbooking_load_view('single/order-form') ?>
					</div>
				</div>
			</div>
		</div>
		<div class="service-content-section">
			<h5 class="service-info-title"><?php esc_html_e('Description', 'wpbooing') ?></h5>

			<div class="service-content-wrap">
				<?php
				if (have_posts()) {

					while (have_posts()) {
						the_post();
						the_content();
					}
				}
				?>
			</div>
		</div>
		<div class="service-content-section">

				<div class="search-room-availablity">
					<form method="post" name="form-search-room" class="form-search-room">
						<?php wp_nonce_field('room_search','room_search')?>
						<input name="action" value="ajax_search_room" type="hidden">
						<div class="search-room-form">
							<h5 class="service-info-title"><?php esc_html_e('Check availablity', 'wpbooing') ?></h5>
							<div class="form-search">
								<div class="form-item w20 form-item-icon">
									<label><?php esc_html_e('Check In', 'wpbooing') ?></label>
									<input class="form-control wpbooking-search-start" name="check_in" placeholder="<?php esc_html_e('Check In', 'wpbooing') ?>">
									<i class="fa fa-calendar"></i>
								</div>
								<div class="form-item w20 form-item-icon">
									<label><?php esc_html_e('Check Out', 'wpbooing') ?></label>
									<input class="form-control wpbooking-search-end" name="check_out" placeholder="<?php esc_html_e('Check Out', 'wpbooing') ?>">
									<i class="fa fa-calendar"></i>
								</div>
								<div class="form-item w20">
									<label><?php esc_html_e('Rooms', 'wpbooing') ?></label>
									<select name="room_number" class="form-control">
										<?php
										for($i=1 ; $i<=20 ; $i++ ){
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
									</select>
								</div>
								<div class="form-item w20">
									<label><?php esc_html_e('Adults', 'wpbooing') ?></label>
									<select name="adults" class="form-control">
										<?php
										for($i=1 ; $i<=20 ; $i++ ){
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
									</select>
								</div>
								<div class="form-item w20">
									<label><?php esc_html_e('Children', 'wpbooing') ?></label>
									<select name="children" class="form-control">
										<?php
										for($i=0 ; $i<=20 ; $i++ ){
											echo '<option value="'.$i.'">'.$i.'</option>';
										}
										?>
									</select>
								</div>
								<button type="button" class="wb-button btn-do-search-room"><?php esc_html_e("CHECK AVAILABLITY","wpbooking") ?></button>
							</div>
						</div>
					</form>
					<div class="search_room_alert">xxx</div>
					<div class="content-search-room">
						<div class="content-loop-room">
							<?php
							global $wp_query;
							WPBooking_Hotel_Service_Type::inst()->search_room();
								var_dump($wp_query->request);
							?>
							<?php
							if(have_posts()) {
								while( have_posts() ) {
									the_post();
									?>
									<div class="loop-room">
										<div class="room-image">1</div>
										<div class="room-content"><?php the_title() ?></div>
										<div class="room-book">3</div>
									</div>
								<?php
								}

							} else {
								esc_html_e("No Data","wpbooking");
							}
							?>

							<?php wp_reset_query(); ?>
						</div>
						<div class="content-info"></div>
					</div>
				</div>

		</div>
		<div class="service-content-section">
			<h5 class="service-info-title"><?php esc_html_e('About Property', 'wpbooing') ?></h5>

			<div class="service-details">
				<?php
				$array = array(
					'max_guests'     => array(
						'title' => esc_html__('Max Guests', 'wpbooking'),
						'icon'  => 'flaticon-people',
					),
					'bedroom'        => array(
						'title' => esc_html__('Bedrooms', 'wpbooking'),
						'icon'  => 'flaticon-hotel-room'
					),
					'bathrooms'      => array(
						'title' => esc_html__('Bathrooms', 'wpbooking'),
						'icon'  => 'flaticon-bathtub'
					),
					'property_floor' => array(
						'title' => esc_html__('Floors', 'wpbooking'),
						'icon'  => 'flaticon-stairs'
					),
					'property_size'  => array(
						'title' => esc_html__('Size (%s)', 'wpbooking'),
						'icon'  => 'flaticon-full-size',
					),
					'double_bed'     => array(
						'title' => esc_html__('Double beds', 'wpbooking'),
						'icon'  => 'flaticon-double-bed'
					),
					'single_bed'     => array(
						'title' => esc_html__('Single beds', 'wpbooking'),
						'icon'  => 'flaticon-single-bed-outline'
					),
					'sofa_bed'       => array(
						'title' => esc_html__('Sofa beds', 'wpbooking'),
						'icon'  => 'flaticon-sofa'
					)
				);
				$space_html = array();
				foreach ($array as $key => $val) {
					if ($meta = get_post_meta(get_the_ID(), $key, TRUE)) {
						$space_html[] = '<li class="service-term">';
						$space_html[] = '<span class="icon-data-wrap">x<span class="icon-data">' . $meta . '</span>';

						if ($icon = $val['icon']) {
							$space_html[] = sprintf('<span class="service-term-icon"><i class="%s"></i></span>', wpbooking_icon_class_handler($icon));
						}
						$space_html[] = '</span>';

						switch ($key) {
							case "property_size":
								$space_html[] = '<a  class="sevice-term-name">' . sprintf($val['title'], get_post_meta(get_the_ID(), 'property_unit', TRUE)) . '</a>';
								break;
							default:
								$space_html[] = '<a  class="sevice-term-name">' . $val['title'] . '</a>';
								break;
						}

						$space_html[] = '</li>';
					}
				}
				if (!empty($space_html)) {
					?>
					<div class="service-detail-item">
						<div class="service-detail-title"><?php esc_html_e('The space', 'wpbooking') ?></div>
						<div class="service-detail-content">
							<ul class="service-list-terms icon_with_data">
								<?php echo implode("\r\n", $space_html) ?>
							</ul>
						</div>
					</div>
				<?php } ?>

				<?php if ($terms = $service->get_terms('wpbooking_amenity')) {
					?>
					<div class="service-detail-item">
						<div class="service-detail-title"><?php esc_html_e('Amenities', 'wpbooking') ?></div>
						<div class="service-detail-content">
							<ul class="service-list-terms">
								<?php foreach ($terms as $term) {
									$html = array();
									$html[] = '<li class="service-term">';
									$icon = wpbooking_get_term_meta($term->term_id, 'icon');
									if ($icon) $html[] = sprintf('<span class="service-term-icon"><i class="%s"></i></span>', wpbooking_icon_class_handler($icon));

									$html[] = '<a  class="sevice-term-name">' . $term->name . '</a>';
									$html[] = '</li>';

									echo implode("\r\n", $html);
								} ?>
							</ul>

						</div>
					</div>
				<?php } ?>
				<?php do_action('wpbooking_after_service_detail_amenities', $service_type, $service) ?>

				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Rate', 'wpbooking') ?>
						<!--<span class="help-icon"><i class="fa fa-question"></i></span>--></div>
					<div class="service-detail-content">
						<?php $array = array(
							'price'        => esc_html__('Nightly Rate: %s', 'wpbooking'),
							'weekly_rate'  => esc_html__('Weekly Rate: %s', 'wpbooking'),
							'monthly_rate' => esc_html__('Monthly Rate: %s', 'wpbooking'),
						);
						foreach ($array as $key => $val) {
							if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
								printf($val, WPBooking_Currency::format_money($value) . '<br>');
							}
						}
						?>
					</div>
				</div>
				<?php if (get_post_meta(get_the_ID(), 'enable_additional_guest_tax', TRUE)) {
						$addition_html=array();
						$array = array(
							'rate_based_on'          => sprintf(esc_html__('Rates are based on occupancy of: %s', 'wpbooking'),'<strong>%s</strong><br>'),
							'additional_guest_money' => sprintf(esc_html__('Each additional guest will pay : %s / night', 'wpbooking').'<br>','<strong>%s</strong>'),
							'tax'                    => sprintf(esc_html__('Tax: %s', 'wpbooking'),'<strong>%s</strong><br>'),
						);
						foreach ($array as $key => $val) {
							if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
								switch($key){
									case "rate_based_on":
										$addition_html[]=sprintf($val,$value);
										break;
									case "tax":
										$addition_html[]=sprintf($val,$value.'%');
										break;
									case "additional_guest_money":
										$addition_html[]=sprintf($val, WPBooking_Currency::format_money($value));
										break;

								}

							}
						}
						if(!empty($addition_html)){
							?>
							<div class="service-detail-item">
								<div
									class="service-detail-title"><?php esc_html_e('Additional Guests / Taxes / Misc', 'wpbooking') ?></div>
								<div class="service-detail-content">
									<?php echo implode("\r\n",$addition_html) ?>
								</div>
							</div>
						<?php } } ?>



				<?php
				$extra_services = $service->get_extra_services();
				if (is_array($extra_services) and !empty($extra_services)) { ?>
					<div class="service-detail-item">
						<div class="service-detail-title"><?php esc_html_e('Extra services', 'wpbooking') ?></div>
						<div class="service-detail-content">
							<ul class="service-extra-price">
								<?php
								foreach ($extra_services as $key => $val) {
									if(!$val['money']) continue;
									$price = WPBooking_Currency::format_money($val['money']);
									if ($val['require']=='yes') $price .= '<span class="required">' . esc_html__('required', 'wpbooking') . '</span>';
									printf('<li>+ %s: %s</li>', wpbooking_get_translated_string($val['title']), $price);
								}
								?>
							</ul>
						</div>
					</div>
				<?php } ?>

				<?php
				$rule_html=array();
				if ($deposit_amount = get_post_meta(get_the_ID(), 'deposit_amount', TRUE)) {
					if (get_post_meta(get_the_ID(), 'deposit_type', TRUE) == 'percent') {
						$rule_html[]=sprintf(esc_html__('Deposit: %s ', 'wpbooking'), $deposit_amount . '% <span class="required">' . esc_html__('required', 'wpbooking') . '</span>');
					} else {
						$rule_html[]=sprintf(esc_html__('Deposit: %s ', 'wpbooking'), WPBooking_Currency::format_money($deposit_amount) . ' <span class="required">' . esc_html__('required', 'wpbooking') . '</span>');
					}
				}

				$array = array(
					'check_in_time'  => esc_html__('Check In Time: %s', 'wpbooking'),
					'check_out_time' => esc_html__('Check Out Time: %s', 'wpbooking'),
				);
				foreach ($array as $key => $val) {
					if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
						$rule_html[]=sprintf($val, '<strong>' . $value . '</strong> <i class="fa fa-clock" ></i>	<br>');
					}
				}
				$array = array(
					'minimum_stay' => esc_html__('Minimum Stay: %s', 'wpbooking'),
				);
				foreach ($array as $key => $val) {
					if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
						$rule_html[]=sprintf($val, $value . '<br>');
					}
				}

				$array = array(
					'cancellation_allowed' => esc_html__('Cancellation Allowed: %s', 'wpbooking'),
				);

				foreach ($array as $key => $val) {
					if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
						$rule_html[]=sprintf($val, $value ? esc_html__('Yes', 'wpbooking') : esc_html__('No', 'wpbooking') . '<br>');
					}
				}

				$host_regulations = get_post_meta(get_the_ID(), 'host_regulations', TRUE);
				if (!empty($host_regulations)) {
					foreach ($host_regulations as $key => $value) {
						if ($value['title'] or $value['content'])
							$rule_html[]= (wpbooking_get_translated_string($value['title']) . ': ' . wpbooking_get_translated_string($value['content']) . '<br>');
					}
				}
				?>
				<?php if(!empty($rule_html)){ ?>
				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Rule', 'wpbooking') ?></div>
					<div class="service-detail-content">
						<?php
						echo implode("\r\n",$rule_html);
						?>
					</div>
				</div>
				<?php }?>

			</div>
		</div>

		<div class="service-content-section">
			<div class="service-map-contact">
				<div class="service-map">
					<?php
					$map_lat = get_post_meta(get_the_ID(), 'map_lat', TRUE);
					$map_lng = get_post_meta(get_the_ID(), 'map_long', TRUE);
					$map_zoom = get_post_meta(get_the_ID(), 'map_zoom', TRUE);
					if (!empty($map_lat) and !empty($map_lng)) { ?>
						<div class="service-map-element" data-lat="<?php echo esc_attr($map_lat) ?>"
							 data-lng="<?php echo esc_attr($map_lng) ?>"
							 data-zoom="<?php echo esc_attr($map_zoom) ?>"></div>
					<?php } ?>
				</div>
				<div class="service-author-contact">
					<div class="author-meta">
						<a href="<?php echo esc_url($service->get_author('profile_url')) ?>">
							<?php echo($service->get_author('avatar')) ?>
						</a>
						<span class="author-since"><?php echo($service->get_author('since')) ?></span>
					</div>
					<div class="author-details">
						<h5 class="author-name">
							<a href="<?php echo esc_url($service->get_author('profile_url')) ?>">
								<?php echo($service->get_author('name')) ?>
							</a>
						</h5>
						<?php if ($address = $service->get_author('address')) {
							printf('<p class="author-address">%s</p>', $address);
						} ?>
						<?php if ($desc = $service->get_author('description')) {
							printf('<div class="author-desc">%s</div>', $desc);
						} ?>
						<?php if (is_user_logged_in() and $service->get_author('id')!=get_current_user_id()) {
							printf('<a href="%s" class="wb-btn wb-btn-success">%s</a>', $service->get_author('contact_now_url'), esc_html__('Contact Now', 'wpbooking'));
						} ?>
					</div>

				</div>
			</div>
		</div>

		<div class="service-content-section comment-section">
			<?php
			if (comments_open(get_the_ID()) || get_comments_number()) :
				comments_template();
			endif;
			?>
		</div>
		<?php echo wpbooking_load_view('single/related') ?>

	</div>
</div>
