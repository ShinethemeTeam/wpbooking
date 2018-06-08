<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 4/9/2018
     * Time: 1:50 PM
     * Since: 1.7
     * Updated: 1.7
     */
    if ( !class_exists( 'WPbooking_Updater' ) ) {
        class WPbooking_Updater
        {
            private $need_update = [];

            public function __construct( $need_update = '' )
            {
                $this->need_update = [
                    'wpbooking_update_availability_tour',
                    'wpbooking_update_availability_car',
                ];
                if ( !empty( $need_update ) ) {
                    $this->need_update[] = $need_update;
                }

                add_action( 'init', [ $this, '__run_update' ] );
                add_action( 'admin_notices', [ $this, '__admin_notice_wpbooking_update' ] );

                add_action( 'admin_menu', [ $this, 'edd_license_menu' ], 20 );
            }

            public function edd_license_menu()
            {
                add_submenu_page( 'wpbooking', 'Add-Ons License', 'Add-Ons License', 'manage_options', 'wpbooking_addons_updater_page', [ $this, 'edd_license_page' ] );
            }

            public function edd_license_page()
            {
                ?>
                <div class="wrap">
                    <h2><?php _e( 'Plugin License Options' ); ?></h2>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'wpbooking_edd_license' ); ?>
                        <?php do_action( 'wpbooking_licenses_page' ); ?>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <?php
            }

            public function __run_update()
            {
                if ( isset( $_GET[ 'wpbooking_update' ] ) && $_GET[ 'wpbooking_update' ] == 'run' && wp_verify_nonce( $_GET[ 'security' ], 'wpbooking-security' ) && $this->hasUpdate() ) {
                    foreach ( $this->hasUpdate() as $func ) {
                        $this->$func();
                        update_option( $func, 'updated', false );
                    }

                    wp_redirect( admin_url() );
                    exit();
                }
            }

            public function isset_table( $table_name )
            {
                global $wpdb;
                $table_name = $wpdb->prefix . $table_name;

                return $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
            }

            public function __admin_notice_wpbooking_update()
            {
                if ( $this->hasUpdate() ) {
                    $url = add_query_arg( [ 'wpbooking_update' => 'run', 'security' => wp_create_nonce( 'wpbooking-security' ) ], admin_url() );
                    ?>
                    <div class="notice notice-warning is-dismissible">
                        <p>
                            <?php _e( 'WPBooking needs to be updated database.', 'wp-booking-management-system' ); ?>
                            <a href="<?php echo esc_url( $url ); ?>" id="wppbooking-run-update"
                               class="button button-primary">
                                <?php echo esc_html__( 'Update', 'wp-booking-management-system' ); ?>
                            </a>
                        </p>
                    </div>
                    <?php
                }
            }

            private function hasUpdate()
            {
                $return = [];
                foreach ( $this->need_update as $need ) {
                    if ( !get_option( $need, '' ) ) {
                        $return[] = $need;
                    }
                }

                return $return;
            }

            public function wpbooking_update_availability_tour()
            {
                if ( !$this->isset_table( 'wpbooking_availability_tour' ) ) {
                    return;
                }
                global $wpdb;
                $sql = "INSERT INTO {$wpdb->prefix}wpbooking_availability_tour (
                    post_id,
                    `start`,
                    `end`,
                    price,
                    calendar_minimum,
                    calendar_maximum,
                    calendar_price,
                    adult_price,
                    child_price,
                    infant_price,
                    `status`,
                    base_id
                ) SELECT
                    avai.post_id,
                    avai.`start`,
                    avai.`end`,
                    avai.price,
                    avai.calendar_minimum,
                    avai.calendar_maximum,
                    avai.calendar_price,
                    avai.adult_price,
                    avai.child_price,
                    avai.infant_price,
                    avai.`status`,
                    avai.base_id
                FROM
                    {$wpdb->prefix}wpbooking_availability AS avai
                INNER JOIN {$wpdb->prefix}wpbooking_service AS sv ON sv.post_id = avai.post_id
                where sv.service_type = 'tour'";

                $wpdb->query( $sql );

                $sql = "DELETE avai.*
                FROM
                    {$wpdb->prefix}wpbooking_availability AS avai
                INNER JOIN {$wpdb->prefix}wpbooking_service AS sv ON sv.post_id = avai.post_id
                WHERE
                    sv.service_type = 'tour'";

                $wpdb->query( $sql );

            }

            public function wpbooking_update_availability_car()
            {
                if ( !$this->isset_table( 'wpbooking_availability_car' ) ) {
                    return;
                }
                global $wpdb;
                $sql = "INSERT INTO {$wpdb->prefix}wpbooking_availability_car (
                    post_id,
                    `start`,
                    `end`,
                    price,
                    calendar_minimum,
                    calendar_maximum,
                    calendar_price,
                    adult_price,
                    child_price,
                    infant_price,
                    `status`,
                    base_id
                ) SELECT
                    avai.post_id,
                    avai.`start`,
                    avai.`end`,
                    avai.price,
                    avai.calendar_minimum,
                    avai.calendar_maximum,
                    avai.calendar_price,
                    avai.adult_price,
                    avai.child_price,
                    avai.infant_price,
                    avai.`status`,
                    avai.base_id
                FROM
                    {$wpdb->prefix}wpbooking_availability AS avai
                INNER JOIN {$wpdb->prefix}wpbooking_service AS sv ON sv.post_id = avai.post_id
                where sv.service_type = 'car'";

                $wpdb->query( $sql );

                $sql = "DELETE avai.*
                FROM
                    {$wpdb->prefix}wpbooking_availability AS avai
                INNER JOIN {$wpdb->prefix}wpbooking_service AS sv ON sv.post_id = avai.post_id
                WHERE
                    sv.service_type = 'car'";

                $wpdb->query( $sql );

            }

            public static function get_inst()
            {
                static $instance;
                if ( is_null( $instance ) ) {
                    $instance = new self();
                }

                return $instance;
            }
        }

        WPbooking_Updater::get_inst();
    }