<?php
/**
 * Created by ShineTheme.
 * User: NAZUMI
 * Date: 12/2/2016
 * Version: 1.0
 */
?>
<div class="wpbooking-extension">
    <div class="wb-content">
        <div class="header">
            <h2 class="title"><?php echo esc_html__('Extensions for WPBooking','wpbooking')?></h2>

        </div>
        <div class="control">
            <div class="desc">
                <p><?php echo wp_kses(__('These extensions <strong>add functionality</strong> to your WPBooking powered store.','wpbooking'),array('strong' => array()))?></p>
            </div>
            <div class="all-extension">
                <a href="#" class="button button-primary"><?php echo esc_html__('Browse all extensions','wpbooking'); ?></a>
            </div>
        </div>
        <div class="content">
            <div class="result-text">
                <p><?php echo __('Total <strong><span>50</span></strong> extensions. Showing 12- 32','wpbooking')?></p>
            </div>
            <div class="ex-sidebar">
                <div class="box-search">
                    <h3 class="title"><?php echo esc_html__('Search','wpbooking'); ?></h3>
                    <div class="box-content">
                        <form class="search-extensions" method="get" action="">
                            <p><?php echo esc_html__('Find an extensions','wpbooking');?></p>
                            <input type="text" name="s" value="" class="search-field" placeholder="<?php echo esc_html__('Type to search')?>" />
                            <input type="submit" name="submit" value="<?php echo esc_html__('Search','wpbooking')?>" />
                        </form>
                    </div>
                </div>
                <div class="box-categories">
                    <h3 class="title"><?php echo esc_html__('Category','wpbooking'); ?></h3>
                    <div class="box-content">
                        <ul class="list-cat">
                            <li><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('All','wpbooking'); ?></a><span>42</span></li>
                            <li class="active"><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('Aliquam','wpbooking'); ?></a><span>6</span></li>
                            <li><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('Posuere','wpbooking'); ?></a><span>4</span></li>
                            <li><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('Consectetur','wpbooking'); ?></a><span>15</span></li>
                            <li><a href="#"><i class="fa fa-folder-o"></i> <?php echo esc_html__('Vivamus','wpbooking'); ?></a><span>17</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="extension-list">
                <div class="list">

                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                                <span class="featured"><?php echo esc_html__('HOT','wpbooking')?></span>
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Review','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Wishlist','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Payment','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Google Seo','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Review','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="extension">
                            <div class="thumnail">
                                <img src="<?php echo wpbooking_admin_assets_url('images/wb-step.png')?>" />
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo esc_html__('WP Review','wpbooking'); ?></h3>
                                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque nec scelerisque massa.</p>
                                <a class="read-more" href="#"><?php echo esc_html__('Read more','wpbooking');?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pagination">
                    <ul class="ex-pagination">
                        <li><a href="#" class="prev"><?php echo esc_html__('Prev','wpbooking');?></a></li>
                        <li><a href="#"><?php echo esc_html__('1','wpbooking');?></a></li>
                        <li class="active"><span><?php echo esc_html__('2','wpbooking');?></span></li>
                        <li><a href="#" class="next"><?php echo esc_html__('Next','wpbooking');?></a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="footer">
            <a href="#" class="button button-primary"><?php echo esc_html__('Browse all extensions','wpbooking'); ?></a>
        </div>
    </div>
</div>