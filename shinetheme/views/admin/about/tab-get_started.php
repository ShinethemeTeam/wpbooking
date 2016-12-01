<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 12/1/2016
 * Version: 1.0
 */

?>
<div class="wb-control">
    <?php
    if(!empty($tabs[$is_key-1])){
        if(!empty($tabs[$is_key-1]['url'])){
            $url = $tabs[$is_key-1]['url'];
        }else {
            $url = add_query_arg(array("page" => $slug_page_menu, "wb_tab" => $tabs[$is_key-1]['id']), admin_url("admin.php"));
        }
        ?>
        <div class="wb-left">
            <a href="<?php echo esc_url($url); ?>" class="prev button button-primary"><?php echo esc_attr($tabs[$is_key-1]['name']) ?></a>
        </div>
    <?php } ?>

    <div class="wb-desc <?php echo(count($tabs) >1)?'':'full-width'; ?>">
        <p><?php echo wp_kses(__('We are spending you <strong>4 Unable faster steps</strong> to create new accommodation.', 'wpbooking'), array('strong' => array()))?></p>
        <p><?php echo esc_html__('There are below:','wpbooking'); ?></p>
    </div>
    <?php
    if(!empty($tabs[$is_key+1])){
        if(!empty($tabs[$is_key+1]['url'])){
            $url = $tabs[$is_key+1]['url'];
        }else {
            $url = add_query_arg(array("page" => $slug_page_menu, "wb_tab" => $tabs[$is_key+1]['id']), admin_url("admin.php"));
        }
        ?>
        <div class="wb-right">
            <a href="<?php echo esc_url($url); ?>" class="prev button button-primary"><?php echo esc_attr($tabs[$is_key+1]['name']) ?></a>
        </div>
    <?php } ?>
</div>
<div class="wb-content">
    <div class="header">
        <h2 class="title"><?php echo esc_html__('Get Started','wpbooking')?></h2>
    </div>

    <div class="content">
        <div class="step">
            <div class="left">
                <h3><span><?php echo esc_html__('1st.','wpbooking') ?></span> <?php echo esc_html__('Create new accommodation','wpbooking')?></h3>
                <p><?php echo esc_html__('Pellentesque facilisis quis velit non tincidunt. Sed at venenatis dolor. Donec egestas mi vel quam.','wpbooking');?></p>
                <ul>
                   <li><?php echo esc_html__('Create unlimited accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create unlimited amenity of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create unlimited facility of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create unlimited extra service of accommodation','wpbooking')?></li>
                   <li><?php echo esc_html__('Create unlimited room type for accommodation ','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><span><?php echo esc_html__('2nd.','wpbooking') ?></span> <?php echo esc_html__('Create accommodation’s room','wpbooking')?></h3>
                <p><?php echo esc_html__('Pellentesque facilisis quis velit non tincidunt. Sed at venenatis dolor. Donec egestas mi vel quam.','wpbooking');?></p>
                <ul>
                    <li><?php echo esc_html__('Room Price','wpbooking')?></li>
                    <li><?php echo esc_html__('Room Facilities','wpbooking')?></li>
                    <li><?php echo esc_html__('Room availability','wpbooking')?></li>
                    <li><?php echo esc_html__('Room check in out time ','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><span><?php echo esc_html__('3rd.','wpbooking') ?></span> <?php echo esc_html__('Seting Tax, Cancellation Policy','wpbooking')?></h3>
                <p><?php echo esc_html__('Pellentesque facilisis quis velit non tincidunt. Sed at venenatis dolor. Donec egestas mi vel quam.','wpbooking');?></p>
                <ul>
                    <li><?php echo esc_html__('Create unlimited accommodation','wpbooking')?></li>
                    <li><?php echo esc_html__('Create unlimited amenity of accommodation','wpbooking')?></li>
                    <li><?php echo esc_html__('Create unlimited facility of accommodation','wpbooking')?></li>
                    <li><?php echo esc_html__('Create unlimited extra service of accommodation','wpbooking')?></li>
                    <li><?php echo esc_html__('Create unlimited room type for accommodation ','wpbooking')?></li>
                </ul>
            </div>
            <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" class="img-step"/>
        </div>
        <div class="step">
            <div class="left">
                <h3><span><?php echo esc_html__('Finish.','wpbooking') ?></span> <?php echo esc_html__('All have done! ','wpbooking')?></h3>
            </div>
        </div>
    </div>
    <div class="footer">
        <h3 class="question"><?php echo esc_html__('Have a questions?', 'wpbooking'); ?></h3>
        <div class="link">
            <a href="#"><?php echo esc_html__('Need Help?','wpbooking')?></a>
            <a href="#"><?php echo esc_html__('FAQ','wpbooking')?></a>
            <a href="#"><?php echo esc_html__('Submit a Ticket?','wpbooking')?></a>
        </div>
    </div>
</div>
