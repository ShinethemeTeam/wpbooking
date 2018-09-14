<?php
    /**
     * Created by PhpStorm.
     * User: Administrator
     * Date: 11/8/2017
     * Time: 1:50 PM
     * Since: 1.0.0
     * Updated: 1.0.0
     */

    wp_enqueue_script( 'inventory-js' );
    wp_enqueue_style( 'gantt-css' );

    if ( !$post_id ):
        ?>
        <div class="wpbooking-condition desc-item service-type-accommodation">
            <strong>Note:</strong> Please save this accommodation before you can see inventory.
        </div>
    <?php else:
        global $post;
        $old_post       = $post;
        $post_id_origin = $post_id;
        $post_id        = wpbooking_origin_id( $post_id, 'wpbooking_service' );
        $args  = [
            'post_type'      => 'wpbooking_hotel_room',
            'posts_per_page' => -1,
            'post_parent'    => $post_id
        ];
        $rooms = [];
        $query = new WP_Query( $args );
        while ( $query->have_posts() ): $query->the_post();
            $rooms[] = [
                'id'   => get_the_ID(),
                'name' => get_the_title()
            ];
        endwhile;
        wp_reset_postdata();
        ?>

        <div class="wpbooking-calendar-wrapper">
            <div class="wpbooking-inventory-form">
                <span class="mr10"><strong><?php echo esc_html__( 'View by period:', 'wp-booking-management-system' ); ?></strong></span>
                <input type="text" name="wpbooking-inventory-start" class="wpbooking-inventory-start disabled" value=""
                       autocomplete="off"
                       placeholder="<?php echo esc_html__( 'Start date', 'wp-booking-management-system' ) ?>">
                <input type="text" name="wpbooking-inventory-end" class="wpbooking-inventory-end disabled" value=""
                       autocomplete="off"
                       placeholder="<?php echo esc_html__( 'End date', 'wp-booking-management-system' ) ?>">
                <button class="wpbooking-inventory-goto"><?php echo esc_html__( 'View', 'wp-booking-management-system' ); ?></button>
                <button type="button"
                        class="calendar-bulk-edit wpbooking-inventory-goto pull-right"><?php echo esc_html__( 'Bulk Edit', 'wp-booking-management-system' ); ?></button>
            </div>
            <div class="gantt wpbooking-gantt wpbooking-inventory" data-id="<?php echo esc_attr( $post_id ); ?>"
                 data-rooms="<?php echo esc_attr( json_encode( $rooms ) ); ?>" data-table=""></div>
            <div class="wpbooking-inventory-color">
                <div class="inventory-color-item">
                    <span class="available"></span> <?php echo esc_html__( 'Available', 'wp-booking-management-system' ); ?>
                </div>
                <div class="inventory-color-item">
                    <span class="unavailable"></span> <?php echo esc_html__( 'Unavailable', 'wp-booking-management-system' ); ?>
                </div>
                <div class="inventory-color-item">
                    <span class="out_stock"></span> <?php echo esc_html__( 'Out of Stock', 'wp-booking-management-system' ); ?>
                </div>
            </div>
            <div class="form-bulk-edit wpbooking-fixed">
                <div class="form-container">
                    <div class="overlay">
                        <span class="spinner is-active"></span>
                    </div>
                    <div class="form-title">
                        <h3 class="clearfix">
                            <?php echo esc_html__( 'Select a Room', 'wp-booking-management-system' ); ?>
                            <select name="room_id" class="ml20 post-bulk">
                                <option
                                        value=""><?php echo esc_html__( '---- room ----', 'wp-booking-management-system' ); ?></option>
                                <?php
                                    foreach ( $rooms as $room ) {
                                        echo '<option value="' . esc_attr( $room[ 'id' ] ) . '">' . esc_html( $room[ 'name' ] ) . '</option>';
                                    }
                                ?>
                            </select>
                            <button type="button"
                                    class="calendar-bulk-close wpbooking-btn-close pull-right">x
                            </button>
                        </h3>
                    </div>
                    <div class="form-content clearfix">
                        <div class="form-group">
                            <div class="form-title">
                                <h4 class=""><input type="checkbox" class="check-all"
                                                    data-name="day-of-week"> <?php echo esc_html__( 'Days Of Week', 'wp-booking-management-system' ); ?>
                                </h4>
                            </div>
                            <div class="form-content">
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Sunday"><?php echo esc_html__( 'Sunday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Monday"><?php echo esc_html__( 'Monday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Tuesday"><?php echo esc_html__( 'Tuesday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Wednesday"><?php echo esc_html__( 'Wednesday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Thursday"><?php echo esc_html__( 'Thursday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Friday"><?php echo esc_html__( 'Friday', 'wp-booking-management-system' ); ?>
                                </label>
                                <label class="block"><input type="checkbox" name="day-of-week[]"
                                                            value="Saturday"><?php echo esc_html__( 'Saturday', 'wp-booking-management-system' ); ?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group group-day">
                            <div class="form-title">
                                <h4 class=""><input type="checkbox" class="check-all"
                                                    data-name="day-of-month"> <?php echo esc_html__( 'Days Of Month', 'wp-booking-management-system' ); ?>
                                </h4>
                            </div>
                            <div class="form-inner">
                                <?php for ( $i = 1; $i <= 31; $i++ ):
                                    if ( $i == 1 ) {
                                        echo '<div>';
                                    }
                                    $day = sprintf( '%02d', $i );
                                    ?>
                                    <label><input type="checkbox" name="day-of-month[]"
                                                  value="<?php echo esc_attr( $i ); ?>"><?php echo esc_attr( $day ); ?>
                                    </label>

                                    <?php
                                    if ( $i != 1 && $i % 5 == 0 ) echo '</div><div>';
                                    if ( $i == 31 ) echo '</div>';
                                    ?>

                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group group-month">
                            <div class="form-title">
                                <h4 class=""><input type="checkbox" class="check-all"
                                                    data-name="months"> <?php echo esc_html__( 'Months', 'wp-booking-management-system' ); ?>
                                    (*)</h4>
                            </div>
                            <div class="form-inner">
                                <?php
                                    $months = [
                                        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
                                    ];
                                    foreach ( $months as $key => $month ):
                                        if ( $key == 0 ) {
                                            echo '<div>';
                                        }
                                        ?>
                                        <label><input type="checkbox" name="months[]"
                                                      value="<?php echo esc_attr( $month ); ?>"><?php echo esc_html( $month ); ?>
                                        </label>
                                        <?php
                                        if ( $key != 0 && ( $key + 1 ) % 2 == 0 ) echo '</div><div>';
                                        if ( $key + 1 == count( $months ) ) echo '</div>';
                                        ?>
                                    <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-title">
                                <h4 class="">
                                    <input type="checkbox" class="check-all"
                                           data-name="years"> <?php echo esc_html__( 'Years', 'wp-booking-management-system' ); ?>
                                    (*)
                                </h4>
                            </div>
                            <div class="form-content">
                                <?php
                                    $year = date( 'Y' );
                                    $j    = $year - 1;
                                    for ( $i = $year; $i <= $year + 4; $i++ ):
                                        if ( $i == $year ) {
                                            echo '<div>';
                                        }
                                        ?>
                                        <label>
                                            <input type="checkbox" name="years[]"
                                                   value="<?php echo esc_attr( $i ); ?>"><?php echo esc_attr( $i ); ?>
                                        </label>

                                        <?php
                                        if ( $i != $year && ( $i == $j + 2 ) ) {
                                            echo '</div><div>';
                                            $j = $i;
                                        }
                                        if ( $i == $year + 4 ) echo '</div>';
                                        ?>

                                    <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-content flex lh30 clearfix">
                        <label class=" mr10"><span><strong><?php echo esc_html__( 'Price', 'wp-booking-management-system' ); ?>
                                    : </strong></span><input
                                    type="text" value="" name="price-bulk" id="price-bulk"
                                    placeholder="<?php echo esc_html__( 'Price', 'wp-booking-management-system' ); ?>"></label>
                        <label class="">
                            <span><strong><?php echo esc_html__( 'Status', 'wp-booking-management-system' ); ?>: </strong></span>
                            <select name="status-bulk">
                                <option value="available"><?php echo esc_html__( 'Available', 'wp-booking-management-system' ) ?></option>
                                <option
                                        value="not_available"><?php echo esc_html__( 'Unavailable', 'wp-booking-management-system' ) ?></option>
                            </select>
                        </label>
                        <input type="hidden" class="type-bulk" name="type-bulk" value="accommodation">
                        <input name="table" value="wpbooking_availability" type="hidden">
                        <div class="clear"></div>
                    </div>
                    <div class="form-message"></div>
                    <div class="form-footer">
                        <button type="button"
                                class="calendar-bulk-save wpbooking-inventory-goto"><?php echo esc_html__( 'Save', 'wp-booking-management-system' ); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $post    = $old_post;
        $post_id = $post_id_origin;
    endif;
?>
