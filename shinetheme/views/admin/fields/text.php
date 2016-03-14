<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php
        $value = Traveler_Admin_Setting::inst()->get_option($data['id'],$data['std'])
        ?>
        <input type="text" class="form-control form-control-admin" value="<?php echo esc_html($value) ?>" name="st_traveler_booking_settings[<?php echo esc_html($data['id']) ?>]" placeholder="<?php echo esc_html($data['label']) ?>">
    </div>
</div>