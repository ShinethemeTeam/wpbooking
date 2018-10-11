<?php
    /**
     * Created by PhpStorm.
     * User: MSI
     * Date: 25/06/2018
     * Time: 04:40 CH
     */
    if ( isset( $atts ) ) {
        extract( $atts );
    }
    $services_in        = isset( $services_in ) ? $services_in : '';
    $form_fields        = get_option( 'widget_wpbooking_widget_form_search' );
    $service_type       = "";
    $form               = [];
    $search_more_fields = [];
    $page_search        = get_post_type_archive_link( 'wpbooking_service' );
    if ( !empty( $form_fields ) ) {
        foreach ( $form_fields as $k => $v ) {
            if ( !empty( $v[ 'service_type' ] ) ) {
                $service_type = $v[ 'service_type' ];
            }
            if ( !empty( $id ) && $id == $k ) {
                $form[] = $v;
                if ( !empty( $form ) ) {
                    ?>
                    <form action="<?php echo esc_url( $page_search ) ?>"
                          class="wpbooking-search-form is_search_form wpbooking-search-form-shortcode">
                        <input type="hidden" name="wpbooking_action" value="archive_filter">
                        <input type="hidden" name="services_in" value="<?php echo esc_attr( $services_in ); ?>">
                        <input type="hidden" name="service_type" value="<?php echo esc_attr( $service_type ); ?>">
                        <input type="hidden" name="wpbooking_search_form_archive" value="<?php echo esc_attr( $id ) ?>">
                        <?php
                            if ( $layout = WPBooking_Input::get( 'layout' ) ) {
                                echo '<input type="hidden" name="layout" value="' . $layout . '">';
                            }
                        ?>
                        <div class="wpbooking-search-form-wrap">
                            <?php
                                foreach ( $form as $k2 => $v2 ) {
                                    if ( !empty( $v2[ 'field_search' ] ) ) {
                                        foreach ( $v2[ 'field_search' ] as $k3 => $v3 ) {
                                            $service_type = $k3;
                                            if ( !empty( $v3 ) ) {
                                                foreach ( $v3 as $k4 => $v4 ) {
                                                    if ( isset( $v4[ 'in_more_filter' ] ) && $v4[ 'in_more_filter' ] ) {
                                                        $search_more_fields[ $k4 ] = $v4;
                                                        continue;
                                                    }
                                                    WPBooking_Widget_Form_Search::inst()->get_field_html( $v4, $service_type );
                                                }
                                            }
                                        }
                                    }
                                }
                            ?>
                            <?php if ( !empty( $search_more_fields ) ) {
                                ?>
                                <div class="wpbooking-search-form-more-wrap">
                                    <a href="#" onclick="return false"
                                       class="btn btn-link wpbooking-show-more-fields"><span
                                                class=""><?php echo esc_html__( 'Advanced Search', 'wp-booking-management-system' ) ?>
                                            <i class="fa fa-caret-down" aria-hidden="true"></i></span></a>
                                    <div class="wpbooking-search-form-more">
                                        <?php
                                            foreach ( $search_more_fields as $k => $v ) {
                                                WPBooking_Widget_Form_Search::inst()->get_field_html( $v, $service_type );
                                            } ?>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <div class="search-button-wrap">
                                <button class="wb-button"
                                        type="submit"><?php echo esc_html__( "Search", 'wp-booking-management-system' ) ?></button>
                            </div>
                        </div>
                    </form>

                    <?php do_action( 'wpbooking_after_shortcode_form_search', $form_fields, $service_type, $atts ) ?>
                <?php }
            }
        }
    }
?>
