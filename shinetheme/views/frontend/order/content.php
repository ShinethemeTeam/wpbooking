<?php
$order_id = get_the_ID();
echo wpbooking_get_message();
$order=new WB_Order($order_id);
$booking=WPBooking_Order::inst();
$order_data=$order->get_order_data();
$service_type = $order_data['service_type'];
$checkout_form_data=WPBooking_Checkout_Controller::inst()->get_billing_form_fields();
do_action('wpbooking_before_order_content');
?>
<div class="wpbooking-order-detail-page">
    <div class="wpbooking-title">

        <?php
        $check_order = WPBooking_Order::inst()->_handling_check_meta_order_show();
        if($check_order == 'hide'){ ?>
            <?php esc_html_e('Booking Details','wpbooking'); ?>
        <?php }else{ ?>
            <?php esc_html_e('Success Booking','wpbooking'); ?>
        <?php } ?>

    </div>
    <?php if($check_order == 'show'){ ?>
        <div class="wpbooking-thankyou-message">
            <i class="fa fa-check-circle"></i>
            <?php
            if($customer_name=$order->get_customer('name')){
                printf(esc_html__('%s, Congratulation! Your booking has been successful! Below is booking details.','wpbooking'),$customer_name);
            }else{
                esc_html_e('Thank you, your order has been received!','wpbooking');
            }
            ?>
        </div>
    <?php } ?>
	<div class="order-head-info wpbooking-bootstrap">
        <div class="row">
            <div class="col-md-6">
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Booking code:','wpbooking')?></span>
                    <span class="head-info-content hl">#<?php the_ID() ?></span>
                </div>
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Method of Payment:','wpbooking')?></span>
                    <span class="head-info-content "><span class="bold"><?php echo esc_html($order->get_payment_gateway()) ?></span></span>
                </div>
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Booking status:','wpbooking')?></span>
                    <span class="head-info-content booking-status"><?php echo ($order->get_status_html()) ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="head-info total">
                    <span class="head-info-title"><?php  esc_html_e('Total','wpbooking')?></span>
                    <span class="head-info-content"><?php echo WPBooking_Currency::format_money($order->get_total(array('without_deposit'=>false))) ?></span>
                </div>
            </div>
        </div>
	</div>
	<?php do_action('wpbooking_before_order_information_table',$order) ?>
    <div class="order-information-content">
        <?php
        $post_id=$order_data['post_id'];
        $service=new WB_Service($order_data['post_id']);
        $featured=$service->get_featured_image();
        $service_type=$order_data['service_type'];
        ?>
            <div class="title">
                <?php esc_html_e("your booking information","wpbooking") ?>
            </div>
            <div class="review-order-item">
                <div class="review-order-item-info">
                    <div class="review-order-item-img">
                        <a href="<?php echo get_permalink($post_id)?>" target="_blank">
                            <?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
                        </a>
                    </div>
                    <div class="review-order-item-title">
                        <h4 class="service-name">
                            <a href="<?php echo get_permalink($order_data['post_id'])?>" target="_blank"><?php echo get_the_title($order_data['post_id'])?></a>
                        </h4>
                        <div class="wb-hotel-star">
                            <?php
                            $service->get_star_rating_html();
                            ?>
                        </div>
                        <?php if($address=$service->get_address()){
                            printf('<p class="service-address"><i class="fa fa-map-marker"></i> %s</p>',$address);
                        } ?>

                        <?php do_action('wpbooking_order_detail_after_address',$order_data) ?>
                        <?php do_action('wpbooking_order_detail_after_address_'.$service_type,$order_data) ?>
                    </div>
                </div>
                <?php do_action('wpbooking_order_detail_item_information',$order_data) ?>
                <?php do_action('wpbooking_order_detail_item_information_'.$service_type,$order_data) ?>
                <div class="total-info-order">
                    <div class="review-cart-total">
                        <div class="review-cart-item">
                            <?php do_action('wpbooking_order_detail_total_item_information_'.$service_type,$order_data) ?>
                            <?php
                            $tax = unserialize($order_data['tax']);
                            if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != 'no' and !empty($tax['vat']['price'])) {
                                $vat_amount = $tax['vat']['amount']."% ";
                                $unit = $tax['vat']['unit'];
                                if($unit == 'fixed') $vat_amount = '';
                                ?>
                                <span class="total-title">
                                    <?php  echo sprintf(esc_html__("%s V.A.T",'wpbooking'),$vat_amount); ?>
                                </span>
                                <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
                            <?php } ?>
                            <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != 'no' and !empty($tax['citytax']['price'])) {
                                ?>
                                <span class="total-title">
                                    <?php  esc_html_e("City Tax",'wpbooking'); ?>
                                </span>
                                <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
                            <?php } ?>

                        </div>
                        <?php do_action("wpbooking_after_order_detail_total_price",$order_id,$order_data) ?>
                        <?php if(!empty($tax['total_price'])) echo '<span class="total-line"></span>' ?>
                        <div class="review-cart-item total">
                            <?php $price_total = $order_data['price']; ?>
                            <span class="total-title text-up text-bold"><?php _e('Total Amount', 'wpbooking') ?></span>
                            <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money($price_total); ?></span>
                            <?php
                            if(!empty($order_data['deposit_price']) and $order_data['deposit_price']<$price_total){
                                $price_deposit = $order_data['deposit_price'];
                                $property = $price_total - $price_deposit;
                                ?>
                                <span class="total-title text-color"> <?php _e('Deposit/Pay Now', 'wpbooking') ?></span>
                                <span class="total-amount text-color"><?php echo WPBooking_Currency::format_money($price_deposit); ?></span>
                                <span class="total-title text-bold"><?php _e('You’ll pay at the property', 'wpbooking') ?></span>
                                <span class="total-amount text-bold"><?php echo WPBooking_Currency::format_money($property); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <?php do_action('wpbooking_after_order_information_table',$order_id) ?>
</div>
