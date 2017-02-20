<div class="wpbooking_dashboard_widget">
    <div class="table table_left table_current_month">
            <table>
                <thead>
                <tr>
                    <td colspan="2"><?php esc_html_e('Current Month','wpbooking') ?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="first t monthly_earnings"><?php esc_html_e('Earnings','wpbooking') ?></td>
                    <td class="b b-earnings" ><?php echo WPBooking_Currency::format_money($current_month_earning) ?></td>
                </tr>
                <tr>
                    <td class="first t monthly_sales"><?php esc_html_e('Sales','wpbooking')?></td>
                    <td class="b b-sales" ><?php echo (int)$current_month_sale ?></td>
                </tr>
                </tbody>
            </table>
            <table>
                <thead>
                <tr>
                    <td colspan="2"><?php esc_html_e('Last Month','wpbooking')?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="first t earnings"><?php esc_html_e('Earnings','wpbooking') ?></td>
                    <td class="b b-last-month-earnings"><?php echo WPBooking_Currency::format_money($last_month_earning) ?></td>
                </tr>
                <tr>
                    <td class="first t sales">
                        <?php esc_html_e('Sales','wpbooking')?>						</td>
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
                        <?php esc_html_e('Today','wpbooking')?>							</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="t sales"><?php esc_html_e('Earnings','wpbooking') ?></td>
                    <td class="last b b-earnings" ><?php echo WPBooking_Currency::format_money($today_earning) ?></td>
                </tr>
                <tr>
                    <td class="t sales">
                        <?php esc_html_e('Sales','wpbooking')?>						</td>
                    <td class="last b b-sales" ><?php echo (int)$today_sale ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    <div class="table table_right table_totals">
        <table>
            <thead>
            <tr>
                <td colspan="2"><?php esc_html_e('Totals','wpbooking')?></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t earnings"><?php esc_html_e('Earnings','wpbooking') ?></td>
                <td class="last b b-earnings" ><?php echo WPBooking_Currency::format_money($total_earning) ?></td>
            </tr>
            <tr>
                <td class="t sales"><?php esc_html_e('Sales','wpbooking')?></td>
                <td class="last b b-sales" ><?php echo (int)$total_sale ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"></div>
    <?php do_action('wpbooking_after_report_widget_content') ?>
</div>
