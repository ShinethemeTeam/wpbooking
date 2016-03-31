<?php
$class = "traveler-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?>">
    <div class="traveler-build-group ">
        <label class="control-label"><?php echo balanceTags($data['title']) ?>:</label>
        <textarea class="item" data-type="textarea" data-name-shortcode="<?php echo esc_attr($parent) ?>" name="<?php echo balanceTags($data['name']) ?>" id="<?php echo balanceTags($data['name']) ?>"><?php echo balanceTags($data['value']) ?></textarea>
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>