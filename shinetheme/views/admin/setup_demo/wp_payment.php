<?php $is_tab = WPBooking_Input::request('wp_step','wp_general');
?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php esc_html_e("Service Setup","wpbooking") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <h3><?php esc_html_e("Service Room","wpbooking") ?></h3>
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <tr class="wpbooking-setting-service_type_room_review wpbooking-form-group">
                    <th scope="row">
                        <label for="service_type_room_review"><?php esc_html_e("Review:","wpbooking") ?></label>
                    </th>
                    <td>
                        <table class="form-table wpbooking-settings ">
                            <tbody>
                            <tr class="wpbooking_gateway_bank_transfer_enable wpbooking-form-group  ">
                                <th scope="row">
                                    <label for="gateway_bank_transfer_enable">Enable:</label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" value="1" name="wpbooking_gateway_bank_transfer_enable" checked="" class="form-control min-width-500" id="wpbooking_gateway_bank_transfer_enable">
                                        Yes, I want to enable Bank Transfer
                                    </label>
                                    <i class="wpbooking-desc"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>


                </tbody>
            </table>

        </div>
        <div class="control">
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php esc_html_e("Continue","wpbooking") ?></button>
        </div>
    </div>
</form>
