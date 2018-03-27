<?php
    /**
     * @since 1.0.0
     **/

    $class      = ' wpbooking-form-group ';
    $data_class = '';
    if ( !empty( $data[ 'condition' ] ) ) {
        $class      .= ' wpbooking-condition';
        $data_class .= ' data-condition=' . $data[ 'condition' ] . ' ';
    }
    $property_available_for = get_post_meta( $post_id, 'property_available_for', true );

    $df_price = get_post_meta( $post_id, 'base_price', true );
    $post_id  = wpbooking_origin_id( $post_id, 'wpbooking_service' );
?>

<div class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <label for="<?php echo esc_html( $data[ 'id' ] ); ?>"><strong><?php echo esc_html( $data[ 'label' ] ); ?></strong></label>
    <div class="st-metabox-content-wrapper">
        <div class="form-group full-width">
            <div class="wpbooking-calendar-wrapper wb_room" data-post-id="<?php echo esc_attr( $post_id ); ?>"
                 data-post-encrypt="<?php echo wpbooking_encrypt( $post_id ); ?>">
                <div class="wpbooking-calendar-content">
                    <div class="overlay">
                        <span class="spinner is-active"></span>
                    </div>
                    <div class="calendar-room2 <?php echo ( $property_available_for == 'specific_periods' ) ? 'specific_periods' : false ?>">

                    </div>
                    <div class="calendar-room <?php echo ( $property_available_for == 'specific_periods' ) ? 'specific_periods' : false ?>">

                    </div>
                </div>
                <div class="wpbooking-calendar-sidebar">
                    <div class="form-container calendar-room-form">
                        <h4 class="form-title"><?php echo esc_html__( 'Set price by arranged date', 'wpbooking' ) ?></h4>
                        <p class="form-desc"><?php echo esc_html__( 'You can book rooms for any purposes (like discount, high price, ...)', 'wpbooking' ); ?></p>
                        <div class="calendar-room-form-item full-width">
                            <label class="calendar-label"
                                   for="calendar-checkin"><?php echo esc_html__( 'Start Date', 'wpbooking' ); ?></label>
                            <div class="calendar-input-icon">
                                <input class="calendar-input date-picker" type="text" id="calendar-checkin"
                                       name="calendar-checkin" value="" readonly="readonly"
                                       placeholder="<?php echo esc_html__( 'From Date', 'wpbooking' ); ?>">
                                <label for="calendar-checkin" class="fa"><i class="fa fa-calendar"></i></label>
                            </div>
                        </div>
                        <div class="calendar-room-form-item full-width">
                            <label class="calendar-label"
                                   for="calendar-checkout"><?php echo esc_html__( 'End Date', 'wpbooking' ); ?></label>
                            <div class="calendar-input-icon">
                                <input class="calendar-input date-picker" type="text" id="calendar-checkout"
                                       name="calendar-checkout" value="" readonly="readonly"
                                       placeholder="<?php echo esc_html__( 'To Date', 'wpbooking' ); ?>">
                                <label for="calendar-checkout" class="fa"><i class="fa fa-calendar"></i></label>
                            </div>
                        </div>
                        <div class="calendar-room-form-item full-width">
                            <label class="calendar-label"
                                   for="calendar-status"><?php echo esc_html__( 'Status', 'wpbooking' ); ?></label>
                            <select name="calendar-status" id="calendar-status">
                                <option value="available"><?php echo esc_html__( 'Available', 'wpbooking' ); ?></option>
                                <option value="not_available"><?php echo esc_html__( 'Not Available', 'wpbooking' ); ?></option>
                            </select>
                        </div>
                        <div class="calendar-room-form-item full-width clear_both">
                            <label class="calendar-label"
                                   for="calendar-price"><?php echo esc_html__( 'Price', 'wpbooking' ); ?></label>
                            <input class="calendar-input" type="number" id="calendar-price" min="0"
                                   name="calendar-price" value=""
                                   placeholder="<?php echo esc_html__( 'Price', 'wpbooking' ); ?>">
                        </div>
                        <div class="calendar-room-form-item full-width hidden">
                            <label class="calendar-label"
                                   for="calendar-price-week"><?php echo esc_html__( 'Price', 'wpbooking' ); ?></label>
                            <input class="calendar-input-week" type="text" id="calendar-price-week"
                                   name="calendar-price-week" value=""
                                   placeholder="<?php echo esc_html__( 'Price', 'wpbooking' ); ?>">
                        </div>
                        <div class="calendar-room-form-item full-width hidden">
                            <label class="calendar-label"
                                   for="calendar-price-month"><?php echo esc_html__( 'Price', 'wpbooking' ); ?></label>
                            <input class="calendar-input-month" type="text" id="calendar-price-month"
                                   name="calendar-price-month" value=""
                                   placeholder="<?php echo esc_html__( 'Price', 'wpbooking' ); ?>">
                        </div>
                        <div class="clear"></div>
                        <div class="clearfix mb10">
                            <input type="hidden" id="calendar-post-id" name="post_id"
                                   value="<?php echo esc_attr( $post_id ); ?>">
                            <input type="hidden" id="calendar-post-encrypt" name="calendar-post-encrypt"
                                   value="<?php echo wpbooking_encrypt( $post_id ); ?>">
                            <button type="button" id="calendar-save"
                                    class="button button-large wb-button-primary"><?php echo esc_html__( 'Save', 'wpbooking' ); ?></button>

                            <button type="button"
                                    class="calendar-bulk-edit button button-large right"><?php echo esc_html__( 'Bulk Edit', 'wpbooking' ); ?></button>
                        </div>
                        <div class="form-message mb10">
                        </div>
                    </div>


                    <div class="calendar-help">
                        <div class="help-label"><?php esc_html_e( 'How to set Availability ?', 'wpbooking' ) ?></div>
                        <h4><strong><?php esc_html_e( 'Way 1:', 'wpbooking' ) ?></strong></h4>
                        <ul class="list">
                            <li>+ <?php esc_html_e( 'To set availability on your calendar:', 'wpbooking' ) ?>
                                <ul>
                                    <li>
                                        - <?php esc_html_e( 'A right sight table, click to Start Date picker to set a start date', 'wpbooking' ) ?></li>
                                    <li>
                                        - <?php esc_html_e( 'A right sight table, click to End Date picker to set a end date of the period you want to edit', 'wpbooking' ) ?></li>
                                </ul>
                            </li>
                            <li>
                                + <?php esc_html_e( 'A right sight table, allowing you to set status and price for that period', 'wpbooking' ) ?></li>

                        </ul>
                        <h4><strong><?php esc_html_e( 'Way 2:', 'wpbooking' ) ?></strong></h4>
                        <ul class="list">
                            <li>
                                + <?php esc_html_e( 'Drag the mouse in the left calendar to get start date and end date', 'wpbooking' ) ?>
                            </li>
                            <li>
                                + <?php esc_html_e( 'A right sight table, allowing you to set status and price for that period', 'wpbooking' ) ?></li>

                        </ul>
                    </div>
                    <div class="form-bulk-edit">
                        <div class="form-container">
                            <div class="overlay">
                                <span class="spinner is-active"></span>
                            </div>
                            <div class="form-title">
                                <h3 class="clearfix"><?php echo esc_html__( 'Bulk Price Edit', 'wpbooking' ); ?>
                                    <button type="button" class="calendar-bulk-close wpbooking-btn-close pull-right">x
                                    </button>
                                </h3>
                            </div>
                            <div class="form-content clearfix">
                                <div class="form-group">
                                    <div class="form-title">
                                        <h4 class=""><input type="checkbox" class="check-all"
                                                            data-name="day-of-week"> <?php echo esc_html__( 'Days Of Week', 'wpbooking' ); ?>
                                        </h4>
                                    </div>
                                    <div class="form-content">
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Sunday"><?php echo esc_html__( 'Sunday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Monday"><?php echo esc_html__( 'Monday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Tuesday"><?php echo esc_html__( 'Tuesday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Wednesday"><?php echo esc_html__( 'Wednesday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Thursday"><?php echo esc_html__( 'Thursday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Friday"><?php echo esc_html__( 'Friday', 'wpbooking' ); ?>
                                        </label>
                                        <label class="block"><input type="checkbox" name="day-of-week[]"
                                                                    value="Saturday"><?php echo esc_html__( 'Saturday', 'wpbooking' ); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group group-day">
                                    <div class="form-title">
                                        <h4 class=""><input type="checkbox" class="check-all"
                                                            data-name="day-of-month"> <?php echo esc_html__( 'Days Of Month', 'wpbooking' ); ?>
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
                                                            data-name="months"> <?php echo esc_html__( 'Months', 'wpbooking' ); ?>
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
                                        <h4 class=""><input type="checkbox" class="check-all"
                                                            data-name="years"> <?php echo esc_html__( 'Years', 'wpbooking' ); ?>
                                            (*)</h4>
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
                                                <label><input type="checkbox" name="years[]"
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
                            <div class="form-content flex clearfix">
                                <label class=" mr10"><span><strong><?php echo esc_html__( 'Price', 'wpbooking' ); ?>
                                            : </strong></span><input type="text" value="" name="price-bulk"
                                                                     id="price-bulk"
                                                                     placeholder="<?php echo esc_html__( 'Price', 'wpbooking' ); ?>"></label>
                                <label class="">
                                    <span><strong><?php echo esc_html__( 'Status', 'wpbooking' ); ?>: </strong></span>
                                    <select name="status-bulk">
                                        <option value="available"><?php echo esc_html__( 'Available', 'wpbooking' ) ?></option>
                                        <option value="not_available"><?php echo esc_html__( 'Unavailable', 'wpbooking' ) ?></option>
                                    </select>
                                </label>
                                <input type="hidden" class="post-bulk" name="post_id"
                                       value="<?php echo esc_attr( $post_id ); ?>">
                                <input type="hidden" class="type-bulk" name="type-bulk" value="accommodation">
                                <input type="hidden" name="post-encrypt"
                                       value="<?php echo wpbooking_encrypt( $post_id ); ?>">
                                <div class="clear"></div>
                            </div>
                            <div class="form-message"></div>
                            <div class="form-footer">
                                <button type="button"
                                        class="calendar-bulk-save button button-primary button-large"><?php echo esc_html__( 'Save', 'wpbooking' ); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <i class="wpbooking-desc"><?php echo do_shortcode( $data[ 'desc' ] ) ?></i>
</div>