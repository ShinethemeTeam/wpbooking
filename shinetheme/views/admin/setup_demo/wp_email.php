<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php esc_html_e("Email Setup","wpbooking") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <h3><?php esc_html_e("Booking Email","wpbooking") ?></h3>
            <table class="form-table wpbooking-settings ">
                <tbody>
                <tr class="wpbooking_email_from wpbooking-form-group  ">
                    <th scope="row">
                        <label for="wpbooking_email_from"><?php esc_html_e("Email From Name:","wpbooking") ?></label>
                    </th>
                    <td>
                        <input type="text" name="wpbooking_email_from" value="<?php echo esc_html_e("WPBooking Plugin","wpbooking") ?>" class="form-control  min-width-500">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_email_from_address wpbooking-form-group  ">
                    <th scope="row">
                        <label for="wpbooking_email_from_address"><?php esc_html_e("Email From Address:","wpbooking") ?></label>
                    </th>
                    <td>
                        <?php $email  = get_option("admin_email"); ?>
                        <input type="text" placeholder="no-reply@domain.com" name="wpbooking_email_from_address" value="<?php echo esc_attr($email) ?>" class="form-control  min-width-500" id="wpbooking_email_from_address">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_system_email">
                    <th scope="row">
                        <label for="system_email"><?php esc_html_e("Email  System to get Booking, Registration Notifications...etc:","wpbooking") ?></label>
                    </th>
                    <td>
                        <input id="wpbooking_system_email" class="form-control  min-width-500" value="" name="wpbooking_system_email" placeholder="system@domain.com"  type="text">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_on_booking_email_customer wpbooking-form-group  ">
                    <th scope="row">
                        <label for="on_booking_email_customer"><?php esc_html_e("Enable Email To Customer:","wpbooking") ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" value="1" checked="" name="wpbooking_on_booking_email_customer" class="form-control min-width-500" id="wpbooking_on_booking_email_customer">
                            <?php esc_html_e("Enable Email To Customer","wpbooking") ?>
                        </label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_on_booking_email_admin wpbooking-form-group  ">
                    <th scope="row">
                        <label for="on_booking_email_admin"><?php esc_html_e("Enable Email To Admin:","wpbooking") ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" value="1" checked="" name="wpbooking_on_booking_email_admin" class="form-control min-width-500" id="wpbooking_on_booking_email_admin">
                            <?php esc_html_e("Enable Email To Admin","wpbooking") ?>		</label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                </tbody>
            </table>
            <h3><?php esc_html_e("Customer Registration","wpbooking") ?></h3>
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <tr class="wpbooking_on_registration_email_customer wpbooking-form-group  ">
                        <th scope="row">
                            <label for="on_registration_email_customer"><?php esc_html_e("Enable Email To Customer:","wpbooking") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" value="1" checked="" name="wpbooking_on_registration_email_customer" class="form-control min-width-500" id="wpbooking_on_registration_email_customer">
                                <?php esc_html_e("Enable Email To Customer","wpbooking") ?>		</label>
                            <i class="wpbooking-desc"></i>
                        </td>
                    </tr>
                    <tr class="wpbooking_on_registration_email_admin wpbooking-form-group  ">
                        <th scope="row">
                            <label for="on_registration_email_admin"><?php esc_html_e("Enable Email To Admin:","wpbooking") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="wpbooking_on_registration_email_admin" class="form-control min-width-500" checked="" name="wpbooking_on_registration_email_admin" value="1">
                                <?php esc_html_e("Enable Email To Admin","wpbooking") ?>		</label>
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
