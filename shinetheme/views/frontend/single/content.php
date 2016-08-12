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
	<div class="container-fluid wpbooking-single-content">
		<div class="row">
			<div class="col-md-12">
				<?php if (has_post_thumbnail() and get_the_post_thumbnail()) {
					echo "<div class=single-thumbnai>";
					the_post_thumbnail("full");
					echo "</div>";

				} ?>
			</div>

		</div>

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
		<div class="row">
			<div class="col-sm-8 col-service-title">
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
				<?php if (is_user_logged_in() and get_current_user_id() != $post->post_author) { ?>
					<a class="btn btn-primary" data-toggle="modal"
					   data-target="#wb-send-message"><?php esc_html_e('Contact Host', 'wpbooking') ?></a>
				<?php } ?>
			</div>
			<div class="col-sm-4 col-order-form">
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
				<?php if (get_post_meta(get_the_ID(), 'enable_additional_guest_tax', TRUE)) { ?>
					<div class="service-detail-item">
						<div
							class="service-detail-title"><?php esc_html_e('Additional Guests / Taxes / Misc', 'wpbooking') ?></div>
						<div class="service-detail-content">
							<?php $array = array(
								'rate_based_on'          => sprintf(esc_html__('Rates are based on occupancy of: %s', 'wpbooking'),'<strong>%s</strong><br>'),
								'additional_guest_money' => sprintf(esc_html__('Each additional guest will pay : %s / night', 'wpbooking').'<br>','<strong>%s</strong>'),
								'tax'                    => sprintf(esc_html__('Tax: %s', 'wpbooking'),'<strong>%s</strong><br>'),
							);
							foreach ($array as $key => $val) {
								if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
									switch($key){
										case "rate_based_on":
											printf($val,$value);
											break;
										case "tax":
											printf($val,$value.'%');
											break;
										case "additional_guest_money":
											printf($val, WPBooking_Currency::format_money($value));
											break;

									}

								}
							}
							?>
						</div>
					</div>
				<?php } ?>



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

				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Rule', 'wpbooking') ?></div>
					<div class="service-detail-content">
						<?php
						if ($deposit_amount = get_post_meta(get_the_ID(), 'deposit_amount', TRUE)) {
							if (get_post_meta(get_the_ID(), 'deposit_type', TRUE) == 'percent') {
								printf(esc_html__('Deposit: %s ', 'wpbooking'), $deposit_amount . '% <span class="required">' . esc_html__('required', 'wpbooking') . '</span>');
							} else {
								printf(esc_html__('Deposit: %s ', 'wpbooking'), WPBooking_Currency::format_money($deposit_amount) . ' <span class="required">' . esc_html__('required', 'wpbooking') . '</span>');
							}
						}

						$array = array(
							'check_in_time'  => esc_html__('Check In Time: %s', 'wpbooking'),
							'check_out_time' => esc_html__('Check Out Time: %s', 'wpbooking'),
						);
						foreach ($array as $key => $val) {
							if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
								printf($val, '<strong>' . $value . '</strong> <i class="fa fa-clock" ></i>	<br>');
							}
						}
						$array = array(
							'minimum_stay' => esc_html__('Minimum Stay: %s', 'wpbooking'),
						);
						foreach ($array as $key => $val) {
							if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
								printf($val, $value . '<br>');
							}
						}

						$array = array(
							'cancellation_allowed' => esc_html__('Cancellation Allowed: %s', 'wpbooking'),
						);

						foreach ($array as $key => $val) {
							if ($value = get_post_meta(get_the_ID(), $key, TRUE)) {
								printf($val, $value ? esc_html__('Yes', 'wpbooking') : esc_html__('No', 'wpbooking') . '<br>');
							}
						}

						$host_regulations = get_post_meta(get_the_ID(), 'host_regulations', TRUE);
						if (!empty($host_regulations)) {
							foreach ($host_regulations as $key => $value) {
								if ($value['title'] or $value['content'])
									echo(wpbooking_get_translated_string($value['title']) . ': ' . wpbooking_get_translated_string($value['content']) . '<br>');
							}
						}
						?>
					</div>
				</div>

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
						<?php if (is_user_logged_in()) {
							printf('<a href="%s" class="wb-btn wb-btn-success">%s</a>', $service->get_author('contact_now_url'), esc_html__('Contact Now', 'wpbooking'));
						} ?>
					</div>

				</div>
			</div>
		</div>

		<div class="service-content-section comment-section">
			<?php
			if (comments_open() || get_comments_number()) :
				comments_template();
			endif;
			?>
		</div>
		<?php echo wpbooking_load_view('single/related') ?>

	</div>
</div>
<div class="modal fade" id="wb-send-message" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="<?php echo home_url('/') ?>" method="post" class="wb-send-message-form"
				  onsubmit="return false">
				<input type="hidden" name="wpbooking_action" value="send_message">
				<input type="hidden" name="post_id" value="<?php the_ID() ?>">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php printf(esc_html__('Send Message To %s', 'wpbooking'), get_the_author()) ?></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="wb-message-input"><?php esc_html_e('Your Message', 'wpbooking') ?></label>
						<textarea name="wb-message-input" id="wb-message-input" cols="30"
								  placeholder="<?php esc_html_e('Your Message', 'wpbooking') ?>" rows="10"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default"
							data-dismiss="modal"><?php esc_html_e('Close', 'wpbooking') ?></button>
					<button type="submit" class="btn btn-primary"
							type="submit"><?php esc_html_e('Send Message', 'wpbooking') ?></button>
					<div class="message-box text-left"></div>
				</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div><!-- /.modal -->

