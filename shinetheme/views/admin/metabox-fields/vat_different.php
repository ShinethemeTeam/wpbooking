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
        <label for="<?php esc_html_e("Do you use V.A.T ?","wpbooking") ?>"><?php esc_html_e("Do you use V.A.T ?","wpbooking") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <select class="form-control widefat small" name="vat_excluded">
                    <option <?php selected("yes_included",get_post_meta($post_id, 'vat_excluded', TRUE)) ?> value="yes_included"><?php esc_html_e("Yes, Included",'wpbooking') ?></option>
                    <option <?php selected("yes_not_included",get_post_meta($post_id, 'vat_excluded', TRUE)) ?> value="yes_not_included"><?php esc_html_e("Yes, Not included",'wpbooking') ?></option>
                    <option <?php selected("no",get_post_meta($post_id, 'vat_excluded', TRUE)) ?> value="no"><?php esc_html_e("No",'wpbooking') ?></option>
                </select>
                <p class="help-block"><?php esc_html_e('Yes, Included / Yes, Not included / No','wpbooking') ?></p>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
    </div>
    <div class="st-metabox-left">
        <label for="<?php esc_html_e("VAT amount","wpbooking") ?>"><?php esc_html_e("VAT amount","wpbooking") ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <input type="text" name="vat_amount" value="<?php echo get_post_meta($post_id, 'vat_amount', TRUE) ?>" class="widefat form-control small">
                <label class="label_vat_unit" for="<?php esc_html_e("Unit","wpbooking") ?>"><?php  esc_html_e("Unit","wpbooking") ?></label>
                <select class="form-control widefat small" name="vat_unit">
                    <option <?php selected("percent",get_post_meta($post_id, 'vat_unit', TRUE)) ?> value="percent"><?php esc_html_e("Percent (%)",'wpbooking') ?></option>
                    <option <?php selected("fixed",get_post_meta($post_id, 'vat_unit', TRUE)) ?> value="fixed"><?php esc_html_e("Fixed",'wpbooking') ?></option>
                </select>
                <p class="help-block"><?php esc_html_e('Sed ut perspiciatis unde omnis ','wpbooking') ?></p>
            </div>
        </div>
        <i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
    </div>
</div>
