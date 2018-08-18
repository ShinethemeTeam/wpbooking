<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php echo esc_html__("Payment Setup","wp-booking-management-system") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <table class="form-table wpbooking-settings ">
                <tbody>
                <?php
                $gateway=WPBooking_Payment_Gateways::inst();
                $all=$gateway->get_gateways();
                ?>
                <?php if(!empty($all))
                {
                    foreach($all as $key=>$value)
                    {
                        ?>
                        <tr class="wpbooking_gateway_<?php echo esc_attr($key)?>_enable wpbooking-form-group  ">
                            <th scope="row">
                                <label for="gateway_<?php echo esc_attr($key)?>_enable"><?php echo esc_attr($value->get_info('label')) ?>:</label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" value="1" name="wpbooking_gateway_<?php echo esc_attr($key)?>_enable" checked="" class="form-control min-width-500" id="wpbooking_gateway_<?php echo esc_attr($key)?>_enable">
                                    <?php echo sprintf(esc_html__("Yes, I want to enable %s","wp-booking-management-system"),$value->get_info('label')) ?>
                                </label>
                                <i class="wpbooking-desc"></i>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="control">
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php echo esc_html__("Finish","wp-booking-management-system") ?></button>
        </div>
    </div>
</form>
