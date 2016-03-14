<div class="form-row">
    <label for="" class="form-label"><?php echo esc_html($v['label']) ?></label>
    <div class="controls">
        <?php if(!empty($v['value'])){ ?>
            <?php $data_value = st_membership()->get_option($v['id'],$v['std']);?>
            <?php foreach($v['value'] as $key=>$value){
                $is_check = "";
                  if(!empty($data_value) and is_array($data_value)){
                      foreach($data_value as $key2=>$value2){
                          if($value2 == $key){
                              $is_check = "checked";
                          }
                      }
                  }

                ?>
                <input type="checkbox" class="form-control" <?php echo esc_html($is_check) ?>   name="st_settings_membership[<?php echo esc_html($v['id'])?>][]" value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?><br>
            <?php } ?>
        <?php } ?>
    </div>
</div>