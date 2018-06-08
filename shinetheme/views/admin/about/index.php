<?php
$_version = WPBooking_System::inst()->get_version_plugin();
$extension_url = add_query_arg(array('page' => 'wpbooking_page_extensions'), admin_url('admin.php'));
$tabs = array(
    array(
        'id' => 'get_started',
        'name' => esc_html__('Get Started','wp-booking-management-system'),
    ),
    array(
        'id' => 'extensions',
        'name' => esc_html__('Extensions','wp-booking-management-system'),
        'url' => $extension_url
    ),
);

$menu_page=WPBooking()->get_menu_page();
$slug_page_menu = $menu_page['menu_slug'];

?>
<div class="wrap wpbooking-info">
	<h1><?php echo sprintf(esc_html__('Welcome to Wp Booking %s','wp-booking-management-system'), $_version); ?></h1>
    <p class="description"><?php echo esc_html__('Wpbooking is ready to receive and manage bookings from visitor','wp-booking-management-system');?></p>
    <?php $is_tab = WPBooking_Input::request('wb_tab'); $key = 0; ?>
    <?php if(count($tabs) > 1){ ?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <?php
                foreach($tabs as $k=>$v){
                    if(empty($is_tab) and $k == 0){
                        $is_tab = $v['id'];
                    }
                    if($is_tab == $v['id']){
                        $url='#';
                        $key = $k;
                    }else{
                        if(!empty($v['url'])){
                            $url = $v['url'];
                        }else {
                            $url = add_query_arg(array("page" => $slug_page_menu, "wb_tab" => $v['id']), admin_url("admin.php"));
                        }
                    }
                    ?>
                    <a class="nav-tab <?php if($is_tab == $v['id']) echo "nav-tab-active"; ?>" href="<?php echo esc_url($url) ?>"><?php echo esc_html($v['name']) ?></a>
            <?php } ?>
        </h2>
    </div>
    <?php } ?>
    <div class="wrap">
        <?php
        if($is_tab){
            echo wpbooking_admin_load_view('about/tab-'.$is_tab, array('tabs' => $tabs, 'is_key' => $key ,'slug_page_menu' => $slug_page_menu));
        }else{
            echo wpbooking_admin_load_view('about/tab-get_started', array('tabs' => $tabs, 'is_key' => $key ,'slug_page_menu' => $slug_page_menu));
        }
        ?>
    </div>
</div>