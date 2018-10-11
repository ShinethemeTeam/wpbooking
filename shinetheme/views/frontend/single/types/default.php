<?php
    $service       = wpbooking_get_service();
    $service_type  = $service->get_type();
    $hotel_id      = get_the_ID();
    $hotel_origin  = wpbooking_origin_id( $hotel_id, 'wpbookign_service' );
    $external_link = get_post_meta( $hotel_origin, 'external_link', true );
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
            if ( $address ) {
                ?>
                <div class="service-address" itemprop="address">
                    <i class="fa fa-map-marker"></i> <?php echo esc_html( $address ) ?>
                </div>
            <?php } ?>
        <?php do_action( 'wpbooking_after_service_address_rate', $hotel_id, $service->get_type(), $service ) ?>
    </div>
    <div class="wb-price-html" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <?php $service->get_price_html( true ); ?>
    </div>
    <div class="row-service-gallery-contact">
        <div class="col-service-gallery">
            <div class="wb-tabs-gallery-map">
                <?php
                    $map_lat  = get_post_meta( $hotel_id, 'map_lat', true );
                    $map_lng  = get_post_meta( $hotel_id, 'map_long', true );
                    $map_zoom = get_post_meta( $hotel_id, 'map_zoom', true );
                ?>
                <ul class="wb-tabs">
                    <li class="active"><a href="#photos"><i class="fa fa-camera"></i>
                            &nbsp;<?php echo esc_html__( 'Photos', 'wp-booking-management-system' ); ?></a></li>
                    <?php if ( !empty( $map_lat ) and !empty( $map_lng ) ) { ?>
                        <li><a href="#map"><i class="fa fa-map-marker"></i>
                                &nbsp;<?php echo esc_html__( 'On the map', 'wp-booking-management-system' ); ?></a></li>
                    <?php } ?>
                </ul>
                <div class="wp-tabs-content">
                    <div class="wp-tab-item" id="photos">
                        <div class="service-gallery-single">
                            <div class="fotorama" data-allowfullscreen="true" data-nav="thumbs">
                                <?php
                                    $gallery = $service->get_gallery();
                                    if ( !empty( $gallery ) and is_array( $gallery ) ) {
                                        foreach ( $gallery as $k => $v ) {
                                            echo( $v[ 'gallery' ] );
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        if ( !empty( $map_lat ) and !empty( $map_lng ) ) { ?>
                            <div class="wp-tab-item" id="map">
                                <div class="service-map">

                                    <div class="service-map-element" data-lat="<?php echo esc_attr( $map_lat ) ?>"
                                         data-lng="<?php echo esc_attr( $map_lng ) ?>"
                                         data-zoom="<?php echo esc_attr( $map_zoom ) ?>"></div>

                                </div>
                            </div>
                        <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-service-reviews-meta">
            <div class="wb-service-reviews-meta">
                <?php
                    do_action( 'wpbooking_before_contact_meta' );
                ?>
                <?php
                    $contact_meta = [
                        'contact_number' => 'fa-phone',
                        'contact_email'  => 'fa-envelope',
                        'website'        => 'fa-home',
                    ];
                    $html         = '';
                    foreach ( $contact_meta as $key => $val ) {
                        if ( $value = get_post_meta( $hotel_id, $key, true ) ) {
                            switch ( $key ) {
                                case 'contact_number':
                                    $value = sprintf( '<a href="tel:%s" itemprop="telephone" >%s</a>', esc_html( $value ), do_shortcode( $value ) );
                                    break;

                                case 'contact_email':
                                    $value = sprintf( '<a href="mailto:%s" itemprop="email" >%s</a>', esc_html( $value ), do_shortcode( $value ) );
                                    break;
                                case 'website';
                                    $value = '<a target=_blank href="' . esc_url( $value ) . '" itemprop="url" >' . do_shortcode( $value ) . '</a>';
                                    break;
                            }
                            $html .= '<div class="wb-meta-contact">
                                    <i class="fa ' . esc_attr( $val ) . ' wb-icon-contact"></i>
                                    <span>' . do_shortcode( $value ) . '</span>
                                </div>';
                        }
                    }
                    if ( !empty( $html ) ) {
                        echo '<div class="wb-contact-box wp-box-item">' . do_shortcode( $html ) . '</div>';
                    }
                    if ( !empty( $external_link ) ) {
                        echo '<a class="wb-btn wb-btn-default" target="_blank" href="' . esc_url( $external_link ) . '">' . esc_html__( 'Book Now', 'wp-booking-management-system' ) . '</a>';
                    }
                    do_action( 'wpbooking_after_contact_meta' );
                ?>
            </div>
        </div>
    </div>
    <div class="service-content-section">
        <h5 class="service-info-title"><?php echo esc_html__( 'Description', 'wp-booking-management-system' ) ?></h5>
        <div class="service-content-wrap" itemprop="description">
            <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        the_content();
                    }
                }
            ?>
        </div>
    </div>
    <?php
        $amenities = get_post_meta( $hotel_id, 'wpbooking_select_amenity', true );
        if ( !empty( $amenities ) ) {
            ?>
            <div class="service-content-section">
                <h5 class="service-info-title"><?php echo esc_html__( 'Amenities', 'wp-booking-management-system' ) ?></h5>
                <div class="service-content-wrap">
                    <ul class="wb-list-amenities">
                        <?php
                            foreach ( $amenities as $val ) {
                                $amenity = get_term_by( 'id', $val, 'wpbooking_amenity' );
                                if ( !empty( $amenity->term_id ) ) {
                                    $icon = get_tax_meta( $amenity->term_id, 'wpbooking_icon' );
                                    if ( !empty( $amenity ) ) {
                                        echo '<li><i class="fa fa-check-square-o"></i> &nbsp; <i class="' . wpbooking_handle_icon( $icon ) . '"></i> ' . esc_html( $amenity->name ) . '</li>';
                                    }
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    <?php do_action( 'wpbooking_after_service_amenity' ) ?>
    <div class="service-content-section">
        <h5 class="service-info-title"><?php echo esc_html__( 'Accommodation Policies', 'wp-booking-management-system' ) ?></h5>

        <div class="service-details">
            <?php
                $check_in      = [
                    'checkin_from' => esc_html__( 'from %s ', 'wp-booking-management-system' ),
                    'checkin_to'   => esc_html__( 'to %s', 'wp-booking-management-system' )
                ];
                $check_out     = [
                    'checkout_from' => esc_html__( 'from %s ', 'wp-booking-management-system' ),
                    'checkout_to'   => esc_html__( 'to %s', 'wp-booking-management-system' )
                ];
                $time_html     = '';
                $checkin_html  = esc_html__( 'Check In: ', 'wp-booking-management-system' );
                $checkout_html = esc_html__( 'Check Out: ', 'wp-booking-management-system' );
                foreach ( $check_in as $key => $val ) {
                    $value = get_post_meta( $hotel_id, $key, true );
                    if ( $key == 'checkin_from' && empty( $value ) ) {
                        $checkin_html = '';
                        break;
                    } else {
                        if ( !empty( $value ) ) {
                            $checkin_html .= sprintf( $val, $value );
                        }
                        if ( $key == 'checkin_to' && empty( $value ) ) {
                            $checkin_html = str_replace( 'from ', '', $checkin_html );
                        }
                    }
                }
                $bool = false;
                foreach ( $check_out as $key => $val ) {
                    $value = get_post_meta( $hotel_id, $key, true );
                    if ( $key == 'checkout_to' && empty( $value ) ) {
                        $checkout_html = '';
                        break;
                    } else {
                        if ( !empty( $value ) ) {
                            $checkout_html .= sprintf( $val, $value );
                            if ( $bool ) $checkout_html = $value;
                        }
                        if ( $key == 'checkout_from' && empty( $value ) ) {
                            $bool = true;
                        }
                    }
                }
                $time_html = $checkin_html . '<br>' . $checkout_html;
                if ( !empty( $checkin_html ) || !empty( $checkout_html ) ) {
                    ?>
                    <div class="service-detail-item">
                        <div class="service-detail-title"><?php echo esc_html__( 'Time', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <?php echo( $time_html ) ?>
                        </div>
                    </div>
                    <?php
                }
                $array                = [
                    'deposit_payment_status' => '',
                    'deposit_payment_amount' => wp_kses( esc_html__( 'Deposit: %s &nbsp;&nbsp; required', 'wp-booking-management-system' ), [ 'span' => [ 'class' => [] ] ] ),
                    'allow_cancel'           => esc_html__( 'Allowed Cancellation: Yes', 'wp-booking-management-system' ),
                    'cancel_free_days_prior' => esc_html__( 'Time allowed to free: %s', 'wp-booking-management-system' ),
                    'cancel_guest_payment'   => esc_html__( 'Fee cancel for booking: %s', 'wp-booking-management-system' ),
                ];
                $cancel_guest_payment = [
                    'first_night' => esc_html__( '100&#37; of the first night', 'wp-booking-management-system' ),
                    'full_stay'   => esc_html__( '100&#37; of the full stay', 'wp-booking-management-system' ),
                ];

                $deposit_html  = [];
                $allow_deposit = '';
                foreach ( $array as $key => $val ) {
                    $meta = get_post_meta( $hotel_id, $key, true );
                    if ( $key == 'deposit_payment_status' ) {
                        $allow_deposit = $meta;
                        continue;
                    }
                    if ( !empty( $meta ) ) {
                        if ( $key == 'deposit_payment_amount' ) {
                            if ( empty( $allow_deposit ) ) {
                                $deposit_html[] = '';
                            } elseif ( $allow_deposit == 'amount' ) {
                                $deposit_html[] = sprintf( $val, WPBooking_Currency::format_money( $meta ) );
                            } else {
                                $deposit_html[] = sprintf( $val, $meta . '%' );
                            }
                            continue;
                        }
                        if ( $key == 'cancel_guest_payment' ) {
                            $deposit_html[] = sprintf( $val, $cancel_guest_payment[ $meta ] );
                            continue;
                        }
                        if ( $key == 'cancel_free_days_prior' ) {
                            if ( $meta == 'day_of_arrival' )
                                $deposit_html[] = sprintf( $val, esc_html__( 'Day of arrival (6 pm)', 'wp-booking-management-system' ) );
                            else
                                $deposit_html[] = sprintf( $val, $meta . esc_html__( ' day', 'wp-booking-management-system' ) );

                            continue;
                        }

                    }
                    if ( $key == 'allow_cancel' ) {
                        $deposit_html[] = $val;
                        continue;
                    }
                }

                if ( !empty( $deposit_html ) ) {
                    ?>
                    <div class="service-detail-item">
                        <div
                                class="service-detail-title"><?php echo esc_html__( 'Prepayment / Cancellation', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <?php
                                foreach ( $deposit_html as $value ) {
                                    if ( !empty( $value ) ) echo ( $value ) . '<br>';
                                }
                            ?>
                        </div>
                    </div>
                <?php } ?>


            <?php
                $tax_html         = [];
                $array            = [
                    'vat_excluded'     => '',
                    'vat_unit'         => '',
                    'vat_amount'       => esc_html__( 'V.A.T: %s &nbsp;&nbsp;', 'wp-booking-management-system' ),
                    'citytax_excluded' => '',
                    'citytax_unit'     => '',
                    'citytax_amount'   => esc_html__( 'City tax: %s', 'wp-booking-management-system' ),
                ];
                $citytax_unit     = [
                    'stay'             => esc_html__( ' /stay', 'wp-booking-management-system' ),
                    'person_per_stay'  => esc_html__( ' /person per stay', 'wp-booking-management-system' ),
                    'night'            => esc_html__( ' /night', 'wp-booking-management-system' ),
                    'percent'          => esc_html__( '%', 'wp-booking-management-system' ),
                    'person_per_night' => esc_html__( ' /person per night', 'wp-booking-management-system' ),
                ];
                $vat_excluded     = '';
                $citytax_excluded = '';
                $ct_unit          = '';
                foreach ( $array as $key => $val ) {
                    $value = get_post_meta( $hotel_id, $key, true );
                    if ( !empty( $value ) ) {
                        switch ( $key ) {
                            case 'vat_excluded':
                                $vat_excluded = $value;
                                break;
                            case 'vat_unit':
                                $ct_unit = $value;
                                break;
                            case 'vat_amount':
                                $amount = '';
                                if ( !empty( $ct_unit ) ) {
                                    if ( $ct_unit == 'percent' ) {
                                        $amount = $value . '%';
                                    } else {
                                        $amount = WPBooking_Currency::format_money( $value );
                                    }
                                }

                                if ( $vat_excluded == 'yes_included' ) {
                                    $tax_html[] = sprintf( $val, $amount . ' &nbsp;&nbsp;<span class="enforced_red">' . wp_kses( esc_html__( 'included', 'wp-booking-management-system' ), [ 'span' => [ 'class' => [] ] ] ) . '</span>' );
                                } elseif ( $vat_excluded != '' ) {
                                    $tax_html[] = sprintf( $val, $amount );
                                }
                                break;
                            case 'citytax_excluded':
                                $citytax_excluded = $value;
                                break;
                            case 'citytax_unit':
                                $ct_unit = $value;
                                break;
                            case 'citytax_amount':
                                if ( !empty( $ct_unit ) ) {
                                    if ( $ct_unit == 'percent' ) {
                                        $str_citytax = sprintf( $val, $value ) . $citytax_unit[ $ct_unit ];
                                    } else {
                                        $str_citytax = sprintf( $val, WPBooking_Currency::format_money( $value ) ) . $citytax_unit[ $ct_unit ];
                                    }
                                }
                                if ( $citytax_excluded != '' ) {
                                    if ( $citytax_excluded == 'yes_included' ) {
                                        $tax_html[] = $str_citytax . '&nbsp;&nbsp; <span class="enforced_red">' . esc_html__( 'included', 'wp-booking-management-system' ) . '</span>';
                                    } else {
                                        $tax_html[] = $str_citytax;
                                    }
                                }
                                break;
                        }
                    }
                }

                if ( !empty( $tax_html ) ) {
                    ?>
                    <div class="service-detail-item">
                        <div
                                class="service-detail-title"><?php echo esc_html__( 'Tax', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <?php foreach ( $tax_html as $value ) {
                                echo ( $value ) . '<br>';
                            } ?>
                        </div>
                    </div>
                <?php } ?>

            <?php
                if ( $terms_conditions = get_post_meta( $hotel_id, 'terms_conditions', true ) ) { ?>
                    <div class="service-detail-item">
                        <div class="service-detail-title"><?php echo esc_html__( 'Term & Condition', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <?php echo( $terms_conditions ); ?>
                        </div>
                    </div>
                <?php } ?>

            <?php
                $card       = get_post_meta( $hotel_id, 'creditcard_accepted', true );
                $card_image = [
                    'americanexpress'    => 'wb-americanexpress',
                    'visa'               => 'wb-visa',
                    'euromastercard'     => 'wb-euromastercard',
                    'dinersclub'         => 'wb-dinersclub',
                    'jcb'                => 'wb-jcb',
                    'maestro'            => 'wb-maestro',
                    'discover'           => 'wb-discover',
                    'unionpaydebitcard'  => 'wb-unionpaydebitcard',
                    'unionpaycreditcard' => 'wb-unionpaycreditcard',
                    'bankcard'           => 'wb-bankcard',
                ];
                if ( !empty( $card ) ) {
                    ?>
                    <div class="service-detail-item">
                        <div class="service-detail-title"><?php echo esc_html__( 'Accepted Cards', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <ul class="wb-list-card-acd">
                                <?php foreach ( $card as $key => $val ) {
                                    if ( !empty( $val ) ) {
                                        echo '<li class="' . esc_attr( $card_image[ $key ] ) . '">';
                                        echo '</li>';
                                    }
                                } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>

        </div>
    </div>

    <div class="service-content-section comment-section">
        <?php
            wp_reset_postdata();
            wp_reset_query();
            if ( comments_open( $hotel_id ) || get_comments_number() ) :
                comments_template();
            endif;
        ?>
    </div>
<?php echo wpbooking_load_view( 'single/related' ) ?>