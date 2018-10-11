<?php
    if ( !empty( $cart[ 'rooms' ] ) ) {
        $booking = WPBooking_Checkout_Controller::inst();
        ?>
        <div class="review-order-item-table wpbooking-bootstrap">
            <table>
                <thead>
                <tr>
                    <td width="5%"></td>
                    <td width="50%"><?php echo esc_html__( 'Rooms', 'wp-booking-management-system' ) ?></td>
                    <td class="text-center"><?php echo esc_html__( 'Price', 'wp-booking-management-system' ) ?>
                        (<?php echo WPBooking_Currency::get_current_currency( 'currency' ) ?>)
                    </td>
                    <td class="text-center"
                        width="15%"><?php echo esc_html__( 'Number', 'wp-booking-management-system' ) ?></td>
                    <td class="text-center"><?php echo esc_html__( 'Total', 'wp-booking-management-system' ) ?>
                        (<?php echo WPBooking_Currency::get_current_currency( 'currency' ) ?>)
                    </td>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ( $cart[ 'rooms' ] as $k => $v ) {
                        $service_room     = new WB_Service( $k );
                        $featured         = $service_room->get_featured_image_room();
                        $price_room       = WPBooking_Accommodation_Service_Type::inst()->_get_price_room_in_cart( $cart, $k );
                        $price_total_room = WPBooking_Accommodation_Service_Type::inst()->_get_total_price_room_in_cart( $cart, $k );
                        ?>
                        <tr class="room-<?php echo esc_attr( $k ) ?> ">
                            <td width="5%" class="text-center">
                                <a class="delete-cart-item tooltip_desc"
                                   onclick="return confirm('<?php echo esc_html__( 'Do you want to delete it?', 'wp-booking-management-system' ) ?>')"
                                   href="<?php echo esc_url( add_query_arg( [ 'delete_item_hotel_room' => $k ], $booking->get_checkout_url() ) ) ?>">
                                    <i class="fa fa-trash-o"></i>
                                    <span class="tooltip_content"><?php echo esc_html__( "Remove this room", 'wp-booking-management-system' ) ?></span>
                                </a>
                            </td>
                            <td width="50%">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-3 room-image">
                                            <?php echo wp_kses( $featured[ 'thumb' ], [ 'img' => [ 'src' => [], 'alt' => [] ] ] ) ?>
                                        </div>
                                        <div class="col-md-9">

                                            <div class="room-info">
                                                <div class="title"><?php echo esc_html( get_the_title( $k ) ) ?></div>
                                                <?php if ( $max = $service_room->get_meta( 'max_guests' ) ) { ?>
                                                    <div class="sub-title"><?php echo esc_html__( "Max", "wp-booking-management-system" ) ?><?php echo esc_attr( $max ) ?><?php echo esc_html__( "people", "wp-booking-management-system" ) ?></div>
                                                <?php } ?>
                                                <?php
                                                    if ( !empty( $v[ 'list_date_price' ] ) ) { ?>
                                                        <span class="btn_detail_checkout"><?php echo esc_html__( "Details", "wp-booking-management-system" ) ?>
                                                            <i class="fa fa-caret-down" aria-hidden="true"></i>
</span>
                                                    <?php } ?>
                                                <div class="content_details">
                                                    <?php
                                                        if ( !empty( $v[ 'list_date_price' ] ) ) { ?>
                                                            <div class="extra-service">
                                                                <div class="title"><?php echo esc_html__( "Price by Night", "wp-booking-management-system" ) ?></div>
                                                                <div class="extra-item">
                                                                    <table>
                                                                        <thead>
                                                                        <tr>
                                                                            <th width="60%">
                                                                                <?php echo esc_html__( "Night", "wp-booking-management-system" ) ?>
                                                                            </th>
                                                                            <th class="text-center">
                                                                                <?php echo esc_html__( "Price", "wp-booking-management-system" ) ?>
                                                                            </th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php $i = 1;
                                                                            foreach ( $v[ 'list_date_price' ] as $k_list_date => $v_list_date ) { ?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <?php echo esc_html__( "Night", "wp-booking-management-system" ) ?> <?php echo esc_html( $i ) ?>
                                                                                        <br>
                                                                                        <span class="desc">(<?php echo date( get_option( 'date_format' ), $k_list_date ) ?>
                                                                                            )</span>
                                                                                    </td>
                                                                                    <td class="text-center">
                                                                                        <?php echo WPBooking_Currency::format_money( $v_list_date ) ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php $i++;
                                                                            } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                    <?php
                                                        if ( !empty( $v[ 'extra_fees' ] ) ) { ?>
                                                            <?php
                                                            foreach ( $v[ 'extra_fees' ] as $extra_service ) {
                                                                if ( !empty( $extra_service[ 'data' ] ) ) {
                                                                    ?>
                                                                    <div class="extra-service">
                                                                        <div class="title"><?php echo esc_html( $extra_service[ 'title' ] ) ?></div>
                                                                        <div class="extra-item">
                                                                            <table>
                                                                                <thead>
                                                                                <tr>
                                                                                    <th width="60%">
                                                                                        <?php echo esc_html__( "Service name", 'wp-booking-management-system' ) ?>
                                                                                    </th>
                                                                                    <th class="text-center">
                                                                                        <?php echo esc_html__( "Price", 'wp-booking-management-system' ) ?>
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                <?php
                                                                                    foreach ( $extra_service[ 'data' ] as $value ) {
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <?php echo esc_html( $value[ 'title' ] ) ?>
                                                                                                <br>
                                                                                                x
                                                                                                <span class="desc"><?php echo esc_html( $value[ 'quantity' ] ) ?></span>
                                                                                            </td>
                                                                                            <td class="text-center">
                                                                                                <?php echo WPBooking_Currency::format_money( $value[ 'price' ] ) ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } ?>
                                                        <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="container-fluid td-inner">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="room-info">
                                                <div class="title price">
                                                    <?php echo WPBooking_Currency::format_money( $price_room ); ?>
                                                    <?php
                                                        $type = $v[ 'type' ];
                                                        if ( $type == 'per_people' ) {
                                                            ?>
                                                            x <?php echo esc_attr( $cart[ 'person' ] ); ?>
                                                        <?php } ?>
                                                </div>
                                                <div class="sub-title">
                                                    <?php
                                                        if ( isset( $v[ 'discount_by_day' ] ) && !empty( $v[ 'discount_by_day' ] ) ) {
                                                            ?>
                                                            <?php echo esc_html__( 'Discount', 'wp-booking-management-system' ); ?>:
                                                            <strong><?php echo (float)$v[ 'discount_by_day' ][ 'price' ] . '%'; ?></strong>
                                                            <?php
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" width="15%">
                                <div class="container-fluid td-inner">
                                    <div class="row">
                                        <div class="col-md-12"><?php echo esc_attr( $v[ 'number' ] ) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="container-fluid td-inner">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                                echo WPBooking_Currency::format_money( $price_total_room );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
?>