<?php
$class = "traveler-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?> ">
    <div class="traveler-build-group ">
        <label class="control-label"><?php echo balanceTags($data['title']) ?>:</label>
        <div class="traveler-row group-checkbox">
            <?php if(!empty($data['options'])){
                foreach($data['options'] as $k=>$v){
                    echo ' <div class="traveler-col-md-4"><label><input type="checkbox" class="item_check_box" value="'.$v.'">'.$k.'</label></div>';
                }
            } ?>
        </div>
        <input type="hidden" class="item" data-name-shortcode="<?php echo esc_attr($parent) ?>" name="<?php echo balanceTags($data['name']) ?>" id="<?php echo balanceTags($data['name']) ?>">
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>