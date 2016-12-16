<?php
$class = "wpbooking-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?>">
    <div class="wpbooking-build-group ">
        <label class="control-label"><?php echo esc_attr($data['title']) ?>:</label>
        <select class="item" data-type="dropdown"  data-name-shortcode="<?php echo esc_attr($parent) ?>" name="<?php echo esc_attr($data['name']) ?>" id="<?php echo esc_attr($data['name']) ?>">
            <?php if(!empty($data['options'])){
                    foreach($data['options'] as $k=>$v){
                        echo '<option value="'.esc_attr($k).'">'.esc_html($v).'</option>';
                    }
            } ?>

        </select>
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>