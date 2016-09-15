<?php
global $wp_query;
get_header();
$my_query = $wp_query;
if(WPBooking_Input::get('wb_test'))
var_dump($my_query->request);
echo wpbooking_load_view('wrap/start');
?>
<div class="wpbooking-container hentry">
    <?php echo wpbooking_load_view('archive/header',array('my_query'=>$my_query))?>
    <div class="wpbooking-loop-wrap">
        <?php echo wpbooking_load_view('archive/loop',array('my_query'=>$my_query))?>
        <?php echo wpbooking_load_view('archive/pagination',array('my_query'=>$my_query))?>
    </div>

</div>
<?php
echo wpbooking_load_view('wrap/end');
get_sidebar('wpbooking');
get_footer();
