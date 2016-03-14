<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php
        $value = st_membership()->get_option($v['id'],$v['std'])
        ?>
        <?php wp_editor(stripslashes($value),"st_settings_membership"); ?>
    </div>
</div>
