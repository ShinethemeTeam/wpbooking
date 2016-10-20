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

							?>
							<?php
							if(have_posts()) {
								while( have_posts() ) {
									the_post();
									?>
									<div class="loop-room">
										<div class="room-image">
											<img class="" src="http://localhost/shinetheme/traveler-booking/wp-content/uploads/2016/08/12122691_1656913957929210_758468824321059402_n-150x150.jpg">
										</div>
										<div class="room-content">
											<div class="room-title">
												<?php the_title() ?>
											</div>
											<div class="room-info">
												<div class="control left">
													<div class="img">
														<img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0ODcuOTAxIDQ4Ny45MDEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4Ny45MDEgNDg3LjkwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ3NC4yLDMwMy44MDFjLTM4LjktMzItODAuOS01My45LTkyLjYtNTkuN3YtNTguMmM4LjMtNi43LDEzLjItMTYuOCwxMy4yLTI3LjZ2LTY1LjVjMC0zNS44LTI5LjEtNjUtNjUtNjVoLTE0LjEgICAgYy0zNS44LDAtNjUsMjkuMS02NSw2NXY2NS41YzAsMTAuOCw0LjksMjAuOSwxMy4yLDI3LjZ2NTguMmMtMTEuNyw1LjgtNTMuNywyNy43LTkyLjYsNTkuN2MtOC43LDcuMi0xMy43LDE3LjgtMTMuNywyOS4ydjQ0LjkgICAgYzAsMy4zLDIuNyw2LDYsNmMzLjMsMCw2LTIuNyw2LTZ2LTQ0LjljMC03LjgsMy40LTE1LDkuMy0xOS45YzQwLjItMzMsODMuNy01NSw5Mi01OWMzLjEtMS41LDUtNC42LDUtOHYtNjMuMWMwLTItMS0zLjktMi43LTUgICAgYy02LjYtNC40LTEwLjUtMTEuNy0xMC41LTE5LjZ2LTY1LjVjMC0yOS4yLDIzLjgtNTMsNTMtNTNoMTQuMWMyOS4yLDAsNTMsMjMuOCw1Myw1M3Y2NS41YzAsNy45LTMuOSwxNS4yLTEwLjUsMTkuNiAgICBjLTEuNywxLjEtMi43LDMtMi43LDV2NjMuMWMwLDMuNCwxLjksNi41LDUsOGM4LjMsNC4xLDUxLjksMjYsOTIsNTljNS45LDQuOSw5LjMsMTIuMSw5LjMsMTkuOXY0NC45YzAsMy4zLDIuNyw2LDYsNnM2LTIuNyw2LTYgICAgdi00NC45QzQ4OCwzMjEuNjAxLDQ4MywzMTEuMDAxLDQ3NC4yLDMwMy44MDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTQxLjQsOTIuMDAxaC0xMS41Yy0yOS44LDAtNTQsMjQuMi01NCw1NHY1My4zYzAsOC45LDQsMTcuMywxMC43LDIzdjQ2LjJjLTEwLjMsNS4yLTQzLjksMjIuOS03NSw0OC40ICAgIGMtNy40LDYuMS0xMS42LDE1LTExLjYsMjQuNnYzNi41YzAuMiwzLjIsMi45LDUuOSw2LjIsNS45YzMuMywwLDYtMi43LDYtNnYtMzYuNWMwLTYsMi42LTExLjYsNy4yLTE1LjMgICAgYzMyLjYtMjYuOCw2OC00NC42LDc0LjctNDcuOWMyLjktMS40LDQuNy00LjMsNC43LTcuNXYtNTEuNGMwLTItMS0zLjktMi43LTVjLTUtMy40LTguMS05LTguMS0xNXYtNTMuM2MwLTIzLjIsMTguOS00Miw0Mi00MiAgICBoMTEuNWMyMy4yLDAsNDIsMTguOSw0Miw0MnY1My4zYzAsNi0zLDExLjctOC4xLDE1Yy0xLjcsMS4xLTIuNywzLTIuNyw1djQyLjJjMCwzLjMsMi43LDYsNiw2YzMuMywwLDYtMi43LDYtNnYtMzkuMiAgICBjNi44LTUuNywxMC43LTE0LjEsMTAuNy0yM3YtNTMuM0MxOTUuNCwxMTYuMjAxLDE3MS4yLDkyLjAwMSwxNDEuNCw5Mi4wMDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzUwLjUsMjY0LjMwMWMwLTMuNC0yLjctNi4xLTYtNi4xcy02LDIuNy02LDZjMCw4LjYtNywxNS43LTE1LjcsMTUuN2MtMy4zLDAtNi40LTEuMS05LTIuOGMtMC40LTAuNS0wLjktMC45LTEuNS0xLjIgICAgYy0zLjItMi45LTUuMi03LTUuMi0xMS42YzAtMy4zLTIuNy02LTYtNnMtNiwyLjctNiw2YzAsNy42LDMuMSwxNC41LDguMSwxOS41bC02LjgsMTUxYy0wLjEsMS44LDAuNiwzLjUsMiw0LjdsMjEuMSwxOS4xICAgIGMxLjEsMSwyLjYsMS41LDQsMS41YzEuNCwwLDIuOS0wLjUsNC0xLjVsMjAuOC0xOC44YzEuMy0xLjIsMi4xLTIuOSwyLTQuN2wtNi42LTE1Mi43QzM0Ny45LDI3Ny41MDEsMzUwLjUsMjcxLjIwMSwzNTAuNSwyNjQuMzAxICAgIHogTTMyMy41LDQ0Ni4wMDFsLTE1LTEzLjVsNi40LTE0MS43YzIuNSwwLjcsNS4xLDEuMiw3LjksMS4yYzMuMiwwLDYuMy0wLjYsOS4yLTEuNmw2LjEsMTQyLjVMMzIzLjUsNDQ2LjAwMXoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
													</div>
													Max 4
												</div>
												<div class="control">
													<div class="img">
														<img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTcuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ0Ny4wMjEgNDQ3LjAyMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDQ3LjAyMSA0NDcuMDIxOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8cGF0aCBkPSJNNDQ2LjkwOCw3LjU5OWMtMC4wMDItNC4xMzktMy4zNTctNy40OTQtNy40OTYtNy40OTZMMjQ3LjUxLDBjLTEyLjk1OCwwLTIzLjUsMTAuNTQyLTIzLjUsMjMuNXY0OCAgIGMwLDEyLjk1OCwxMC41NDIsMjMuNSwyMy41LDIzLjVoMzcuODk1TDk1LjAxNiwyODUuNDA4bDAuMDE0LTQ1Ljk2NEM5NC45OTksMjI2LjUxNyw4NC40NTcsMjE2LDcxLjUzLDIxNkgyMy41MSAgIGMtNi4yODMsMC0xMi4xODgsMi40NDgtMTYuNjI4LDYuODk0Yy00LjQ0LDQuNDQ2LTYuODgsMTAuMzU0LTYuODcyLDE2LjYzMWwwLjEwMywxOTkuODk3YzAuMDAyLDQuMTM5LDMuMzU3LDcuNDk0LDcuNDk2LDcuNDk2ICAgbDE5MS45MDEsMC4xMDNjMTIuOTU4LDAsMjMuNS0xMC41NDIsMjMuNS0yMy41di00OGMwLTEyLjk1OC0xMC41NDItMjMuNS0yMy41LTIzLjVoLTM3Ljg5NWwxOTAuMzg5LTE5MC40MDhsLTAuMDE0LDQ1Ljk2MyAgIGMwLjAzMSwxMi45MjcsMTAuNTczLDIzLjQ0NCwyMy41LDIzLjQ0NGg0OC4wMmM2LjI4MywwLDEyLjE4OC0yLjQ0OCwxNi42MjgtNi44OTRjNC40NC00LjQ0Niw2Ljg4LTEwLjM1NCw2Ljg3Mi0xNi42MzEgICBMNDQ2LjkwOCw3LjU5OXogTTQyOS41MjUsMjEzLjUyN2MtMS42MDYsMS42MDgtMy43NDIsMi40OTQtNi4wMTUsMi40OTRoLTQ4LjAyYy00LjY3NiwwLTguNDg5LTMuODA0LTguNS04LjQ1OWwwLjAyLTY0LjA1OSAgIGMwLjAwMS0zLjAzNC0xLjgyNi01Ljc3LTQuNjI5LTYuOTMxYy0yLjgwMi0xLjE2Mi02LjAyOS0wLjUyLTguMTc1LDEuNjI1bC0yMTYsMjE2LjAyMWMtMi4xNDUsMi4xNDUtMi43ODYsNS4zNzEtMS42MjUsOC4xNzMgICBjMS4xNjEsMi44MDMsMy44OTYsNC42Myw2LjkyOSw0LjYzaDU2YzQuNjg3LDAsOC41LDMuODEzLDguNSw4LjV2NDhjMCw0LjY4Ny0zLjgxMyw4LjUtOC40OTYsOC41bC0xODQuNDA1LTAuMDk5TDE1LjAxLDIzOS41MTEgICBjLTAuMDAzLTIuMjcyLDAuODgtNC40MSwyLjQ4NS02LjAxOGMxLjYwNi0xLjYwOCwzLjc0Mi0yLjQ5NCw2LjAxNS0yLjQ5NGg0OC4wMmM0LjY3NiwwLDguNDg5LDMuODA0LDguNSw4LjQ1OWwtMC4wMiw2NC4wNTkgICBjLTAuMDAxLDMuMDM0LDEuODI2LDUuNzcsNC42MjksNi45MzFjMi44MDIsMS4xNjEsNi4wMjksMC41Miw4LjE3NS0xLjYyNWwyMTYtMjE2LjAyMWMyLjE0NS0yLjE0NSwyLjc4Ni01LjM3MSwxLjYyNS04LjE3MyAgIGMtMS4xNjEtMi44MDMtMy44OTYtNC42My02LjkyOS00LjYzaC01NmMtNC42ODcsMC04LjUtMy44MTMtOC41LTguNXYtNDhjMC00LjY4NywzLjgxMy04LjUsOC40OTYtOC41bDE4NC40MDUsMC4wOTlsMC4wOTksMTkyLjQxMSAgIEM0MzIuMDEzLDIwOS43ODIsNDMxLjEzMSwyMTEuOTE5LDQyOS41MjUsMjEzLjUyN3oiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik01NS41MSwyNDcuNDUzYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djU2LjU2OGMwLDQuMTQyLDMuMzU4LDcuNSw3LjUsNy41czcuNS0zLjM1OCw3LjUtNy41di01Ni41NjggICBDNjMuMDEsMjUwLjgxMSw1OS42NTMsMjQ3LjQ1Myw1NS41MSwyNDcuNDUzeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSw0OC4wMjFjLTQuMTQyLDAtNy41LDMuMzU4LTcuNSw3LjV2ODBjMCw0LjE0MiwzLjM1OCw3LjUsNy41LDcuNXM3LjUtMy4zNTgsNy41LTcuNXYtODAgICBDNDE1LjAxLDUxLjM3OSw0MTEuNjUzLDQ4LjAyMSw0MDcuNTEsNDguMDIxeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSwxNjAuMDIxYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djE2YzAsNC4xNDIsMy4zNTgsNy41LDcuNSw3LjVzNy41LTMuMzU4LDcuNS03LjV2LTE2ICAgQzQxNS4wMSwxNjMuMzc5LDQxMS42NTMsMTYwLjAyMSw0MDcuNTEsMTYwLjAyMXoiIGZpbGw9IiMwMDAwMDAiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />
													</div>
													30m2
												</div>
											</div>
											<div class="room-facilities">
												<div class="title"><?php esc_html_e("Facilities","wpbooking") ?>:</div>
												<div class="facilities">Air conditioning, bathtub, Flat TV, free latop  using, Oceanvie</div>
											</div>
										</div>
										<div class="room-book">
											<div class="room-total-price">
												$ 100.00
											</div>
											<div class="room-number">
												<select class="form-control">
													<?php
													for($i=0;$i<20;$i++){
														echo "<option value='{$i}'>{$i}</option>";
													}
													?>
												</select>
											</div>
											<div class="room-extra">
												<span class="btn_extra"><?php esc_html_e("Extra services","wpbooking") ?></span>
											</div>
										</div>
										<div class="more-extra">
											<table>
												<thead>
													<tr>
														<td width="10%">

														</td>
														<td width="50%">
															<?php esc_html_e("Service name",'wpbooking') ?>
														</td>
														<td class="text-center">
															<?php esc_html_e("Quantity",'wpbooking') ?>
														</td>
														<td class="text-center">
															<?php esc_html_e("Price $",'wpbooking') ?>
														</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="text-center">
															<input type="checkbox">
														</td>
														<td>
															<span class="title">Extra Bed</span>
															<span class="desc">Extra Bed</span>
														</td>
														<td>
															<select class="form-control">
																<?php
																for($i=0;$i<20;$i++){
																	echo "<option value='{$i}'>{$i}</option>";
																}
																?>
															</select>
														</td>
														<td class="text-center text-color">
															$100
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								<?php
								}

							} else {
								esc_html_e("No Data","wpbooking");
							}
							?>

							<?php wp_reset_query(); ?>
						</div>
						<div class="content-info">
							<div class="content-price">
								<div class="number">10 rooms</div>
								<div class="price"> $ 100</div>
								<button type="button" class="wb-button"><?php esc_html_e("BOOK NOW",'wpbooking') ?></button>
							</div>
						</div>
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
