<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="text" class="form-control form-control-admin min-width-500" value="<?php echo esc_html($value) ?>" name="traveler_booking_<?php echo esc_html($data['id']) ?>" placeholder="<?php echo esc_html($data['label']) ?>">
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>