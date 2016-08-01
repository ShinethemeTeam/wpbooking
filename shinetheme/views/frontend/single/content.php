<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:23 PM
 */
global $post;
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
$service_type = get_post_meta(get_the_ID(),'service_type',true);
$service=new WB_Service();
?>
<div  itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />
    <div class="container-fluid wpbooking-single-content">
        <div class="row">
            <div class="col-md-12">
                <?php if(has_post_thumbnail() and get_the_post_thumbnail()){
                    echo "<div class=single-thumbnai>";
                    the_post_thumbnail( "full" );
                    echo "</div>";

                }?>
            </div>

        </div>

		<div class="service-title-gallery">
			<h1 class="service-title" itemprop="name"><?php the_title(); ?></h1>
			<div class="service-address-rate">
				<?php $address=$service->get_address();
				if($address){
					?>
					<div class="service-address">
						<i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
					</div>
				<?php }?>
				<div class="service-rate">
					<?php
					$service->get_rate_html();
					?>
				</div>
			</div>
			<?php do_action('wpbooking_after_service_address_rate',get_the_ID(),$service->get_type(),$service) ?>

		</div>
		<div class="row">
			<div class="col-sm-8 col-service-title">
				<div class="service-title-gallery">


					<div class="service-gallery-single">
						<?php
						$gallery=$service->get_gallery();
						if(!empty($gallery)){
							foreach($gallery as $media){
								printf('<div class="gallery-item">%s<a class="hover-tag" data-effect="mfp-zoom-out" href="%s"><i class="fa fa-plus"></i></a></div>',$media['gallery'],$media['gallery_url']);
							}
						}
						?>
					</div>
				</div>
				<?php if(is_user_logged_in() and get_current_user_id()!=$post->post_author){ ?>
					<a class="btn btn-primary" data-toggle="modal" data-target="#wb-send-message"><?php esc_html_e('Contact Host','wpbooking') ?></a>
				<?php }?>
			</div>
			<div class="col-sm-4 col-order-form">
				<div class="service-order-form">
					<div class="service-price"><?php $service->get_price_html();?></div>
					<div class="order-form-content">
						<?php echo wpbooking_load_view('single/order-form') ?>
					</div>
				</div>
			</div>
		</div>
		<div class="service-content-section">
			<h5 class="service-info-title"><?php esc_html_e('Description','wpbooing')?></h5>
			<div class="service-content-wrap">
				<?php
				if(have_posts()){
					while(have_posts())
					{
						the_post();
						the_content();
					}
				}
				?>
			</div>
		</div>
		<div class="service-content-section">
			<h5 class="service-info-title"><?php esc_html_e('About Property','wpbooing')?></h5>
			<div class="service-details">
				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('The space','wpbooking') ?></div>
					<div class="service-detail-content">

					</div>
				</div>
				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Amenities','wpbooking') ?></div>
					<div class="service-detail-content">

					</div>
				</div>
				<?php do_action('wpbooking_after_service_detail_amenities',$service_type,$service) ?>

				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Rate','wpbooking') ?>	<span class="help-icon"><fa class="fa-question"></fa></span></div>
					<div class="service-detail-content">
						<?php $array=array(
							'price'=>esc_html__('Nightly Rate: %s','wpbooking'),
							'weekly_rate'=>esc_html__('Weekly Rate: %s','wpbooking'),
							'monthly_rate'=>esc_html__('Monthly Rate: %s','wpbooking'),
						);
						foreach($array as $key=>$val){
							if($value=get_post_meta(get_the_ID(),$key,true)){
								printf($val,WPBooking_Currency::format_money($value).'<br>');
							}
						}
						?>
					</div>
				</div>
				<?php if(get_post_meta(get_the_ID(),'enable_additional_guest_tax',true)){ ?>
				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Additional Guests / Taxes / Misc','wpbooking') ?></div>
					<div class="service-detail-content">
						<?php $array=array(
							'rate_based_on'=>esc_html__('Rates are based on occupancy of: <strong>%s</strong>','wpbooking'),
							'additional_guest_money'=>esc_html__('Each additional guest will pay : %s / night','wpbooking'),
							'tax'=>esc_html__('Tax: <strong>%s</strong>','wpbooking'),
						);
						foreach($array as $key=>$val){
							if($value=get_post_meta(get_the_ID(),$key,true)){
								printf($val,WPBooking_Currency::format_money($value).'<br>');
							}
						}
						?>
					</div>
				</div>
				<?php } ?>



				<?php
				$extra_services=$service->get_extra_services();
				if(is_array($extra_services) and !empty($extra_services)){ ?>
				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Extra services','wpbooking') ?></div>
					<div class="service-detail-content">
						<ul class="service-extra-price">
							<?php
							foreach($extra_services as $key=>$val){
								$price=WPBooking_Currency::format_money($val['money']);
								if($val['require']) $price.='<span class="required">'.esc_html__('required','wpbooking').'</span>';
								printf('<li>+ %s: %s</li>',wpbooking_get_translated_string($val['title']),$price);
							}
							?>
						</ul>
					</div>
				</div>
				<?php } ?>

				<div class="service-detail-item">
					<div class="service-detail-title"><?php esc_html_e('Rule','wpbooking') ?></div>
					<div class="service-detail-content">
						<?php
						if($deposit_amount=get_post_meta(get_the_ID(),'deposit_amount',true)){
							if(get_post_meta(get_the_ID(),'deposit_type',true)=='percent'){
								printf(esc_html__('Deposit: %s ','wpbooking'),$deposit_amount.'% <span class="required">'.esc_html__('required','wpbooking').'</span>');
							}else{
								printf(esc_html__('Deposit: %s ','wpbooking'),WPBooking_Currency::format_money($deposit_amount).' <span class="required">'.esc_html__('required','wpbooking').'</span>');
							}
						}

						$array=array(
							'check_in_time'=>esc_html__('Check In Time: %s','wpbooking'),
							'check_out_time'=>esc_html__('Check Out Time: %s','wpbooking'),
						);
						foreach($array as $key=>$val){
							if($value=get_post_meta(get_the_ID(),$key,true)){
								printf($val,'<strong>'.$value.'</strong> <i class="fa fa-clock" ></i>	<br>');
							}
						}
						$array=array(
							'minimum_stay'=>esc_html__('Minimum Stay: %s','wpbooking'),
						);
						foreach($array as $key=>$val){
							if($value=get_post_meta(get_the_ID(),$key,true)){
								printf($val,$value.'<br>');
							}
						}

						$array=array(
							'cancellation_allowed'=>esc_html__('Cancellation Allowed: %s','wpbooking'),
						);

						foreach($array as $key=>$val){
							if($value=get_post_meta(get_the_ID(),$key,true)){
								printf($val,$value?esc_html__('Yes','wpbooking'):esc_html__('No','wpbooking').'<br>');
							}
						}

						$host_regulations=get_post_meta(get_the_ID(),'host_regulations',true);
						if(!empty($host_regulations)){
							foreach($host_regulations as $key=>$value){
								echo (wpbooking_get_translated_string($value['title']).': '.wpbooking_get_translated_string($value['content']).'<br>');
							}
						}
						?>
					</div>
				</div>

			</div>
		</div>

        <div class="row">

            <div class="col-md-12">
                <?php
                $taxonomy = WPBooking_Admin_Taxonomy_Controller::inst()->get_taxonomies();
                if(!empty($taxonomy)) {
                    foreach( $taxonomy as $k => $v ) {
                        if(in_array($service_type,$v['service_type'])){
                            $terms = get_the_terms( get_the_ID() , $v['name'] );
                            if(!empty( $terms )) {
                                echo "<h4>".$v['label']."</h4>";
                                ?>
                                <ul class="booking-item-features">
                                    <?php
                                    foreach( $terms as $key2 => $value2 ) {
                                        ?>
                                        <li class="">
                                            <span class="booking-item-feature-title"><?php echo esc_html( $value2->name ) ?></span>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            <?php
                            }
                        }
                    }
                }?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#maps"><?php _e('Maps','wpbooking')?></a></li>
                    <li><a data-toggle="tab" href="#gallery"><?php _e('Gallery','wpbooking') ?></a></li>
                    <li><a data-toggle="tab" href="#place-order"><?php _e('Place Order','wpbooking')?></a></li>
                </ul>
                <div class="tab-content">
                    <div id="maps" class="tab-pane fade in active">
                        <div class="content-single">
                            <?php
                            $map_lat = get_post_meta( get_the_ID() , 'map_lat', true );
                            $map_lng = get_post_meta( get_the_ID() , 'map_long', true );
                            $map_zoom = get_post_meta( get_the_ID() , 'map_zoom', true );
                            if(!empty($map_lat) and !empty($map_lng)){ ?>
                                <div class="wpbooking_google_map" data-lat="<?php echo esc_attr($map_lat) ?>" data-lng="<?php echo esc_attr($map_lng) ?>" data-zoom="<?php echo esc_attr($map_zoom) ?>"></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="gallery" class="tab-pane fade">
                        <?php
                        $gallery = get_post_meta(get_the_ID(),'gallery',true);
                        $gallery = explode(",",$gallery);
                        if(!empty($gallery)){
                            ?>
                            <div class="fotorama" data-width="100%" data-allowfullscreen="true" data-nav="thumbs">
                                <?php
                                foreach($gallery as $k=>$v){
                                    echo wp_get_attachment_image($v,apply_filters('wpbooking_single_loop_image_size','full',$service_type,get_the_ID()));
                                }
                                ?>
                            </div>
                        <?php } ?>

                    </div>
					<div id="place-order" class="tab-pane fade tab-padding">
						<?php echo wpbooking_load_view('single/order-form') ?>
					</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            </div>
        </div>



    </div>
</div>
<div class="modal fade" id="wb-send-message" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="<?php echo home_url('/') ?>" method="post" class="wb-send-message-form"  onsubmit="return false" >
				<input type="hidden" name="wpbooking_action" value="send_message">
				<input type="hidden" name="post_id" value="<?php the_ID()?>">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php printf(esc_html__('Send Message To %s','wpbooking'),get_the_author()) ?></h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="wb-message-input"><?php esc_html_e('Your Message','wpbooking') ?></label>
					<textarea name="wb-message-input"  id="wb-message-input" cols="30" placeholder="<?php esc_html_e('Your Message','wpbooking') ?>" rows="10"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close','wpbooking') ?></button>
				<button type="submit" class="btn btn-primary" type="submit"><?php esc_html_e('Send Message','wpbooking') ?></button>
				<div class="message-box text-left"></div>
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

