<?php
/**
 * Custom widget for displaying posts.
 *
 * @package tempone
 */

/**
 * Register Tempone Posts Widget.
 */
function tempone_posts_widget_init() {
	register_widget( 'Tempone_Posts_Widget' );
}
add_action( 'widgets_init', 'tempone_posts_widget_init' );

/**
 * Tempone Posts Widget Class.
 */
class Tempone_Posts_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'tempone-post-widget',
			__( 'Tempone: Posts', 'tempone' ),
			array(
				'description' => __( 'Display posts by recent, category, tag, or most commented. Fully customizable.', 'tempone' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		// Security check.
		if ( ! defined( 'ABSPATH' ) ) {
			die( '-1' );
		}

		// Widget settings.
		$title    = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title    = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$type     = ! empty( $instance['type'] ) ? $instance['type'] : 'Recent Post';
		$category = ! empty( $instance['category'] ) ? $instance['category'] : '';
		$post_tag = ! empty( $instance['post_tag'] ) ? $instance['post_tag'] : '';
		$number   = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$orderby  = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order    = ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';

		// Build query args.
		$query_args = array(
			'post_status'    => 'publish',
			'posts_per_page' => $number,
			'orderby'        => $orderby,
			'order'          => $order,
			'no_found_rows'  => true,
		);

		// Type-specific query modifications.
		if ( 'Most Comments' === $type ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order']   = 'DESC';
		} elseif ( 'Popular Post' === $type ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'tempone_views';
		} elseif ( 'Post Format: Gallery' === $type ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => 'post-format-gallery',
				),
			);
		} elseif ( 'Post Format: Video' === $type ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'post_format',
					'field'    => 'slug',
					'terms'    => 'post-format-video',
				),
			);
		}

		// Filter by tag.
		if ( $post_tag ) {
			$query_args['tag'] = sanitize_text_field( $post_tag );
		}

		// Filter by category.
		if ( $category ) {
			$query_args['category_name'] = sanitize_text_field( $category );
		}

		// Run the query.
		$r = new WP_Query( $query_args );

		// Output.
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div class="post-widget">';

		if ( $r->have_posts() ) {
			while ( $r->have_posts() ) {
				$r->the_post();
				get_template_part( 'tp/content', 'image-side' );
			}
		} else {
			echo '<p class="widget-no-posts">' . esc_html__( 'No posts found.', 'tempone' ) . '</p>';
		}

		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		wp_reset_postdata();
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']    = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['type']     = ! empty( $new_instance['type'] ) ? sanitize_text_field( $new_instance['type'] ) : 'Recent Post';
		$instance['category'] = ! empty( $new_instance['category'] ) ? sanitize_text_field( $new_instance['category'] ) : '';
		$instance['post_tag'] = ! empty( $new_instance['post_tag'] ) ? sanitize_text_field( $new_instance['post_tag'] ) : '';
		$instance['number']   = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
		$instance['orderby']  = ! empty( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'date';
		$instance['order']    = ! empty( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : 'DESC';

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$types    = array( 'Recent Post', 'Popular Post', 'Most Comments', 'Post Format: Gallery', 'Post Format: Video' );
		$orderbys = array( 'ID', 'title', 'date', 'rand' );
		$orders   = array( 'ASC', 'DESC' );

		$defaults = array(
			'title'    => '',
			'type'     => 'Recent Post',
			'category' => '',
			'number'   => '5',
			'orderby'  => 'date',
			'order'    => 'DESC',
			'post_tag' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = sanitize_text_field( $instance['title'] );
		$type     = sanitize_text_field( $instance['type'] );
		$category = sanitize_text_field( $instance['category'] );
		$number   = absint( $instance['number'] );
		$orderby  = sanitize_text_field( $instance['orderby'] );
		$order    = sanitize_text_field( $instance['order'] );
		$post_tag = sanitize_text_field( $instance['post_tag'] );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Widget Title', 'tempone' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				<?php esc_html_e( 'Select Type', 'tempone' ); ?>
			</label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				<?php foreach ( $types as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $type, $option ); ?>>
						<?php echo esc_html( $option ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Select Category', 'tempone' ); ?>
			</label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<option value=""><?php esc_html_e( 'All Categories', 'tempone' ); ?></option>
				<?php
				$categories = get_categories( array( 'hide_empty' => 0 ) );
				foreach ( $categories as $cat ) :
					?>
					<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $category, $cat->slug ); ?>>
						<?php echo esc_html( $cat->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>">
				<?php esc_html_e( 'Tag (separate by comma)', 'tempone' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_tag' ) ); ?>" type="text" value="<?php echo esc_attr( $post_tag ); ?>" />
		</p>

		<hr>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<?php esc_html_e( 'Number of posts to show', 'tempone' ); ?>
			</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $number ); ?>" style="width:60px;" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php esc_html_e( 'Order By', 'tempone' ); ?>
			</label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php foreach ( $orderbys as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $orderby, $option ); ?>>
						<?php echo esc_html( ucfirst( $option ) ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php esc_html_e( 'Order', 'tempone' ); ?>
			</label>
			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php foreach ( $orders as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $order, $option ); ?>>
						<?php echo esc_html( $option ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}
}
