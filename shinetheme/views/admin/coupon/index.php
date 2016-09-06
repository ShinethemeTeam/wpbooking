<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 8/31/2016
 * Time: 4:36 PM
 */
$item_id = WPBooking_Input::get('item_id');
?>
<div class="wrap">
    <div id="poststuff">
        <div class="postbox">
            <h3 class="hndle ui-sortable-handle"><span><?php esc_html_e('Coupon', 'wpbooking') ?></span></h3>
            <div class="inside">
                <div class="st-metabox-tabs-content">
                    <form action="" class="wb_save_coupon_form" method="post">
                        <?php wp_nonce_field('wpbooking_save_coupon', 'wpbooking_save_coupon'); ?>
                        <input type="hidden" name="action" value="wpbooking_save_coupon">
                        <?php if ($item_id) {
                            printf('<input type="hidden" name="item_id" value="%d">', $item_id);
                        } ?>
                        <div class="st-metabox-tab-content-wrap coupon-metabox">
                            <?php if (WPBooking_Input::post('wpbooking_save_coupon')) {
                                echo wpbooking_get_admin_message();
                            } ?>
                            <div class="wpbooking-field-title">
                                <h4 class="field-title"><?php if ($item_id) printf(esc_html__('Edit Coupon: %s', 'wpbooking'), get_the_title($item_id)); else esc_html_e('Add New Discount Coupon', 'wpbooking'); ?></h4>
                            </div>

                            <div class="form-table wpbooking-settings  wpbooking-form-group  width-">
                                <div class="st-metabox-left">
                                    <label for="coupon_code"><?php esc_html_e('Coupon Code', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="coupon_code" type="text" name="coupon_code"
                                                   placeholder="<?php esc_html_e('Type your code', 'wpbooking') ?>"
                                                   value="<?php $default = ($item_id) ? get_the_title($item_id) : false;

                                                   echo WPBooking_Input::post('coupon_code', $default) ?>"
                                                   class="widefat form-control small">
                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter your coupon code', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->

                            <div
                                class="form-table wpbooking-settings  wpbooking-form-group  width- wb-coupon-services-field">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Services', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <?php $default = ($item_id) ? get_post_meta($item_id, 'coupon_type', true) : 0;
                                            $value = WPBooking_Input::post('coupon_type', $default) ?>
                                            <select name="coupon_type" id="coupon_type" class="form-control">
                                                <option value="all"><?php esc_html_e('All', 'wpbooking') ?></option>
                                                <option <?php selected($value, 'specific_services') ?>
                                                    value="specific_services"><?php esc_html_e('Specific Services', 'wpbooking') ?></option>
                                            </select>
                                            <div class="wb-autocomplete-wrap wpbooking-condition"
                                                 data-condition="coupon_type:is(specific_services)">
                                                <input id="services_ids" type="text" name="services_ids"
                                                       placeholder="<?php esc_html_e('Type to search', 'wpbooking') ?>"
                                                       value="<?php $default = ($item_id) ? get_post_meta($item_id, 'services_ids', true) : false;
                                                       echo WPBooking_Input::post('services_ids', $default) ?>"
                                                       class="widefat form-control wb-autocomplete" data-type="wpbooking_service">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="metabox-help"><?php esc_html_e('Select booking services, where is possible to apply this coupon code.
    ', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->

                            <div class="form-table wpbooking-settings  wpbooking-form-group  width- wb">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Values', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="coupon_value" type="text" name="coupon_value" placeholder=""
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'coupon_value', true) : 0;
                                                   echo WPBooking_Input::post('coupon_value', $default) ?>"
                                                   class="widefat form-control small">
                                            <?php $default = ($item_id) ? get_post_meta($item_id, 'coupon_value_type', true) : 0;
                                            $value = WPBooking_Input::post('coupon_value_type', $default) ?>
                                            <select name="coupon_value_type" class="inline-input form-control"
                                                    id="coupon_value_type">
                                                <option
                                                    value="fixed_amount"><?php esc_html_e('Fixed Amount', 'wpbooking') ?></option>
                                                <option <?php selected($value, 'percentage') ?>
                                                    value="percentage"><?php esc_html_e('Percentage Off', 'wpbooking') ?></option>
                                            </select>

                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter number of fixed or percentage saving', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->

                            <div class="form-table wpbooking-settings  wpbooking-form-group  wb-datetime-field">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Coupon Start Date', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="start_date" type="text" name="start_date"
                                                   placeholder="<?php echo date('m/d/Y') ?>"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'start_date', true) : false;
                                                   echo WPBooking_Input::post('start_date', $default) ?>"
                                                   class="widefat form-control wb-date">
                                            <input id="start_time" type="text" name="start_time"
                                                   placeholder="12:00"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'start_time', true) : false;
                                                   echo WPBooking_Input::post('start_time', $default) ?>"
                                                   class="widefat form-control wb-time">
                                            <?php $default = ($item_id) ? get_post_meta($item_id, 'start_date', true) : 0;
                                            $value = WPBooking_Input::post('start_ampm', $default) ?>
                                            <select class="form-control wb-time" name="start_ampm">
                                                <option value="am"><?php esc_html_e('am', 'wpbooking') ?></option>
                                                <option <?php selected($value, 'pm') ?>
                                                    value="pm"><?php esc_html_e('pm', 'wpbooking') ?></option>
                                            </select>


                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter number of fixed or percentage saving', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->
                            <div class="form-table wpbooking-settings  wpbooking-form-group  wb-datetime-field">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Coupon End Date', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="end_date" type="text" name="end_date"
                                                   placeholder="<?php echo date('m/d/Y') ?>"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'end_date', true) : false;
                                                   echo WPBooking_Input::post('end_date', $default) ?>"
                                                   class="widefat form-control wb-date">
                                            <input id="end_time" type="text" name="start_time"
                                                   placeholder="12:00"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'end_time', true) : false;
                                                   echo WPBooking_Input::post('end_time', $default) ?>"
                                                   class="widefat form-control wb-time">

                                            <?php $default = ($item_id) ? get_post_meta($item_id, 'end_ampm', true) : 0;
                                            $value = WPBooking_Input::post('end_ampm', $default) ?>
                                            <select class="form-control wb-time" name="start_ampm">
                                                <option value="am"><?php esc_html_e('am', 'wpbooking') ?></option>
                                                <option <?php selected($value, 'pm') ?>
                                                    value="pm"><?php esc_html_e('pm', 'wpbooking') ?></option>
                                            </select>

                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter number of fixed or percentage saving', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->


                            <div class="form-table wpbooking-settings  wpbooking-form-group  width-">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Minimum Spend', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="minimum_spend" type="text" name="minimum_spend"
                                                   placeholder="<?php esc_html_e('No minimum', 'wpbooking') ?>"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'minimum_spend', true) : false;
                                                   echo WPBooking_Input::post('minimum_spend', $default) ?>"
                                                   class="widefat form-control small">
                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter your minimum booking spend when coupon is applicable', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->

                            <div class="form-table wpbooking-settings  wpbooking-form-group  width-">
                                <div class="st-metabox-left">
                                    <label><?php esc_html_e('Usage limit', 'wpbooking') ?></label>
                                </div>
                                <div class="st-metabox-right">
                                    <div class="st-metabox-content-wrapper">
                                        <div class="form-group">
                                            <input id="usage_limit" type="text" name="usage_limit"
                                                   placeholder="<?php esc_html_e('Unlimited usage', 'wpbooking') ?>"
                                                   value="<?php $default = ($item_id) ? get_post_meta($item_id, 'usage_limit', true) : false;
                                                   echo WPBooking_Input::post('usage_limit', $default) ?>"
                                                   class="widefat form-control small">
                                        </div>
                                    </div>
                                    <div
                                        class="metabox-help"><?php esc_html_e('Enter maximum number of times, when coupon is applicable', 'wpbooking') ?></div>
                                </div>
                            </div>
                            <!-- End .wpbooking-form-group-->

                            <div class="coupon-submit">
                                <input type="submit" class="button button-primary" name="submit"
                                       value="<?php if ($item_id) esc_html_e('Save Changes', 'wpbooking'); else esc_html_e('Add New Coupon', 'wpbooking'); ?>">
                            </div>
                        </div>
                    </form>

                    <div class="wb_list_coupon">
                        <div class="st-metabox-tab-content-wrap coupon-metabox">
                            <div class="wpbooking-field-title">
                                <h4 class="field-title"><?php esc_html_e('All Discount Coupons', 'wpbooking') ?></h4>
                            </div>
                            <?php
                            $query = new WP_Query(array(
                                'post_type'      => 'wpbooking_coupon',
                                'posts_per_page' => 10,
                                'paged'          => WPBooking_Input::get('page_number', 1)
                            ));
                            if ($query->have_posts()) {
                                ?>
                                <table class="wb-coupon-table" cellpadding="0" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="col-min"><?php esc_html_e('Coupon Code', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('Values', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('Minimum Spend', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('Start Date', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('End Date', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('Usage Limit', 'wpbooking') ?></th>
                                        <th><?php esc_html_e('Services', 'wpbooking') ?></th>
                                        <th class="col-min">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($query->have_posts()) {
                                        $query->the_post();
                                        $coupon = new WB_Coupon();
                                        ?>
                                        <tr>
                                            <td class="col-min">
                                                <a href="<?php echo esc_url($coupon->get_edit_url()) ?>"
                                                   class="edit_url">
                                                    <?php the_title() ?>
                                                </a>
                                            </td>
                                            <td><?php switch ($coupon->get_value_type()) {
                                                    case "percentage":
                                                        echo esc_html($coupon->get_value() . '%');
                                                        break;

                                                    default:
                                                        echo WPBooking_Currency::format_money($coupon->get_value());
                                                        break;
                                                } ?></td>
                                            <td>
                                                <?php echo WPBooking_Currency::format_money(esc_html($coupon->get_meta('minimum_spend'))) ?>
                                            </td>
                                            <td>
                                                <?php if($coupon->get_meta('start_date_timestamp')) echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), (double)$coupon->get_meta('start_date_timestamp')) ?>
                                            </td>
                                            <td>
                                                <?php if($coupon->get_meta('end_date_timestamp')) echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), (double)$coupon->get_meta('end_date_timestamp')) ?>
                                            </td>
                                            <td>
                                                <?php echo esc_html($coupon->get_meta('usage_limit')) ?>
                                            </td>
                                            <td>
                                                <?php $services = $coupon->get_services();
                                                switch ($coupon->get_type()) {
                                                    case "specific_services":
                                                        if (!empty($services)) {
                                                            foreach ($services as $key => $value) {
                                                                printf('<p>#%d - %s</p>', $key + 1, get_the_title($value));
                                                            }

                                                        } else {
                                                            esc_html_e('No Services Allowed', 'wpbooking');
                                                        }
                                                        break;

                                                    case "all":
                                                    default:
                                                        esc_html_e('All', 'wpbooking');
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td class="col-min col-row-actions">
                                                <a href="<?php echo esc_url($coupon->get_edit_url()) ?>"><?php esc_html_e('Edit', 'wpbooking') ?></a>
                                                <a href="<?php echo esc_url($coupon->get_delete_url()) ?>"
                                                   onclick="return confirm('<?php esc_html_e('Do you want to delete?') ?>')"><?php esc_html_e('Delete', 'wpbooking') ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="8" class="text-right pagination">
                                            <?php echo paginate_links(array(
                                                'base'    => admin_url('admin.php') . '%_%',
                                                'total'   => $query->max_num_pages,
                                                'current' => WPBooking_Input::get('page_number', 1),
                                                'format'  => '?page_number=%#%',
                                            )) ?>
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <?php
                            } else {
                                printf('<div class="alert alert-danger">%s</div>', esc_html__('No coupon code found', 'wpbooking'));
                            }

                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

