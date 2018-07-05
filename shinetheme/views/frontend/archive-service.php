<?php
global $wp_query;
get_header();
echo wpbooking_load_view('wrap/start');
$id_form = WPBooking_Input::get( 'wpbooking_search_form_archive' );
?>
<div class="wpbooking-container wb-archive-wrapper hentry">
<?php
        if(!empty($id_form)){
            echo wpbooking_load_view('shortcode/form-search/form-search',array(
                'id' => $id_form,
            ));
        }
    ?>
    <?php echo wpbooking_load_view('archive/header')?>
    <div class="wpbooking-loop-wrap">
        <?php echo wpbooking_load_view('archive/loop')?>
        <?php echo wpbooking_load_view('archive/pagination')?>
    </div>
</div>
<?php
    get_sidebar('wpbooking');
echo wpbooking_load_view('wrap/end');
get_footer();