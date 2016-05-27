<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/22/2016
 * Time: 2:21 PM
 */
if(!class_exists('WPBooking_Widget_Cart'))
{
	class WPBooking_Widget_Cart extends  WP_Widget
	{
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				FALSE, // Base ID
				__( 'WPBooking Cart Content', 'wpbooking' ), // Name,
				array(
					'description'=>__('[WPBooking] Cart Content','wpbooking')
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}

			echo wpbooking_load_view('cart/cart-widget');

			echo $args['after_widget'];
		}


		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 * @return void
		 */
		public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

			return $instance;
		}

		static function _add_cart_update_content($sections=array())
		{
			$sections['.wpbooking-cart-widget-content']=wpbooking_load_view('cart/cart-widget');
			return $sections;
		}
		static function  widget_init()
		{
			add_action( 'widgets_init',array(__CLASS__,'register') );
			add_filter('wpbooking_cart_updated_content',array(__CLASS__,'_add_cart_update_content'));
		}
		static function register()
		{
			register_widget( __CLASS__ );
		}
	}
	
	WPBooking_Widget_Cart::widget_init();



}