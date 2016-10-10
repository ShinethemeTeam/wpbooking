<?php 
/**
*@since 1.0.0
**/

$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$property_available_for=get_post_meta($post_id,'property_available_for',true);
$df_price=get_post_meta($post_id,'price-default',true);

?>

<div class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<label for="<?php echo esc_html( $data['id'] ); ?>"><strong><?php echo esc_html( $data['label'] ); ?></strong></label>
	<div class="st-metabox-content-wrapper">
		<div class="form-group" style="width: 100%;">
			<div class="wpbooking-calendar-wrapper" data-post-id="<?php echo esc_attr($post_id); ?>" data-post-encrypt="<?php echo wpbooking_encrypt( $post_id ); ?>">
				<div class="wpbooking-calendar-content">
					<div class="overlay">
						<span class="spinner is-active"></span>
					</div>
					<div class="calendar-room2 <?php echo ($property_available_for=='specific_periods')?'specific_periods':FALSE ?>">
						
					</div>
					<div class="calendar-room <?php echo ($property_available_for=='specific_periods')?'specific_periods':FALSE ?>">

					</div>
				</div>
				<div class="wpbooking-calendar-sidebar">
					<div class="form-container calendar-price-df-form">
						<h4 class="form-title"><?php echo esc_html__('Base Price','wpbooking') ?></h4>
						<p class="form-desc"><?php echo esc_html__('Default price for room','wpbooking'); ?></p>
						<div class="form-group wb-default-price">
							<div class="input-group">
								<span class="input-group-addon" ><?php echo WPBooking_Currency::get_current_currency('title').' '.WPBooking_Currency::get_current_currency('symbol') ?></span>
								<input type="number" class="form-control" id="price-default" value="<?php echo esc_attr($df_price); ?>" name="price-default" placeholder="0" >
							</div>
						</div>
					</div>
					<div class="form-container calendar-room-form">
						<h4 class="form-title"><?php echo esc_html__('Set price by date arrange','wpbooking') ?></h4>
						<p class="form-desc"><?php echo esc_html__('You can make room for any purpose ( like discount , high price , ... )','wpbooking'); ?></p>
						<div class="calendar-room-form-item full-width" >
							<label class="calendar-label" for="calendar-checkin"><?php echo __('Check In', 'wpbooking'); ?></label>
							<div class="calendar-input-icon">
								<input class="calendar-input date-picker" type="text" id="calendar-checkin" name="calendar-checkin" value="" readonly="readonly" placeholder="<?php echo __('From Date','wpbooking'); ?>">
								<label for="calendar-checkin" class="fa"><i class="fa fa-calendar"></i></label>
							</div>
						</div>
						<div class="calendar-room-form-item full-width" >
							<label class="calendar-label" for="calendar-checkout"><?php echo __('Check Out', 'wpbooking'); ?></label>
							<div class="calendar-input-icon">
								<input class="calendar-input date-picker" type="text" id="calendar-checkout" name="calendar-checkout" value="" readonly="readonly" placeholder="<?php echo __('To Date','wpbooking'); ?>">
								<label for="calendar-checkout" class="fa"><i class="fa fa-calendar"></i></label>
							</div>
						</div>
						<div class="calendar-room-form-item full-width" >
							<label class="calendar-label" for="calendar-status"><?php echo __('Status', 'wpbooking'); ?></label>
							<select name="calendar-status" id="calendar-status">
								<option value="available"><?php echo __('Available','wpbooking'); ?></option>
								<option value="not_available"><?php echo __('Not Available','wpbooking'); ?></option>
							</select>
						</div>
						<div class="calendar-room-form-item full-width" style="clear:both" >
							<label class="calendar-label" for="calendar-price"><?php echo __('Nightly Price', 'wpbooking'); ?></label>
							<input class="calendar-input" type="text" id="calendar-price" name="calendar-price" value="" placeholder="<?php echo __('Price','wpbooking'); ?>">
						</div>
						<div class="calendar-room-form-item full-width hidden" >
							<label class="calendar-label" for="calendar-price-week"><?php echo __('Weekly Price', 'wpbooking'); ?></label>
							<input class="calendar-input-week" type="text" id="calendar-price-week" name="calendar-price-week" value="" placeholder="<?php echo __('Price','wpbooking'); ?>">
						</div>
						<div class="calendar-room-form-item full-width hidden" >
							<label class="calendar-label" for="calendar-price-month"><?php echo __('Montly Price', 'wpbooking'); ?></label>
							<input class="calendar-input-month" type="text" id="calendar-price-month" name="calendar-price-month" value="" placeholder="<?php echo __('Price','wpbooking'); ?>">
						</div>
						<div class="calendar-room-form-item full-width " >
							<label class="calendar-label" for="calendar-can-check-in"><input data-style="icheckbox_square-orange" class="wb-icheck calendar-can-check-in" type="checkbox" id="calendar-can-check-in" name="calendar-can-check-in" checked="checked" value="1" > <?php echo __('Can Check In?', 'wpbooking'); ?></label>

						</div>
						<div class="calendar-room-form-item full-width" >
							<label class="calendar-label" for="calendar-can-check-out"><input data-style="icheckbox_square-orange" class="wb-icheck calendar-can-check-out" type="checkbox" id="calendar-can-check-out" name="calendar-can-check-out" checked="checked" value="1" > <?php echo __('Can Check Out?', 'wpbooking'); ?></label>

						</div>
						<div class="clear"></div>
						<div class="clearfix mb10" >
							<input type="hidden" id="calendar-post-id" name="post_id" value="<?php echo esc_attr($post_id); ?>">
							<input type="hidden" id="calendar-post-encrypt" name="calendar-post-encrypt" value="<?php echo wpbooking_encrypt( $post_id ); ?>">
							<button type="button" id="calendar-save" class="button button-large wb-button-primary"><?php echo __('Save','wpbooking'); ?></button>
<!--							<button type="button" id="calendar-bulk-edit" class="button button-large" style="float: right;">--><?php //echo __('Bulk Edit','wpbooking'); ?><!--</button>-->
						</div>
						<div class="" style="margin-bottom: 10px;">
							
						</div>
						<div class="form-message" style="margin-bottom: 10px;">
						</div>
					</div>
					<div class="form-container property-form">

						<div class="calendar-room-form-item full-width" >
							<label class="calendar-label" ><?php echo __('This Property is available:', 'wpbooking'); ?></label>
							<div class="calendar-input-icon mt20">
								<label class="calendar-label wb-radio-input" ><input data-style="iradio_square-orange" class="wb-icheck property_available_forever" type="radio"  name="property_available_for" data-post-id="<?php echo esc_attr($post_id); ?>" value="forever" <?php if($property_available_for=='forever' or !$property_available_for) echo 'checked' ?> > <?php echo __('Forever', 'wpbooking'); ?></label>
								<label class="calendar-label wb-radio-input" ><input data-style="iradio_square-orange" class="wb-icheck property_available_specific" type="radio"  name="property_available_for" data-post-id="<?php echo esc_attr($post_id); ?>" value="specific_periods" <?php if($property_available_for=='specific_periods') echo 'checked' ?> > <?php echo __('For specific periods', 'wpbooking'); ?></label>
							</div>
						</div>
					</div>

					<div class="calendar-help">
						<div class="help-label"><?php esc_html_e('How to set Availability ?','wpbooking') ?></div>
						<ul class="list">
							<li>+ <?php esc_html_e('To set availability on your calendar:','wpbooking') ?>
								<ul>
									<li>- <?php esc_html_e('Click a day in the calendar to set a start date','wpbooking') ?></li>
									<li>- <?php esc_html_e('Click another day to set the end date of the period you want to edit','wpbooking') ?></li>
								</ul>
							</li>
							<li>+ <?php esc_html_e('A right sight table, allowing you to set availbility for that period','wpbooking')?></li>
						</ul>
					</div>
					<div id="form-bulk-edit">
						<div class="form-container">
							<div class="overlay">
								<span class="spinner is-active"></span>
							</div>
							<div class="form-title">
								<h3 class="clearfix"><?php echo __('Bulk Price Edit', 'wpbooking'); ?>
									<button style="float: right;" type="button" id="calendar-bulk-close" class="button button-small"><?php echo __('Close','wpbooking'); ?></button>
								</h3>
							</div>
							<div class="form-content clearfix">
								<h4 style="margin-bottom: 20px;"><?php echo __('Choose Date:', 'wpbooking'); ?></h4>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="day-of-week"> <?php echo __('Days Of Week', 'wpbooking'); ?></h4>
									</div>
									<div class="form-content">
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Sunday" style="margin-right: 5px;"><?php echo __('Sunday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Monday" style="margin-right: 5px;"><?php echo __('Monday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Tuesday" style="margin-right: 5px;"><?php echo __('Tuesday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Wednesday" style="margin-right: 5px;"><?php echo __('Wednesday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Thursday" style="margin-right: 5px;"><?php echo __('Thursday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Friday" style="margin-right: 5px;"><?php echo __('Friday', 'wpbooking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Saturday" style="margin-right: 5px;"><?php echo __('Saturday', 'wpbooking'); ?></label>
									</div>
								</div>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="day-of-month"> <?php echo __('Days Of Month', 'wpbooking'); ?></h4>
									</div>
									<div class="form-content">
									<?php for( $i = 1; $i <= 31; $i ++):
										if( $i == 1){
											echo '<div>';
										}
									?>
										<label style="width: 40px;"><input type="checkbox" name="day-of-month[]" value="<?php echo $i; ?>" style="margin-right: 5px;"><?php echo $i; ?></label>
					
									<?php 
										if( $i != 1 && $i % 5 == 0 ) echo '</div><div>';
										if( $i == 31 ) echo '</div>';
									?>
						
									<?php endfor; ?>
									</div>
								</div>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="months"> <?php echo __('Months', 'wpbooking'); ?>(*)</h4>
									</div>
									<div class="form-content">
									<?php 
									$months = array(
										'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
									);
									foreach( $months as $key => $month ):
										if( $key == 0 ){
											echo '<div>';
										}
									?>
										<label style="width: 100px;"><input type="checkbox" name="months[]" value="<?php echo $month; ?>" style="margin-right: 5px;"><?php echo $month; ?></label>
					
									<?php 
										if( $key != 0 && ($key + 1) % 2 == 0 ) echo '</div><div>';
										if( $key + 1 == count( $months ) ) echo '</div>';
									?>
						
									<?php endforeach; ?>
									</div>
								</div>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="years"> <?php echo __('Years', 'wpbooking'); ?>(*)</h4>
									</div>
									<div class="form-content">
									<?php 
									$year = date('Y');
									$j = $year -1 ;
									for( $i = $year; $i <= $year + 13; $i ++ ):
										if( $i == $year ){
											echo '<div>';
										}
									?>
										<label style="width: 100px;"><input type="checkbox" name="years[]" value="<?php echo $i; ?>" style="margin-right: 5px;"><?php echo $i; ?></label>
					
									<?php 
										if( $i != $year && ($i == $j + 2 ) ) { echo '</div><div>'; $j = $i; }
										if( $i == $year + 13 ) echo '</div>';
									?>
						
									<?php endfor; ?>
									</div>
								</div>
							</div>
							<div class="form-content clearfix">
								<label class="block"><span><strong><?php echo __('Price', 'wpbooking'); ?>: </strong></span><input type="text" value="" name="price-bulk" id="price-bulk" placeholder="<?php echo __('Price', 'wpbooking'); ?>"></label>
								<input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
								<input type="hidden" name="post-encrypt" value="<?php echo wpbooking_encrypt( $post_id ); ?>">
								<div class="form-message" style="margin-top: 20px;"></div>
							</div>
							<div class="form-footer">
								<button type="button" id="calendar-bulk-save" class="button button-primary button-large"><?php echo __('Save','wpbooking'); ?></button><!-- 
								<button type="button" id="calendar-bulk-cancel" class="button button-large"><?php echo __('Cancel','wpbooking'); ?></button> -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>