<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php
        $value = st_membership()->get_option($v['id'],$v['std'])
        ?>
        <input type="text" class="form-control form-control-admin" value="<?php echo esc_html($value) ?>" name="st_settings_membership[<?php echo esc_html($v['id']) ?>]" placeholder="<?php echo esc_html($v['label']) ?>">
    </div>
</div>