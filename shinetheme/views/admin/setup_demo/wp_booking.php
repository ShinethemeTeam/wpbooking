<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php esc_html_e("Booking Setup","wpbooking") ?></h1>
        <div class="item_setup <?php echo esc_html($is_tab) ?>">
            <table class="form-table wpbooking-settings">
                <tbody>
                <tr class="">
                    <th scope="row">
                        <label for=""><?php esc_html_e("Allow Guest Checkout?:","wpbooking") ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" value="1" name="wpbooking_allow_guest_checkout" class="form-control min-width-500" id="wpbooking_allow_guest_checkout">
                       <?php esc_html_e(" Allow Guest Checkout?","wpbooking") ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="control">
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php esc_html_e("Save & Continue","wpbooking") ?></button>
        </div>
    </div>
</form>