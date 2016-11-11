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
</table>
