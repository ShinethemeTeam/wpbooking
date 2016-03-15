<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <textarea id="<?php echo esc_html($data['id']) ?>" name="st_traveler_booking_settings[<?php echo esc_html($data['id']) ?>]" class="form-control form-control-admin min-width-300"><?php echo esc_html($value) ?></textarea>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>

</tr>