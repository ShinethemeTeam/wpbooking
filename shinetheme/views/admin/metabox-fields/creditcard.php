<?php
/**
 *@since 1.0.0
 **/

$old_data = get_post_meta( $post_id, esc_html( $data['id'] ), true );
if(empty($old_data)) $old_data  = array();

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$class.=' width-'.$data['width'];
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
    </div>
    <div class="st-metabox-right">
        <?php //echo $field; ?>
        <div class="st-metabox-content-wrapper">
            <div class="wpbooking-row">
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[americanexpress]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("americanexpress",$old_data)) echo 'checked' ?>  type="checkbox">
                            <span class="creditcard americanexpress"><?php echo esc_html__("American Express","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[visa]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("visa",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard visa"><?php echo esc_html__("Visa","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label <?php if (array_key_exists("americanexpress",$old_data)) echo 'checked' ?> >
                            <input name="<?php echo esc_attr($data['id']) ?>[euromastercard]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("euromastercard",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard euromastercard"><?php echo esc_html__("Euro/Mastercard","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div><div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[dinersclub]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("dinersclub",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard dinersclub"><?php echo esc_html__("Diners Club","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[jcb]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("jcb",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard jcb"><?php echo esc_html__("JCB","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[maestro]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("maestro",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard maestro"><?php echo esc_html__("Maestro","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[discover]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("discover",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard discover"><?php echo esc_html__("Discover","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[unionpaydebitcard]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("unionpaydebitcard",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard unionpaydebitcard"><?php echo esc_html__("UnionPay debit card","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[unionpaycreditcard]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("unionpaycreditcard",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard unionpaycreditcard"><?php echo esc_html__("UnionPay credit card","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
                <div class="wpbooking-col-sm-6">
                    <div class="form-group">
                        <label >
                            <input name="<?php echo esc_attr($data['id']) ?>[bankcard]" id="<?php echo esc_attr($data['id']) ?>" <?php if (array_key_exists("bankcard",$old_data)) echo 'checked' ?>   type="checkbox">
                            <span class="creditcard bankcard"><?php echo esc_html__("Bankcard","wp-booking-management-system") ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode( $data['desc'] ) ?></i>
    </div>
</div>