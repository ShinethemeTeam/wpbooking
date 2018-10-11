<?php
    $list_extra    = [];
    $list_extra    = get_post_meta( get_the_ID(), 'extra_services', true );
    $hotel_id      = wp_get_post_parent_id( get_the_ID() );
    $hotel_origin  = wpbooking_origin_id( $hotel_id, 'wpbooking_service' );
    $external_link = get_post_meta( $hotel_origin, 'external_link', true );

    $service_room = new WB_Service( get_the_ID() );
    $room_origin  = wpbooking_origin_id( get_the_ID(), 'wpbooking_hotel_room' );
    $check_in     = WPBooking_Input::request( 'checkin_y' ) . "-" . WPBooking_Input::request( 'checkin_m' ) . "-" . WPBooking_Input::request( 'checkin_d' );
    $check_out    = WPBooking_Input::request( 'checkout_y' ) . "-" . WPBooking_Input::request( 'checkout_m' ) . "-" . WPBooking_Input::request( 'checkout_d' );
    if ( $check_in == '--' ) $check_in = '';
    if ( $check_out == '--' ) $check_out = '';
    $person = (int)WPBooking_Input::request( 'adults' ) + (int)WPBooking_Input::request( 'children' );
    $diff   = strtotime( $check_out ) - strtotime( $check_in );
    $diff   = $diff / ( 60 * 60 * 24 );
    if ( $diff < 0 ) $diff = 0;
?>
<div class="loop-room post-<?php the_ID() ?>">
    <div class="room-image">
        <?php
            $featured = $service_room->get_featured_image_room( 'thumb300' );
            echo do_shortcode( $featured );
        ?>

    </div>
    <div class="room-content">
        <div class="room-title">
            <?php the_title() ?>
        </div>
        <div class="room-info">
            <div class="control left">
                <?php
                    $max_guests = get_post_meta( get_the_ID(), 'max_guests', true );
                    if ( !empty( $max_guests ) ) {
                        ?>
                        <div class="img">
                            <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0ODcuOTAxIDQ4Ny45MDEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4Ny45MDEgNDg3LjkwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ3NC4yLDMwMy44MDFjLTM4LjktMzItODAuOS01My45LTkyLjYtNTkuN3YtNTguMmM4LjMtNi43LDEzLjItMTYuOCwxMy4yLTI3LjZ2LTY1LjVjMC0zNS44LTI5LjEtNjUtNjUtNjVoLTE0LjEgICAgYy0zNS44LDAtNjUsMjkuMS02NSw2NXY2NS41YzAsMTAuOCw0LjksMjAuOSwxMy4yLDI3LjZ2NTguMmMtMTEuNyw1LjgtNTMuNywyNy43LTkyLjYsNTkuN2MtOC43LDcuMi0xMy43LDE3LjgtMTMuNywyOS4ydjQ0LjkgICAgYzAsMy4zLDIuNyw2LDYsNmMzLjMsMCw2LTIuNyw2LTZ2LTQ0LjljMC03LjgsMy40LTE1LDkuMy0xOS45YzQwLjItMzMsODMuNy01NSw5Mi01OWMzLjEtMS41LDUtNC42LDUtOHYtNjMuMWMwLTItMS0zLjktMi43LTUgICAgYy02LjYtNC40LTEwLjUtMTEuNy0xMC41LTE5LjZ2LTY1LjVjMC0yOS4yLDIzLjgtNTMsNTMtNTNoMTQuMWMyOS4yLDAsNTMsMjMuOCw1Myw1M3Y2NS41YzAsNy45LTMuOSwxNS4yLTEwLjUsMTkuNiAgICBjLTEuNywxLjEtMi43LDMtMi43LDV2NjMuMWMwLDMuNCwxLjksNi41LDUsOGM4LjMsNC4xLDUxLjksMjYsOTIsNTljNS45LDQuOSw5LjMsMTIuMSw5LjMsMTkuOXY0NC45YzAsMy4zLDIuNyw2LDYsNnM2LTIuNyw2LTYgICAgdi00NC45QzQ4OCwzMjEuNjAxLDQ4MywzMTEuMDAxLDQ3NC4yLDMwMy44MDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTQxLjQsOTIuMDAxaC0xMS41Yy0yOS44LDAtNTQsMjQuMi01NCw1NHY1My4zYzAsOC45LDQsMTcuMywxMC43LDIzdjQ2LjJjLTEwLjMsNS4yLTQzLjksMjIuOS03NSw0OC40ICAgIGMtNy40LDYuMS0xMS42LDE1LTExLjYsMjQuNnYzNi41YzAuMiwzLjIsMi45LDUuOSw2LjIsNS45YzMuMywwLDYtMi43LDYtNnYtMzYuNWMwLTYsMi42LTExLjYsNy4yLTE1LjMgICAgYzMyLjYtMjYuOCw2OC00NC42LDc0LjctNDcuOWMyLjktMS40LDQuNy00LjMsNC43LTcuNXYtNTEuNGMwLTItMS0zLjktMi43LTVjLTUtMy40LTguMS05LTguMS0xNXYtNTMuM2MwLTIzLjIsMTguOS00Miw0Mi00MiAgICBoMTEuNWMyMy4yLDAsNDIsMTguOSw0Miw0MnY1My4zYzAsNi0zLDExLjctOC4xLDE1Yy0xLjcsMS4xLTIuNywzLTIuNyw1djQyLjJjMCwzLjMsMi43LDYsNiw2YzMuMywwLDYtMi43LDYtNnYtMzkuMiAgICBjNi44LTUuNywxMC43LTE0LjEsMTAuNy0yM3YtNTMuM0MxOTUuNCwxMTYuMjAxLDE3MS4yLDkyLjAwMSwxNDEuNCw5Mi4wMDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzUwLjUsMjY0LjMwMWMwLTMuNC0yLjctNi4xLTYtNi4xcy02LDIuNy02LDZjMCw4LjYtNywxNS43LTE1LjcsMTUuN2MtMy4zLDAtNi40LTEuMS05LTIuOGMtMC40LTAuNS0wLjktMC45LTEuNS0xLjIgICAgYy0zLjItMi45LTUuMi03LTUuMi0xMS42YzAtMy4zLTIuNy02LTYtNnMtNiwyLjctNiw2YzAsNy42LDMuMSwxNC41LDguMSwxOS41bC02LjgsMTUxYy0wLjEsMS44LDAuNiwzLjUsMiw0LjdsMjEuMSwxOS4xICAgIGMxLjEsMSwyLjYsMS41LDQsMS41YzEuNCwwLDIuOS0wLjUsNC0xLjVsMjAuOC0xOC44YzEuMy0xLjIsMi4xLTIuOSwyLTQuN2wtNi42LTE1Mi43QzM0Ny45LDI3Ny41MDEsMzUwLjUsMjcxLjIwMSwzNTAuNSwyNjQuMzAxICAgIHogTTMyMy41LDQ0Ni4wMDFsLTE1LTEzLjVsNi40LTE0MS43YzIuNSwwLjcsNS4xLDEuMiw3LjksMS4yYzMuMiwwLDYuMy0wLjYsOS4yLTEuNmw2LjEsMTQyLjVMMzIzLjUsNDQ2LjAwMXoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K"/>
                        </div>
                        <?php echo esc_html__( "Max", "wp-booking-management-system" ) ?><?php echo esc_attr( $max_guests ); ?>
                    <?php } ?>
            </div>
            <div class="control">
                <?php
                    $room_size = get_post_meta( get_the_ID(), 'room_size', true );
                    if ( !empty( $room_size ) ) {
                        ?>
                        <div class="img">
                            <img
                                    src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTcuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ0Ny4wMjEgNDQ3LjAyMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDQ3LjAyMSA0NDcuMDIxOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8cGF0aCBkPSJNNDQ2LjkwOCw3LjU5OWMtMC4wMDItNC4xMzktMy4zNTctNy40OTQtNy40OTYtNy40OTZMMjQ3LjUxLDBjLTEyLjk1OCwwLTIzLjUsMTAuNTQyLTIzLjUsMjMuNXY0OCAgIGMwLDEyLjk1OCwxMC41NDIsMjMuNSwyMy41LDIzLjVoMzcuODk1TDk1LjAxNiwyODUuNDA4bDAuMDE0LTQ1Ljk2NEM5NC45OTksMjI2LjUxNyw4NC40NTcsMjE2LDcxLjUzLDIxNkgyMy41MSAgIGMtNi4yODMsMC0xMi4xODgsMi40NDgtMTYuNjI4LDYuODk0Yy00LjQ0LDQuNDQ2LTYuODgsMTAuMzU0LTYuODcyLDE2LjYzMWwwLjEwMywxOTkuODk3YzAuMDAyLDQuMTM5LDMuMzU3LDcuNDk0LDcuNDk2LDcuNDk2ICAgbDE5MS45MDEsMC4xMDNjMTIuOTU4LDAsMjMuNS0xMC41NDIsMjMuNS0yMy41di00OGMwLTEyLjk1OC0xMC41NDItMjMuNS0yMy41LTIzLjVoLTM3Ljg5NWwxOTAuMzg5LTE5MC40MDhsLTAuMDE0LDQ1Ljk2MyAgIGMwLjAzMSwxMi45MjcsMTAuNTczLDIzLjQ0NCwyMy41LDIzLjQ0NGg0OC4wMmM2LjI4MywwLDEyLjE4OC0yLjQ0OCwxNi42MjgtNi44OTRjNC40NC00LjQ0Niw2Ljg4LTEwLjM1NCw2Ljg3Mi0xNi42MzEgICBMNDQ2LjkwOCw3LjU5OXogTTQyOS41MjUsMjEzLjUyN2MtMS42MDYsMS42MDgtMy43NDIsMi40OTQtNi4wMTUsMi40OTRoLTQ4LjAyYy00LjY3NiwwLTguNDg5LTMuODA0LTguNS04LjQ1OWwwLjAyLTY0LjA1OSAgIGMwLjAwMS0zLjAzNC0xLjgyNi01Ljc3LTQuNjI5LTYuOTMxYy0yLjgwMi0xLjE2Mi02LjAyOS0wLjUyLTguMTc1LDEuNjI1bC0yMTYsMjE2LjAyMWMtMi4xNDUsMi4xNDUtMi43ODYsNS4zNzEtMS42MjUsOC4xNzMgICBjMS4xNjEsMi44MDMsMy44OTYsNC42Myw2LjkyOSw0LjYzaDU2YzQuNjg3LDAsOC41LDMuODEzLDguNSw4LjV2NDhjMCw0LjY4Ny0zLjgxMyw4LjUtOC40OTYsOC41bC0xODQuNDA1LTAuMDk5TDE1LjAxLDIzOS41MTEgICBjLTAuMDAzLTIuMjcyLDAuODgtNC40MSwyLjQ4NS02LjAxOGMxLjYwNi0xLjYwOCwzLjc0Mi0yLjQ5NCw2LjAxNS0yLjQ5NGg0OC4wMmM0LjY3NiwwLDguNDg5LDMuODA0LDguNSw4LjQ1OWwtMC4wMiw2NC4wNTkgICBjLTAuMDAxLDMuMDM0LDEuODI2LDUuNzcsNC42MjksNi45MzFjMi44MDIsMS4xNjEsNi4wMjksMC41Miw4LjE3NS0xLjYyNWwyMTYtMjE2LjAyMWMyLjE0NS0yLjE0NSwyLjc4Ni01LjM3MSwxLjYyNS04LjE3MyAgIGMtMS4xNjEtMi44MDMtMy44OTYtNC42My02LjkyOS00LjYzaC01NmMtNC42ODcsMC04LjUtMy44MTMtOC41LTguNXYtNDhjMC00LjY4NywzLjgxMy04LjUsOC40OTYtOC41bDE4NC40MDUsMC4wOTlsMC4wOTksMTkyLjQxMSAgIEM0MzIuMDEzLDIwOS43ODIsNDMxLjEzMSwyMTEuOTE5LDQyOS41MjUsMjEzLjUyN3oiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik01NS41MSwyNDcuNDUzYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djU2LjU2OGMwLDQuMTQyLDMuMzU4LDcuNSw3LjUsNy41czcuNS0zLjM1OCw3LjUtNy41di01Ni41NjggICBDNjMuMDEsMjUwLjgxMSw1OS42NTMsMjQ3LjQ1Myw1NS41MSwyNDcuNDUzeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSw0OC4wMjFjLTQuMTQyLDAtNy41LDMuMzU4LTcuNSw3LjV2ODBjMCw0LjE0MiwzLjM1OCw3LjUsNy41LDcuNXM3LjUtMy4zNTgsNy41LTcuNXYtODAgICBDNDE1LjAxLDUxLjM3OSw0MTEuNjUzLDQ4LjAyMSw0MDcuNTEsNDguMDIxeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSwxNjAuMDIxYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djE2YzAsNC4xNDIsMy4zNTgsNy41LDcuNSw3LjVzNy41LTMuMzU4LDcuNS03LjV2LTE2ICAgQzQxNS4wMSwxNjMuMzc5LDQxMS42NTMsMTYwLjAyMSw0MDcuNTEsMTYwLjAyMXoiIGZpbGw9IiMwMDAwMDAiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K"/>
                        </div>
                        <?php
                        echo esc_attr( $room_size );
                        $room_measunit = get_post_meta( $hotel_id, 'room_measunit', true );
                        if ( $room_measunit == 'feet' )
                            echo ' ft<sup>2</sup>';
                        else echo ' m<sup>2</sup>';
                    }
                ?>
            </div>
        </div>
        <div class="room-facilities">
            <?php $facilities = get_post_meta( get_the_ID(), 'taxonomy_room', true ); ?>
            <?php if ( !empty( $facilities ) ) { ?>
                <?php
                foreach ( $facilities as $taxonomy => $term_ids ) {
                    $rental_features = get_taxonomy( $taxonomy );
                    if ( !empty( $term_ids ) and !empty( $rental_features->labels->name ) ) {
                        echo '<div class="title">' . esc_html( $rental_features->labels->name ) . '</div>';
                        foreach ( $term_ids as $key => $value ) {
                            $term = get_term( $value, $taxonomy );
                            if ( !is_wp_error( $term ) and !empty( $term->name ) ) {
                                ?>
                                <div class="item col-33">
                                    <?php
                                        $icon = get_tax_meta( $term->term_id, 'wpbooking_icon' );
                                    ?>
                                    <i class="<?php echo wpbooking_handle_icon( $icon ); ?>"></i>
                                    <?php echo esc_html( $term->name ) ?>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            } ?>
        </div>
    </div>
    <?php if ( empty( $external_link ) ) { ?>
        <div class="room-book">
            <?php
                $price     = get_post_meta( get_the_ID(), 'base_price', true );
                $check_in  = WPBooking_Input::request( 'checkin_y' ) . "-" . WPBooking_Input::request( 'checkin_m' ) . "-" . WPBooking_Input::request( 'checkin_d' );
                $check_out = WPBooking_Input::request( 'checkout_y' ) . "-" . WPBooking_Input::request( 'checkout_m' ) . "-" . WPBooking_Input::request( 'checkout_d' );
                if ( $check_in == '--' ) $check_in = '';
                if ( $check_out == '--' ) $check_out = '';

                $is_minimum_stay = true;
                if ( $check_in and $check_out ) {
                    $service             = new WB_Service( WPBooking_Input::request( 'hotel_id' ) );
                    $check_in_timestamp  = strtotime( $check_in );
                    $check_out_timestamp = strtotime( $check_out );
                    $minimum_stay        = $service->get_minimum_stay();
                    $dDiff               = wpbooking_timestamp_diff_day( $check_in_timestamp, $check_out_timestamp );
                    if ( $dDiff < $minimum_stay ) {
                        $is_minimum_stay = false;
                    }
                }

                if ( !empty( $check_in ) and !empty( $check_out ) and $is_minimum_stay ) {
                    ?>
                    <div class="room-total-price">
                        <?php

                            $guest = (int) WPBooking_Input::request('adults') + (int) WPBooking_Input::request('children');
                            $price = WPBooking_Accommodation_Service_Type::inst()->_get_price_room_with_date( $room_origin, $check_in, $check_out, $guest );
                            $price = WPBooking_Accommodation_Service_Type::inst()->get_discount_by_day( $room_origin, $price, $diff );
                            echo WPBooking_Currency::format_money( $price );
                        ?>
                        <br>
                        <span class="small">
                        <?php
                            if ( $diff > 0 ) {
                                echo sprintf( esc_html__( "/ %s nights", "wp-booking-management-system" ), $diff );
                            } else {
                                echo sprintf( esc_html__( "/ %s night", "wp-booking-management-system" ), $diff );
                            }
                        ?>
                    </span>
                    </div>
                    <div class="room-number">
                        <select class="form-control option_number_room"
                                name="wpbooking_room[<?php the_ID() ?>][number_room]"
                                data-price-base="<?php echo esc_attr( $price ) ?>">
                            <?php
                                $max_room = get_post_meta( get_the_ID(), 'room_number', true );
                                if ( empty( $max_room ) ) $max_room = 20;
                                for ( $i = 0; $i <= $max_room; $i++ ) {
                                    echo "<option value='{$i}'>{$i}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="room-extra">
                        <?php if ( !empty( $list_extra ) ) { ?>
                            <span class="btn_extra"><?php echo esc_html__( "Extra services", "wp-booking-management-system" ) ?></span>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <button onclick="return false"
                            class="wb-btn wb-btn-default wb-btn-sm button_show_price is_single_search_result"><?php echo esc_html__( "Show Price", "wp-booking-management-system" ) ?></button>
                <?php } ?>
        </div>
        <?php
        $number_night = $diff;
        ?>
        <div class="more-extra" data-diff="<?php echo esc_attr( $number_night ); ?>"
             data-person="<?php echo esc_attr( $person ); ?>">
            <?php if ( !empty( $list_extra ) ) {
                ?>
                <table>
                    <thead>
                    <tr>
                        <td width="10%">

                        </td>
                        <td width="40%">
                            <?php echo esc_html__( "Service name", 'wp-booking-management-system' ) ?>
                        </td>
                        <td class="text-center">
                            <?php echo esc_html__( "Quantity", 'wp-booking-management-system' ) ?>
                        </td>
                        <td class="text-center">
                            <?php
                                echo sprintf( esc_html__( "Price (%s)", 'wp-booking-management-system' ), WPBooking_Currency::get_current_currency( 'currency' ) )
                            ?>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $list_extra as $k => $v ) { ?>
                        <tr>
                            <td class="text-center">
                                <input class="option_is_extra" type="checkbox"
                                       value="<?php echo esc_attr( $v[ 'is_selected' ] ) ?>" <?php if ( $v[ 'require' ] == 'yes' ) echo 'checked onclick="return false"'; ?>
                                       name="wpbooking_room[<?php the_ID() ?>][extra_service][<?php echo esc_attr( $k ) ?>][is_check]">
                            </td>
                            <td>
                                <span class="title"><?php echo esc_html( $v[ 'is_selected' ] ) ?></span>
                                <span class="desc"><?php echo ( !empty( $v[ 'desc' ] ) ) ? esc_html( $v[ 'desc' ] ) : '' ?></span>
                            </td>
                            <td>
                                <select class="form-control option_extra_quantity"
                                        name="wpbooking_room[<?php the_ID() ?>][extra_service][<?php echo esc_attr( $k ) ?>][quantity]"
                                        data-price-extra="<?php echo esc_attr( $v[ 'money' ] ) ?>"
                                        data-type-extra="<?php echo ( isset( $v[ 'type' ] ) ) ? esc_attr( $v[ 'type' ] ) : ''; ?>">
                                    <?php
                                        $start = 0;
                                        if ( $v[ 'require' ] == 'yes' ) $start = 1;
                                        for ( $i = $start; $i <= $v[ 'quantity' ]; $i++ ) {
                                            echo "<option value='{$i}'>{$i}</option>";
                                        }
                                    ?>
                                </select>
                                <input type="hidden"
                                       name="wpbooking_room[<?php the_ID() ?>][extra_service][<?php echo esc_attr( $k ) ?>][type]"
                                       value="<?php echo ( isset( $v[ 'type' ] ) ) ? esc_attr( $v[ 'type' ] ) : ''; ?>">
                            </td>
                            <td class="text-center text-color">
                                <?php echo WPBooking_Currency::format_money( $v[ 'money' ] ); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>

    <?php } ?>
    <div class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <div class="title col-7">
                <?php the_title() ?>
            </div>
            <?php
                if ( !empty( $diff ) ) {
                    ?>
                    <div class="price col-3 text-right">
                        <?php echo WPBooking_Currency::format_money( $price ); ?>
                        <span class="small">
                        <?php
                            if ( $diff > 0 ) {
                                echo sprintf( esc_html__( "/ %s nights", "wp-booking-management-system" ), $diff );
                            } else {
                                echo sprintf( esc_html__( "/ %s night", "wp-booking-management-system" ), $diff );
                            }
                        ?>
                    </span>
                    </div>
                    <?php
                }
            ?>
            <div class="gallery col-6">
                <div class="service-gallery-single">
                    <div class="fotorama_room" data-allowfullscreen="true" data-nav="thumbs">
                        <?php
                            $gallery = get_post_meta( get_the_ID(), 'gallery_room', true );
                            if ( !empty( $gallery ) and is_array( $gallery ) ) {
                                foreach ( $gallery as $k => $v ) {
                                    echo wp_get_attachment_image( $v, 'full' );
                                }
                            } else {
                                $featured = $service_room->get_featured_image_room( 'thumb300' );
                                echo do_shortcode( $featured );
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="info col-4">
                <div class="item ">
                    <?php
                        $max_guests = get_post_meta( get_the_ID(), 'max_guests', true );
                        if ( !empty( $max_guests ) ) {
                            ?>
                            <div class="img">
                                <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCA0ODcuOTAxIDQ4Ny45MDEiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQ4Ny45MDEgNDg3LjkwMTsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ3NC4yLDMwMy44MDFjLTM4LjktMzItODAuOS01My45LTkyLjYtNTkuN3YtNTguMmM4LjMtNi43LDEzLjItMTYuOCwxMy4yLTI3LjZ2LTY1LjVjMC0zNS44LTI5LjEtNjUtNjUtNjVoLTE0LjEgICAgYy0zNS44LDAtNjUsMjkuMS02NSw2NXY2NS41YzAsMTAuOCw0LjksMjAuOSwxMy4yLDI3LjZ2NTguMmMtMTEuNyw1LjgtNTMuNywyNy43LTkyLjYsNTkuN2MtOC43LDcuMi0xMy43LDE3LjgtMTMuNywyOS4ydjQ0LjkgICAgYzAsMy4zLDIuNyw2LDYsNmMzLjMsMCw2LTIuNyw2LTZ2LTQ0LjljMC03LjgsMy40LTE1LDkuMy0xOS45YzQwLjItMzMsODMuNy01NSw5Mi01OWMzLjEtMS41LDUtNC42LDUtOHYtNjMuMWMwLTItMS0zLjktMi43LTUgICAgYy02LjYtNC40LTEwLjUtMTEuNy0xMC41LTE5LjZ2LTY1LjVjMC0yOS4yLDIzLjgtNTMsNTMtNTNoMTQuMWMyOS4yLDAsNTMsMjMuOCw1Myw1M3Y2NS41YzAsNy45LTMuOSwxNS4yLTEwLjUsMTkuNiAgICBjLTEuNywxLjEtMi43LDMtMi43LDV2NjMuMWMwLDMuNCwxLjksNi41LDUsOGM4LjMsNC4xLDUxLjksMjYsOTIsNTljNS45LDQuOSw5LjMsMTIuMSw5LjMsMTkuOXY0NC45YzAsMy4zLDIuNyw2LDYsNnM2LTIuNyw2LTYgICAgdi00NC45QzQ4OCwzMjEuNjAxLDQ4MywzMTEuMDAxLDQ3NC4yLDMwMy44MDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMTQxLjQsOTIuMDAxaC0xMS41Yy0yOS44LDAtNTQsMjQuMi01NCw1NHY1My4zYzAsOC45LDQsMTcuMywxMC43LDIzdjQ2LjJjLTEwLjMsNS4yLTQzLjksMjIuOS03NSw0OC40ICAgIGMtNy40LDYuMS0xMS42LDE1LTExLjYsMjQuNnYzNi41YzAuMiwzLjIsMi45LDUuOSw2LjIsNS45YzMuMywwLDYtMi43LDYtNnYtMzYuNWMwLTYsMi42LTExLjYsNy4yLTE1LjMgICAgYzMyLjYtMjYuOCw2OC00NC42LDc0LjctNDcuOWMyLjktMS40LDQuNy00LjMsNC43LTcuNXYtNTEuNGMwLTItMS0zLjktMi43LTVjLTUtMy40LTguMS05LTguMS0xNXYtNTMuM2MwLTIzLjIsMTguOS00Miw0Mi00MiAgICBoMTEuNWMyMy4yLDAsNDIsMTguOSw0Miw0MnY1My4zYzAsNi0zLDExLjctOC4xLDE1Yy0xLjcsMS4xLTIuNywzLTIuNyw1djQyLjJjMCwzLjMsMi43LDYsNiw2YzMuMywwLDYtMi43LDYtNnYtMzkuMiAgICBjNi44LTUuNywxMC43LTE0LjEsMTAuNy0yM3YtNTMuM0MxOTUuNCwxMTYuMjAxLDE3MS4yLDkyLjAwMSwxNDEuNCw5Mi4wMDF6IiBmaWxsPSIjMDAwMDAwIi8+Cgk8L2c+CjwvZz4KPGc+Cgk8Zz4KCQk8cGF0aCBkPSJNMzUwLjUsMjY0LjMwMWMwLTMuNC0yLjctNi4xLTYtNi4xcy02LDIuNy02LDZjMCw4LjYtNywxNS43LTE1LjcsMTUuN2MtMy4zLDAtNi40LTEuMS05LTIuOGMtMC40LTAuNS0wLjktMC45LTEuNS0xLjIgICAgYy0zLjItMi45LTUuMi03LTUuMi0xMS42YzAtMy4zLTIuNy02LTYtNnMtNiwyLjctNiw2YzAsNy42LDMuMSwxNC41LDguMSwxOS41bC02LjgsMTUxYy0wLjEsMS44LDAuNiwzLjUsMiw0LjdsMjEuMSwxOS4xICAgIGMxLjEsMSwyLjYsMS41LDQsMS41YzEuNCwwLDIuOS0wLjUsNC0xLjVsMjAuOC0xOC44YzEuMy0xLjIsMi4xLTIuOSwyLTQuN2wtNi42LTE1Mi43QzM0Ny45LDI3Ny41MDEsMzUwLjUsMjcxLjIwMSwzNTAuNSwyNjQuMzAxICAgIHogTTMyMy41LDQ0Ni4wMDFsLTE1LTEzLjVsNi40LTE0MS43YzIuNSwwLjcsNS4xLDEuMiw3LjksMS4yYzMuMiwwLDYuMy0wLjYsOS4yLTEuNmw2LjEsMTQyLjVMMzIzLjUsNDQ2LjAwMXoiIGZpbGw9IiMwMDAwMDAiLz4KCTwvZz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K"/>
                            </div>
                            <span><?php echo esc_html__( "Max Guest:", "wp-booking-management-system" ) ?><?php echo esc_attr( $max_guests ); ?><?php if ( $max_guests > 1 ) echo esc_html__( "guests", "wp-booking-management-system" ); else echo esc_html__( "guest", "wp-booking-management-system" ) ?></span>
                        <?php } ?>
                </div>
                <div class="item space">
                    <?php
                        $room_size = get_post_meta( get_the_ID(), 'room_size', true );
                        if ( !empty( $room_size ) ) {
                    ?>
                    <div class="img">
                        <img
                                src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTcuMS4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDQ0Ny4wMjEgNDQ3LjAyMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDQ3LjAyMSA0NDcuMDIxOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGc+Cgk8cGF0aCBkPSJNNDQ2LjkwOCw3LjU5OWMtMC4wMDItNC4xMzktMy4zNTctNy40OTQtNy40OTYtNy40OTZMMjQ3LjUxLDBjLTEyLjk1OCwwLTIzLjUsMTAuNTQyLTIzLjUsMjMuNXY0OCAgIGMwLDEyLjk1OCwxMC41NDIsMjMuNSwyMy41LDIzLjVoMzcuODk1TDk1LjAxNiwyODUuNDA4bDAuMDE0LTQ1Ljk2NEM5NC45OTksMjI2LjUxNyw4NC40NTcsMjE2LDcxLjUzLDIxNkgyMy41MSAgIGMtNi4yODMsMC0xMi4xODgsMi40NDgtMTYuNjI4LDYuODk0Yy00LjQ0LDQuNDQ2LTYuODgsMTAuMzU0LTYuODcyLDE2LjYzMWwwLjEwMywxOTkuODk3YzAuMDAyLDQuMTM5LDMuMzU3LDcuNDk0LDcuNDk2LDcuNDk2ICAgbDE5MS45MDEsMC4xMDNjMTIuOTU4LDAsMjMuNS0xMC41NDIsMjMuNS0yMy41di00OGMwLTEyLjk1OC0xMC41NDItMjMuNS0yMy41LTIzLjVoLTM3Ljg5NWwxOTAuMzg5LTE5MC40MDhsLTAuMDE0LDQ1Ljk2MyAgIGMwLjAzMSwxMi45MjcsMTAuNTczLDIzLjQ0NCwyMy41LDIzLjQ0NGg0OC4wMmM2LjI4MywwLDEyLjE4OC0yLjQ0OCwxNi42MjgtNi44OTRjNC40NC00LjQ0Niw2Ljg4LTEwLjM1NCw2Ljg3Mi0xNi42MzEgICBMNDQ2LjkwOCw3LjU5OXogTTQyOS41MjUsMjEzLjUyN2MtMS42MDYsMS42MDgtMy43NDIsMi40OTQtNi4wMTUsMi40OTRoLTQ4LjAyYy00LjY3NiwwLTguNDg5LTMuODA0LTguNS04LjQ1OWwwLjAyLTY0LjA1OSAgIGMwLjAwMS0zLjAzNC0xLjgyNi01Ljc3LTQuNjI5LTYuOTMxYy0yLjgwMi0xLjE2Mi02LjAyOS0wLjUyLTguMTc1LDEuNjI1bC0yMTYsMjE2LjAyMWMtMi4xNDUsMi4xNDUtMi43ODYsNS4zNzEtMS42MjUsOC4xNzMgICBjMS4xNjEsMi44MDMsMy44OTYsNC42Myw2LjkyOSw0LjYzaDU2YzQuNjg3LDAsOC41LDMuODEzLDguNSw4LjV2NDhjMCw0LjY4Ny0zLjgxMyw4LjUtOC40OTYsOC41bC0xODQuNDA1LTAuMDk5TDE1LjAxLDIzOS41MTEgICBjLTAuMDAzLTIuMjcyLDAuODgtNC40MSwyLjQ4NS02LjAxOGMxLjYwNi0xLjYwOCwzLjc0Mi0yLjQ5NCw2LjAxNS0yLjQ5NGg0OC4wMmM0LjY3NiwwLDguNDg5LDMuODA0LDguNSw4LjQ1OWwtMC4wMiw2NC4wNTkgICBjLTAuMDAxLDMuMDM0LDEuODI2LDUuNzcsNC42MjksNi45MzFjMi44MDIsMS4xNjEsNi4wMjksMC41Miw4LjE3NS0xLjYyNWwyMTYtMjE2LjAyMWMyLjE0NS0yLjE0NSwyLjc4Ni01LjM3MSwxLjYyNS04LjE3MyAgIGMtMS4xNjEtMi44MDMtMy44OTYtNC42My02LjkyOS00LjYzaC01NmMtNC42ODcsMC04LjUtMy44MTMtOC41LTguNXYtNDhjMC00LjY4NywzLjgxMy04LjUsOC40OTYtOC41bDE4NC40MDUsMC4wOTlsMC4wOTksMTkyLjQxMSAgIEM0MzIuMDEzLDIwOS43ODIsNDMxLjEzMSwyMTEuOTE5LDQyOS41MjUsMjEzLjUyN3oiIGZpbGw9IiMwMDAwMDAiLz4KCTxwYXRoIGQ9Ik01NS41MSwyNDcuNDUzYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djU2LjU2OGMwLDQuMTQyLDMuMzU4LDcuNSw3LjUsNy41czcuNS0zLjM1OCw3LjUtNy41di01Ni41NjggICBDNjMuMDEsMjUwLjgxMSw1OS42NTMsMjQ3LjQ1Myw1NS41MSwyNDcuNDUzeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSw0OC4wMjFjLTQuMTQyLDAtNy41LDMuMzU4LTcuNSw3LjV2ODBjMCw0LjE0MiwzLjM1OCw3LjUsNy41LDcuNXM3LjUtMy4zNTgsNy41LTcuNXYtODAgICBDNDE1LjAxLDUxLjM3OSw0MTEuNjUzLDQ4LjAyMSw0MDcuNTEsNDguMDIxeiIgZmlsbD0iIzAwMDAwMCIvPgoJPHBhdGggZD0iTTQwNy41MSwxNjAuMDIxYy00LjE0MiwwLTcuNSwzLjM1OC03LjUsNy41djE2YzAsNC4xNDIsMy4zNTgsNy41LDcuNSw3LjVzNy41LTMuMzU4LDcuNS03LjV2LTE2ICAgQzQxNS4wMSwxNjMuMzc5LDQxMS42NTMsMTYwLjAyMSw0MDcuNTEsMTYwLjAyMXoiIGZpbGw9IiMwMDAwMDAiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K"/>
                    </div>
                    <span>
                             <?php echo esc_html__( "Room Size:", "wp-booking-management-system" ) ?>
                             <?php
                                 echo esc_attr( $room_size );
                                 $room_measunit = get_post_meta( $hotel_id, 'room_measunit', true );
                                 if ( $room_measunit == 'feet' )
                                     echo ' ft<sup>2</sup>';
                                 else echo ' m<sup>2</sup>';
                                 }
                             ?>
                        </span>
                </div>
                <div class="item"><b><?php echo esc_html__( "Bath rooms", "wp-booking-management-system" ) ?>
                        : </b><?php echo esc_attr( get_post_meta( get_the_ID(), 'bath_rooms', true ) ) ?> <?php echo esc_html__( 'room(s)', 'wp-booking-management-system' ) ?>
                </div>
                <div class="item"><b><?php echo esc_html__( "Living rooms", "wp-booking-management-system" ) ?>
                        : </b><?php echo esc_attr( get_post_meta( get_the_ID(), 'living_rooms', true ) ) ?> <?php echo esc_html__( 'room(s)', 'wp-booking-management-system' ) ?>
                </div>
                <div class="item"><b><?php echo esc_html__( "Bed rooms", "wp-booking-management-system" ) ?>
                        : </b><?php echo esc_attr( get_post_meta( get_the_ID(), 'bed_rooms', true ) ) ?> <?php echo esc_html__( 'room(s)', 'wp-booking-management-system' ) ?>
                </div>
            </div>
            <div class="facilities">
                <?php $facilities = get_post_meta( get_the_ID(), 'taxonomy_room', true ); ?>
                <?php if ( !empty( $facilities ) ) { ?>
                    <?php
                    foreach ( $facilities as $taxonomy => $term_ids ) {
                        $rental_features = get_taxonomy( $taxonomy );
                        if ( !empty( $term_ids ) and !empty( $rental_features->labels->name ) ) {
                            echo '<div class="title">' . esc_html( $rental_features->labels->name ) . '</div>';
                            foreach ( $term_ids as $key => $value ) {
                                $term = get_term( $value, $taxonomy );
                                if ( !is_wp_error( $term ) and !empty( $term->name ) ) {
                                    ?>
                                    <div class="item col-33">
                                        <?php
                                            $icon = get_tax_meta( $term->term_id, 'wpbooking_icon' );
                                        ?>
                                        <i class="<?php echo wpbooking_handle_icon( $icon ); ?>"></i>
                                        <?php echo esc_html( $term->name ) ?>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                } ?>
            </div>
        </div>

    </div>
</div>