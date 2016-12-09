<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 12/8/2016
 * Version: 1.0
 */

$service = wpbooking_get_service();
$service_type=$service->get_type();
$hotel_id=get_the_ID();
$tour=WPBooking_Tour_Service_Type::inst();
$next10Month=$tour->getNext10MonthAvailable();
$pricing_type=$service->get_meta('pricing_type');
$age_options=$service->get_meta('age_options');
?>
<div itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
    <meta itemprop="url" content="<?php the_permalink(); ?>"/>
    <div class="container-fluid wpbooking-single-content entry-header">
    <div class="wb-service-title-address">
        <h1 class="wb-service-title" itemprop="name"><?php the_title(); ?></h1>
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
        <?php do_action('wpbooking_after_service_address_rate', get_the_ID(), $service->get_type(), $service) ?>

        <?php
        do_action('wpbooking_before_contact_meta');
        ?>
        <?php
        $contact_meta = array(
            'contact_number' => 'fa-phone',
            'contact_email' => 'fa-envelope',
            'website' => 'fa-home',
        );
        $html = '';
        foreach($contact_meta as $key => $val) {
            if ($value = get_post_meta(get_the_ID(), $key, true)) {
                switch($key){
                    case 'contact_number':
                        $value=sprintf('<a href="tel:%s">%s</a>',esc_html($value),esc_html($value));
                        break;

                    case 'contact_email':
                        $value=sprintf('<a href="mailto:%s">%s</a>',esc_html($value),esc_html($value));
                        break;
                    case 'website';
                        $value = '<a target=_blank href="'.$value.'">'.$value.'</a>';
                        break;
                }
                $html .= '<li class="wb-meta-contact">
                                    <i class="fa '.$val.' wb-icon-contact"></i>
                                    <span>'.$value.'</span>
                                </li>';
            }
        }
        if(!empty($html)){
            echo '<ul class="wb-contact-list">'.$html.'</ul>';
        }
        ?>
    </div>
    <div class="row-service-gallery-contact">
        <div class="col-service-gallery">
            <div class="wb-tabs-gallery-map">
                <?php
                $map_lat = get_post_meta(get_the_ID(), 'map_lat', TRUE);
                $map_lng = get_post_meta(get_the_ID(), 'map_long', TRUE);
                $map_zoom = get_post_meta(get_the_ID(), 'map_zoom', TRUE);
                ?>
                <ul class="wb-tabs">
                    <li class="active"><a href="#photos"><i class="fa fa-camera"></i> &nbsp;<?php esc_html_e('Photos','wpbooking'); ?></a></li>
                    <?php if (!empty($map_lat) and !empty($map_lng)) { ?>
                        <li ><a href="#map"><i class="fa fa-map-marker"></i> &nbsp;<?php esc_html_e('On the map','wpbooking'); ?></a></li>
                    <?php } ?>
                </ul>
                <div class="wp-tabs-content">
                    <div class="wp-tab-item" id="photos">
                        <div class="service-gallery-single">
                            <div class="fotorama" data-allowfullscreen="true" data-nav="thumbs">
                                <?php
                                $gallery = $service->get_gallery();
                                if(!empty($gallery) and is_array($gallery)){
                                    foreach($gallery as $k => $v){
                                        echo ($v['gallery']);
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (!empty($map_lat) and !empty($map_lng)) { ?>
                        <div class="wp-tab-item" id="map">
                            <div class="service-map">

                                <div class="service-map-element" data-lat="<?php echo esc_attr($map_lat) ?>"
                                     data-lng="<?php echo esc_attr($map_lng) ?>"
                                     data-zoom="<?php echo esc_attr($map_zoom) ?>"></div>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-service-reviews-meta">
            <div class="wb-service-reviews-meta">
                <form class="wb-tour-booking-form" method="post" action="" onsubmit="return false">
                    <div class="wb-price-html">
                        <?php $service->get_price_html(true); ?>
                    </div>
                    <div class="wb-tour-form-wrap">
                        <div class="form-group">
                            <label class="form-group"><?php esc_html_e('Departure date','wpbooking') ?></label>
                            <div class="controls">
                                <div class="departure-date-control">
                                    <label >
                                        <i class="fa fa-calendar"></i>
                                        <select class="wb-departure-date" name="wb-departure-date">
                                            <?php if(!empty($next10Month)){
                                                foreach($next10Month as $key=>$item){
                                                    if(!empty($item['days'])){
                                                        foreach($item['days'] as $day){
                                                            printf('<option value="%s" class="%s" data-price="%s">%s</option>',$day['start'],$key,esc_attr(WPBooking_Currency::format_money($day['calendar_price'])),date('d',$day['start']));
                                                        }
                                                    }
                                                }
                                            } ?>
                                        </select>
                                        <select class="wb-departure-month">
                                            <?php if(!empty($next10Month)){
                                                foreach($next10Month as $key=>$item){
                                                    printf('<option value="%s">%s</option>',$key,$item['label']);
                                                }
                                            } ?>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <label  class="form-control"><?php esc_html_e('Adults','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['adult']['minimum']) or !empty($age_options['adult']['maximum'])){
                                        printf(' (%s - %s)',$age_options['adult']['minimum'],$age_options['adult']['maximum']);
                                    }
                                }

                                ?>

                            </label>
                            <div class="controls">
                                <select class="form-control" name="adult_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-control">
                            <label  class="form-control"><?php esc_html_e('Children','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['child']['minimum']) or !empty($age_options['child']['maximum'])){
                                        printf(' (%s - %s)',$age_options['child']['minimum'],$age_options['child']['maximum']);
                                    }
                                }
                                ?></label>
                            <div class="controls">
                                <select class="form-control" name="children_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-control">
                            <label  class="form-control"><?php esc_html_e('Infant','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['infant']['minimum']) or !empty($age_options['infant']['maximum'])){
                                        printf(' (%s - %s)',$age_options['infant']['minimum'],$age_options['infant']['maximum']);
                                    }
                                }
                                ?></label>
                            <div class="controls">
                                <select class="form-control" name="infant_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="wb-button"><?php esc_html_e('Book Now','wpbooking') ?></button>
                    </div>
                </form>
                <div class="wb-share">
                    <div class="wb-button-share">
                        <i class="fa fa-share-alt"></i><a href="#"><?php esc_html_e('Share','wpbooking'); ?></a>
                    </div>
                    <ul class="wb-list-social">
                        <li><a class="wb-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink() ?>&amp;title=<?php the_title()?>" target="_blank" original-title="Facebook"><i class="fa fa-facebook"></i></a></li>
                        <li><a class="wb-twitter" href="http://twitter.com/share?url=<?php the_permalink() ?>&amp;title=<?php the_title()?>" target="_blank" original-title="Twitter"><i class="fa fa-twitter fa-lg"></i></a></li>
                        <li><a class="wb-google" href="https://plus.google.com/share?url=<?php the_permalink() ?>&amp;title=<?php the_title()?>" target="_blank" original-title="Google+"><i class="fa fa-google-plus fa-lg"></i></a></li>
                        <li><a class="wb-pinterest" href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());" target="_blank" original-title="Pinterest"><i class="fa fa-pinterest fa-lg"></i></a></li>
                        <li><a class="wb-linkedin" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title()?>" target="_blank" original-title="LinkedIn"><i class="fa fa-linkedin fa-lg"></i></a></li>
                    </ul>
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
    <?php do_action('wpbooking_after_service_description') ?>

    <div class="service-content-section comment-section">
        <?php
        if (comments_open(get_the_ID()) || get_comments_number()) :
            comments_template();
        endif;
        ?>
    </div>
<?php echo wpbooking_load_view('single/related') ?>