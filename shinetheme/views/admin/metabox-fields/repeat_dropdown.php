<?php
/**
 * @since 1.0.0
 **/
$data = wp_parse_args($data, array(
    'add_new_label' => esc_html__('Add New', 'wp-booking-management-system'),
    'select_label'=>esc_html__('Please Select','wp-booking-management-system')
));
$old_data = (isset($data['custom_data'])) ? esc_html($data['custom_data']) : get_post_meta($post_id, esc_html($data['id']), true);

$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition=' . $data['condition'] . ' ';
}

$class .= ' width-' . $data['width'];
$name = isset($data['custom_name']) ? esc_html($data['custom_name']) : esc_html($data['id']);

$field = '';

if (is_array($data['value']) && !empty($data['value'])) {
    $array_with_out_key = FALSE;
    $keys = array_keys($data['value']);
    if ($keys[0] === 0) {
        $array_with_out_key = true;
    }

    $field .= '<select name="' . $name . '[]" id="' . esc_html($data['id']) . '" class="widefat form-control ' . esc_html($data['class']) . '">';
    if(!empty($data['select_label'])) $field.=sprintf('<option value="">%s</option>',$data['select_label']);
    foreach ($data['value'] as $key => $value) {
        $compare = $key;
        if ($array_with_out_key) $compare = $value;

        $checked = '';
        if (!empty($data['std']) && (esc_html($key) == esc_html($data['std']))) {
            $checked = ' selected ';
        }
        if ($old_data && !empty($old_data)) {

            if (esc_html($compare) == esc_html($old_data[0])) {
                $checked = ' selected ';
            } else {
                $checked = '';
            }
        }
        $option_val = $key;
        if ($array_with_out_key) $option_val = $value;

        $field .= '<option value="' . esc_html($option_val) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
    }
    $field .= '</select>';
}


?>
<div
    class="form-table wpbooking-settings <?php echo esc_attr($data['type']) ?> <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
    <div class="st-metabox-left">
        <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
    </div>
    <div class="st-metabox-right">
        <div class="st-metabox-content-wrapper">
            <div class="form-group">
                <div class="default-item">
                    <?php echo do_shortcode($field); ?>
                </div>
                <div class="add-more-box">
                    <?php if(!empty($old_data)){
                        foreach ($old_data as $k=>$v){
                            if(!$k) continue;
                            ?>
                            <div class="more-item">
                                <select name="<?php echo esc_attr($name) ?>[]" class="widefat form-control <?php echo esc_attr($data['class']) ?>">
                                    <?php if(!empty($data['select_label'])) printf('<option value="">%s</option>',$data['select_label']);?>
                                    <?php
                                    foreach ($data['value'] as $key => $value) {


                                        if (esc_html($v) == esc_html($key)) {
                                            $checked = ' selected ';
                                        } else {
                                            $checked = '';
                                        }

                                        echo '<option value="' . esc_html($key) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="wb-repeat-dropdown-remove"><i class="fa fa-trash"></i> <?php echo esc_html__('delete','wp-booking-management-system')?></span>
                            </div>
                            <?php
                        }
                    } ?>
                </div>
                <a href="#" class="wb-repeat-dropdown-add" onclick="return false"><i
                        class="fa fa-plus-square"></i> <?php echo esc_html($data['add_new_label']) ?></a>
            </div>
        </div>

        <div class="metabox-help"><?php echo do_shortcode($data['desc']) ?></div>
    </div>
</div>