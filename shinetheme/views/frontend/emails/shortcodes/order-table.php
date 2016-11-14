<?php
$order_id=WPBooking()->get('order_id');
$order=new WB_Order($order_id);
$booking=WPBooking_Order::inst();
$order_data=$order->get_order_data();
$service_type = $order_data['service_type'];
?>

<?php
$post_id=$order_data['post_id'];
$service=new WB_Service($order_data['post_id']);
$featured=$service->get_featured_image();
$service_type=$order_data['service_type'];
?>
<table class=service_info>
    <tr>
        <td colspan=4 class=content-row>
            <div class=col-3>
                <a href="<?php echo get_permalink($post_id)?>" target="_blank">
                    <?php echo wp_kses($featured['thumb'],array('img'=>array('src'=>array(),'alt'=>array())))?>
                </a>
            </div>
            <div class=col-7>
                <h3> <a href="<?php echo get_permalink($order_data['post_id'])?>" target="_blank"><?php echo get_the_title($order_data['post_id'])?></a> </h3>
                <h4><?php
                    if($address=$service->get_address()){
                       echo esc_html($address);
                    } ?>
                </h4>
                <h4 class=color_black>
                    <span class=bold><?php esc_html_e("From:","wpbooking") ?> </span> <?php echo date(get_option('date_format'),$order_data['check_in_timestamp']) ?>
                    <span class=bold><?php esc_html_e("To:","wpbooking") ?> </span><?php echo date(get_option('date_format'),$order_data['check_out_timestamp']) ?>
                    <?php
                    $diff=$order_data['check_out_timestamp'] - $order_data['check_in_timestamp'];
                    $diff = $diff / (60 * 60 * 24);
                    if($diff > 1){
                        echo sprintf(esc_html__('(%s nights)','wpbooking'),$diff);
                    }else{
                        echo sprintf(esc_html__('(%s night)','wpbooking'),$diff);
                    }
                    ?>
                </h4>
            </div>
        </td>
    </tr>

    <?php do_action('wpbooking_email_detail_item_information',$order_data) ?>
    <?php do_action('wpbooking_email_detail_item_information_'.$service_type,$order_data) ?>

    <tr>
        <td colspan=4 class="text-right content-row">
            <div class="content-total">
                <br>
                <?php do_action('wpbooking_order_detail_total_item_information_'.$service_type,$order_data) ?>
                <?php
                $tax = unserialize($order_data['tax']);
                if (!empty($tax['vat']['excluded']) and $tax['vat']['excluded'] != 'no' and $tax['vat']['price']>0) {
                    $vat_amount = $tax['vat']['amount']."% ";
                    $unit = $tax['vat']['unit'];
                    if($unit == 'fixed') $vat_amount = '';
                    ?>
                    <span class="total-title">
                                    <?php  echo sprintf(esc_html__("%s V.A.T",'wpbooking'),$vat_amount); ?>
                                </span>
                    <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['vat']['price']); ?></span>
                <?php } ?>
                <?php if (!empty($tax['citytax']['excluded']) and $tax['citytax']['excluded'] != 'no' and $tax['citytax']['price']>0) {
                    ?>
                    <span class="total-title">
                                    <?php  esc_html_e("City Tax",'wpbookng'); ?>
                                </span>
                    <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
                <?php } ?>
            </div>

        </td>
    </tr>
    <tr>
        <td colspan=4 class="text-right content-row">
            <div class="content-total">
                <br>
                <?php $price_total = $order_data['price']; ?>
                <span class="total-title bold"><?php _e('TOTAL AMOUNT', 'wpbooking') ?></span>
                <span class="total-amount bold"><?php echo WPBooking_Currency::format_money($price_total); ?></span>
                <?php
                if(!empty($order_data['deposit_price'])){
                    $price_deposit = $order_data['deposit_price'];
                    $property = $price_total - $price_deposit;
                    ?>
                    <span class="total-title color"> <?php _e('Deposit/Pay Now', 'wpbooking') ?></span>
                    <span class="total-amount color"><?php echo WPBooking_Currency::format_money($price_deposit); ?></span>
                    <span class="total-title bold"><?php _e('You’ll pay at the property', 'wpbooking') ?></span>
                    <span class="total-amount bold"><?php echo WPBooking_Currency::format_money($property); ?></span>
                <?php } ?>
            </div>

        </td>
    </tr>
</table>
