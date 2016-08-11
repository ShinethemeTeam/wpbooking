<?php
get_header();
if(get_query_var( 'paged' )) {
    $paged = get_query_var( 'paged' );
} else if(get_query_var( 'page' )) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$is_page = get_the_ID();
$args = array(
    'post_type' => 'wpbooking_service' ,
    's'         => '' ,
    'paged'     => $paged,
    'posts_per_page'     => apply_filters('wpbooking_archive_posts_per_page',10,$is_page),
);
$service_type = '';
$list_page_search = apply_filters("wpbooking_add_page_archive_search",array());
if(!empty($list_page_search[$is_page]))
{
    $service_type = $list_page_search[$is_page];
}
$my_query = WPBooking_Service_Controller::inst()->query($args,$service_type);
if(WPBooking_Input::get('wb_test'))
var_dump($my_query);
echo wpbooking_load_view('wrap/start');
?>
<div class="wpbooking-container">
    <div class="row">
        <!--<div class="col-md-3">
            <?php /*echo get_sidebar(); */?>
        </div>-->
        <div class="col-md-12 ">

			<?php echo wpbooking_load_view('archive/header',array('my_query'=>$my_query,'service_type'=>$service_type))?>
			<div class="wpbooking-loop-wrap">
				<?php echo wpbooking_load_view('archive/loop',array('my_query'=>$my_query,'service_type'=>$service_type))?>
				<?php echo wpbooking_load_view('archive/pagination',array('my_query'=>$my_query,'service_type'=>$service_type))?>
			</div>
        </div>
    </div>
</div>
<?php
echo wpbooking_load_view('wrap/end');
wp_reset_query();
get_footer();
