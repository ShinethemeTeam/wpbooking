<?php
/**
 * @since 1.0.0
 **/
$data = wp_parse_args($data, array(
    'add_new_label' => esc_html__('Add New', 'wp-booking-management-system'),
    'select_label'  => esc_html__('Please Select', 'wp-booking-management-system')
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
?>
<div class="form-table wpbooking-settings <?php echo esc_attr($data['type']) ?> <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
    <div class="single-bed-option">
        <div class="st-metabox-left">
            <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
        </div>
        <div class="st-metabox-right">
            <div class="st-metabox-content-wrapper">
                <div class="form-group">
                    <div class="default-item">
                        <?php
                        $single_meta = get_post_meta($post_id, $name . '_single_', true);

                        if(empty($single_meta[0]))$single_meta[0] =  array();
                        $data_single_meta = wp_parse_args($single_meta[0], array(
                            'number'   => 1,
                            'bed_type'     => ''
                        ));

                        if (is_array($data['value']) && !empty($data['value'])) {
                            $array_with_out_key = FALSE;
                            $keys = array_keys($data['value']);
                            if ($keys[0] === 0) {
                                $array_with_out_key = true;
                            }

                            echo '<select name="' . esc_attr($name) . '_single_[bed_type][]"  class="widefat small form-control ' . esc_html($data['class']) . '">';
                            if (!empty($data['select_label'])) $field .= sprintf('<option value="">%s</option>', $data['select_label']);
                            foreach ($data['value'] as $key => $value) {
                                $compare = $key;
                                if ($array_with_out_key) $compare = $value;

                                $checked = '';
                                if (!empty($data['std']) && (esc_html($key) == esc_html($data['std']))) {
                                    $checked = ' selected ';
                                }
                                $option_val = $key;
                                if ($array_with_out_key) $option_val = $value;
                                if (!empty($data_single_meta['bed_type'])) {
                                    if (esc_html($compare) == esc_html($data_single_meta['bed_type'])) {
                                        $checked = ' selected ';
                                    }
                                }
                                echo '<option value="' . esc_html($option_val) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                            }
                            echo '</select> x ';

                            echo sprintf('<select class="small form-control" name="%s_single_[number][]">', esc_html($name));
                            for ($i = 1; $i < 12; $i++) {
                                $checked = '';
                                if (!empty($data_single_meta['number'])) {
                                    if (esc_html($i) == esc_html($data_single_meta['number'])) {
                                        $checked = ' selected ';
                                    }
                                }
                                echo '<option '.esc_attr($checked).' value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                            }
                            echo '</select>';

                        } ?>
                    </div>
                    <div class="add-more-box">
                        <?php if (!empty($single_meta)) {
                            foreach ($single_meta as $k => $v) {
                                if (!$k) continue;
                                ?>
                                <div class="more-item">
                                    <select name="<?php echo esc_attr($name) ?>_single_[bed_type][]"
                                            class="widefat small form-control <?php echo esc_attr($data['class']) ?>">
                                        <?php if (!empty($data['select_label'])) printf('<option value="">%s</option>', $data['select_label']); ?>
                                        <?php
                                        foreach ($data['value'] as $key => $value) {


                                            if (esc_html($v['bed_type']) == esc_html($key)) {
                                                $checked = ' selected ';
                                            } else {
                                                $checked = '';
                                            }

                                            echo '<option value="' . esc_html($key) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                                        }
                                        ?>
                                    </select> x
                                    <?php

                                    printf('<select class="small form-control" name="%s_single_[number][]">', esc_html($name));
                                    for ($i = 1; $i < 12; $i++) {
                                        if (esc_html($v['number']) == esc_html($i)) {
                                            $checked = ' selected ';
                                        } else {
                                            $checked = '';
                                        }
                                        echo '<option '.esc_attr($checked).' value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                                    }
                                    echo '</select>';

                                    ?>
                                    <span class="wb-repeat-dropdown-remove"><i class="fa fa-trash"></i></span>
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
        <div class="clear"></div>

        <div class="st-metabox-left">
            <label><?php echo esc_html__('Enter the number of guests who can sleep here', 'wp-booking-management-system') ?></label>
        </div>
        <div class="st-metabox-right">
            <div class="st-metabox-content-wrapper">
                <div class="form-group">
                    <select name="<?php echo esc_attr($name) ?>_single_num_guests" id="" class="small form-control">
                        <?php for ($i = 1; $i < 20; $i++) {
                            printf('<option value="%s" %s>%s</option>', $i, selected(get_post_meta($post_id, $name . '_single_num_guests', true), $i, false), $i);
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="st-metabox-left">
            <label><?php echo esc_html__('Private bath room', 'wp-booking-management-system') ?></label>
        </div>
        <div class="st-metabox-right">
            <div class="st-metabox-content-wrapper">
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr($name) ?>_single_private_bathroom"
                               value="1" <?php checked(get_post_meta($post_id, $name . '_single_private_bathroom', true),1) ?> > <?php echo esc_html__('Yes', 'wp-booking-management-system') ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?php
    $bed_rooms = get_post_meta($post_id, 'bed_rooms', true);
    $multi_meta = get_post_meta($post_id, $name . '_multi_', true);
    ?>
    <div class="multi-bed-option">
        <div class="multi-item-default">
            <label class="multi-item-title"><?php echo esc_html__('Bed room #__number_room__', 'wp-booking-management-system') ?></label>
            <div class="clear"></div>
            <div class="st-metabox-left">
                <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
            </div>
            <div class="st-metabox-right">
                <div class="st-metabox-content-wrapper">
                    <div class="form-group">
                        <div class="default-item">
                            <?php
                            if (is_array($data['value']) && !empty($data['value'])) {
                                $array_with_out_key = FALSE;
                                $keys = array_keys($data['value']);
                                if ($keys[0] === 0) {
                                    $array_with_out_key = true;
                                }

                                echo '<select name="' . esc_attr($name) . '_multi_[__number_room__][bed_type][bed_type][]" class="widefat small form-control ' . esc_html($data['class']) . '">';
                                if (!empty($data['select_label'])) $field .= sprintf('<option value="">%s</option>', $data['select_label']);
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

                                    echo '<option value="' . esc_html($option_val) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                                }
                                echo '</select> x ';

                                echo sprintf('<select class="small form-control" name="%s_multi_[__number_room__][bed_type][number][]">', esc_html($name));
                                for ($i = 1; $i < 12; $i++) {
                                    echo '<option value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                                }
                                echo '</select>';

                            }
                            ?>
                        </div>
                        <div class="add-more-box">
                            <?php if (!empty($multi_meta['bed_type'])) {
                                foreach ($multi_meta['bed_type'] as $k => $v) {
                                    if (!$k) continue;
                                    ?>
                                    <div class="more-item">
                                        <select name="<?php echo esc_attr($name) ?>_multi_[__number_room__][bed_type][bed_type][]"
                                                class="widefat small form-control <?php echo esc_attr($data['class']) ?>">
                                            <?php if (!empty($data['select_label'])) printf('<option value="">%s</option>', $data['select_label']); ?>
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
                                        </select> x
                                        <?php

                                        printf('<select class="small form-control" name="%_multi_[__number_room__][bed_type][number][]">', esc_html($name));
                                        for ($i = 1; $i < 12; $i++) {
                                            echo '<option value="' . esc_attr($i) . '" >' . esc_html($i) . '</option>';
                                        }
                                        echo '</select>';

                                        ?>
                                        <span class="wb-repeat-dropdown-remove"><i
                                                class="fa fa-trash"></i></span>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                        <a href="#" class="wb-repeat-dropdown-add" onclick="return false"><i
                                class="fa fa-plus-square"></i> <?php echo esc_html($data['add_new_label']) ?>
                        </a>
                    </div>
                </div>
                <div class="metabox-help"><?php echo do_shortcode($data['desc']) ?></div>
            </div>
            <div class="clear"></div>

            <div class="st-metabox-left">
                <label><?php echo esc_html__('Enter the number of guests who can sleep here', 'wp-booking-management-system') ?></label>
            </div>
            <div class="st-metabox-right">
                <div class="st-metabox-content-wrapper">
                    <div class="form-group">
                        <select name="<?php echo esc_attr($name) ?>_multi_[__number_room__][num_guests]" id=""
                                class="small form-control">
                            <?php for ($i = 1; $i < 20; $i++) {
                                printf('<option value="%s" %s>%s</option>', $i, selected($multi_meta['num_guests'], $i, false), $i);
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="st-metabox-left">
                <label><?php echo esc_html__('Private bath room', 'wp-booking-management-system') ?></label>
            </div>
            <div class="st-metabox-right">
                <div class="st-metabox-content-wrapper">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($name) ?>_multi_[__number_room__][private_bath]"
                                   value="1" <?php checked($multi_meta['private_bath'],1) ?> > <?php echo esc_html__('Yes', 'wp-booking-management-system') ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (!empty($bed_rooms) and $bed_rooms >= 1) {
            for ($i = 1; $i <= $bed_rooms; $i++) {
                if (empty($multi_meta[($i-1)])) $multi_meta[($i-1)] = array();
                $data_multi_meta = wp_parse_args($multi_meta[($i-1)], array(
                    'num_guests'   => 1,
                    'private_bath' => 0,
                    'bed_type'     => array()
                ));

                ?>
                <div class="multi-item-row <?php echo esc_html("number_".$i) ?>" data-number="<?php echo esc_html($i) ?>">
                    <label class="multi-item-title"><?php printf(esc_html__('Bed room #%d', 'wp-booking-management-system'), $i) ?></label>
                    <div class="st-metabox-left">
                        <label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
                    </div>
                    <div class="st-metabox-right">
                        <div class="st-metabox-content-wrapper">
                            <div class="form-group">
                                <div class="default-item">
                                    <?php

                                    if (is_array($data['value']) && !empty($data['value'])) {
                                        $array_with_out_key = FALSE;
                                        $keys = array_keys($data['value']);
                                        if ($keys[0] === 0) {
                                            $array_with_out_key = true;
                                        }
                                        echo '<select name="' . esc_attr($name) . '_multi_['.esc_attr($i).'][bed_type][bed_type][]" class="widefat small form-control ' . esc_html($data['class']) . '">';
                                        if (!empty($data['select_label'])) $field .= sprintf('<option value="">%s</option>', $data['select_label']);

                                        foreach ($data['value'] as $key => $value) {
                                            $compare = $key;
                                            if ($array_with_out_key) $compare = $value;


                                            $checked = '';
                                            if (!empty($data['std']) && (esc_html($key) == esc_html($data['std']))) {
                                                $checked = ' selected ';
                                            }

                                            $option_val = $key;
                                            if ($array_with_out_key) $option_val = $value;

                                            if (!empty($data_multi_meta['bed_type'])) {

                                                foreach($data_multi_meta['bed_type'] as $key_bed => $value_bed){
                                                    if ($key_bed == 0 and esc_html($compare) == esc_html($value_bed['bed_type'])) {
                                                        $checked = ' selected ';
                                                    }
                                                }

                                            }

                                            echo '<option value="' . esc_html($option_val) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                                        }
                                        echo '</select> x ';

                                        echo sprintf('<select class="small form-control" name="%s_multi_[%s][bed_type][number][]">', esc_html($name),$i);
                                        for ($j = 1; $j< 12; $j++) {
                                            $checked = '';
                                            if (!empty($data_multi_meta['bed_type'])) {
                                                foreach($data_multi_meta['bed_type'] as $key_bed => $value_bed){
                                                    if ($key_bed == 0 and esc_html($j) == esc_html($value_bed['number'])) {
                                                        $checked = ' selected ';
                                                    }
                                                }
                                            }
                                            echo '<option '.esc_attr($checked).' value="' . esc_attr($j) . '" >' . esc_html($j) . '</option>';
                                        }
                                        echo '</select>';

                                    }
                                    ?>
                                </div>
                                <div class="add-more-box">
                                    <?php if (!empty($data_multi_meta['bed_type'])) {
                                        foreach ($data_multi_meta['bed_type'] as $k => $v) {
                                            if (!$k) continue;
                                            ?>
                                            <div class="more-item">
                                                <select name="<?php echo esc_attr($name) ?>_multi_[<?php echo esc_html($i) ?>][bed_type][bed_type][]"
                                                        class="widefat small form-control <?php echo esc_attr($data['class']) ?>">
                                                    <?php if (!empty($data['select_label'])) printf('<option value="">%s</option>', $data['select_label']); ?>
                                                    <?php
                                                    foreach ($data['value'] as $key => $value) {
                                                        if (esc_html($v['bed_type']) == esc_html($key)) {
                                                            $checked = ' selected ';
                                                        } else {
                                                            $checked = '';
                                                        }
                                                        echo '<option value="' . esc_html($key) . '" ' . esc_attr($checked) . '>' . esc_html($value) . '</option>';
                                                    }
                                                    ?>
                                                </select> x
                                                <?php

                                                printf('<select class="small form-control" name="%s_multi_[%s][bed_type][number][]">', esc_html($name),$i);
                                                for ($j = 1; $j < 12; $j++) {
                                                    if (esc_html($v['number']) == esc_html($j)) {
                                                        $checked = ' selected ';
                                                    } else {
                                                        $checked = '';
                                                    }
                                                    echo '<option ' . esc_attr($checked) . ' value="' . esc_attr($j) . '" >' . esc_html($j) . '</option>';
                                                }
                                                echo '</select>';
                                                ?>
                                                <span class="wb-repeat-dropdown-remove"><i
                                                        class="fa fa-trash"></i></span>
                                            </div>
                                            <?php
                                        }
                                    } ?>
                                </div>
                                <a href="#" class="wb-repeat-dropdown-add" onclick="return false"><i
                                        class="fa fa-plus-square"></i> <?php echo esc_html($data['add_new_label']) ?>
                                </a>
                            </div>
                        </div>
                        <div class="metabox-help"><?php echo do_shortcode($data['desc']) ?></div>
                    </div>
                    <div class="clear"></div>

                    <div class="st-metabox-left">
                        <label><?php echo esc_html__('Enter the number of guests who can sleep here', 'wp-booking-management-system') ?></label>
                    </div>
                    <div class="st-metabox-right">
                        <div class="st-metabox-content-wrapper">
                            <div class="form-group">
                                <select name="<?php echo esc_attr($name) ?>_multi_[<?php echo esc_html($i) ?>][num_guests]" id=""
                                        class="small form-control">
                                    <?php for ($k = 1; $k < 20; $k++) {
                                        printf('<option value="%s" %s>%s</option>', $k, selected($data_multi_meta['num_guests'], $k, false), $k);
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="st-metabox-left">
                        <label><?php echo esc_html__('Private bath room', 'wp-booking-management-system') ?></label>
                    </div>
                    <div class="st-metabox-right">
                        <div class="st-metabox-content-wrapper">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="<?php echo esc_attr($name) ?>_multi_[<?php echo esc_html($i) ?>][private_bath]"
                                           value="1" <?php checked($data_multi_meta['private_bath'],1) ?> > <?php echo esc_html__('Yes', 'wp-booking-management-system') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }?>
    </div>
</div>