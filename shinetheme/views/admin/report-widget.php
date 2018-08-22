<div class="wpbooking_dashboard_widget">
    <div class="table table_left table_current_month">
            <table>
                <thead>
                <tr>
                    <td colspan="2"><?php echo esc_html__('Current Month','wp-booking-management-system') ?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="first t monthly_earnings"><?php echo esc_html__('Earnings','wp-booking-management-system') ?></td>
                    <td class="b b-earnings" ><?php echo WPBooking_Currency::format_money($current_month_earning) ?></td>
                </tr>
                <tr>
                    <td class="first t monthly_sales"><?php echo esc_html__('Sales','wp-booking-management-system')?></td>
                    <td class="b b-sales" ><?php echo (int)$current_month_sale ?></td>
                </tr>
                </tbody>
            </table>
            <table>
                <thead>
                <tr>
                    <td colspan="2"><?php echo esc_html__('Last Month','wp-booking-management-system')?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="first t earnings"><?php echo esc_html__('Earnings','wp-booking-management-system') ?></td>
                    <td class="b b-last-month-earnings"><?php echo WPBooking_Currency::format_money($last_month_earning) ?></td>
                </tr>
                <tr>
                    <td class="first t sales">
                        <?php echo esc_html__('Sales','wp-booking-management-system')?>						</td>
                    <td class="b b-last-month-sales">
                        <?php echo (int)$last_month_sale ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    <div class="table table_right table_today">
            <table>
                <thead>
                <tr>
                    <td colspan="2">
                        <?php echo esc_html__('Today','wp-booking-management-system')?>							</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="t sales"><?php echo esc_html__('Earnings','wp-booking-management-system') ?></td>
                    <td class="last b b-earnings" ><?php echo WPBooking_Currency::format_money($today_earning) ?></td>
                </tr>
                <tr>
                    <td class="t sales">
                        <?php echo esc_html__('Sales','wp-booking-management-system')?>						</td>
                    <td class="last b b-sales" ><?php echo (int)$today_sale ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    <div class="table table_right table_totals">
        <table>
            <thead>
            <tr>
                <td colspan="2"><?php echo esc_html__('Totals','wp-booking-management-system')?></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t earnings"><?php echo esc_html__('Earnings','wp-booking-management-system') ?></td>
                <td class="last b b-earnings" ><?php echo WPBooking_Currency::format_money($total_earning) ?></td>
            </tr>
            <tr>
                <td class="t sales"><?php echo esc_html__('Sales','wp-booking-management-system')?></td>
                <td class="last b b-sales" ><?php echo (int)$total_sale ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"></div>
    <?php do_action('wpbooking_after_report_widget_content') ?>
</div>
