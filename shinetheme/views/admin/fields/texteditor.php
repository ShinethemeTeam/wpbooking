<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <div class="min-width-500 width-800">
            <?php wp_editor(stripslashes($value),"traveler_booking_".$data['id']); ?>
        </div>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>