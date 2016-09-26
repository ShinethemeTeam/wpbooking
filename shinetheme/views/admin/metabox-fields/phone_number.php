<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

if(!empty($data['custom_name'])){
	if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
	$old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$class.=' width-'.$data['width'];
if(!empty($data['container_class'])) $class.=' '.$data['container_class'];

$field = '';

$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );
$phone_code=get_post_meta(get_the_ID(),'phone_code',true);

?>

<div class="wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<div class="form-group">
                <div class="input-group small">
                    <span class="input-group-addon addon-button">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <?php $codes=WB_Helpers::get_phone_country_code();
                            if(!empty($codes)){
                                foreach($codes as $k){
                                    printf('<li data-code="%s"><i class="flag-icon flag-icon-%s"></i> %s</li>',$k['code'],$k['flag'],$k['code']);
                                }
                            }
                            ?>
                        </ul>
                    </span>
                    <input type="hidden" name="phone_code" value="<?php echo esc_html($phone_code) ?>">
                    <input type="text" class="form-control" id="<?php echo esc_attr($data['id'])?>" value="<?php echo esc_attr($old_data) ?>" name="<?php echo esc_attr($name)?>">
                </div>
				<?php
				if(!empty($data['help_inline'])){
					printf('<span class="help_inline">%s</span>',$data['help_inline']);
				}
				?>

			</div>
		</div>
		<div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
	</div>
</div>