<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( !class_exists( 'WPBooking_Admin_Location' ) ) {
        class WPBooking_Admin_Location extends WPBooking_Controller
        {
            static $_inst;
            static $_old_location_id;

            function __construct()
            {
                parent::__construct();

                add_action( 'init', [ $this, '_register_taxonomy' ] );


                add_action( 'save_post', [ $this, '_update_min_price_location_by_service' ], 99 );
                add_action( 'wpbooking_save_metabox_section', [ $this, '_update_min_price_location_by_service' ], 99 );
                add_action( 'wpbooking_before_save_metabox_section', [ $this, '_update_old_location_id' ], 99 );
                add_action( 'wpbooking_after_add_availability', [ $this, '_update_min_price_location_by_service' ], 99 );

                add_action( 'create_term', [ $this, '_update_min_price_location' ], 99, 3 );
                add_action( 'edit_term', [ $this, '_update_min_price_location' ], 99, 3 );

                add_action( 'wpbooking_location_edit_form_fields', [ $this, '_edit_custom_fields' ] );
                add_action( 'wpbooking_location_add_form_fields', [ $this, '_edit_custom_fields' ] );
                add_action( 'edited_wpbooking_location', [ $this, '_save_custom_fields' ] );
                add_action( 'created_wpbooking_location', [ $this, '_save_custom_fields' ], 10, 2 );

                add_shortcode( 'wpbooking_location', [ $this, 'add_location_shortcode' ] );
            }

            public function add_location_shortcode( $atts )
            {
                $atts = shortcode_atts( [
                    'location_id' => 0,
                    'unit'        => 'c',
                    'image_size'  => 'thumbnail'
                ], $atts, 'wpbooking_location' );

                extract( $atts );
                $image_id = get_tax_meta( $location_id, 'featured_image', true );
                $location = get_term( $location_id, 'wpbooking_location' );
                if ( strpos( $image_size, 'x' ) ) {
                    $image_size = explode( 'x', $image_size );
                }
                $image = wp_get_attachment_image_url( $image_id, $image_size );

                wp_enqueue_script( 'wpbooking-simpleWeather' );

                return '
                    <div class="wpbooking-location-item" data-address="' . esc_attr( $location->name ) . '" data-unit="' . esc_attr( $unit ) . '">
                        <div class="wpbooking-location-image">
                            <img src="' . esc_url( $image ) . '" alt="' . esc_attr( $location->name ) . '" class="img-responsive">
                        </div>
                        <h4 class="wpbooking-location-temp"></h4>
                        <h2 class="wpbooking-location-title"><a href="'.get_term_link($location).'" target="_blank">' . esc_html( $location->name ) . '</a></h2>
                    </div>
                ';

            }

            function _save_custom_fields( $location_id )
            {
                if ( empty( $location_id ) ) return;
                $map_lat  = WPBooking_Input::post( 'map_lat' );
                $map_long = WPBooking_Input::post( 'map_long' );
                $map_zoom = WPBooking_Input::post( 'map_zoom' );
                if ( !empty( $map_lat ) && !empty( $map_long ) && !empty( $map_zoom ) ) {
                    update_tax_meta( $location_id, 'map_lat', $map_lat );
                    update_tax_meta( $location_id, 'map_long', $map_long );
                    update_tax_meta( $location_id, 'map_zoom', $map_zoom );
                }
                $featured_image = WPBooking_Input::post( 'featured_image' );
                update_tax_meta( $location_id, 'featured_image', $featured_image );
            }

            function _add_map_custom_fields()
            {
                ?>
                <div class="form-field st-custom-location-map">
                    <label for="map_lat_log"><?php echo esc_html__( 'Map Lat & Long', 'wp-booking-management-system' ); ?></label>
                    <div class="st_location_map">
                        <input type="hidden" name="map_lat" id="map_lat" value="">
                        <input type="hidden" name="map_long" id="map_long" value="">
                        <input type="hidden" name="map_zoom" id="map_zoom" value="">
                        <input type="text" name="gmap-search" value=""
                               placeholder="<?php echo esc_html__( 'Enter a address...', 'wp-booking-management-system' ); ?>"
                               class="gmap-search">
                        <div class="gmap-content"></div>
                    </div>
                    <p><?php echo esc_html__( 'This is the location we will provide guests. Click to move the marker if you need to move it', 'wp-booking-management-system' ); ?></p>
                </div>
                <?php
            }

            function _edit_custom_fields( $term_object )
            {
                if ( empty( $term_object->term_id ) ) $location_id = 0; else $location_id = $term_object->term_id;
                $lat  = get_tax_meta( $location_id, 'map_lat' );
                $lng  = get_tax_meta( $location_id, 'map_long' );
                $zoom = get_tax_meta( $location_id, 'map_zoom' );
                ?>
                <tr class="form-field">
                    <th scope="row" valign="top">
                        <label for="map_lat_log"><?php echo esc_html__( 'Map Lat & Long', 'wp-booking-management-system' ); ?></label>
                    </th>
                    <td>
                        <div class="st_location_map">
                            <input type="hidden" name="map_lat" id="map_lat"
                                   value="<?php echo( !empty( $lat ) ? esc_html( $lat ) : '' ) ?>">
                            <input type="hidden" name="map_long" id="map_long"
                                   value="<?php echo( !empty( $lng ) ? esc_html( $lng ) : '' ) ?>">
                            <input type="hidden" name="map_zoom" id="map_zoom"
                                   value="<?php echo( !empty( $zoom ) ? esc_html( $zoom ) : '' ) ?>">
                            <input type="text" name="gmap-search" value=""
                                   placeholder="<?php echo esc_html__( 'Enter a address...', 'wp-booking-management-system' ); ?>"
                                   class="gmap-search">
                            <div class="gmap-content"></div>
                        </div>
                        <p><?php echo esc_html__( 'This is the location we will provide guests. Click to move the marker if you need to move it', 'wp-booking-management-system' ); ?></p>
                    </td>
                </tr>

                <?php
                $wpbooking_featured_image = get_tax_meta( $location_id, 'featured_image' );
                $thumbnail_url            = wp_get_attachment_url( $wpbooking_featured_image );
                ?>
                <tr class="form-field">
                    <th scope="row" valign="top">
                        <label><?php echo esc_html__( 'Location Featured Image', 'wp-booking-management-system' ); ?></label>
                    </th>
                    <td>
                        <div class="upload-wrapper">
                            <div class="upload-items">
                                <?php
                                    if ( !empty( $thumbnail_url ) ):
                                        ?>
                                        <div class="upload-item">
                                            <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                                                 alt="<?php echo esc_html__( 'Featured Thumb', 'wp-booking-management-system' ) ?>"
                                                 class="frontend-image img-responsive">
                                        </div>
                                    <?php endif; ?>
                            </div>
                            <input type="hidden" class="save-image-id" name="featured_image"
                                   value="<?php echo esc_attr( $wpbooking_featured_image ); ?>">
                            <button type="button"
                                    class="upload-button <?php if ( empty( $thumbnail_url ) ) echo 'no_image'; ?>"
                                    data-uploader_title="<?php echo esc_html__( 'Select an image to upload', 'wp-booking-management-system' ); ?>"
                                    data-uploader_button_text="<?php echo esc_html__( 'Use this image', 'wp-booking-management-system' ); ?>"><?php echo esc_html__( 'Upload', 'wp-booking-management-system' ); ?></button>
                            <button type="button"
                                    class="delete-button <?php if ( empty( $thumbnail_url ) ) echo 'none'; ?>"
                                    data-delete-title="<?php echo esc_html__( 'Do you want delete this image?', 'wp-booking-management-system' ) ?>"><?php echo esc_html__( 'Delete', 'wp-booking-management-system' ); ?></button>
                        </div>
                    </td>
                </tr>


                <?php
            }

            function _register_taxonomy()
            {
                $labels = [
                    'name'              => esc_html__( 'Locations', 'wp-booking-management-system' ),
                    'singular_name'     => esc_html__( 'Location', 'wp-booking-management-system' ),
                    'search_items'      => esc_html__( 'Search for Locations', 'wp-booking-management-system' ),
                    'all_items'         => esc_html__( 'All Locations', 'wp-booking-management-system' ),
                    'parent_item'       => esc_html__( 'Parent Location', 'wp-booking-management-system' ),
                    'parent_item_colon' => esc_html__( 'Parent Location:', 'wp-booking-management-system' ),
                    'edit_item'         => esc_html__( 'Edit Location', 'wp-booking-management-system' ),
                    'update_item'       => esc_html__( 'Update Location', 'wp-booking-management-system' ),
                    'add_new_item'      => esc_html__( 'Add New Location', 'wp-booking-management-system' ),
                    'new_item_name'     => esc_html__( 'New Location Name', 'wp-booking-management-system' ),
                    'menu_name'         => esc_html__( 'Location', 'wp-booking-management-system' ),
                ];

                $args = [
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'meta_box_cb'       => false,
                    'rewrite'           => [ 'slug' => 'location' ],
                ];
                $args = apply_filters( 'wpbooking_register_location_taxonomy', $args );

                register_taxonomy( 'wpbooking_location', [ 'wpbooking_service' ], $args );

                $hide = apply_filters( 'wpbooking_hide_locaton_select_box', true );
                if ( $hide )
                    WPBooking_Assets::add_css( "#wpbooking_locationdiv{display:none!important}" );
            }

            /**
             * Update min_price location when save service
             *
             * @since  1.3
             * @author quandq
             *
             * @param $post_id
             *
             * @return bool
             */
            function _update_min_price_location_by_service( $post_id )
            {
                if ( get_post_type( $post_id ) != 'wpbooking_service' ) return false;
                $service_type    = get_post_meta( $post_id, 'service_type', true );
                $new_location_id = get_post_meta( $post_id, 'location_id', true );
                if ( !empty( self::$_old_location_id ) and $new_location_id != self::$_old_location_id ) {
                    $this->_update_min_price_location( self::$_old_location_id, false, 'wpbooking_location' );
                }
                if ( !empty( $new_location_id ) ) {
                    $this->_update_min_price_location( $new_location_id, false, 'wpbooking_location' );
                }
            }

            /**
             * Update min_price location when save location
             *
             * @since  1.3
             * @author quandq
             *
             * @param      $term_id
             * @param bool $term_taxonomy_id
             * @param bool $taxonomy
             */
            function _update_min_price_location( $term_id, $term_taxonomy_id = false, $taxonomy = false )
            {
                if ( $taxonomy == 'wpbooking_location' ) {
                    $service_types = WPBooking_Service_Controller::inst()->get_service_types();
                    if ( !empty( $service_types ) ) {
                        foreach ( $service_types as $service_type => $obj ) {
                            $list_location[] = $term_id;
                            $child           = get_term_children( $term_id, 'wpbooking_location' );
                            $list_location   = array_unique( array_merge( $list_location, $child ) );
                            $min_price       = $this->_get_min_price_by_location_id( $list_location, $service_type );
                            update_tax_meta( $term_id, 'min_price_' . $service_type, $min_price );
                        }
                    }
                    $this->_update_parent_id_location( $term_id );
                }
            }

            /**
             * Update min_price parent location
             *
             * @since  1.3
             * @author quandq
             *
             * @param $location_id
             */
            function _update_parent_id_location( $location_id )
            {
                $list_location[] = $location_id;
                $parent          = get_term_by( 'id', $location_id, 'wpbooking_location' );
                if ( !empty( $parent->parent ) ) {
                    while ( $parent->parent != '0' ) {
                        $term_id         = $parent->parent;
                        $list_location[] = $term_id;
                        $child           = get_term_children( $location_id, 'wpbooking_location' );
                        $list_location   = array_unique( array_merge( $list_location, $child ) );
                        $service_types   = WPBooking_Service_Controller::inst()->get_service_types();
                        if ( !empty( $service_types ) ) {
                            foreach ( $service_types as $service_type => $obj ) {
                                $min_price = $this->_get_min_price_by_location_id( $list_location, $service_type );
                                update_tax_meta( $term_id, 'min_price_' . $service_type, $min_price );
                            }
                        }
                        $parent = get_term_by( 'id', $term_id, 'wpbooking_location' );
                    }
                }
            }

            /**
             * Get min_price Location by Service
             *
             * @since  1.3
             * @author quandq
             *
             * @param $location_id
             * @param $service_type
             *
             * @return int
             */
            function _get_min_price_by_location_id( $location_id, $service_type )
            {
                if ( empty( $location_id ) ) return 0;
                if ( is_array( $location_id ) ) {
                    $list_location = implode( ',', $location_id );
                } else {
                    $list_location = $location_id;
                }
                global $wpdb;
                $sql    = "
            SELECT 
            MIN(CAST(mt1.meta_value as DECIMAL)) as min_price
            FROM 
            {$wpdb->prefix}posts 
            INNER JOIN {$wpdb->prefix}wpbooking_service ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}wpbooking_service.post_id and {$wpdb->prefix}wpbooking_service.service_type = '{$service_type}'
            INNER JOIN {$wpdb->prefix}postmeta as mt1 ON {$wpdb->prefix}posts.ID = mt1.post_id and mt1.meta_key = 'price'
            LEFT JOIN {$wpdb->prefix}term_relationships ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id)
            WHERE
                1 = 1
            AND ({$wpdb->prefix}term_relationships.term_taxonomy_id IN ({$list_location}))";
                $result = $wpdb->get_row( $sql, ARRAY_A );
                if ( !empty( $result ) ) {
                    return $result[ 'min_price' ];
                }

                return 0;
            }

            /**
             * Get Location Old ID
             *
             * @since  1.3
             * @author quandq
             *
             * @param $post_id
             *
             * @return bool
             */
            function _update_old_location_id( $post_id )
            {
                if ( get_post_type( $post_id ) != 'wpbooking_service' ) return false;
                $old_location_id        = get_post_meta( $post_id, 'location_id', true );
                self::$_old_location_id = $old_location_id;
            }

            static function inst()
            {
                if ( !self::$_inst ) {
                    self::$_inst = new self();
                }

                return self::$_inst;
            }
        }

        WPBooking_Admin_Location::inst();
    }