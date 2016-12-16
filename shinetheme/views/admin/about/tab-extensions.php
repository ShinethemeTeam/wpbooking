<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 12/2/2016
 * Version: 1.0
 */

$curlSession = curl_init();
curl_setopt($curlSession, CURLOPT_URL, WPBooking()->API_URL.'?action=st_get_extension');
curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

$data = str_replace('(','',curl_exec($curlSession));
$data = str_replace(')','',$data);
$jsonData = json_decode($data);

curl_close($curlSession);

?>
<div class="wpbooking-extension">
    <div class="wb-content">
        <div class="header">
            <h2 class="title"><?php echo esc_html__('Extensions for WPBooking','wpbooking')?></h2>

        </div>

        <?php if(!empty($jsonData) && $jsonData->status == 1) { ?>

        <div class="control">
            <div class="desc">
                <p><?php echo wp_kses(__('These extensions <strong>add functionality</strong> to your WPBooking powered store.','wpbooking'),array('strong' => array()))?></p>
            </div>
            <div class="all-extension">
                <a href="<?php echo esc_url('http://188.166.251.178/wpbooking/?post_type=download&s=&no_scroll');?>" target="_blank" class="button button-primary"><?php echo esc_html__('Browse all extensions','wpbooking'); ?></a>
            </div>
        </div>
        <div class="content">
            <div class="result-text">
                <p><?php echo __('Total ','wpbooking')?><?php echo '<span class="ex-total">'.$jsonData->data->post_count.'</span>';?><?php echo esc_html__(' extensions. Showing ','wpbooking')?><span class="ex-from">1</span> - <span class="ex-to"><?php echo ($jsonData->data->max_pages > 1)?esc_attr($jsonData->posts_per_page):$jsonData->data->post_count; ?></span></p>
            </div>
            <div class="ex-sidebar">
                <div class="box-search">
                    <h3 class="title"><?php echo esc_html__('Search','wpbooking'); ?></h3>
                    <div class="box-content">
                        <form class="search-extensions" method="get" action="">
                            <p><?php echo esc_html__('Find an extensions','wpbooking');?></p>
                            <input type="text" name="s" value="" class="search-field" placeholder="<?php echo esc_html__('Type to search','wpbooking')?>" />
                            <input type="submit" name="submit" class="wb-search-extension" value="<?php echo esc_html__('Search','wpbooking')?>" />
                        </form>
                    </div>
                </div>
                <?php if(!is_wp_error($jsonData->data->cat)){ ?>
                <div class="box-categories">
                    <h3 class="title"><?php echo esc_html__('Category','wpbooking'); ?></h3>
                    <div class="box-content">
                        <ul class="list-cat">
                            <li class="active"><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('All','wpbooking'); ?></a><span><?php echo esc_attr($jsonData->data->post_count); ?></span></li>
                            <?php foreach($jsonData->data->cat as $key => $val){ ?>
                            <li><a href="#" data-id="<?php echo esc_attr($val->term_id); ?>" ><i class="fa fa-folder-o"></i> <?php echo esc_attr($val->name); ?></a><span><?php echo esc_attr($val->count); ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="extension-list">
                <div class="list">

                    <?php
                    foreach ($jsonData->data->posts as $key => $val) {
                        ?>
                        <div class="item">
                            <div class="extension">
                                <div class="thumnail">
                                    <img src="<?php echo esc_url($val->thumb_url); ?>" alt="<?php echo esc_attr($val->title); ?>"/>
<!--                                            <span class="featured">--><?php //echo esc_html__('HOT', 'wpbooking') ?><!--</span>-->
                                </div>
                                <div class="info">
                                    <h3 class="title"><?php echo esc_attr($val->title); ?></h3>
                                    <p class="desc"><?php echo esc_attr($val->short_ex); ?></p>
                                    <a class="read-more" target="_blank"
                                       href="<?php echo esc_url($val->url); ?>"><?php echo esc_html__('Read more', 'wpbooking'); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php }
                    ?>

                </div>
                <?php
                if($jsonData->data->max_pages > 1){
                ?>
                <div class="pagination">
                    <ul class="ex-pagination">
                        <li class="hidden"><a href="#" data-paged="1" class="prev"><?php echo esc_html__('Prev','wpbooking');?></a></li>
                        <?php for($i = 1; $i <= $jsonData->data->max_pages; $i++){
                            if($i == 1){
                                echo '<li class="active"><span>'.$i.'</span></li>';
                            }else{
                                echo '<li><a href="#" data-paged="'.$i.'">'.$i.'</a></li>';
                            }
                            ?>
                        <?php } ?>
                        <li><a href="#" data-paged="2" class="next"><?php echo esc_html__('Next','wpbooking');?></a></li>
                    </ul>
                </div>
                <?php } ?>
            </div>

        </div>
        <div class="footer">
            <a href="<?php echo esc_url('http://188.166.251.178/wpbooking/?post_type=download&s=&no_scroll');?>" target="_blank" class="button button-primary"><?php echo esc_html__('Browse all extensions','wpbooking'); ?></a>
        </div>
        <?php }else{
            ?>
        <div class="content">
            <div class="result-text">
                <h2 class="title-no-ex"><?php echo esc_html__('No extensions.','wpbooking')?></h2>
            </div>
        </div>
        <?php
        } ?>
    </div>
</div>
