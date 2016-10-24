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
	if ($wp_query->have_posts()) {
		while ($wp_query->have_posts()) {
            $wp_query->the_post();
			$service = new WB_Service();
			$url = add_query_arg(array(
				'check_in'  => WPBooking_Input::get('check_in'),
				'check_out' => WPBooking_Input::get('check_out'),
				'adult'     => WPBooking_Input::get('adult_s'),
				'child'     => WPBooking_Input::get('child_s'),
			), get_permalink());

			switch ($service_type = $service->get_type()) {
				case "room":
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
					break;
				case 'accommodation':
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
											$hotel_star = $service->get_meta('star_rating');
											for($i=1; $i<=5; $i++){
												$active=FALSE;
												if($hotel_star >= $i) $active='active';
												echo sprintf('<span class="%s"><i class="fa fa-star-o icon-star"></i></span>',$active);
											}

                                            echo '<span>'.$hotel_star.' '._n('star','stars',(int)$hotel_star,'wpbooking').'</span>';

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
					break;
			}
		}
	} else {
		printf('<h3>%s</h3>', esc_html__('Found nothing match your search', 'wpbooking'));
	}
	?>
</ul>
