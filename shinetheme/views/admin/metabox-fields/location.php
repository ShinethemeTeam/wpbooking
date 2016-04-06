<?php 
/**
*@since 1.0.0
**/

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';

?>
<div class="form-table traveler-settings ">
<div class="st-metabox-left <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<div class="st-metabox-content-wrapper">
		<div class="form-group">
			<div class="traveler-select-loction">
	            <input placeholder="<?php echo __('Type to search', 'traveler-booking'); ?>" type="text" class="widefat form-control" name="search" value="">
	            <div class="list-location-wrapper">
	                <?php
	               	 	$html_location = array();
	                    if(is_array($html_location) && count($html_location)):
	                        foreach($html_location as $key => $location):
	                ?>
	                    <div data-name="<?php echo $location['parent_name']; ?>" class="item" style="margin-left: <?php echo $location['level'].'px;'; ?> margin-bottom: 5px;">
	                        <label for="<?php echo 'location-'.$location['ID']; ?>">
	                            <input <?php if(in_array('_'.$location['ID'].'_', $multi_location)) echo 'checked'; ?> id="<?php echo 'location-'.$location['ID']; ?>" type="checkbox" name="<?php echo esc_attr( $field_name ); ?>[]" value="<?php echo '_'.$location['ID'].'_'; ?>">
	                            <span><?php echo $location['post_title']; ?></span>
	                        </label>
	                    </div>
	                <?php  endforeach; endif; ?>
	            </div>
	        </div>
		</div>
	</div>
	<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>