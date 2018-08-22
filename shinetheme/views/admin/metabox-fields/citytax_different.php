<?php
$old_data = (isset($data['custom_data'])) ? esc_html($data['custom_data']) : get_post_meta($post_id, esc_html($data['id']), TRUE);
$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition=' . $data['condition'] . ' ';
}
if (!empty($data['container_class'])) $class .= ' ' . $data['container_class'];
$class .= ' width-' . $data['width'];
$name = isset($data['custom_name']) ? esc_html($data['custom_name']) : esc_html($data['id']);
?>
<div class="form-table content_tax_vat wpbooking-settings <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
    <h4 class="field-title"> <?php echo esc_html($data['label']); ?> </h4>
    <div class="st-metabox-left">
        <label for="<?php  echo esc_html__("Do you use City Tax ?","wp-booking-management-system") ?>"><?php  echo esc_html__("Do you use City Tax ?","wp-booking-management-system") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <select class="form-control widefat small" name="citytax_excluded">
                    <option <?php selected("",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value=""><?php echo esc_html__("No",'wp-booking-management-system') ?></option>
                    <option <?php selected("yes_included",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value="yes_included"><?php echo esc_html__("Yes, Included",'wp-booking-management-system') ?></option>
                    <option <?php selected("yes_not_included",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value="yes_not_included"><?php echo esc_html__("Yes, Not included",'wp-booking-management-system') ?></option>
                </select>
                <p class="help-block"><?php echo esc_html__('Yes, Included / Yes, Not included / No','wp-booking-management-system') ?></p>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </div>
    <div class="st-metabox-left">
        <label for="<?php  echo esc_html__("City Tax amount","wp-booking-management-system") ?>"><?php  echo esc_html__("City Tax amount","wp-booking-management-system") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <div class="tax_input_col_left">
                    <input type="text" name="citytax_amount" value="<?php echo esc_attr(get_post_meta($post_id, 'citytax_amount', TRUE)) ?>" class="widefat form-control small" placeholder="0">
                </div>
                <div class="tax_input_col_right">
                    <label class="label_vat_unit" for="<?php echo esc_html__("Unit","wp-booking-management-system") ?>"><?php  echo esc_html__("Unit","wp-booking-management-system") ?></label>
                    <select class="form-control widefat small" name="citytax_unit">
                        <option <?php selected("stay",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="stay"> <?php printf(esc_html__("%s /stay",'wp-booking-management-system'),WPBooking_Currency::get_current_currency('symbol'));?></option>
                        <option <?php selected("person_per_stay",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="person_per_stay"> <?php printf(esc_html__("%s /person per stay",'wp-booking-management-system'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("night",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="night"> <?php printf(esc_html__("%s /night",'wp-booking-management-system'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("person_per_night",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="person_per_night"> <?php printf(esc_html__("%s /person per night",'wp-booking-management-system'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("percent",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="percent"><?php echo esc_html__("Percent (%)",'wp-booking-management-system') ?></option>
                    </select>
                    <p class="help-block"></p>
                </div>
            </div>
            <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
        </div>

    </div>
</div>
