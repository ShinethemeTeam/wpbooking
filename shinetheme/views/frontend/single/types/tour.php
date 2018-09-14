<?php
    /**
     * Created by WpBooking Team.
     * User: NAZUMI
     * Date: 12/8/2016
     * Version: 1.0
     */

    $service       = wpbooking_get_service();
    $service_type  = $service->get_type();
    $tour_id       = get_the_ID();
    $tour_origin   = wpbooking_origin_id( $tour_id, 'wpbooking_service' );
    $external_link = get_post_meta( $tour_origin, 'external_link', true );

    $tour        = WPBooking_Tour_Service_Type::inst();
    $start_month = $tour->get_first_month_has_tour();
    if ( !$start_month ) {
        $start_month = date( 'm' );
    }

    $start_month  = sprintf( "%02d", $start_month );
    $start_date   = $start_month . '-01-' . date( 'Y' );
    $pricing_type = $service->get_meta( 'pricing_type' );
    $age_options  = $service->get_meta( 'age_options' );

    wp_enqueue_script( 'wpbooking-daterangepicker-js' );
    wp_enqueue_style( 'wpbooking-daterangepicker' );
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
                <div class="service-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <i class="fa fa-map-marker"></i> <?php echo esc_html( $address ) ?>
                </div>
            <?php } ?>
        <?php do_action( 'wpbooking_after_service_address_rate', get_the_ID(), $service->get_type(), $service ) ?>

        <?php
            $contact_meta = [
                'contact_number' => 'fa-phone',
                'contact_email'  => 'fa-envelope',
                'website'        => 'fa-home',
            ];
            $html         = '';
            foreach ( $contact_meta as $key => $val ) {
                if ( $value = get_post_meta( get_the_ID(), $key, true ) ) {
                    switch ( $key ) {
                        case 'contact_number':
                            $value = sprintf( '<a href="tel:%s" itemprop="telephone">%s</a>', esc_html( $value ), esc_html( $value ) );
                            break;

                        case 'contact_email':
                            $value = sprintf( '<a href="mailto:%s" itemprop="email">%s</a>', esc_html( $value ), esc_html( $value ) );
                            break;
                        case 'website';
                            $value = '<a target=_blank href="' . esc_url( $value ) . '" itemprop="url">' . esc_html( $value ) . '</a>';
                            break;
                    }
                    $html .= '<li class="wb-meta-contact">
                                    <i class="fa ' . esc_html( $val ) . ' wb-icon-contact"></i>
                                    <span>' . do_shortcode( $value ) . '</span>
                                </li>';
                }
            }
            if ( !empty( $html ) ) {
                echo '<ul class="wb-contact-list">' . do_shortcode( $html ) . '</ul>';
            }
        ?>
    </div>
    <div class="row-service-gallery-contact">
        <div class="col-service-gallery">
            <div class="wb-tabs-gallery-map">
                <?php
                    $map_lat  = get_post_meta( get_the_ID(), 'map_lat', true );
                    $map_lng  = get_post_meta( get_the_ID(), 'map_long', true );
                    $map_zoom = get_post_meta( get_the_ID(), 'map_zoom', true );
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
            <div class="wb-tour-meta">
                <?php
                    $tour_type     = get_post_meta( get_the_ID(), 'tour_type', true );
                    $tax_tour_type = get_term_by( 'id', (int)$tour_type, 'wb_tour_type' );
                ?>
                <ul class="list-meta">
                    <?php if ( !empty( $tax_tour_type->name ) ) { ?>
                        <li class="tour_type"><i class="fa fa-flag"></i> <?php echo esc_attr( $tax_tour_type->name ); ?>
                        </li>
                    <?php }
                        if ( $duration = get_post_meta( get_the_ID(), 'duration', true ) ) {
                            echo '<li class="duration" itemprop="duration" ><i class="fa fa-clock-o"></i> ' . esc_html__( 'Duration: ', 'wp-booking-management-system' ) . $duration . '</li>';
                        }
                        if ( $max_people = get_post_meta( get_the_ID(), 'max_guests', true ) ) {
                            echo '<li class="max_people"><i class="fa fa-users"></i> ' . esc_html__( 'Max: ', 'wp-booking-management-system' ) . $max_people . esc_html__( ' people', 'wp-booking-management-system' ) . ' </li>';
                        }
                    ?>
                </ul>

                <?php
                    $discount_by_people = get_post_meta( $tour_id, 'discount_by_no_people', true );
                    if ( !empty( $discount_by_people ) ) {
                        ?>
                        <table class="table mt20">
                            <tr>
                                <th><?php echo esc_html__( 'Range', 'wp-booking-management-system' ) ?></th>
                                <th><?php echo esc_html__( 'Discount (%)', 'wp-booking-management-system' ); ?></th>
                            </tr>
                            <?php
                                foreach ( $discount_by_people as $range ) {
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html( $range[ 'title' ] ); ?></td>
                                        <td><?php echo (float)$range[ 'price' ]. '%'; ?></td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </table>
                        <?php
                    }
                ?>
            </div>
        </div>
        <div class="col-service-reviews-meta">
            <div class="wb-service-reviews-meta">
                <form class="wb-tour-booking-form" method="post" action="">
                    <input type="hidden" name="post_id" value="<?php the_ID() ?>">
                    <div class="wb-price-html" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <?php $service->get_price_html( true ); ?>
                    </div>
                    <?php
                        if ( empty( $external_link ) ) {
                            ?>
                            <div class="wb-tour-form-wrap">
                                <div class="wpbooking-form-group">
                                    <div class="departure-date-group clearfix"
                                         data-post_id="<?php echo get_the_ID(); ?>"
                                         data-start-month="<?php echo esc_attr( $start_date ); ?>">
                                        <label class="title"
                                               for="departure-date-field"><?php echo esc_html__( 'Departure Date', 'wp-booking-management-system' ); ?></label>
                                        <div class="item-search datepicker-field">
                                            <div class="item-search-content">
                                                <i class="fa fa-calendar"></i>
                                                <input type="hidden" class="checkin_d" name="checkin_d"
                                                       value="<?php echo esc_attr( WPBooking_Input::get( 'checkin_d' ) ); ?>"/>
                                                <input type="hidden" class="checkin_m" name="checkin_m"
                                                       value="<?php echo esc_attr( WPBooking_Input::get( 'checkin_m' ) ); ?>"/>
                                                <input type="hidden" class="checkin_y" name="checkin_y"
                                                       value="<?php echo esc_attr( WPBooking_Input::get( 'checkin_y' ) ) ?>"/>
                                                <input
                                                        class="wpbooking-date-start wb-required"
                                                        readonly type="text">
                                                <input class="wpbooking-check-in-out" type="text" name="check_in_out">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $onoff_people = (array)get_post_meta( get_the_ID(), 'onoff_people', true );
                                    if ( !in_array( 'adult', $onoff_people ) ) {
                                        ?>
                                        <div class="wpbooking-form-control">
                                            <label class="wpbooking-form-control"><?php echo esc_html__( 'Adults', 'wp-booking-management-system' );
                                                    if ( !$pricing_type or $pricing_type == 'per_person' ) {
                                                        if ( !empty( $age_options[ 'adult' ][ 'minimum' ] ) or !empty( $age_options[ 'adult' ][ 'maximum' ] ) ) {
                                                            printf( ' (%s - %s)', $age_options[ 'adult' ][ 'minimum' ], $age_options[ 'adult' ][ 'maximum' ] );
                                                        }
                                                    }

                                                ?>

                                            </label>
                                            <div class="controls">
                                                <select class="wpbooking-form-control" name="adult_number">
                                                    <?php for ( $i = 0; $i <= 20; $i++ ) {
                                                        printf( '<option value="%s">%s</option>', $i, $i );
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php
                                    if ( !in_array( 'child', $onoff_people ) ) {
                                        ?>
                                        <div class="wpbooking-form-control">
                                            <label class="wpbooking-form-control"><?php echo esc_html__( 'Children', 'wp-booking-management-system' );
                                                    if ( !$pricing_type or $pricing_type == 'per_person' ) {
                                                        if ( !empty( $age_options[ 'child' ][ 'minimum' ] ) or !empty( $age_options[ 'child' ][ 'maximum' ] ) ) {
                                                            printf( ' (%s - %s)', $age_options[ 'child' ][ 'minimum' ], $age_options[ 'child' ][ 'maximum' ] );
                                                        }
                                                    }
                                                ?></label>
                                            <div class="controls">
                                                <select class="wpbooking-form-control" name="children_number">
                                                    <?php for ( $i = 0; $i <= 20; $i++ ) {
                                                        printf( '<option value="%s">%s</option>', $i, $i );
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php
                                    if ( !in_array( 'infant', $onoff_people ) ) {
                                        ?>
                                        <div class="wpbooking-form-control">
                                            <label class="wpbooking-form-control"><?php echo esc_html__( 'Infant', 'wp-booking-management-system' );
                                                    if ( !$pricing_type or $pricing_type == 'per_person' ) {
                                                        if ( !empty( $age_options[ 'infant' ][ 'minimum' ] ) or !empty( $age_options[ 'infant' ][ 'maximum' ] ) ) {
                                                            printf( ' (%s - %s)', $age_options[ 'infant' ][ 'minimum' ], $age_options[ 'infant' ][ 'maximum' ] );
                                                        }
                                                    }
                                                ?></label>
                                            <div class="controls">
                                                <select class="wpbooking-form-control" name="infant_number">
                                                    <?php for ( $i = 0; $i <= 20; $i++ ) {
                                                        printf( '<option value="%s">%s</option>', $i, $i );
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php
                                    $extra_service = get_post_meta( get_the_ID(), 'extra_services', true );
                                    if ( !empty( $extra_service ) ) {
                                        echo '<span class="btn_extra">' . esc_html__( "Extra services", 'wp-booking-management-system' ) . '</span>';
                                        foreach ( $extra_service as $k => $v ) {
                                            $name = sanitize_title( $v[ 'is_selected' ] );
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
                                                            if ( $v[ 'require' ] == 'yes' )
                                                                $start = 1;
                                                            for ( $i = $start; $i <= $v[ 'quantity' ]; $i++ ) {
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
                                <button type="submit"
                                        class="wb-button wb-order-button"><?php echo esc_html__( 'Book Now', 'wp-booking-management-system' ) ?>
                                    <i class="fa fa-spinner fa-pulse "></i></button>

                            </div>
                        <?php } else { ?>
                            <div class="wb-tour-form-wrap text-center">
                                <a class="wb-btn wb-btn-default" href="<?php echo esc_url( $external_link ) ?>"
                                   target="_blank"><?php echo esc_html__( 'Book Now', 'wp-booking-management-system' ); ?></a>

                            </div>
                        <?php } ?>
                </form>
                <?php
                    do_action( 'wpbooking_after_booking_form' );
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
    <?php do_action( 'wpbooking_after_service_description' ) ?>
    <div class="service-content-section">
        <h5 class="service-info-title"><?php echo esc_html__( 'Payment Policies', 'wp-booking-management-system' ) ?></h5>
        <div class="service-details">
            <?php
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
                $deposit_html         = [];
                $allow_deposit        = '';
                foreach ( $array as $key => $val ) {
                    $meta = get_post_meta( get_the_ID(), $key, true );
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
                        <div class="service-detail-title"><?php echo esc_html__( 'Prepayment / Cancellation', 'wp-booking-management-system' ) ?></div>
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
                    $value = get_post_meta( get_the_ID(), $key, true );
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
                                    $tax_html[] = sprintf( $val, $amount . ' &nbsp;&nbsp;' . wp_kses( '<span class="enforced_red">' . esc_html__( 'included', 'wp-booking-management-system' ) . '</span>', [ 'span' => [ 'class' => [] ] ] ) );
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
                if ( $terms_conditions = get_post_meta( get_the_ID(), 'terms_conditions', true ) ) { ?>
                    <div class="service-detail-item">
                        <div class="service-detail-title"><?php echo esc_html__( 'Term & Condition', 'wp-booking-management-system' ) ?></div>
                        <div class="service-detail-content">
                            <?php echo( $terms_conditions ); ?>
                        </div>
                    </div>
                <?php } ?>

        </div>
    </div>
    <?php
        do_action( 'wpbooking_before_comment_template' );
    ?>
    <div class="service-content-section comment-section">
        <?php
            wp_reset_postdata();
            wp_reset_query();
            if ( comments_open( get_the_ID() ) || get_comments_number() ) :
                comments_template();
            endif;
        ?>
    </div>
<?php echo wpbooking_load_view( 'single/related' ) ?>