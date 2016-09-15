<?php $is_tab = WPBooking_Input::request('wp_step','wp_general');
?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php esc_html_e("Payment Setup","wpbooking") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <tr class="wpbooking_gateway_bank_transfer_enable wpbooking-form-group  ">
                        <th scope="row">
                            <label for="gateway_bank_transfer_enable"><?php esc_html_e("Bank Transfer:","wpbooking") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" value="1" name="wpbooking_gateway_bank_transfer_enable" checked="" class="form-control min-width-500" id="wpbooking_gateway_bank_transfer_enable">
                                <?php esc_html_e("Yes, I want to enable Bank Transfer","wpbooking") ?>
                            </label>
                            <i class="wpbooking-desc"></i>
                        </td>
                    </tr>
                    <tr class="wpbooking_gateway_paypal_enable wpbooking-form-group  ">
                        <th scope="row">
                            <label for="gateway_paypal_enable"><?php esc_html_e("PayPal:","wpbooking") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" value="1" name="wpbooking_gateway_paypal_enable" class="form-control min-width-500" id="wpbooking_gateway_paypal_enable">
                                <?php esc_html_e("Yes, I want to enable PayPal","wpbooking") ?>		</label>
                            <i class="wpbooking-desc"></i>
                        </td>
                    </tr>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="control">
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php esc_html_e("Finish","wpbooking") ?></button>
        </div>
    </div>
</form>
