<?php
global $current_user;
$user_id = $current_user->ID;
global $wpdb;
$page=WPBooking_Input::request('page_number',1);
$limit=10;
$offset=($page-1)*$limit;
$join = $where = "";
if ($service_type = WPBooking_Input::get('service_type')) {
    $join = " INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id) ";
    $where = "
    AND (
            (
                {$wpdb->prefix}postmeta.meta_key = 'service_type'
                AND {$wpdb->prefix}postmeta.meta_value = '{$service_type}'
            )
        )
    ";
}
$sql = "
    SELECT SQL_CALC_FOUND_ROWS *
    FROM
        {$wpdb->prefix}posts
    INNER JOIN {$wpdb->prefix}wpbooking_favorite ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}wpbooking_favorite.post_id
    {$join}
    WHERE
        1 = 1
        AND {$wpdb->prefix}wpbooking_favorite.user_id = {$user_id}
        AND {$wpdb->prefix}posts.post_type = 'wpbooking_service'
        {$where}
    ORDER BY
        {$wpdb->prefix}posts.ID DESC
    LIMIT {$offset},{$limit}";
$res = $wpdb->get_results($sql);
$total_item=$wpdb->get_var('SELECT FOUND_ROWS()');
$total=ceil($total_item/$limit);
$types = WPBooking_Service_Controller::inst()->get_service_types();
?>
<h3 class="tab-page-title">
    <?php
    echo esc_html__('Your Wishlist', 'wp-booking-management-system');
    ?>
</h3>
<?php if (!empty($types) and count($types) > 1) { ?>
    <ul class="service-filters">
        <?php
        $class = FALSE;
        if (!WPBooking_Input::get('service_type')) $class = 'active';
        printf('<li class="%s"><a href="%s">%s</a></li>', $class, get_permalink(wpbooking_get_option('myaccount-page') ). 'tab/your_wishlist', esc_html__('All', 'wp-booking-management-system'));
        foreach ($types as $type_id => $type) {
            $class = FALSE;
            if(WPBooking_Input::get('service_type')==$type_id) $class='active';
            $url = esc_url(add_query_arg(array('service_type' => $type_id), get_permalink(wpbooking_get_option('myaccount-page')).'tab/your_wishlist'));
            printf('<li class="%s"><a href="%s">%s</a></li>', $class, $url, $type->get_info('label'));
        }
        ?>
    </ul>
<?php } ?>
<div class="wpbooking-account-services">
    <?php if (!empty($res)) {
        $title = sprintf(esc_html__('You are wishing %d service(s)', 'wp-booking-management-system'),$total_item);
        if($service_type and $service_type_object=WPBooking_Service_Controller::inst()->get_service_type($service_type)){
            $title=sprintf(esc_html__('You are wishing %d %s(s)','wp-booking-management-system'),$total_item,strtolower($service_type_object->get_info('label')));
        }
        echo "<div class='lable'>{$title}</div>";
        global $post;
        foreach($res as $post){
            setup_postdata($post);
            $service=new WB_Service($post->ID);
            ?>
            <div class="service-item">
                <div class="service-img">
                    <?php echo ($service->get_featured_image('thumb')) ?>
                </div>
                <div class="service-info">
                    <h5 class="service-title">
                        <a href="<?php the_permalink()?>" target="_blank"><?php the_title()?></a>
                    </h5>
                    <p class="service-price"><?php $service->get_price_html(TRUE) ?></p>
                    <div class="service-status">
                        <a href="#" data-post="<?php the_ID() ?>"
                           class="service-fav <?php if ($service->check_favorite()) echo 'active'; ?>"><i
                                class="fa fa-heart"></i></a>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        printf('<div class="alert alert-danger">%s</div>', esc_html__('Not Found Service(s)', 'wp-booking-management-system'));
    }
    ?>
    <div class="wpbooking-pagination">
        <?php  echo paginate_links(array(
            'total'=>$total,
            'current'  => $page,
            'format'   => '?page_number=%#%',
            'add_args' => array()
        ));?>
    </div>
</div>
<?php wp_reset_postdata(); ?>