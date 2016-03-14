<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php
        $value = st_membership()->get_option($v['id'],$v['std'])
        ?>
        <textarea id="<?php echo esc_html($v['id']) ?>" name="st_settings_membership[<?php echo esc_html($v['id']) ?>]" class="form-control form-control-admin"><?php echo esc_html($value) ?></textarea>
    </div>
</div>