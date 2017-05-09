<?php
/**
 * Created by WpBooking Team.
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
<div itemscope itemtype="http://schema.org/Place" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
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
            <div class="service-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <i class="fa fa-map-marker"></i> <?php echo esc_html($address) ?>
            </div>
        <?php } ?>
        <?php do_action('wpbooking_after_service_address_rate', get_the_ID(), $service->get_type(), $service) ?>

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
                        $value=sprintf('<a href="tel:%s" itemprop="telephone">%s</a>',esc_html($value),esc_html($value));
                        break;

                    case 'contact_email':
                        $value=sprintf('<a href="mailto:%s" itemprop="email">%s</a>',esc_html($value),esc_html($value));
                        break;
                    case 'website';
                        $value = '<a target=_blank href="'.esc_url($value).'" itemprop="url">'.esc_html($value).'</a>';
                        break;
                }
                $html .= '<li class="wb-meta-contact">
                                    <i class="fa '.esc_html($val).' wb-icon-contact"></i>
                                    <span>'.do_shortcode($value).'</span>
                                </li>';
            }
        }
        if(!empty($html)){
            echo '<ul class="wb-contact-list">'.do_shortcode($html).'</ul>';
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
            <div class="wb-tour-meta">
                <?php
                $tour_type = get_post_meta(get_the_ID(), 'tour_type', true);
                $tax_tour_type = get_term_by('id', (int)$tour_type, 'wb_tour_type');
                ?>
                <ul class="list-meta">
                    <?php if(!empty($tax_tour_type->name)){ ?>
                    <li class="tour_type"><i class="fa fa-flag"></i> <?php echo esc_attr($tax_tour_type->name); ?></li>
                    <?php }
                    if($duration = get_post_meta(get_the_ID(),'duration', true)){
                        echo '<li class="duration" itemprop="duration" ><i class="fa fa-clock-o"></i> '.esc_html__('Duration: ','wpbooking').$duration.'</li>';
                    }
                    if($max_people = get_post_meta(get_the_ID(), 'max_guests',true)){
                        echo '<li class="max_people"><i class="fa fa-users"></i> '.esc_html__('Max: ','wpbooking').$max_people.esc_html__(' people','wpbooking').' </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="col-service-reviews-meta">
            <div class="wb-service-reviews-meta">
                <form class="wb-tour-booking-form" method="post" action="" >
                    <input type="hidden" name="post_id" value="<?php the_ID() ?>">
                    <div class="wb-price-html" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <?php $service->get_price_html(true); ?>
                    </div>
                    <div class="wb-tour-form-wrap">
                        <div class="wpbooking-form-group">
                            <label class="wpbooking-form-group"><?php esc_html_e('Departure date','wpbooking') ?></label>
                            <div class="controls">
                                <div class="departure-date-control">
                                    <label >
                                        <i class="fa fa-calendar"></i>
                                        <select class="wb-departure-date" name="wb-departure-date">
                                            <?php if(!empty($next10Month)){

                                                foreach($next10Month as $key=>$item){
                                                    if(!empty($item['days'])){
                                                        foreach($item['days'] as $day){
                                                            $day=wp_parse_args($day,array(
                                                                'calendar_price'=>false,
                                                                'adult_price'=>false,
                                                                'child_price'=>false,
                                                                'infant_price'=>false,
                                                            ));

                                                            if($service->get_meta('pricing_type') == 'per_unit'){
                                                                $calendar_price=$day['calendar_price'];
                                                            }else{
                                                                if( $day['adult_price'] <= $day['child_price'] and $day['adult_price'] <= $day['infant_price']) {
                                                                    $calendar_price = $day['adult_price'];
                                                                }
                                                                if( $day['child_price'] <= $day['adult_price'] and $day['child_price'] <= $day['infant_price']) {
                                                                    $calendar_price = $day['child_price'];
                                                                }
                                                                if( $day['infant_price'] <= $day['adult_price'] and $day['infant_price'] <= $day['child_price']) {
                                                                    $calendar_price = $day['infant_price'];
                                                                }
                                                            }
                                                            //$calendar_price=$day['calendar_price']?$day['calendar_price']:$day['adult_price'];
                                                            //if(!$calendar_price)  $calendar_price=$day['child_price']?$day['child_price']:$day['infant_price'];
                                                            $selected=false;
                                                            if(!empty($_GET['start_date']) and $_GET['start_date']==$day['start']) $selected='selected';
                                                            if(!empty($_GET['checkin_d']) and !empty($_GET['checkin_m']) and !empty($_GET['checkin_y'])){
                                                                if(strtotime($_GET['checkin_d'].'-'.$_GET['checkin_m'].'-'.$_GET['checkin_y'])==$day['start'])$selected='selected';
                                                            }

                                                            printf('<option value="%s" class="%s" data-price="%s" %s>%s</option>',$day['start'],$key,esc_attr(WPBooking_Currency::format_money($calendar_price)),$selected,date('d',$day['start']));
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
                        <div class="wpbooking-form-control">
                            <label  class="wpbooking-form-control"><?php esc_html_e('Adults','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['adult']['minimum']) or !empty($age_options['adult']['maximum'])){
                                        printf(' (%s - %s)',$age_options['adult']['minimum'],$age_options['adult']['maximum']);
                                    }
                                }

                                ?>

                            </label>
                            <div class="controls">
                                <select class="wpbooking-form-control" name="adult_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="wpbooking-form-control">
                            <label  class="wpbooking-form-control"><?php esc_html_e('Children','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['child']['minimum']) or !empty($age_options['child']['maximum'])){
                                        printf(' (%s - %s)',$age_options['child']['minimum'],$age_options['child']['maximum']);
                                    }
                                }
                                ?></label>
                            <div class="controls">
                                <select class="wpbooking-form-control" name="children_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="wpbooking-form-control">
                            <label  class="wpbooking-form-control"><?php esc_html_e('Infant','wpbooking');
                                if(!$pricing_type or $pricing_type=='per_person'){
                                    if(!empty($age_options['infant']['minimum']) or !empty($age_options['infant']['maximum'])){
                                        printf(' (%s - %s)',$age_options['infant']['minimum'],$age_options['infant']['maximum']);
                                    }
                                }
                                ?></label>
                            <div class="controls">
                                <select class="wpbooking-form-control" name="infant_number">
                                    <?php for($i=0;$i<=20;$i++){
                                        printf('<option value="%s">%s</option>',$i,$i);
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        $extra_service=get_post_meta(get_the_ID(),'extra_services',true);
                        if(!empty($extra_service)){
                            echo '<span class="btn_extra">'.esc_html__("Extra services",'wpbooking').'</span>';
                            foreach($extra_service as $k=>$v) {
                                $name = sanitize_title($v['is_selected']);
                                ?>
                                <div class="wpbooking-form-control more-extra">
                                    <label class="wpbooking-form-control"><?php echo esc_html( $v[ 'is_selected' ] ) ?>
                                        <span class="price"><?php echo WPBooking_Currency::format_money( $v[ 'money' ] ); ?></span>
                                    </label>
                                    <div class="controls">
                                        <select class="wpbooking-form-control option_extra_quantity"
                                                name="wpbooking_extra_service[<?php echo esc_attr( $name ) ?>][quantity]"
                                                data-price-extra="<?php echo esc_attr( $v[ 'money' ] ) ?>">
                                            <?php
                                            $start = 0;
                                            if($v[ 'require' ] == 'yes')
                                                $start = 1;
                                            for( $i = $start ; $i <= $v[ 'quantity' ] ; $i++ ) {
                                                echo "<option value='{$i}'>{$i}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="booking-message"></div>
                        <button type="submit" class="wb-button wb-order-button"><?php esc_html_e('Book Now','wpbooking') ?>
                            <i class="fa fa-spinner fa-pulse "></i></button>
                    </div>
                </form>
                <?php
                do_action('wpbooking_after_booking_form');
                ?>
            </div>
        </div>
    </div>
    <div class="service-content-section">
        <h5 class="service-info-title"><?php esc_html_e('Description', 'wpbooking') ?></h5>

        <div class="service-content-wrap" itemprop="description">
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
    <div class="service-content-section">
        <h5 class="service-info-title"><?php esc_html_e('Payment Policies', 'wpbooking') ?></h5>
        <div class="service-details">
            <?php
            $array = array(
                'deposit_payment_status' => '',
                'deposit_payment_amount' => wp_kses(__('Deposit: %s &nbsp;&nbsp;<span class="enforced_red">required</span>','wpbooking'),array('span'=>array('class'=>array()))),
                'allow_cancel' => esc_html__('Allowed Cancellation: Yes','wpbooking'),
                'cancel_free_days_prior' => esc_html__('Time allowed to free: %s','wpbooking'),
                'cancel_guest_payment' => esc_html__('Fee cancel for booking: %s','wpbooking'),
            );
            $cancel_guest_payment = array(
                'first_night' => esc_html__('100% of the first night','wpbooking'),
                'full_stay' => esc_html__('100% of the full stay','wpbooking'),
            );
            $deposit_html = array();
            $allow_deposit = '';
            foreach ($array as $key => $val) {
                $meta = get_post_meta(get_the_ID(), $key, TRUE);
                if($key == 'deposit_payment_status'){
                    $allow_deposit = $meta;
                    continue;
                }
                if (!empty($meta)) {
                    if($key == 'deposit_payment_amount') {
                        if(empty($allow_deposit)) {
                            $deposit_html[] = '';
                        }elseif($allow_deposit == 'amount'){
                            $deposit_html[] = sprintf($val, WPBooking_Currency::format_money($meta));
                        }else{
                            $deposit_html[] = sprintf($val, $meta.'%');
                        }
                        continue;
                    }
                    if($key == 'cancel_guest_payment'){
                        $deposit_html[] = sprintf($val, $cancel_guest_payment[$meta]);
                        continue;
                    }
                    if($key == 'cancel_free_days_prior'){
                        if($meta == 'day_of_arrival')
                            $deposit_html[] = sprintf($val, esc_html__('Day of arrival (6 pm)','wpbooking'));
                        else
                            $deposit_html[] = sprintf($val, $meta.esc_html__(' day','wpbooking'));

                        continue;
                    }

                }
                if($key == 'allow_cancel'){
                    $deposit_html[] = $val;
                    continue;
                }
            }

            if (!empty($deposit_html)) {
                ?>
                <div class="service-detail-item">
                    <div class="service-detail-title"><?php esc_html_e('Prepayment / Cancellation', 'wpbooking') ?></div>
                    <div class="service-detail-content">
                        <?php
                        foreach($deposit_html as $value){
                            if(!empty($value)) echo ($value).'<br>';
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>


            <?php
            $tax_html=array();
            $array = array(
                'vat_excluded'          => '',
                'vat_unit'          => '',
                'vat_amount' => esc_html__('V.A.T: %s &nbsp;&nbsp;'),
                'citytax_excluded' => '',
                'citytax_unit' => '',
                'citytax_amount' => esc_html__('City tax: %s'),
            );
            $citytax_unit = array(
                'stay' => esc_html__(' /stay','wpbooking'),
                'person_per_stay' => esc_html__(' /person per stay','wpbooking'),
                'night' => esc_html__(' /night','wpbooking'),
                'percent' => esc_html__('%','wpbooking'),
                'person_per_night' => esc_html__(' /person per night','wpbooking'),
            );
            $vat_excluded = '';
            $citytax_excluded = '';
            $ct_unit= '';
            foreach ($array as $key => $val) {
                $value = get_post_meta(get_the_ID(), $key, TRUE);
                if (!empty($value)) {
                    switch($key){
                        case 'vat_excluded':
                            $vat_excluded = $value;
                            break;
                        case 'vat_unit':
                            $ct_unit = $value;
                            break;
                        case 'vat_amount':
                            $amount = '';
                            if(!empty($ct_unit)) {
                                if ($ct_unit == 'percent') {
                                    $amount = $value.'%';
                                } else {
                                    $amount = WPBooking_Currency::format_money($value);
                                }
                            }

                            if($vat_excluded == 'yes_included'){
                                $tax_html[] = sprintf($val, $amount.' &nbsp;&nbsp;'.wp_kses(__('<span class="enforced_red">included</span>','wpbooking'),array('span' => array('class' => array()))));
                            }elseif($vat_excluded != 'no'){
                                $tax_html[] = sprintf($val, $amount);
                            }
                            break;
                        case 'citytax_excluded':
                            $citytax_excluded = $value;
                            break;
                        case 'citytax_unit':
                            $ct_unit = $value;
                            break;
                        case 'citytax_amount':
                            if(!empty($ct_unit)) {
                                if ($ct_unit == 'percent') {
                                    $str_citytax = sprintf($val, $value) . $citytax_unit[$ct_unit];
                                } else {
                                    $str_citytax = sprintf($val, WPBooking_Currency::format_money($value)) . $citytax_unit[$ct_unit];
                                }
                            }
                            if($citytax_excluded != 'no') {
                                if ($citytax_excluded == 'yes_included') {
                                    $tax_html[] = $str_citytax . '&nbsp;&nbsp; <span class="enforced_red">' . esc_html__('included', 'wpbooking') . '</span>';
                                } else {
                                    $tax_html[] = $str_citytax;
                                }
                            }
                            break;
                    }
                }
            }

            if(!empty($tax_html)){
                ?>
                <div class="service-detail-item">
                    <div
                        class="service-detail-title"><?php esc_html_e('Tax', 'wpbooking') ?></div>
                    <div class="service-detail-content">
                        <?php foreach($tax_html as $value){
                            echo ($value).'<br>';
                        }?>
                    </div>
                </div>
            <?php }  ?>

            <?php
            if ($terms_conditions = get_post_meta(get_the_ID(),'terms_conditions',true)) { ?>
                <div class="service-detail-item">
                    <div class="service-detail-title"><?php esc_html_e('Term & Condition', 'wpbooking') ?></div>
                    <div class="service-detail-content">
                        <?php echo ($terms_conditions); ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
    <?php
    do_action('wpbooking_before_comment_template');
    ?>
    <div class="service-content-section comment-section">
        <?php
        if (comments_open(get_the_ID()) || get_comments_number()) :
            comments_template();
        endif;
        ?>
    </div>
<?php echo wpbooking_load_view('single/related') ?>