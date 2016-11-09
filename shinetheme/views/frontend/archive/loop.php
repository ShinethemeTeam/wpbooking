<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/20/2016
 * Time: 3:42 PM
 */
?>

<ul class="wpbooking-loop-items list">
	<?php
	global $wp_query;
    if(isset($my_query)) $wp_query = $my_query;
	//var_dump($wp_query->request);
	if ($wp_query->have_posts()) {
		while ($wp_query->have_posts()) {
            $wp_query->the_post();
			$service = new WB_Service();
			$url = add_query_arg(array(
				'checkin_d'  => WPBooking_Input::get('checkin_d'),
				'checkin_m'  => WPBooking_Input::get('checkin_m'),
				'checkin_y'  => WPBooking_Input::get('checkin_y'),
				'checkout_d' => WPBooking_Input::get('checkout_d'),
				'checkout_m' => WPBooking_Input::get('checkout_m'),
				'checkout_y' => WPBooking_Input::get('checkout_y'),
				'adult'     => WPBooking_Input::get('adult_s'),
				'child'     => WPBooking_Input::get('child_s'),
			), get_permalink());

			?>
			<li <?php post_class('loop-item') ?>>
				<div class="content-item">
					<div class="service-thumbnail">
						<?php
						echo $service->get_featured_image('thumb');
						?>
					</div>
					<div class="service-content">
						<div class="service-content-inner">
							<h3 class="service-title"><a
									href="<?php echo esc_url($url) ?>"><strong><?php the_title() ?></strong></a></h3>

							<div class="service-address-rate">
								<div class="wb-hotel-star">
									<?php
									$service->get_star_rating_html();
									?>
								</div>
								<?php $address = $service->get_address();
								if ($address) {
									?>
									<div class="service-address">
										<i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
									</div>
								<?php } ?>
							</div>
							<div class="wb-score-review">
								<?php echo $service->get_review_score(); ?>
							</div>
							<?php do_action('wpbooking_after_service_address_rate', get_the_ID(), $service->get_type(), $service) ?>
						</div>
						<div class="service-price-book-now">
							<div class="service-price">
								<?php
								$service->get_price_html();
								?>
							</div>
							<div class="service-book-now">
								<a class="wb-btn wb-btn-primary"
								   href="<?php echo esc_url($url) ?>"><?php esc_html_e('Book Now', 'wpbooking') ?></a>
							</div>
						</div>
					</div>
				</div>
			</li>
			<?php
		}
	} else {
		printf('<h3>%s</h3>', esc_html__('Found nothing match your search', 'wpbooking'));
	}
	?>
</ul>
