<?php 
/**
*@since 1.0.0
**/

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );
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

if( is_array( $data['value'] ) && !empty( $data['value'] ) ){

	foreach( $data['value'] as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' checked ';
		}
		if( $old_data && !empty( $old_data ) ){
			if(  esc_html( $key ) == esc_html( $old_data ) ){
				$checked = ' checked ';
			}else{
				$checked = '';
			}
		}
		
		$field .= '<div style="margin-bottom: 7px;"><label for="'.esc_html( $data['id'] ). '_' .esc_html( $key ) . '"><input type="radio" name="'. esc_html( $data['id'] ) .'" id="'. esc_html( $data['id'] ). '_'. esc_html( $key ) . '" class="'. esc_html( $data['class'] ) . '" value="'. esc_html( $key ) .'" ' . $checked .'> <span>'. esc_html( $value ) .'</span></label></div>';
	}
}

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