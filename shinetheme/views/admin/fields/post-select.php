<?php
$value = traveler_get_option($data['id'],$data['std']);
$my_posts = get_posts( array( 'post_type' => array( 'post' ), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'any' ) );

?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <?php if(!empty($my_posts)){ ?>
            <select class="form-control form-control-admin min-width-500" name="traveler_booking_<?php echo esc_html($data['id']) ?>">
                <?php echo '<option value="">-- ' . __( 'Choose One', 'traveler-booking' ) . ' --</option>'; ?>
                <?php foreach($my_posts as $k=>$v){ ?>
                    <option <?php if($value == $v->ID) echo "selected"; ?> value="<?php echo esc_attr($v->ID) ?>"><?php echo esc_html($v->post_title) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
