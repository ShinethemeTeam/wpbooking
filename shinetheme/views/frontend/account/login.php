<?php
    $full_url    = $current_url = "//" . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
    $error_field = [ 'u' => '', 'p' => '' ];
    if ( !WPBooking_Input::post( 'action' ) )
        WPBooking()->set( 'error_code', '' );

    if ( !empty( WPBooking()->get( 'error_code' ) ) ) {
        if ( strpos( WPBooking()->get( 'error_code' ), 'username' ) ) {
            $error_field[ 'u' ] = 'wb-error';
        } else {
            $error_field[ 'p' ] = 'wb-error';
        }
    }
?>
<form action="" method="post" id="wpbooking-login-form" class="login-register-form">
    <input type="hidden" name="action" value="wpbooking_do_login">
    <input type="hidden" name="url" value="<?php echo esc_url( $full_url ) ?>">
    <h3 class="form-title"><?php echo esc_html__( 'Login', 'wp-booking-management-system' ) ?></h3>
    <div class="form-group-wrap">
        <div class="form-group">
            <label for="input-login"
                   class="control-label"><?php echo esc_html__( 'Username or email address', 'wp-booking-management-system' ) ?>
                <span class="required">*</span></label>
            <input type="text" class="form-control <?php echo esc_attr( $error_field[ 'u' ] ); ?>" required
                   value="<?php echo esc_attr( WPBooking_Input::post( 'login' ) ) ?>" name="login" id="input-login">
        </div>
        <div class="form-group">
            <label for="input-password"
                   class="control-label"><?php echo esc_html__( 'Password', 'wp-booking-management-system' ) ?> <span
                        class="required">*</span></label>
            <input type="password" class="form-control <?php echo esc_attr( $error_field[ 'p' ] ); ?>" required
                   id="input-password" name="password">
        </div>
        <div class="form-group">
            <button type="submit"
                    class="wb-btn wb-btn-default"><?php echo esc_html__( 'Login', 'wp-booking-management-system' ) ?></button>
            <label class="remember-me">
                <input type="checkbox" <?php checked( WPBooking_Input::post( 'remember' ), 1 ) ?> name="remember"
                       value="1"><?php echo esc_html__( 'Remember Me', 'wp-booking-management-system' ) ?>
            </label>
            <div class="wpbooking-socials-login">
                <?php do_action( 'wpbooking_before_login_button' ); ?>
            </div>
        </div>
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
           class="lost-password"><?php echo esc_html__( 'Is your password lost?', 'wp-booking-management-system' ) ?></a>
        <?php
            if ( wpbooking_is_any_register() ) {
                ?>
                <hr>
                <p class="register-url"><?php echo esc_html__( 'Don\'t have an account yet? ', 'wp-booking-management-system' ); ?>
                    <a href="<?php echo WPBooking_User::inst()->get_register_url(); ?>"><?php echo esc_html__( 'Create an account', 'wp-booking-management-system' ); ?></a>
                </p>
                <?php do_action( "wpbooking_after_register_user_link" ) ?>
            <?php } ?>
    </div>
    <?php
        if ( WPBooking_Input::post( 'action' ) == 'wpbooking_do_login' || WPBooking_Input::get( 'checkemail' ) == 'confirm' || WPBooking_Input::get( 'password' ) == 'changed' )
            echo wpbooking_get_message()
    ?>
</form>
