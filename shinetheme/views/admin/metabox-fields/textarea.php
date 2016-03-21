<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );
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

$field .= '<div style="margin-bottom: 7px;"><textarea rows="10" id="'. esc_html( $data['id'] ).'" name="'. esc_html( $data['id'] ).'" class=" form-control widefat '. esc_html( $data['class'] ).'">'. esc_html( $old_data ).'</textarea></div>';

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