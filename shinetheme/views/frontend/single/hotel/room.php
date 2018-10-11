<?php
    wp_enqueue_script( 'wpbooking-daterangepicker-js' );
    wp_enqueue_style( 'wpbooking-daterangepicker' );

    $post_origin   = wpbooking_origin_id( get_the_ID(), 'wpbooking_service' );
    $external_link = get_post_meta( $post_origin, 'external_link', true );
?>
<div class="service-content-section">
    <?php
        global $wp_query;
        $rooms = WPBooking_Accommodation_Service_Type::inst()->search_room();
    ?>
    <div class="search-room-availablity">
        <?php if ( empty( $external_link ) ) { ?>
            <form method="post" name="form-search-room" class="form-search-room">
                <?php wp_nonce_field( 'room_search', 'room_search' ) ?>
                <input name="action" value="ajax_search_room" type="hidden">
                <input name="hotel_id" value="<?php the_ID() ?>" type="hidden">
                <input name="wpbooking_paged" class="wpbooking_paged" value="1" type="hidden">
                <div class="search-room-form">
                    <h5 class="service-info-title"><?php echo esc_html__( 'Check availability', 'wp-booking-management-system' ) ?></h5>
                    <div class="form-search">
                        <?php
                            $check_in = WPBooking_Input::request( 'checkin_y' ) . "-" . WPBooking_Input::request( 'checkin_m' ) . "-" . WPBooking_Input::request( 'checkin_d' );
                            if ( $check_in == '--' ) $check_in = ''; else$check_in = date( wpbooking_date_format(), strtotime( $check_in ) );
                            if ( empty( $check_in ) ) {
                                $check_in = date( wpbooking_date_format() );
                            }

                            $check_out = WPBooking_Input::request( 'checkout_y' ) . "-" . WPBooking_Input::request( 'checkout_m' ) . "-" . WPBooking_Input::request( 'checkout_d' );
                            if ( $check_out == '--' ) $check_out = ''; else$check_out = date( wpbooking_date_format(), strtotime( $check_out ) );
                            if ( empty( $check_out ) ) {
                                $check_out = date( wpbooking_date_format(), strtotime( '+1 day', strtotime( date( 'Y-m-d' ) ) ) );
                            }
                            $check_in_out = current_time( wpbooking_date_format() ) . '-' . date( wpbooking_date_format(), strtotime( '+1 day', current_time( 'timestamp' ) ) );

                            $currentdate = current_time('timestamp');
                            $nextdate = strtotime('+1 day', $currentdate);
                        ?>
                        <div class="form-item w20 form-item-icon">
                            <label><?php echo esc_html__( 'Check In', 'wp-booking-management-system' ) ?><i class="fa fa-calendar"></i>
                                <input class="checkin_d" name="checkin_d"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkin_d', date('d', $currentdate) ) ) ?>"
                                       type="hidden">
                                <input class="checkin_m" name="checkin_m"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkin_m', date('m', $currentdate) ) ) ?>"
                                       type="hidden">
                                <input class="checkin_y" name="checkin_y"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkin_y', date('Y', $currentdate) ) ) ?>"
                                       type="hidden">
                                <input type="text" readonly class="form-control wpbooking-search-start"
                                       value="<?php echo esc_attr( $check_in ) ?>" name="check_in"
                                       placeholder="<?php echo esc_html__( 'Check In', 'wp-booking-management-system' ) ?>">
                            </label>
                            <input class="wpbooking-check-in-out" type="text" name="check_in_out"
                                   value="<?php echo esc_html( WPBooking_Input::request( 'check_in_out', $check_in_out ) ); ?>">
                        </div>
                        <div class="form-item w20 form-item-icon">
                            <label><?php echo esc_html__( 'Check Out', 'wp-booking-management-system' ) ?>
                                <input class="checkout_d" name="checkout_d"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkout_d', date('d', $nextdate) ) ) ?>"
                                       type="hidden">
                                <input class="checkout_m" name="checkout_m"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkout_m', date('m', $nextdate) ) ) ?>"
                                       type="hidden">
                                <input class="checkout_y" name="checkout_y"
                                       value="<?php echo esc_html( WPBooking_Input::request( 'checkout_y', date('Y', $nextdate) ) ) ?>"
                                       type="hidden">
                                <input type="text" readonly class="form-control wpbooking-search-end"
                                       value="<?php echo do_shortcode( $check_out ) ?>" name="check_out"
                                       placeholder="<?php echo esc_html__( 'Check Out', 'wp-booking-management-system' ) ?>">
                                <i class="fa fa-calendar"></i>
                            </label>
                        </div>
                        <div class="form-item w20">
                            <label><?php echo esc_html__( 'Rooms', 'wp-booking-management-system' ) ?></label>
                            <select name="room_number" class="form-control">
                                <?php
                                    for ( $i = 1; $i <= 20; $i++ ) {
                                        echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-item w20">
                            <label><?php echo esc_html__( 'Adults', 'wp-booking-management-system' ) ?></label>
                            <select name="adults" class="form-control">
                                <?php
                                    for ( $i = 1; $i <= 20; $i++ ) {
                                        echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-item w20">
                            <label><?php echo esc_html__( 'Children', 'wp-booking-management-system' ) ?></label>
                            <select name="children" class="form-control">
                                <?php
                                    for ( $i = 0; $i <= 20; $i++ ) {
                                        echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-item w100">
                            <button type="button"
                                    class="wb-button btn-do-search-room"><?php echo esc_html__( "CHECK AVAILABILITY ", "wp-booking-management-system" ) ?></button>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
        <div class="search_room_alert"></div>
        <?php
            $is_have_post = '';
            if ( !$rooms->have_posts() ) {
                $is_have_post = 'have_none';
            }
        ?>
        <div class="content-search-room <?php echo esc_html( $is_have_post ) ?>">
            <?php
                $checkin_d = WPBooking_Input::request( 'checkin_d' );
                $checkin_m = WPBooking_Input::request( 'checkin_m' );
                $checkin_y = WPBooking_Input::request( 'checkin_y' );

                $checkout_d   = WPBooking_Input::request( 'checkout_d' );
                $checkout_m   = WPBooking_Input::request( 'checkout_m' );
                $checkout_y   = WPBooking_Input::request( 'checkout_y' );
                $check_in_out = WPBooking_Input::request( 'check_in_out' );

                $class = '';
                if ( !$checkin_d and !$checkin_m and !$checkin_y and !$checkout_d and !$checkout_m and !$checkout_y ) {
                    $class = 'no_date';
                }
            ?>
            <form method="post" class="wpbooking_order_form <?php echo esc_html( $class ) ?>">
                <input name="action" value="wpbooking_add_to_cart" type="hidden">
                <input name="post_id" value="<?php the_ID() ?>" type="hidden">
                <input name="wpbooking_checkin_d" class="form_book_checkin_d"
                       value="<?php echo esc_attr( $checkin_d ) ?>" type="hidden">
                <input name="wpbooking_checkin_m" class="form_book_checkin_m"
                       value="<?php echo esc_attr( $checkin_m ) ?>" type="hidden">
                <input name="wpbooking_checkin_y" class="form_book_checkin_y"
                       value="<?php echo esc_attr( $checkin_y ) ?>" type="hidden">

                <input name="wpbooking_checkout_d" class="form_book_checkout_d"
                       value="<?php echo esc_attr( $checkout_d ) ?>" type="hidden">
                <input name="wpbooking_checkout_m" class="form_book_checkout_m"
                       value="<?php echo esc_attr( $checkout_m ) ?>" type="hidden">
                <input name="wpbooking_checkout_y" class="form_book_checkout_y"
                       value="<?php echo esc_attr( $checkout_y ) ?>" type="hidden">
                <input name="wpbooking_check_in_out" class="form_book_check_in_out"
                       value="<?php echo esc_attr( $check_in_out ) ?>" type="hidden">
                <input name="wpbooking_room_number" class="form_book_room_number" type="hidden">
                <input name="wpbooking_adults" class="form_book_adults" type="hidden">
                <input name="wpbooking_children" class="form_book_children" type="hidden">

                <div class="content-loop-room">
                    <?php
                        $hotel_id = get_the_ID();
                        if ( $rooms->have_posts() ) {
                            while ( $rooms->have_posts() ) {
                                $rooms->the_post();
                                echo wpbooking_load_view( 'single/loop-room', [ 'hotel_id' => $hotel_id ] );
                            }
                        }
                    ?>
                </div>
                <div class="content-info">
                    <div class="content-price">
                        <div class="number"><span
                                    class="info_number">0</span> <?php echo esc_html__( 'room(s) selected', 'wp-booking-management-system' ) ?>
                        </div>
                        <div class="price"><span class="info_price">0</span></div>
                        <button type="button"
                                class="wb-button submit-button"><?php echo esc_html__( "BOOK NOW", 'wp-booking-management-system' ) ?></button>
                    </div>
                </div>

            </form>
        </div>
        <div class="pagination-room">
            <?php echo wpbooking_pagination_room( $rooms ); ?>
        </div>
    </div>
    <?php wp_reset_postdata(); ?>
</div>
