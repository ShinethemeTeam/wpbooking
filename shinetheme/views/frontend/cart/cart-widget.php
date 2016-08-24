<?php
/**
 * Created by PhpStorm.
 * User: me664
 * Date: 4/26/16
 * Time: 5:15 PM
 */
$booking=WPBooking_Order::inst();
$cart=$booking->get_cart();
?>
<div class="wpbooking-cart-widget-wrap">
	<?php
	if ( ! empty( $instance['title'] ) ) {
		echo do_shortcode($args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title']);
	}
	?>

	<div class="wpbooking-cart-widget-content">
	<?php
	if(empty($cart)){
		printf('<p class="alert alert-warning">%s</p>',__('Your cart is currently empty','wpbooking'));
	}else{
		$i=0;
		foreach($cart as $key=>$value)
		{
			$i++;
			$service_type=$value['service_type'];
			$post_id=$value['post_id'];
			$service=new WB_Service($value['post_id']);
			$featured=$service->get_featured_image();
			$class=FALSE;
			if($i==4){
				printf('<span class="show_more_review_order"><span class="more">%s</span><span class="less">%s</span></span>',esc_html__('More','wpbooking'),esc_html__('Less','wpbooking'));
			}
			if($i>3){
				$class.=' is_more_order';
			}
			?>
			<div class="review-order-item <?php echo esc_attr($class) ?>">
				<div class="review-order-item-img">
					<a href="<?php echo get_permalink($post_id)?>" target="_blank"><?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?></a>
				</div>
				<div class="review-order-item-info">
					<h4 class="service-name"><a href="<?php echo get_permalink($value['post_id'])?>" target="_blank"><?php echo get_the_title($value['post_id'])?></a></h4>
					<?php if($address=$service->get_address()){
						printf('<p class="service-address">%s</p>',$address);
					} ?>
					<p class="review-order-item-price"><?php echo ($booking->get_cart_item_total_html($value)) ?></p>
					<?php do_action('wpbooking_review_order_item_information',$value) ?>
					<?php do_action('wpbooking_review_order_item_information_'.$service_type,$value) ?>
				</div>
				<a class="delete-cart-item" onclick="return confirm('<?php esc_html_e('Do you want to delete it?','wpbooking') ?>')" href="<?php echo esc_url(add_query_arg(array('delete_cart_item'=>$key),$booking->get_checkout_url())) ?>">
					<i class="fa fa-times"></i>
				</a>
			</div>
			<?php
		}
	}
	?>
	</div>
	<?php if(!empty($cart)){ ?>
		<p class="cart-total">
			<span><?php esc_html_e('Cart Total:','wpbooking') ?></span>
			<span class="total-amount"><?php echo WPBooking_Currency::format_money(WPBooking_Order::inst()->get_cart_total()) ?></span>
		</p>
		<p class="cart-actions">
			<a href="<?php echo esc_url($booking->get_cart_url()) ?>" class="wb-btn wb-btn-default"><?php esc_html_e('Cart','wpbooking')?></a>
			<a href="<?php echo esc_url($booking->get_checkout_url()) ?>" class="wb-btn wb-btn-blue wb-to-checkout"><?php esc_html_e('Check Out','wpbooking')?></a>
		</p>
	<?php } ?>
</div>