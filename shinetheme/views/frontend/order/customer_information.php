<?php
    /**
     * Created by wpbooking.
     * Developer: nasanji
     * Date: 1/3/2017
     * Version: 1.0
     */
?>
<div class="order-information-content customer wpbooking-bootstrap">
    <div class="title">
        <?php echo esc_html__( "Customer Information", "wp-booking-management-system" ) ?>
    </div>
    <div class="row">
        <?php
            $fist_name = get_post_meta( $order_id, 'wpbooking_user_first_name', true );
            $last_name = get_post_meta( $order_id, 'wpbooking_user_last_name', true );
            $full_name = $fist_name . ' ' . $last_name;
            if ( !empty( $full_name ) ) {
                ?>
                <div class="col-md-12">
                    <label><?php echo esc_html__( "Full name:", "wp-booking-management-system" ) ?> </label>
                    <p><?php echo esc_html( $full_name ) ?></p>
                </div>
            <?php } ?>
        <?php if ( !empty( $email = get_post_meta( $order_id, 'wpbooking_user_email', true ) ) ) { ?>
            <div class="col-md-6">
                <label><?php echo esc_html__( "Email confirmation:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $email ) ?></p>
            </div>
        <?php } ?>
        <?php if ( !empty( $phone = get_post_meta( $order_id, 'wpbooking_user_phone', true ) ) ) { ?>
            <div class="col-md-6">
                <label><?php echo esc_html__( "Telephone:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $phone ) ?></p>
            </div>
        <?php } ?>
        <?php if ( !empty( $address = get_post_meta( $order_id, 'wpbooking_user_address', true ) ) ) { ?>
            <div class="col-md-12">
                <label><?php echo esc_html__( "Address:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $address ) ?></p>
            </div>
        <?php } ?>
        <?php if ( !empty( $postcode_zip = get_post_meta( $order_id, 'wpbooking_user_postcode', true ) ) ) { ?>
            <div class="col-md-6">
                <label><?php echo esc_html__( "Postcode / Zip:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $postcode_zip ) ?></p>
            </div>
        <?php } ?>
        <?php if ( !empty( $apt_unit = get_post_meta( $order_id, 'wpbooking_user_apt_unit', true ) ) ) { ?>
            <div class="col-md-6">
                <label><?php echo esc_html__( "Apt/ Unit:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $apt_unit ) ?></p>
            </div>
        <?php } ?>
        <?php
            $passenger_information = get_post_meta( $order_id, 'wpbooking_passengers', true );
            if ( !empty( $passenger_information ) ) {
                ?>
                <div class="col-xs-12">
                    <label><strong><?php echo esc_html__( 'Passengers Information', 'wp-booking-management-system' ); ?></strong></label>
                    <table>
                        <tr>
                            <th><?php echo esc_html__( 'Name', 'wp-booking-management-system' ); ?></th>
                            <th><?php echo esc_html__( 'Ages', 'wp-booking-management-system' ); ?></th>
                        </tr>
                        <?php
                            foreach ( $passenger_information[ 'name' ] as $key => $name ) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html( $name ); ?></td>
                                    <td><?php echo esc_attr($passenger_information[ 'age' ][ $key ]); ?></td>
                                </tr>
                                <?php
                            }
                        ?>

                    </table>
                </div>
                <?php
            }
        ?>
        <?php if ( !empty( $special_request = get_post_meta( $order_id, 'wpbooking_user_special_request', true ) ) ) { ?>
            <div class="col-md-12">
                <label><?php echo esc_html__( "Special request:", "wp-booking-management-system" ) ?> </label>
                <p><?php echo esc_html( $special_request ) ?></p>
            </div>
        <?php } ?>

        <?php do_action( 'wpbooking_order_detail_customer_information', $order_data ) ?>
        <?php do_action( 'wpbooking_order_detail_customer_information_' . $service_type, $order_data ) ?>

        <div class="col-md-12 text-center">
            <?php
                $page_account = wpbooking_get_option( 'myaccount-page' );
                if ( !empty( $page_account ) ) {
                    $link_page = get_permalink( $page_account );
                    ?>
                    <a href="<?php echo esc_url( $link_page ) ?>tab/booking_history/"
                       class="wb-button wb-btn wb-btn-primary wb-history"><?php echo esc_html__( "Booking History", "wp-booking-management-system" ) ?></a>
                <?php } ?>
        </div>
    </div>
</div>
