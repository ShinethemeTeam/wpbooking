<?php
    /**
     * Created by WpBooking Team.
     * User: NAZUMI
     * Date: 12/2/2016
     * Version: 1.0
     */

    $data     = file_get_contents( WPBooking()->API_URL . '?action=st_get_extension' );
    $data     = str_replace( '(', '', $data );
    $data     = str_replace( ')', '', $data );
    $jsonData = json_decode( $data );

    $started_url   = add_query_arg( [ 'page' => 'wpbooking' ], admin_url( 'admin.php' ) );
    $extension_url = add_query_arg( [ 'page' => 'wpbooking_page_extensions' ], admin_url( 'admin.php' ) );
    $_version      = WPBooking_System::inst()->get_version_plugin();
?>
<div class="wpbooking-extension">
    <div class="wrap">
        <h1><?php echo sprintf( esc_html__( 'Welcome to Wp Booking %s', 'wp-booking-management-system' ), $_version ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab"
               href="<?php echo esc_url( $started_url ); ?>"><?php echo esc_html__( 'Get Started', 'wp-booking-management-system' ); ?></a>
            <a class="nav-tab nav-tab-active"
               href="<?php echo esc_url( $extension_url ); ?>"><?php echo esc_html__( 'Extensions', 'wp-booking-management-system' ) ?></a>
        </h2>
    </div>
    <div class="wb-content">
        <?php if ( !empty( $jsonData ) && $jsonData->status == 1 ) { ?>
            <div class="control">
                <div class="all-extension">
                    <a href="<?php echo esc_url( 'https://wpbooking.org/downloads/' ); ?>"
                       target="_blank"
                       class="button button-primary"><?php echo esc_html__( 'Browse all extensions', 'wp-booking-management-system' ); ?></a>
                </div>
            </div>
            <div class="content">
                <div class="result-text">
                    <p><?php echo esc_html__( 'Total ', 'wp-booking-management-system' ) ?><?php echo '<span class="ex-total">' . esc_html( $jsonData->data->post_count ) . '</span>'; ?><?php echo esc_html__( ' extensions. Showing ', 'wp-booking-management-system' ) ?>
                        <span class="ex-from">1</span> - <span
                            class="ex-to"><?php echo ( $jsonData->data->max_pages > 1 ) ? esc_attr( $jsonData->posts_per_page ) : $jsonData->data->post_count; ?></span>
                    </p>
                </div>
                <div class="ex-sidebar">
                    <div class="box-search">
                        <h3 class="title"><?php echo esc_html__( 'Search', 'wp-booking-management-system' ); ?></h3>
                        <div class="box-content">
                            <form class="search-extensions" method="get" action="">
                                <p><?php echo esc_html__( 'Find an extension', 'wp-booking-management-system' ); ?></p>
                                <input type="text" name="s" value="" class="search-field"
                                       placeholder="<?php echo esc_html__( 'Type to search', 'wp-booking-management-system' ) ?>"/>
                                <input type="submit" name="submit" class="wb-search-extension"
                                       value="<?php echo esc_html__( 'Search', 'wp-booking-management-system' ) ?>"/>
                            </form>
                        </div>
                    </div>
                    <?php
                        if ( !empty( $jsonData->data->cat ) && !is_wp_error( $jsonData->data->cat ) ) { ?>
                            <div class="box-categories">
                                <h3 class="title"><?php echo esc_html__( 'Category', 'wp-booking-management-system' ); ?></h3>
                                <div class="box-content">
                                    <ul class="list-cat">
                                        <li class="active"><a href="#"><i
                                                    class="fa fa-folder-o"></i> <?php echo esc_html__( 'All', 'wp-booking-management-system' ); ?>
                                            </a><span><?php echo esc_attr( $jsonData->data->post_count ); ?></span></li>
                                        <?php foreach ( $jsonData->data->cat as $key => $val ) { ?>
                                            <li><a href="#" data-id="<?php echo esc_attr( $val->term_id ); ?>"><i
                                                        class="fa fa-folder-o"></i> <?php echo esc_attr( $val->name ); ?>
                                                </a><span><?php echo esc_attr( $val->count ); ?></span></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                </div>
                <div class="extension-list">
                    <div class="list">

                        <?php
                            foreach ( $jsonData->data->posts as $key => $val ) {
                                ?>
                                <div class="item">
                                    <div class="extension">
                                        <div class="thumnail">
                                            <a href="<?php echo esc_url( $val->url ) ?>" target="_blank"><img
                                                    src="<?php echo esc_url( $val->thumb_url ); ?>"
                                                    alt="<?php echo esc_attr( $val->title ); ?>"/></a>
                                        </div>
                                        <div class="info">
                                            <h3 class="title"><?php echo esc_attr( $val->title ); ?></h3>
                                            <p class="desc"><?php echo esc_attr( $val->short_ex ); ?></p>
                                            <a class="button" target="_blank"
                                               href="<?php echo esc_url( $val->url ); ?>"><?php echo esc_html__( 'Get this Extension', 'wp-booking-management-system' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        ?>

                    </div>
                    <?php
                        if ( $jsonData->data->max_pages > 1 ) {
                            ?>
                            <div class="pagination">
                                <ul class="ex-pagination">
                                    <li class="hidden"><a href="#" data-paged="1"
                                                          class="prev"><?php echo esc_html__( 'Prev', 'wp-booking-management-system' ); ?></a>
                                    </li>
                                    <?php for ( $i = 1; $i <= $jsonData->data->max_pages; $i++ ) {
                                        if ( $i == 1 ) {
                                            echo '<li class="active"><span>' . esc_html( $i ) . '</span></li>';
                                        } else {
                                            echo '<li><a href="#" data-paged="' . esc_html( $i ) . '">' . esc_html( $i ) . '</a></li>';
                                        }
                                        ?>
                                    <?php } ?>
                                    <li><a href="#" data-paged="2"
                                           class="next"><?php echo esc_html__( 'Next', 'wp-booking-management-system' ); ?></a></li>
                                </ul>
                            </div>
                        <?php } ?>
                </div>

            </div>
            <div class="footer">
                <a href="<?php echo esc_url( 'https://wpbooking.org/downloads/' ); ?>"
                   target="_blank"
                   class="button button-primary"><?php echo esc_html__( 'Browse all extensions', 'wp-booking-management-system' ); ?></a>
            </div>
        <?php } else {
            ?>
            <div class="content">
                <div class="result-text">
                    <h2 class="title-no-ex"><?php echo esc_html__( 'No extensions.', 'wp-booking-management-system' ) ?></h2>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>
