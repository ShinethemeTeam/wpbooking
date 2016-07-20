<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( $post_id, esc_html( $data['id'] ), true);
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div id="wpbooking-list-item_<?php echo esc_html( $data['id'] ); ?>"  class="st-metabox-left">
	<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
</div>
<div class="st-metabox-right">
	<div class="st-metabox-content-wrapper">
	<?php 
		if( !empty( $data['value'] ) && is_array( $data['value'] ) ):
	?>
	<div class="wpbooking-list-item-wrapper">
		<div class="wpbooking-list">
		<?php 
			$conver_data = get_post_meta( $post_id, esc_html( $data['id'] ), true );
			
			if( !empty( $conver_data ) && is_array( $conver_data) ):
				foreach( $conver_data as $convert_key => $convert_val ):
			?>
			<div class="wpbooking-list-item">
				<div class="list-item-head">
					
					<span class="dashicons dashicons-menu"></span>
				
					<div class="item-title"><?php echo esc_html( $convert_val['title'] ); ?></div>
						
					<div class="button-control">
						<a title="<?php esc_html_e('Edit','wpbooking') ?>" class=" btn_list_item_edit" href="#">
			                <span class="fa fa-pencil"></span>
			            </a>
			            <a title="<?php esc_attr_e('Delete','wpbooking')?>" class="light right-item btn_list_item_del" href="#">
			                <span class="fa fa-trash-o"></span>
			            </a>
					</div>
				</div>
				<table class="hidden">	
					<tr>
						<td class="td-left" colspan="3">
							<div class="form-table wpbooking-settings ">
								<div class="st-metabox-left  wpbooking-form-group  title  wpbooking-form-group">
									<?php echo __('Title', 'wpbooking'); ?>
								</div>
								<div class="st-metabox-right">
									<input type="text" class="widefat form-control input-title" name="<?php echo esc_html( $data['id'] ); ?>[title][]" value="<?php echo esc_html( $convert_val['title'] ); ?>">
								</div>
							</div>
								<?php foreach( $data['value'] as $key => $item ):
									if( $item['type'] == 'tab' ){
										continue;
									}

									$custom_name = esc_html( $data['id'] ) . '[' . esc_html( $item['id'] ) . '][]';

									$custom_data = ( isset( $convert_val[ $item['id'] ] ) ) ? esc_html( $convert_val[ $item['id'] ] ) : false;

									$default = array(
										'id'          => '',
										'label'       => '',
										'type'        => '',
										'desc'        => '',
										'std'         => '',
										'class'       => '',
										'location'    => FALSE,
										'map_lat'     => '',
										'map_long'    => '',
										'map_zoom'    => 13,
										'custom_name' => $custom_name,
										'custom_data' => $custom_data,
										'width'=>FALSE
									);

									$item['id'] = esc_html( $data['id'] ) . '_' . esc_html( $item['id'] );

									$item = wp_parse_args( $item , $default );

									$file = 'metabox-fields/' . $item['type'];

									echo wpbooking_admin_load_view( $file, array( 'data' => $item,'is_sub_item'=>true ) );
								?>
									
								<?php endforeach; ?>	
							</table>
						</td>
					</tr>
				</table>	
			</div>
		<?php endforeach; endif; ?>
		</div>
		<div class="text-right">
			<button class="wpbooking-add-item btn button button-default" type="button"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<?php endif; ?>
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</div>
</div>
</div>
<?php 
	if( !empty( $data['value'] ) && is_array( $data['value'] ) ):
?>
<div id="wpbooking-list-item-draft" style="display: none !important;">
	<div class="wpbooking-list-item">
		<div class="list-item-head">
			
<!--			<span class="dashicons dashicons-menu"></span>-->

			<div class="item-title"></div>
				
			<div class="button-control">
				<a title="Edit" class=" btn_list_item_edit" href="#">
	                <span class="fa fa-pencil"></span>
	            </a>
	            <a title="Delete" class="light right-item btn_list_item_del" href="#">
	                <span class="fa fa-trash-o"></span>
	            </a>
			</div>
		</div>
		<table>	
			<tr>
				<td class="td-left" colspan="3">
					<div class="form-table wpbooking-settings  wpbooking-form-group ">
						<div class="st-metabox-left">
							<label><?php echo __('Title', 'wpbooking'); ?></label>
						</div>	
						<div class="st-metabox-right">
							<div class="st-metabox-content-wrapper">
								<div class="form-group">
									<div class="" style="margin-bottom: 7px;">
										<input type="text" class="widefat form-control input-title" name="<?php echo esc_html( $data['id'] ); ?>[title][]" value="">
									</div>
								</div>
							</div>
						</div>
					</div>	
					<?php foreach( $data['value'] as $key => $item ):
						if( $item['type'] == 'tab' ){
							unset( $data['value'][ $key ] );
							continue;
						}
						$custom_name = esc_html( $data['id'] ) . '[' . esc_html( $item['id'] ) . '][]';

						$default = array(
							'id'          => '',
							'label'       => '',
							'type'        => '',
							'desc'        => '',
							'std'         => '',
							'class'       => '',
							'location'    => FALSE,
							'map_lat'     => '',
							'map_long'    => '',
							'map_zoom'    => 13,
							'custom_name' => $custom_name,
							'width'=>FALSE
						);

						$item = wp_parse_args( $item , $default );

						$file = 'metabox-fields/' . $item['type'];

						echo wpbooking_admin_load_view( $file, array( 'data' => $item ) );

						unset( $data['value'][ $key ] );
					?>
						
					<?php endforeach; ?>
				</td>
			</tr>
		</table>	
	</div>	
</div>
<?php endif; ?>