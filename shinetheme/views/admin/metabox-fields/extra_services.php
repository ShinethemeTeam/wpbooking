<?php
/**
 *@since 1.0.0
 **/

$service_types=WPBooking_Service_Controller::inst()->get_service_types();
if(!empty($service_types)){
	foreach($service_types as $type_id=>$type){
		$class=FALSE;
		$class.=$type_id;

		?>
		<div class="form-table wpbooking-settings  wpbooking-form-group wpbooking_extra_service_type wpbooking-condition <?php echo esc_html( $class ); ?>" data-condition="service_type:is(<?php echo esc_attr($type_id) ?>)">
			<div class="st-metabox-left">
				<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
			</div>
			<div class="st-metabox-right">
				<div class="st-metabox-content-wrapper">
					<div class="form-group">
						<div class="list-extra-services">
							<?php
							$old=get_post_meta(get_the_ID(),$data['id'],true);
							if(isset($old[$type_id])) $old=$old[$type_id]; else $old=array();


							$extras=$type['object']->get_extra_services();
							if(!empty($extras)){
								foreach($extras as $k=>$value){
									$checked=FALSE;
									$current=FALSE;
									$is_required=FALSE;
									if(!empty($old[$k])){
										if($old[$k]['is_selected']) {
											$checked='checked';
											if($old[$k]['require']=='yes') $is_required=TRUE;
										}
									}
								?>
									<div class="extra-item">
										<label class="title" ><input type="checkbox" value="<?php echo esc_html($value['title']) ?>" <?php echo esc_attr($checked) ?> name="<?php echo esc_attr($data['id'].'['.$type_id.']['.$k.'][is_selected]') ?>">
										<?php echo esc_html($value['title']) ?></label>
										<div class="money-number">
											<div class="input-group ">
												<span class="input-group-addon" ><?php echo WPBooking_Currency::get_current_currency('title').' '.WPBooking_Currency::get_current_currency('symbol') ?></span>
												<input type="text" class="form-control" value="<?php echo (!empty($current['money']))?$current['money']:FALSE; ?>" name="<?php echo esc_attr($data['id'].'['.$type_id.']['.$k.'][money]') ?>"  >
											</div>
										</div>
										<div class="require-options">
											<select name="<?php echo esc_attr($data['id'].'['.$type_id.']['.$k.'][require]') ?>" >
												<option value="no"><?php esc_html_e('No','wpbooking') ?></option>
												<option <?php echo ($is_required)?'selected':false; ?> value="yes"><?php esc_html_e('Yes','wpbooking') ?></option>
											</select>
											<span class="help_inline"><?php esc_html_e('Required','wpbooking') ?></span>
										</div>
									</div>
								<?php
								}

							}?>
						</div>
						<div class="add-new-extra-service">
							<div class="hidden extra-item-default ">
								<div class="extra-item">
									<label class="title" ><input type="checkbox" value=""  name="">
										<span class="extra-item-name"></span></label>
									<div class="money-number">
										<div class="input-group ">
											<span class="input-group-addon" ><?php echo WPBooking_Currency::get_current_currency('title').' '.WPBooking_Currency::get_current_currency('symbol') ?></span>
											<input type="text" class="form-control" value="" name=""  >
										</div>
									</div>
									<div class="require-options">
										<select name="" >
											<option value="no"><?php esc_html_e('No','wpbooking') ?></option>
											<option value="yes"><?php esc_html_e('Yes','wpbooking') ?></option>
										</select>
										<span class="help_inline"><?php esc_html_e('Required') ?></span>
									</div>
								</div>
							</div>
							<input type="text" class="service-name form-control" placeholder="<?php esc_html_e('Extra Service Name','wpbooking') ?>">
							<a href="#" onclick="return false" class="button wb-btn-add-extra-service" data-id="<?php echo esc_attr($data['id']) ?>" data-type
							="<?php echo esc_attr($type_id) ?>" ><?php esc_html_e('Add New','wpbooking') ?> <i class="fa fa-spin  fa-spinner loading-icon"></i></a>
						</div>
					</div>
				</div>
				<div class="metabox-help"><?php echo balanceTags( $data['desc'] ) ?></div>
			</div>
		</div>
		<?php
	}
}
