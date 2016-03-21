<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true);
if( !empty( $value ) ){
	$old_data = $value;
}

$class = $data['id'];
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition traveler-form-group ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

$field = '<div class="form-group">';

echo '<div class="'.esc_html($class).'" '.esc_attr($data_class).'>';

if( !empty( $data['label'] ) )
	echo '<div class="form-label"><label for="'.esc_html( $data['id'] ).'">'. esc_html( $data['label'] ) .'</label></div>';

$field .= '<div style="margin-bottom: 7px;"><input type="text" name="'. esc_html( $data['id'] ).'" value="' .esc_html( $old_data ).'" class="widefat form-control '. esc_html( $data['class'] ).'"></div>';

$field .= '</div>';
if( !empty( $data['desc'] ) ): ?>
<div class="st-metabox-content-wrapper">
	<div class="st-metabox-content-left">
		<?php echo $field;  ?>
	</div>
	<div class="st-metabox-content-right">
		<div class="description"><?php echo esc_html( $data['desc'] ); ?></div>
	</div>
</div>
<?php else: ?>
	<div class="st-metabox-content-wrapper">;
		<?php echo $field; ?> 
	</div>;	
<?php endif; ?>
</div>