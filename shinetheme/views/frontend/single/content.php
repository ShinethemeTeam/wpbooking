<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/4/2016
 * Time: 3:23 PM
 */
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
$service_type = get_post_meta(get_the_ID(),'service_type',true);
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
            <div class="col-md-12">
                <h3 itemprop="name"><?php the_title(); ?></h3>
                <?php $address  = get_post_meta(get_the_ID(),'address',true); ?>
                <?php if(!empty($address)){ ?>
                    <div> <i class="fa fa-map-marker"></i>
                        <?php echo esc_html($address) ?>
                    </div>
                <?php } ?>
				<a class="btn btn-primary" data-toggle="modal" data-target="#wb-send-message"><?php esc_html_e('Contact Host','wpbooking') ?></a>
				<?php echo wpbooking_load_view('single/price') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="content-single">
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
                                    echo wp_get_attachment_image($v,'full');
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

