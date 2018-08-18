<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php echo esc_html__("Booking Setup","wp-booking-management-system") ?></h1>
        <div class="item_setup <?php echo esc_html($is_tab) ?>">
            <table class="form-table wpbooking-settings">
                <tbody>
                <tr class="">
                    <th scope="row">
                        <label for=""><?php echo esc_html__("Allow Guest to Checkout?","wp-booking-management-system") ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" value="1" name="wpbooking_allow_guest_checkout" class="form-control min-width-500" id="wpbooking_allow_guest_checkout">
                       <?php echo esc_html__("Allow Guest to Checkout?","wp-booking-management-system") ?>
                    </td>
                </tr>
                <tr class="wpbooking_">
                    <th scope="row" colspan="2">
                        <h3 class="margin_0"><?php echo esc_html__("Captcha Google","wp-booking-management-system") ?></h3>
                    </th>
                </tr>
                <tr class="wpbooking_allow_captcha_google_checkout wpbooking-form-group  ">
                    <th scope="row">
                        <label for="allow_captcha_google_checkout"><?php echo esc_html__("Allow Captcha Google to be Checked out?:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <label>
                            <input id="wpbooking_allow_captcha_google_checkout" class="form-control min-width-500" name="wpbooking_allow_captcha_google_checkout" value="1" type="checkbox">
                            <?php echo esc_html__("Allow Captcha Google to be Checked out?","wp-booking-management-system") ?>
                        </label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_google_key_captcha">
                    <th scope="row">
                        <label for="google_key_captcha"><?php echo esc_html__("Google key","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <input id="wpbooking_google_key_captcha" class="form-control  min-width-500" value="" name="wpbooking_google_key_captcha"  type="text">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_google_secret_key_captcha">
                    <th scope="row">
                        <label for="google_secret_key_captcha"><?php echo esc_html__("Google secret key","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <input id="wpbooking_google_secret_key_captcha" class="form-control  min-width-500" name="wpbooking_google_secret_key_captcha"  type="text">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="control">
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php echo esc_html__("Save & Continue","wp-booking-management-system") ?></button>
        </div>
    </div>
</form>