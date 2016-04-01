<?php 
/**
*@since 1.0.0
**/

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
?>

<tr class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<td>
		<label for="<?php echo esc_html( $data['id'] ); ?>"><strong><?php echo esc_html( $data['label'] ); ?></strong></label>
		<div class="st-metabox-content-wrapper">
			<div class="form-group">
				<div class="traveler-calendar-wrapper">
					<div class="traveler-calendar-content">
						<div class="calendar-room">
							
						</div>
					</div>
					<div class="traveler-calendar-sidebar">
						<div class="form-container">
							<form action="">
								<div class="" style="margin-bottom: 10px;">
									<label class="calendar-label" for="calendar-checkin"><?php echo __('From', 'traveler-booking'); ?></label>
									<input class="calendar-input date-picker" type="text" id="calendar-checkin" name="calendar-checkin" value="" readonly="readonly" placeholder="<?php echo __('From Date','traveler-booking'); ?>">
								</div>
								<div class="" style="margin-bottom: 10px;">
									<label class="calendar-label" for="calendar-out"><?php echo __('To', 'traveler-booking'); ?></label>
									<input class="calendar-input date-picker" type="text" id="calendar-out" name="calendar-out" value="" readonly="readonly" placeholder="<?php echo __('To Date','traveler-booking'); ?>">
								</div>
								<div class="" style="margin-bottom: 10px;">
									<label class="calendar-label" for="calendar-price"><?php echo __('To', 'traveler-booking'); ?></label>
									<input class="calendar-input" type="text" id="calendar-price" name="calendar-price" value="" placeholder="<?php echo __('Price','traveler-booking'); ?>">
								</div>
								<div class="" style="margin-bottom: 10px;">
									<label class="calendar-label" for="calendar-status"><?php echo __('Status', 'traveler-booking'); ?></label>
									<select name="calendar-status" id="calendar-status">
										<option value="available"><?php echo __('Available','traveler-booking'); ?></option>
										<option value="not_available"><?php echo __('Not Available','traveler-booking'); ?></option>
									</select>
								</div>
								<div class="" style="margin-bottom: 10px;">
									<input type="submit" name="save_calendar" value="<?php echo __('Save','traveler-booking'); ?>" class="button button-primary button-large">
								</div>
							</form>
						</div>	
					</div>
				</div>
			</div>
		</div>	
		<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</td>
</tr>