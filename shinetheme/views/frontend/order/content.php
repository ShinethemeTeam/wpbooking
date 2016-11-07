<?php
echo wpbooking_get_message();
$order_id = get_the_ID();
$order=new WB_Order($order_id);
$booking=WPBooking_Order::inst();
$order_data=$order->get_order_data();
$service_type = $order_data['service_type'];
$checkout_form_data=WPBooking_Checkout_Controller::inst()->get_billing_form_fields();
do_action('wpbooking_before_order_content');
?>
<div class="wpbooking-order-detail-page">
    <div class="wpbooking-title">
        <?php esc_html_e('Success Booking','wpbooking'); ?>
    </div>
	<div class="wpbooking-thankyou-message">
            <i class="fa fa-check-circle"></i>
		<?php
		if($customer_name=$order->get_customer('name')){
			printf(esc_html__('%s, your order has been received!','wpbooking'),$customer_name);
		}else{
			esc_html_e('Thank you, your order has been received!','wpbooking');
		}
		?>
	</div>
	<div class="order-head-info wpbooking-bootstrap">
        <div class="row">
            <div class="col-md-9">
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Booking code :','wpbooking')?></span>
                    <span class="head-info-content hl">#<?php the_ID() ?></span>
                </div>
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Payment method:','wpbooking')?></span>
                    <span class="head-info-content "><span class="bold"><?php echo esc_html($order->get_payment_gateway()) ?></span></span>
                </div>
                <div class="head-info">
                    <span class="head-info-title"><?php  esc_html_e('Booking Status:','wpbooking')?></span>
                    <span class="head-info-content"><?php echo ($order->get_status_html()) ?></span>
                </div>
            </div>
            <div class="col-md-3">
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
                        <h4 class="service-name"><a href="<?php echo get_permalink($order_data['post_id'])?>" target="_blank"><?php echo get_the_title($order_data['post_id'])?></a></h4>
                        <?php if($address=$service->get_address()){
                            printf('<p class="service-address"><i class="fa fa-map-marker"></i> %s</p>',$address);
                        } ?>
                        <p class="review-order-item-price"></p>
                        <div class="review-order-item-form-to">
                            <span><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date(get_option('date_format'),$order_data['check_in_timestamp']) ?> &nbsp
                            <span><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date(get_option('date_format'),$order_data['check_out_timestamp']) ?> &nbsp
                            <?php
                            $diff=$order_data['check_out_timestamp'] - $order_data['check_in_timestamp'];
                            $diff = $diff / (60 * 60 * 24);
                            if($diff > 1){
                                echo sprintf(esc_html__('(%s days)','wpbooking'),$diff);
                            }else{
                                echo sprintf(esc_html__('(%s day)','wpbooking'),$diff);
                            }
                            ?>
                        </div>
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
                            if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != 'no') {
                                $vat_amount = $tax['vat']['amount']."% ";
                                $unit = $tax['vat']['unit'];
                                if($unit == 'fixed') $vat_amount = '';
                                ?>
                                <span class="total-title">
                                    <?php  echo sprintf(esc_html__("%s V.A.T",'wpbooking'),$vat_amount); ?>
                                </span>
                                <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
                            <?php } ?>
                            <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != 'no') {
                                ?>
                                <span class="total-title">
                                    <?php  esc_html_e("City Tax",'wpbookng'); ?>
                                </span>
                                <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
                            <?php } ?>

                        </div>
                        <span class="total-line"></span>
                        <div class="review-cart-item total">
                            <?php $price_total = $order_data['price']; ?>
                            <span class="total-title text-up text-bold"><?php _e('Total Amount', 'wpbooking') ?></span>
                            <span class="total-amount text-up text-bold"><?php echo WPBooking_Currency::format_money($price_total); ?></span>
                            <?php
                            if(!empty($order_data['deposit_price'])){
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
    <?php do_action('wpbooking_after_order_information_table',$order) ?>
    <div class="order-information-content customer wpbooking-bootstrap">
        <div class="title">
            <?php esc_html_e("Customer Information:","wpbooking") ?>
        </div>
        <div class="row">
            <?php
            $fist_name = get_post_meta($order_id,'wpbooking_user_first_name',true);
            $last_name = get_post_meta($order_id,'wpbooking_user_last_name',true);
            $full_name = $fist_name.' '.$last_name;
            if(!empty($full_name)){?>
                <div class="col-md-12">
                    <label><?php esc_html_e("Full name:","wpbooking") ?> </label>
                    <p><?php echo esc_html($full_name) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($email = get_post_meta($order_id,'wpbooking_user_email',true))){ ?>
                <div class="col-md-6">
                    <label><?php esc_html_e("Email confirmation:","wpbooking") ?> </label>
                    <p><?php echo esc_html($email) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($phone = get_post_meta($order_id,'wpbooking_user_phone',true))){ ?>
                <div class="col-md-6">
                    <label><?php esc_html_e("Telephone:","wpbooking") ?> </label>
                    <p><?php echo esc_html($phone) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($address = get_post_meta($order_id,'wpbooking_user_address',true))){ ?>
                <div class="col-md-12">
                    <label><?php esc_html_e("Address:","wpbooking") ?> </label>
                    <p><?php echo esc_html($address) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($postcode_zip = get_post_meta($order_id,'wpbooking_user_postcode',true))){ ?>
                <div class="col-md-6">
                    <label><?php esc_html_e("Postcode / Zip:","wpbooking") ?> </label>
                    <p><?php echo esc_html($postcode_zip) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($apt_unit = get_post_meta($order_id,'wpbooking_user_apt_unit',true))){ ?>
                <div class="col-md-6">
                    <label><?php esc_html_e("Apt/ Unit:","wpbooking") ?> </label>
                    <p><?php echo esc_html($apt_unit) ?></p>
                </div>
            <?php } ?>
            <?php if(!empty($special_request = get_post_meta($order_id,'wpbooking_user_special_request',true))){ ?>
                <div class="col-md-12">
                    <label><?php esc_html_e("Special request:","wpbooking") ?> </label>
                    <p><?php echo esc_html($apt_unit) ?></p>
                </div>
            <?php } ?>

            <?php do_action('wpbooking_order_detail_customer_information',$order_data) ?>
            <?php do_action('wpbooking_order_detail_customer_information_'.$service_type,$order_data) ?>

            <div class="col-md-12 text-center">
                <?php
                $page_account = wpbooking_get_option('myaccount-page');
                if(!empty($page_account)){
                    $link_page = get_permalink($page_account);
                    ?>
                    <a href="<?php echo esc_url($link_page) ?>tab/booking_history/" class="wb-button wb-btn wb-btn-primary wb-history"><?php esc_html_e("Booking History","wpbooking") ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
