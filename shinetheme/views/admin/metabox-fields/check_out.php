<?php
/**
 *@since 1.0.0
 **/

$old_data = (isset( $data['custom_data'] ) ) ? esc_html( $data['custom_data'] ) : get_post_meta( $post_id, esc_html( $data['id'] ), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );


?>
<div class="form-table wpbooking-settings field-check-in <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <label class="from-group-col">
                    <?php echo esc_html__('from (optional)','wp-booking-management-system') ?>
                    <select class="form-control small" name="checkout_from">
                        <option value=""><?php echo esc_html__('Please Select','wp-booking-management-system') ?></option>
                        <?php $d = [ '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30', '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30' ];
                            foreach ( $d as $time ) {
                                printf( '<option value="%s" %s>%s</option>', $time, selected( get_post_meta( $post_id, 'checkout_from', true ), $time, false ), $time );
                            }
                        ?>
                    </select>
                </label>
                <label class="from-group-col">
                    <?php echo esc_html__('to','wp-booking-management-system') ?>
                    <select class="form-control small" name="checkout_to">
                        <option value=""><?php echo esc_html__('Please Select','wp-booking-management-system') ?></option>
                        <?php $d = [ '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30', '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30' ];
                            foreach ( $d as $time ) {
                                printf( '<option value="%s" %s>%s</option>', $time, selected( get_post_meta( $post_id, 'checkout_to', true ), $time, false ), $time );
                            }
                        ?>
                    </select>
                </label>
            </div>
        </div>
        <div class="metabox-help"><?php echo do_shortcode( $data['desc'] ) ?></div>
    </div>
</div>