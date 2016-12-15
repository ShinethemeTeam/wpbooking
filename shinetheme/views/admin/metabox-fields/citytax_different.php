<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/13/2016
 * Time: 2:38 PM
 */
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
        <label for="<?php  esc_html_e("Do you use City Tax ?","wpbooking") ?>"><?php  esc_html_e("Do you use City Tax ?","wpbooking") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <select class="form-control widefat small" name="citytax_excluded">
                    <option <?php selected("",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value=""><?php esc_html_e("No",'wpbooking') ?></option>
                    <option <?php selected("yes_included",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value="yes_included"><?php esc_html_e("Yes, Included",'wpbooking') ?></option>
                    <option <?php selected("yes_not_included",get_post_meta($post_id, 'citytax_excluded', TRUE)) ?> value="yes_not_included"><?php esc_html_e("Yes, Not included",'wpbooking') ?></option>
                </select>
                <p class="help-block"><?php esc_html_e('Yes, Included / Yes, Not included / No','wpbooking') ?></p>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
    </div>
    <div class="st-metabox-left">
        <label for="<?php  esc_html_e("City Tax amount","wpbooking") ?>"><?php  esc_html_e("City Tax amount","wpbooking") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <div class="tax_input_col_left">
                    <input type="number" name="citytax_amount" value="<?php echo get_post_meta($post_id, 'citytax_amount', TRUE) ?>" class="widefat form-control small">
                </div>
                <div class="tax_input_col_right">
                    <label class="label_vat_unit" for="<?php esc_html_e("Unit","wpbooking") ?>"><?php  esc_html_e("Unit","wpbooking") ?></label>
                    <select class="form-control widefat small" name="citytax_unit">
                        <option <?php selected("stay",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="stay"> <?php printf(esc_html__("%s /stay",'wpbooking'),WPBooking_Currency::get_current_currency('symbol'));?></option>
                        <option <?php selected("person_per_stay",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="person_per_stay"> <?php printf(esc_html__("%s /person per stay",'wpbooking'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("night",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="night"> <?php printf(esc_html__("%s /night",'wpbooking'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("person_per_night",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="person_per_night"> <?php printf(esc_html__("%s /person per night",'wpbooking'),WPBooking_Currency::get_current_currency('symbol'));  ?></option>
                        <option <?php selected("percent",get_post_meta($post_id, 'citytax_unit', TRUE)) ?> value="percent"><?php esc_html_e("Percent (%)",'wpbooking') ?></option>
                    </select>
                    <p class="help-block"></p>
                </div>
            </div>
            <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
        </div>

    </div>
</div>
