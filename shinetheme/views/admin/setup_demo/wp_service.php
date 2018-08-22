<?php $is_tab = WPBooking_Input::request('wp_step','wp_general'); ?>
<form method="post">
    <?php wp_nonce_field('wpbooking_action','wpbooking_save_setup_demo') ?>
    <input type="hidden" name="is_tab" value="<?php echo esc_attr($is_tab) ?>">
    <div class="setup-content">
        <h1 class="text-center"><?php echo esc_html__("Service Setup","wp-booking-management-system") ?></h1>
        <div class="item_setup <?php echo esc_attr($is_tab) ?>">
            <table class="form-table wpbooking-settings ">
                <tbody>
                    <tr class="wpbooking-setting-service_type_accommodation_review wpbooking-form-group">
                    <th scope="row">
                        <label for="service_type_accommodation_review"><?php echo esc_html__("Review:","wp-booking-management-system") ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <ul class="padding-0">
                                <li class="">
                                    <label>
                                        <input type="checkbox" name="wpbooking_service_type_accommodation_enable_review" checked="" class="form-control min-width-500">
                                        <?php echo esc_html__("Enable Review","wp-booking-management-system") ?>                          </label>
                                </li>
                                <li class="">
                                    <label>
                                        <input type="checkbox" name="wpbooking_service_type_accommodation_review_without_booking" checked="" class="form-control min-width-500">
                                        <?php echo esc_html__("Allow user to review without booking","wp-booking-management-system") ?>                           </label>
                                </li>
                                <li class="">
                                    <label>
                                        <input type="checkbox" name="wpbooking_service_type_accommodation_show_rate_review_button" class="form-control min-width-500">
                                        <?php echo esc_html__("Show Rate (Help-full) button in each review?","wp-booking-management-system") ?>                         </label>
                                </li>
                                <li class="">
                                    <label>
                                        <input type="checkbox" name="wpbooking_service_type_accommodation_allowed_review_on_own_listing" checked="" class="form-control min-width-500">
                                        <?php echo esc_html__("User can write review on their own listing?","wp-booking-management-system") ?>                            </label>
                                </li>
                                <li class="">
                                    <label>
                                        <input type="checkbox" name="wpbooking_service_type_accommodation_allowed_vote_for_own_review" class="form-control min-width-500">
                                        <?php echo esc_html__("User can vote for their own review?","wp-booking-management-system") ?>                         </label>
                                </li>
                            </ul>
                        </fieldset>
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
