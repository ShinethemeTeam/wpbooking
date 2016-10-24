<?php
global $wp_query;
get_header();
$my_query = $wp_query;
if(WPBooking_Input::get('wb_test'))
var_dump($my_query->request);
echo wpbooking_load_view('wrap/start');
?>
<div class="wpbooking-container wb-archive-wrapper hentry">
    <?php echo wpbooking_load_view('archive/header')?>
    <div class="wpbooking-loop-wrap">
        <?php echo wpbooking_load_view('archive/loop')?>
        <?php echo wpbooking_load_view('archive/pagination')?>
    </div>

</div>
<?php
echo wpbooking_load_view('wrap/end');
get_sidebar('wpbooking');
get_footer();
