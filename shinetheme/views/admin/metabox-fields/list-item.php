<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true);
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

?>

<tr id="traveler-list-item_<?php echo esc_html( $data['id'] ); ?>" class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<th scope="row">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</th>
	<td>
		<?php 
			if( !empty( $data['value'] ) && is_array( $data['value'] ) ):
		?>
		<div class="traveler-list-item-wrapper">
			<div class="traveler-list">

			</div>
			<button class="traveler-add-item btn button button-primary" type="button"><?php echo __('Add item', 'traveler-booking'); ?></button>
		</div>
		<?php endif; ?>
		<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</td>
</tr>
<?php 
	if( !empty( $data['value'] ) && is_array( $data['value'] ) ):
?>
<tr id="traveler-list-item-draft" style="display: none !important;">
	<td>
		<div class="traveler-list-item">
			<div class="list-item-head">
				
				<span class="dashicons dashicons-menu"></span>
			
				<div class="item-title"></div>
					
				<div class="button-control">
					<a title="Edit" class="button button-primary btn_list_item_edit" href="#">
		                <span class="fa fa-pencil"></span>
		            </a>
		            <a title="Delete" class="button button-secondary light right-item btn_list_item_del" href="#">
		                <span class="fa fa-trash-o"></span>
		            </a>
				</div>
			</div>
			<table>	
				<tr>
					<td class="td-left" colspan="3">
						<table>
							<tr>
								<th class="title  traveler-form-group"><?php echo __('Title', 'traveler-booking'); ?></th>
								<td>
									<input type="text" class="widefat form-control input-title" name="<?php echo esc_html( $data['id'] ); ?>[][title][]" value="">
								</td>
							</tr>
							<?php foreach( $data['value'] as $key => $item ):
								if( $item['type'] == 'tab' ){
									unset( $data['value'][ $key ] );
									continue;
								}
								$custom_name = esc_html( $data['id'] ). '[]' . '[' . esc_html( $item['id'] ) . '][]';
								$default = array(
									'id'       => '',
									'label'    => '',
									'type'     => '',
									'desc'     => '',
									'std'      => '',
									'class'    => '',
									'location' => FALSE,
									'map_lat' => '',
									'map_long' => '',
									'map_zoom' => 13,
									'custom_name' => $custom_name
								);

								$item = wp_parse_args( $item , $default );

								$file = 'metabox-fields/' . $item['type'];

								echo traveler_admin_load_view( $file, array( 'data' => $item ) );

								unset( $data['value'][ $key ] );
							?>
								
							<?php endforeach; ?>	
						</table>
					</td>
				</tr>
			</table>	
		</div>	
	</td>	
</tr>
<?php endif; ?>