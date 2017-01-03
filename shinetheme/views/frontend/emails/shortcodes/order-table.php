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
                <?php do_action('wpbooking_email_order_after_address',$order_data) ?>
                <?php do_action('wpbooking_email_order_after_address_'.$service_type,$order_data) ?>
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
                                    <?php  esc_html_e("City Tax",'wpbookng'); ?>
                                </span>
                    <span class="total-amount"><?php echo WPBooking_Currency::format_money($tax['citytax']['price']); ?></span>
                <?php } ?>

                <?php do_action("wpbooking_after_email_detail_total_price",$order_id,$order_data) ?>

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
