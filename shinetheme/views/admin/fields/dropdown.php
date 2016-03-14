<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php if(!empty($v['value'])){ ?>
            <?php
            $data_value = st_membership()->get_option($v['id'],$v['std']);
            ?>
            <select class="form-control form-control-admin" name="st_settings_membership[<?php echo esc_html($v['id']) ?>]">
                <?php foreach($v['value'] as $key=>$value){ ?>
                    <option <?php if($data_value == $key) echo "selected"; ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                <?php } ?>
            </select>
        <?php } ?>
    </div>
</div>