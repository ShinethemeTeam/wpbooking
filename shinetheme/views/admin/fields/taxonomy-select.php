<?php
$value = traveler_get_option($data['id'],$data['std']);
$terms = get_terms( $data['taxonomy'] ,array('hide_empty' => false));
?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($terms)){ ?>
            <select class="form-control form-control-admin min-width-500" name="traveler_booking_<?php echo esc_html($data['id']) ?>">
                <?php echo '<option value="">-- ' . __( 'Choose One', 'traveler-booking' ) . ' --</option>'; ?>
                <?php foreach($terms as $k=>$v){ ?>
                    <option <?php if($value == $v->term_id) echo "selected"; ?> value="<?php echo esc_attr($v->term_id) ?>"><?php echo esc_html($v->name) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
