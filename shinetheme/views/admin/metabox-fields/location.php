<?php 
/**
*@since 1.0.0
* Location
**/

$locations = get_terms( 'wpbooking_location', array(
	'hide_empty' => false,
) );
$lists = array();
wpbooking_show_tree_terms( $locations, $lists );

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] ). '[]';

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<div class="st-metabox-content-wrapper">
		<div class="form-group">
			<div class="wpbooking-select-loction">
	            <input placeholder="<?php echo __('Type to search', 'wpbooking'); ?>" type="text" class="widefat form-control" name="search" value="">
	            <div class="list-location-wrapper">
	                <?php
	                	/*	Old data */
	                	$old = array();
	                	$old_terms = wp_get_post_terms( $post_id, 'wpbooking_location' );
	                	if( !empty( $old_terms ) && is_array( $old_terms ) ){
	                		foreach( $old_terms as $term ){
	                			$old[] = (int) $term->term_id;
	                		}
	                	}
	                    if( is_array( $lists ) && count( $lists) ):
	                        foreach( $lists as $key => $location ):
	                ?>
	                    <div data-name="<?php echo strtolower($location['parent_name']); ?>" class="item" style="margin-left: <?php echo $location['deep'].'px;'; ?> margin-bottom: 5px;">
	                        <label for="<?php echo 'location-'.$location['id']; ?>">
	                            <input <?php if(in_array($location['id'], $old)) echo 'checked'; ?> id="<?php echo 'location-'.$location['id']; ?>" type="checkbox" name="<?php echo $name; ?>" value="<?php echo $location['id']; ?>">
	                            <span><?php echo $location['name']; ?></span>
	                        </label>
	                    </div>
	                <?php  endforeach; endif; ?>
	            </div>
	        </div>
		</div>
	</div>
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>