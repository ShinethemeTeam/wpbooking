<?php
get_header();
if(get_query_var( 'paged' )) {
    $paged = get_query_var( 'paged' );
} else if(get_query_var( 'page' )) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}
$args = array(
    'post_type' => 'wpbooking_service' ,
    's'         => '' ,
    'paged'     => $paged,
    'posts_per_page'     => 3,
);
$service_type = '';
$is_page = get_the_ID();
$list_page_search = apply_filters("wpbooking_add_page_archive_search",array());
if(!empty($list_page_search[$is_page]))
{
    $service_type = $list_page_search[$is_page];
}
$my_query = WPBooking_Service::inst()->query($args,$service_type);
echo wpbooking_load_view('wrap/start');
?>
<div class="wpbooking-container">
    <div class="row">
        <!--<div class="col-md-3">
            <?php /*echo get_sidebar(); */?>
        </div>-->
        <div class="col-md-12 ">
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
