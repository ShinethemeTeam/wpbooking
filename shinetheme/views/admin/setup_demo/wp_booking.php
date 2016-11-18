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
                        <label for=""><?php esc_html_e("Allow Guest Checkout?","wpbooking") ?>:</label>
                    </th>
                    <td>
                        <input type="checkbox" value="1" name="wpbooking_allow_guest_checkout" class="form-control min-width-500" id="wpbooking_allow_guest_checkout">
                       <?php esc_html_e(" Allow Guest Checkout?","wpbooking") ?>
                    </td>
                </tr>
                <tr class="wpbooking_">
                    <th scope="row" colspan="2">
                        <h3 class="margin_0"><?php esc_html_e("Captcha Google","wpbooking") ?></h3>
                    </th>
                </tr>
                <tr class="wpbooking_allow_captcha_google_checkout wpbooking-form-group  ">
                    <th scope="row">
                        <label for="allow_captcha_google_checkout"><?php esc_html_e("Allow Captcha Google Checkout?:","wpbooking") ?></label>
                    </th>
                    <td>
                        <label>
                            <input id="wpbooking_allow_captcha_google_checkout" class="form-control min-width-500" name="wpbooking_allow_captcha_google_checkout" value="1" type="checkbox">
                            <?php esc_html_e("Allow Captcha Google Checkout?","wpbooking") ?>
                        </label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_google_key_captcha">
                    <th scope="row">
                        <label for="google_key_captcha"><?php esc_html_e("Google key","wpbooking") ?></label>
                    </th>
                    <td>
                        <input id="wpbooking_google_key_captcha" class="form-control  min-width-500" value="" name="wpbooking_google_key_captcha"  type="text">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_google_secret_key_captcha">
                    <th scope="row">
                        <label for="google_secret_key_captcha"><?php esc_html_e("Google secret key","wpbooking") ?></label>
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
            <button name="wpbooking_save_setup" value="true" class="button-primary button button-large"><?php esc_html_e("Save & Continue","wpbooking") ?></button>
        </div>
    </div>
</form>