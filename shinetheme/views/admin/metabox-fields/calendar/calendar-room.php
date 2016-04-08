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

<div class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<label for="<?php echo esc_html( $data['id'] ); ?>"><strong><?php echo esc_html( $data['label'] ); ?></strong></label>
	<div class="st-metabox-content-wrapper">
		<div class="form-group">
			<div class="traveler-calendar-wrapper" data-post-id="<?php echo get_the_ID(); ?>" data-post-encrypt="<?php echo traveler_encrypt( get_the_ID() ); ?>">
				<div class="traveler-calendar-content">
					<div class="overlay">
						<span class="spinner is-active"></span>
					</div>
					<div class="calendar-room">
						
					</div>
				</div>
				<div class="traveler-calendar-sidebar">
					<div class="form-container calendar-room-form">
						<div class="" style="margin-bottom: 10px;">
							<label class="calendar-label" for="calendar-checkin"><?php echo __('From', 'traveler-booking'); ?></label>
							<input class="calendar-input date-picker" type="text" id="calendar-checkin" name="calendar-checkin" value="" readonly="readonly" placeholder="<?php echo __('From Date','traveler-booking'); ?>">
						</div>
						<div class="" style="margin-bottom: 10px;">
							<label class="calendar-label" for="calendar-checkout"><?php echo __('To', 'traveler-booking'); ?></label>
							<input class="calendar-input date-picker" type="text" id="calendar-checkout" name="calendar-checkout" value="" readonly="readonly" placeholder="<?php echo __('To Date','traveler-booking'); ?>">
						</div>
						<div class="" style="margin-bottom: 10px;">
							<label class="calendar-label" for="calendar-price"><?php echo __('Price', 'traveler-booking'); ?></label>
							<input class="calendar-input" type="text" id="calendar-price" name="calendar-price" value="" placeholder="<?php echo __('Price','traveler-booking'); ?>">
						</div>
						<div class="" style="margin-bottom: 10px;">
							<label class="calendar-label" for="calendar-status"><?php echo __('Status', 'traveler-booking'); ?></label>
							<select name="calendar-status" id="calendar-status">
								<option value="available"><?php echo __('Available','traveler-booking'); ?></option>
								<option value="not_available"><?php echo __('Not Available','traveler-booking'); ?></option>
							</select>
						</div>
						<div class="clearfix" style="margin-bottom: 10px;">
							<input type="hidden" id="calendar-post-id" name="post-id" value="<?php echo get_the_ID(); ?>">
							<input type="hidden" id="calendar-post-encrypt" name="calendar-post-encrypt" value="<?php echo traveler_encrypt( get_the_ID() ); ?>">
							<button type="button" id="calendar-save" class="button button-primary button-large"><?php echo __('Save','traveler-booking'); ?></button>
							<button type="button" id="calendar-bulk-edit" class="button button-primary button-large" style="float: right;"><?php echo __('Bulk Edit','traveler-booking'); ?></button>
						</div>
						<div class="" style="margin-bottom: 10px;">
							
						</div>
						<div class="form-message" style="margin-bottom: 10px;">
							
						</div>
					</div>	
					<div id="form-bulk-edit">
						<div class="form-container">
							<div class="overlay">
								<span class="spinner is-active"></span>
							</div>
							<div class="form-title">
								<h3 class="clearfix"><?php echo __('Bulk Price Edit', 'traveler-booking'); ?>
									<button style="float: right;" type="button" id="calendar-bulk-close" class="button button-small"><?php echo __('Close','traveler-booking'); ?></button>
								</h3>
							</div>
							<div class="form-content clearfix">
								<h4 style="margin-bottom: 20px;"><?php echo __('Choose Date:', 'traveler-booking'); ?></h4>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="day-of-week"> <?php echo __('Days Of Week', 'traveler-booking'); ?></h4>
									</div>
									<div class="form-content">
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Sunday" style="margin-right: 5px;"><?php echo __('Sunday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Monday" style="margin-right: 5px;"><?php echo __('Monday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Tuesday" style="margin-right: 5px;"><?php echo __('Tuesday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Wednesday" style="margin-right: 5px;"><?php echo __('Wednesday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Thursday" style="margin-right: 5px;"><?php echo __('Thursday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Friday" style="margin-right: 5px;"><?php echo __('Friday', 'traveler-booking'); ?></label>
										<label class="block"><input type="checkbox" name="day-of-week[]" value="Saturday" style="margin-right: 5px;"><?php echo __('Saturday', 'traveler-booking'); ?></label>
									</div>
								</div>
								<div class="form-group">
									<div class="form-title">
										<h4 class=""><input type="checkbox" class="check-all" data-name="day-of-month"> <?php echo __('Days Of Month', 'traveler-booking'); ?></h4>
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
										<h4 class=""><input type="checkbox" class="check-all" data-name="months"> <?php echo __('Months', 'traveler-booking'); ?></h4>
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
										<h4 class=""><input type="checkbox" class="check-all" data-name="years"> <?php echo __('Years', 'traveler-booking'); ?></h4>
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
								<label class="block"><span><strong><?php echo __('Price', 'traveler-booking'); ?>: </strong></span><input type="text" value="" name="price-bulk" id="price-bulk" placeholder="<?php echo __('Price', 'traveler-booking'); ?>"></label>
								<input type="hidden" name="post-id" value="<?php echo get_the_ID(); ?>">
								<input type="hidden" name="post-encrypt" value="<?php echo traveler_encrypt( get_the_ID() ); ?>">
								<div class="form-message" style="margin-top: 20px;"></div>
							</div>
							<div class="form-footer">
								<button type="button" id="calendar-bulk-save" class="button button-primary button-large"><?php echo __('Save','traveler-booking'); ?></button>
								<button type="button" id="calendar-bulk-cancel" class="button button-large"><?php echo __('Cancel','traveler-booking'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
	<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>