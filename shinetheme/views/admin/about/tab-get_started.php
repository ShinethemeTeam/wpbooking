<?php
    /**
     * Created by WpBooking Team.
     * User: NAZUMI
     * Date: 12/1/2016
     * Version: 1.0
     */

?>
<div class="wb-control">
    <?php
        if ( !empty( $tabs[ $is_key - 1 ] ) ) {
            if ( !empty( $tabs[ $is_key - 1 ][ 'url' ] ) ) {
                $url = $tabs[ $is_key - 1 ][ 'url' ];
            } else {
                $url = add_query_arg( [ "page" => $slug_page_menu, "wb_tab" => $tabs[ $is_key - 1 ][ 'id' ] ], admin_url( "admin.php" ) );
            }
            ?>
            <div class="wb-left">
                <a href="<?php echo esc_url( $url ); ?>"
                   class="prev button button-primary"><?php echo esc_attr( $tabs[ $is_key - 1 ][ 'name' ] ) ?></a>
            </div>
        <?php } ?>

    <div class="wb-desc <?php echo ( count( $tabs ) > 1 ) ? '' : 'full-width'; ?>">
        <p><?php echo wp_kses( esc_html__( 'We are supporting you with four main settings to create a booking system.', 'wp-booking-management-system' ), [ 'strong' => [] ] ) ?></p>
        <p>
            <?php echo esc_html__( 'HomePage:', 'wp-booking-management-system' ); ?>
            <a href="https://wpbooking.org"
               target="_blank"><?php echo esc_html__( 'WPBooking.org', 'wp-booking-management-system' ); ?></a>
        </p>
        <p>
            <?php echo esc_html__( 'Document:', 'wp-booking-management-system' ); ?>
            <a href="http://shinetheme.com/demosd/documentation/wpbooking/?cat=15"
               target="_blank"><?php echo esc_html__( 'Getting Started', 'wp-booking-management-system' ); ?></a> <?php echo esc_html__( 'or for developer', 'wp-booking-management-system' ); ?>
            <a href="http://shinetheme.com/demosd/documentation/wpbooking/?cat=8" target="_blank"><?php echo esc_html__( 'API & Hooks', 'wp-booking-management-system' ) ?></a>
        </p>
        <p><?php echo esc_html__('Support System:', 'wp-booking-management-system'); ?> <a href="http://helpdesk.wpbooking.org/forums/forum/wp-booking-plugin/" target="_blank"><?php echo esc_html__('Give your tickets', 'wpbooking') ?></a></p>
        <p><?php echo esc_html__( 'Including:', 'wp-booking-management-system' ); ?></p>
    </div>
    <?php
        if ( !empty( $tabs[ $is_key + 1 ] ) ) {
            if ( !empty( $tabs[ $is_key + 1 ][ 'url' ] ) ) {
                $url = $tabs[ $is_key + 1 ][ 'url' ];
            } else {
                $url = add_query_arg( [ "page" => $slug_page_menu, "wb_tab" => $tabs[ $is_key + 1 ][ 'id' ] ], admin_url( "admin.php" ) );
            }
            ?>
            <div class="wb-right">
                <a href="<?php echo esc_url( $url ); ?>"
                   class="prev button button-primary"><?php echo esc_attr( $tabs[ $is_key + 1 ][ 'name' ] ) ?></a>
            </div>
        <?php } ?>
</div>
<div class="wb-content">
    <div class="header">
        <h2 class="title"><?php echo esc_html__( 'Get Started', 'wp-booking-management-system' ) ?></h2>
    </div>

    <div class="content">
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__( 'Create a new accommodation', 'wp-booking-management-system' ) ?></h3>
                <p><?php echo esc_html__( 'Accommodations are the core of your Booking site. You will not really have a Booking site without them, so it is extremely important to set them up properly and make them easy for booking.', 'wp-booking-management-system' ); ?></p>
                <ul>
                    <li><?php echo esc_html__( 'Create contact information of accommodation', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Create location of accommodation', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set time for check-in/ check-out', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set amenities of accommodation', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Create room of accommodation', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set facilities of accommodation', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set policies of accommodation such as tax, cancellation policies...', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set photos of accommodation', 'wp-booking-management-system' ) ?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url( 'images/accommodation-final.png' ) ?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__( 'Create accommodation\'s room', 'wp-booking-management-system' ) ?></h3>
                <p><?php echo esc_html__( 'Each accommodation often has multiple rooms. These rooms are created in a accommodation. Including:', 'wp-booking-management-system' ); ?></p>
                <ul>
                    <li><?php echo esc_html__( 'Basic information: name, number of rooms', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Create and set extra services', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set price of room', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set available status of room', 'wp-booking-management-system' ) ?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url( 'images/room-final.png' ) ?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__( 'Create a new tour', 'wp-booking-management-system' ) ?></h3>
                <p><?php echo esc_html__( 'Tour is another part of WpBooking plugin. You can use it for tourism, events or workshops, etc. You need to set tour according to the following information:', 'wp-booking-management-system' ); ?></p>
                <ul>
                    <li><?php echo esc_html__( 'Create contact information of tour', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set location of tour', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set pricing and status of tour', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set policies of tour: tax, cancellation policies', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Set photos of tour', 'wp-booking-management-system' ) ?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url( 'images/tour-final.png' ) ?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__( 'Manage your bookings', 'wp-booking-management-system' ) ?></h3>

                <ul>
                    <li><?php echo esc_html__( 'After users book successfully, you can replace booking status of user booking(s) easily to use Booking Admin Panel', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'You can view booking report by the chart', 'wp-booking-management-system' ) ?></li>
                </ul>

            </div>
            <div class="full">
                <img src="<?php echo wpbooking_admin_assets_url( 'images/allbooking-final.png' ) ?>" class="img-step"/>
                <img src="<?php echo wpbooking_admin_assets_url( 'images/report-final.png' ) ?>" class="img-step"/>
            </div>
        </div>
        <div class="step">
            <div class="left">
                <h3><?php echo esc_html__( 'Configure different settings', 'wp-booking-management-system' ) ?></h3>

                <ul>
                    <li><?php echo esc_html__( 'Currency of booking system', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Booking/register notification email', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Edit notification email easy', 'wp-booking-management-system' ) ?></li>
                    <li><?php echo esc_html__( 'Setting payment methods', 'wp-booking-management-system' ) ?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url( 'images/setting-general-final.png' ) ?>" class="img-step"/>
        </div>
    </div>
    <div class="footer">
        <h3 class="question"><?php echo esc_html__( 'Make questions?', 'wp-booking-management-system' ); ?></h3>
        <div class="link">
            <a href="<?php echo esc_url( 'https://wpbooking.org' ); ?>"
               target="_blank"><?php echo esc_html__( 'Need any Help?', 'wp-booking-management-system' ) ?></a>
            <a href="<?php echo esc_url( 'https://wpbooking.org/pricing-fqa' ); ?>"
               target="_blank"><?php echo esc_html__( 'FAQ', 'wp-booking-management-system' ) ?></a>
            <a href="<?php echo esc_url( 'https://wpbooking.org' ); ?>"
               target="_blank"><?php echo esc_html__( 'Submit a Ticket?', 'wp-booking-management-system' ) ?></a>
        </div>
    </div>
</div>
