<?php
$booking=WPBooking_Checkout_Controller::inst();
$gateway=WPBooking_Payment_Gateways::inst();

$all=$gateway->get_available_gateways();
$pay_amount=$booking->get_total_price_cart_with_tax();
?>
<ul class="wpbooking-all-gateways">
	<?php if(!empty($all))
	{
		foreach($all as $key=>$value)
		{
			if($pay_amount == 0 ){
				if(method_exists($value,'get_name_submit_form') and  $value->get_name_submit_form() == $key) {
					?>
					<li class="wpbooking-gateway-item">
						<div class="gateway-content">

							<label>
								<input type="radio" name="payment_gateway" value="<?php echo esc_attr( $key ) ?>">
								<span><?php echo esc_attr( $value->get_option( 'title' ) , $value->get_info( 'label' ) ) ?></span>
							</label>
							<?php if(!empty( $value->get_option( 'desc' ) )) { ?>
								<div class="gateway-desc gateway-id-<?php echo esc_attr( $key ) ?>">
									<?php echo do_shortcode( $value->get_option( 'desc' ) );
									do_action( 'wpbooking_gateway_desc' , $key , $value );
									do_action( 'wpbooking_gateway_desc_' . $key , $value );
									?>
								</div>
							<?php } ?>
						</div>
					</li>
					<?php
				}
			}else{
				?>
				<li class="wpbooking-gateway-item">
					<div class="gateway-content">

						<label>
							<input type="radio" name="payment_gateway" value="<?php echo esc_attr($key)?>" >
							<span><?php echo esc_attr($value->get_option('title'),$value->get_info('label')) ?></span>
						</label>
						<?php if(!empty($value->get_option('desc'))){ ?>
							<div class="gateway-desc gateway-id-<?php echo esc_attr($key) ?>">
								<?php echo do_shortcode($value->get_option('desc'));
								do_action('wpbooking_gateway_desc',$key,$value);
								do_action('wpbooking_gateway_desc_'.$key,$value);
								?>
							</div>
						<?php } ?>
					</div>
				</li>
				<?php
			}
		}
	}
	?>
</ul>