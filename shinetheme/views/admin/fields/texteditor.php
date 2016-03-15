<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php wp_editor(stripslashes($value),"st_traveler_booking_settings"); ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>