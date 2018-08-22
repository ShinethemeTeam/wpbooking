<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php echo esc_html__("Email Setup","wp-booking-management-system") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <h3><?php echo esc_html__("Booking Email","wp-booking-management-system") ?></h3>
            <table class="form-table wpbooking-settings ">
                <tbody>
                <tr class="wpbooking_email_from wpbooking-form-group  ">
                    <th scope="row">
                        <label for="wpbooking_email_from"><?php echo esc_html__("Email From Name:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <input type="text" name="wpbooking_email_from" value="<?php echo esc_html__("WPBooking Plugin","wp-booking-management-system") ?>" class="form-control  min-width-500">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_email_from_address wpbooking-form-group  ">
                    <th scope="row">
                        <label for="wpbooking_email_from_address"><?php echo esc_html__("Email From Address:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <?php $email  = get_option("admin_email"); ?>
                        <input type="text" placeholder="no-reply@domain.com" name="wpbooking_email_from_address" value="<?php echo esc_attr($email) ?>" class="form-control  min-width-500" id="wpbooking_email_from_address">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_system_email">
                    <th scope="row">
                        <label for="system_email"><?php echo esc_html__("Email  System to get Booking, Registration Notifications...etc:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <input id="wpbooking_system_email" class="form-control  min-width-500" value="" name="wpbooking_system_email" placeholder="system@domain.com"  type="text">
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_on_booking_email_customer wpbooking-form-group  ">
                    <th scope="row">
                        <label for="on_booking_email_customer"><?php echo esc_html__("Enable Email To Customer:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" value="1" checked="" name="wpbooking_on_booking_email_customer" class="form-control min-width-500" id="wpbooking_on_booking_email_customer">
                            <?php echo esc_html__("Enable Email To Customer","wp-booking-management-system") ?>
                        </label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                <tr class="wpbooking_on_booking_email_admin wpbooking-form-group  ">
                    <th scope="row">
                        <label for="on_booking_email_admin"><?php echo esc_html__("Enable Email To Admin:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" value="1" checked="" name="wpbooking_on_booking_email_admin" class="form-control min-width-500" id="wpbooking_on_booking_email_admin">
                            <?php echo esc_html__("Enable Email To Admin","wp-booking-management-system") ?>		</label>
                        <i class="wpbooking-desc"></i>
                    </td>
                </tr>
                </tbody>
            </table>
            <h3><?php echo esc_html__("Customer Registration","wp-booking-management-system") ?></h3>
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <tr class="wpbooking_on_registration_email_customer wpbooking-form-group  ">
                        <th scope="row">
                            <label for="on_registration_email_customer"><?php echo esc_html__("Enable Email To Customer:","wp-booking-management-system") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" value="1" checked="" name="wpbooking_on_registration_email_customer" class="form-control min-width-500" id="wpbooking_on_registration_email_customer">
                                <?php echo esc_html__("Enable Email To Customer","wp-booking-management-system") ?>		</label>
                            <i class="wpbooking-desc"></i>
                        </td>
                    </tr>
                    <tr class="wpbooking_on_registration_email_admin wpbooking-form-group  ">
                        <th scope="row">
                            <label for="on_registration_email_admin"><?php echo esc_html__("Enable Email To Admin:","wp-booking-management-system") ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="wpbooking_on_registration_email_admin" class="form-control min-width-500" checked="" name="wpbooking_on_registration_email_admin" value="1">
                                <?php echo esc_html__("Enable Email To Admin","wp-booking-management-system") ?>		</label>
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
