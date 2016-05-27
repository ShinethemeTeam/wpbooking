<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/22/2016
 * Time: 2:21 PM
 */
if(!class_exists('WPBooking_Widget_Currency_Switcher'))
{
	class WPBooking_Widget_Currency_Switcher extends  WP_Widget
	{
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				FALSE, // Base ID
				__( 'Currency Switcher', 'wpbooking' ), // Name,
				array(
					'description'=>__('[WPBooking] Currency Switcher','wpbooking')
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
			echo ($args['before_widget']);
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			}

			$all_currency = WPBooking_Currency::get_all_currency();

			$all = WPBooking_Currency::get_added_currencies();

			$html = "<select onchange='window.location.href=this.value'>";

			$selected=WPBooking_Currency::get_current_currency('currency');

			foreach ($all as $k => $v) {

				if (isset($v['currency']) and isset($all_currency[$v['currency']])) {

					$url = esc_url(add_query_arg(array('currency'=>$v['currency'])));
					$country = $all_currency[$v['currency']];
					$html .= "<option " . selected($selected, $v['currency'], FALSE) . " value='{$url}'>{$country} - {$v['currency']}</option>";

				} else {
					continue;
				}
			}
			$html .= "</select>";

			echo apply_filters('wpbooking_widget_currency_switcher',$html);

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

		static function  widget_init()
		{
			add_action( 'widgets_init',array(__CLASS__,'register') );
		}
		static function register()
		{
			register_widget( __CLASS__ );
		}
	}
	
	WPBooking_Widget_Currency_Switcher::widget_init();



}