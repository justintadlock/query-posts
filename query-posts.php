<?php
/**
 * Query Posts Widget.
 * Adds a widget with numerous options using the query_posts() function.
 * In 0.2, converted functions to a class that extends WP 2.8's widget class.
 *
 * @package QueryPosts
 */

/**
 * Output of the Query Posts widget.
 *
 * @since 0.2
 */
class Query_Posts_Widget extends WP_Widget {

	function Query_Posts_Widget() {
		$widget_ops = array( 'classname' => 'posts', 'description' => __('Display posts and pages however you want.', 'query-posts') );
		$control_ops = array( 'width' => 800, 'height' => 350, 'id_base' => 'query-posts' );
		$this->WP_Widget( 'query-posts', __('Query Posts', 'query-posts'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		/* Arguments for the query. */
		$args = array();

		/* Widget title and things not in query arguments. */
		$title = apply_filters('widget_title', $instance['title'] );
		$display = $instance['display'];
		$thumbnail = $instance['thumbnail'] ? '1' : '0';
		$wp_reset_query = $instance['wp_reset_query'] ? '1' : '0';

		/* Post type. */
		$args['post_status'] = $instance['post_status'];
		$args['post_type'] = $instance['post_type'];

		/* Sticky posts. */
		$args['caller_get_posts'] = $instance['caller_get_posts'] ? '1' : '0';

		/* Ordering and such. */
		if ( $instance['showposts'] )
			$args['showposts'] = (int)$instance['showposts'];
		if ( $instance['offset'] )
			$args['offset'] = (int)$instance['offset'];
		if ( $instance['posts_per_page'] )
			$args['posts_per_page'] = (int)$instance['posts_per_page'];
		if ( $instance['paged'] )
			$args['paged'] = $instance['paged'];
		if ( $instance['post_parent'] )
			$args['post_parent'] = (int)$instance['post_parent'];
		if ( $instance['order'] )
			$args['order'] = $instance['order'];
		if ( $instance['orderby'] )
			$args['orderby'] = $instance['orderby'];

		/* Category arguments. */
		if ( $instance['cat'] )
			$args['cat'] = $instance['cat'];
		if ( $instance['category_name'] )
			$args['category_name'] = $instance['category_name'];
		if ( $instance['category__and'] )
			$args['category__and'] = explode( ',', str_replace( ' ', '', $instance['category__and'] ) );
		if ( $instance['category__in'] )
			$args['category__in'] = explode( ',', str_replace( ' ', '', $instance['category__in'] ) );
		if ( $instance['category__not_in'] )
			$args['category__not_in'] = explode( ',', str_replace( ' ', '', $instance['category__not_in'] ) );

		/* Tag arguments. */
		if ( $instance['tag'] )
			$args['tag'] = $instance['tag'];
		if ( $instance['tag__and'] )
			$args['tag__and'] = explode( ',', str_replace( ' ', '', $instance['tag__and'] ) );
		if ( $instance['tag__in'] )
			$args['tag__in'] = explode( ',', str_replace( ' ', '', $instance['tag__in'] ) );
		if ( $instance['tag_slug__and'] )
			$args['tag_slug__and'] = explode( ',', str_replace( ' ', '', $instance['tag_slug__and'] ) );
		if ( $instance['tag_slug__in'] )
			$args['tag_slug__in'] = explode( ',', str_replace( ' ', '', $instance['tag_slug__in'] ) );

		/* Post arguments. */
		if ( $instance['p'] )
			$args['p'] = (int)$instance['p'];
		if ( $instance['name'] )
			$args['name'] = $instance['name'];
		if ( $instance['post__in'] )
			$args['post__in'] = explode( ',', str_replace( ' ', '', $instance['post__in'] ) );
		if ( $instance['post__not_in'] )
			$args['post__not_in'] = explode( ',', str_replace( ' ', '', $instance['post__not_in'] ) );

		/* Page arguments. */
		if ( $instanc['page_id'] )
			$args['page_id'] = (int)$instance['page_id'];
		if ( $instance['pagename'] )
			$args['pagename'] = $instance['pagename'];

		/* Author arguments. */
		if ( $instance['author_name'] )
			$args['author_name'] = $instance['author_name'];
		if ( $intance['author'] )
			$args['author'] = (int)$instance['author'];

		/* Time arguments. */
		if ( $instance['hour'] )
			$args['hour'] = (int)$instance['hour'];
		if ( $instance['minute'] )
			$args['minute'] = (int)$instance['minute'];
		if ( $instance['second'] )
			$args['second'] = (int)$instance['second'];
		if ( $instance['day'] )
			$args['day'] = (int)$instance['day'];
		if ( $instance['monthnum'] )
			$args['monthnum'] = (int)$instance['monthnum'];
		if ( $instance['year'] )
			$args['year'] = (int)$instance['year'];
		if ( $instance['w'] )
			$args['w'] = (int)$instance['w'];

		/* Meta arguments. */
		if ( $instance['meta_key'] )
			$args['meta_key'] = $instance['meta_key'];
		if ( $instance['meta_value'] )
			$args['meta_value'] = $instance['meta_value'];
		if ( $instance['meta_compare'] )
			$args['meta_compare'] = $instance['meta_compare'];

		/* Custom taxonomy arguments. */
		$taxonomies = get_object_taxonomies( 'post' );
		if ( is_array( $taxonomies ) ) :
			foreach ( $taxonomies as $taxonomy ) :
				$tax = get_taxonomy( $taxonomy );
				if ( $instance[$tax->query_var] ) :
					$args[$tax->query_var] = $instance[$tax->query_var];
				endif;
			endforeach;
		endif;

		/* Begin display of widget. */
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		query_posts( $args );

		if ( $display == 'ul' || $display == 'ol' ) : ?>

			<<?php echo $display; ?>>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php the_title( '<li><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></li>' ); ?>
			<?php endwhile; endif; ?>
			</<?php echo $display; ?>>

		<?php else: ?>

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); global $post; ?>

				<div <?php post_class(); ?>>

					<?php if ( function_exists( 'get_the_image' ) && $thumbnail )
						get_the_image( array( 'custom_key' => array( 'Thumbnail', 'thumbnail' ), 'default_size' => 'thumbnail' ) ); ?>

					<?php the_title( '<h2 class="post-title entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>

					<p class="byline">
						<?php printf( __('<span class="text">By</span> %1$s <span class="text">on</span> %2$s', 'query-posts'), '<span class="author vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_ID() ) . '" title="' . get_the_author() . '">' . get_the_author() . '</a></span>', '<abbr class="published" title="' . sprintf( get_the_time( __('l, F jS, Y, g:i a', 'query-posts') ) ) . '">' . sprintf( get_the_time( __('F j, Y', 'query-posts') ) ) . '</abbr>' ); ?> 
						<?php edit_post_link( __('Edit', 'query-posts'), ' <span class="separator">|</span> <span class="edit">', '</span> ' ); ?>
					</p>

					<?php if ( $display == 'the_content' ) : ?>

						<div class="entry-content">
							<?php the_content( __('Continue reading', 'query-posts') . ' ' . the_title( '"', '"', false ) ); ?>
							<?php wp_link_pages( array( 'before' => '<p class="pages">' . __('Pages:', 'query-posts'), 'after' => '</p>' ) ); ?>
						</div>

					<?php else : ?>

						<div class="entry-summary">
							<?php the_excerpt(); ?>
						</div>

					<?php endif; ?>

					<?php if ( 'page' != $post->post_type ) : ?>

						<p class="entry-meta">
							<span class="categories"><span class="text"><?php _e('Posted in', 'query-posts'); ?></span> <?php the_category( ', ' ); ?></span> 
							<?php the_tags( '<span class="tags"> <span class="separator">|</span> <span class="text">' . __('Tagged', 'query-posts') . '</span> ', ', ', '</span>' ); ?> 
							<?php if ( comments_open() ) : ?><span class="separator">|</span><?php endif; ?> <?php comments_popup_link( __('Leave a response', 'query-posts'), __('1 Response', 'query-posts'), __('% Responses', 'query-posts'), 'comments-link', false ); ?> 
						</p>

					<?php endif; ?>
				</div>

			<?php endwhile; endif; ?>

		<?php endif;

		if ( $wp_reset_query )
			wp_reset_query();

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['display'] = $new_instance['display'];

		$instance['thumbnail'] = ( isset( $new_instance['thumbnail'] ) ? 1 : 0 );
		$instance['wp_reset_query'] = ( isset( $new_instance['wp_reset_query'] ) ? 1 : 0 );
		$instance['caller_get_posts'] = ( isset( $new_instance['caller_get_posts'] ) ? 1 : 0 );

		$instance['post_status'] = $new_instance['post_status'];
		$instance['post_type'] = $new_instance['post_type'];
		$instance['showposts'] = strip_tags( $new_instance['showposts'] );
		$instance['offset'] = strip_tags( $new_instance['offset'] );
		$instance['posts_per_page'] = strip_tags( $new_instance['posts_per_page'] );
		$instance['paged'] = strip_tags( $new_instance['paged'] );
		$instance['post_parent'] = strip_tags( $new_instance['post_parent'] );
		$instance['order'] = $new_instance['order'];
		$instance['orderby'] = $new_instance['orderby'];

		$instance['cat'] = strip_tags( $new_instance['cat'] );
		$instance['category_name'] = $new_instance['category_name'];
		$instance['category__and'] = strip_tags( $new_instance['category__and'] );
		$instance['category__in'] = strip_tags( $new_instance['category__in'] );
		$instance['category__not_in'] = strip_tags( $new_instance['category__not_in'] );

		$instance['tag'] = strip_tags( $new_instance['tag'] );
		$instance['tag__and'] = strip_tags( $new_instance['tag__and'] );
		$instance['tag__in'] = strip_tags( $new_instance['tag__in'] );
		$instance['tag_slug__and'] = strip_tags( $new_instance['tag_slug__and'] );
		$instance['tag_slug__in'] = strip_tags( $new_instance['tag_slug__in'] );

		$instance['p'] = strip_tags( $new_instance['p'] );
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['post__in'] = strip_tags( $new_instance['post__in'] );
		$instance['post__not_in'] = strip_tags( $new_instance['post__not_in'] );

		$instance['page_id'] = strip_tags( $new_instance['page_id'] );
		$instance['pagename'] = strip_tags( $new_instance['pagename'] );

		$instance['hour'] = $new_instance['hour'];
		$instance['minute'] = $new_instance['minute'];
		$instance['second'] = $new_instance['second'];
		$instance['day'] = $new_instance['day'];
		$instance['monthnum'] = $new_instance['monthnum'];
		$instance['year'] = strip_tags( $new_instance['year'] );
		$instance['w'] = $new_instance['w'];

		$instance['author_name'] = strip_tags( $new_instance['author_name'] );
		$instance['author'] = strip_tags( $new_instance['author'] );

		$instance['meta_key'] = strip_tags( $new_instance['meta_key'] );
		$instance['meta_value'] = strip_tags( $new_instance['meta_value'] );
		$instance['meta_compare'] = strip_tags( $new_instance['meta_compare'] );

		$taxonomies = get_object_taxonomies( 'post' );
		if ( is_array( $taxonomies ) ) :
			foreach ( $taxonomies as $taxonomy ) :
				$tax = get_taxonomy( $taxonomy );
				if ( $tax->query_var ) :
					$instance[$tax->query_var] = $new_instance[$tax->query_var];
				endif;
			endforeach;
		endif;

		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array( 'display' => 'ul', 'post_type' => 'post', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date', 'caller_get_posts' => true, 'wp_reset_query' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div style="float:left;width:17%;">

		<p>
			<label title="<?php _e('What should the title of your widget be?', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'query-posts'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label title="<?php _e('What format to display your posts in', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e('Display:', 'query-posts'); ?></label>
			<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'the_content' == $instance['display'] ) echo ' selected="selected"'; ?>>the_content</option>
				<option <?php if ( 'the_excerpt' == $instance['display'] ) echo ' selected="selected"'; ?>>the_excerpt</option>
				<option <?php if ( 'ul' == $instance['display'] ) echo ' selected="selected"'; ?>>ul</option>
				<option <?php if ( 'ol' == $instance['display'] ) echo ' selected="selected"'; ?>>ol</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('What should be the status of the post(s)?', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'post_status' ); ?>"><code>post_status</code></label>
			<select id="<?php echo $this->get_field_id( 'post_status' ); ?>" name="<?php echo $this->get_field_name( 'post_status' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( !$instance['post_status'] ) echo ' selected="selected"'; ?>></option>
				<option <?php if ( 'publish' == $instance['post_status'] ) echo ' selected="selected"'; ?>>publish</option>
				<option <?php if ( 'private' == $instance['post_status'] ) echo ' selected="selected"'; ?>>private</option>
				<option <?php if ( 'draft' == $instance['post_status'] ) echo ' selected="selected"'; ?>>draft</option>
				<option <?php if ( 'future' == $instance['post_status'] ) echo ' selected="selected"'; ?>>future</option>
				<option <?php if ( 'inherit' == $instance['post_status'] ) echo ' selected="selected"'; ?>>inherit</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('What type of content should be displayed?', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'post_type' ); ?>"><code>post_type</code></label>
			<select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'any' == $instance['post_type'] ) echo ' selected="selected"'; ?>>any</option>
				<option <?php if ( 'page' == $instance['post_type'] ) echo ' selected="selected"'; ?>>page</option>
				<option <?php if ( 'post' == $instance['post_type'] ) echo ' selected="selected"'; ?>>post</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('How many posts should be shown?', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'showposts' ); ?>"><code>showposts</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'showposts' ); ?>" name="<?php echo $this->get_field_name( 'showposts' ); ?>" value="<?php echo $instance['showposts']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Offset (skip) this number of posts', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'offset' ); ?>"><code>offset</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" value="<?php echo $instance['offset']; ?>" />
		</p>
		<p>
			<label title="<?php _e('How many posts should be shown on each page?', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><code>posts_per_page</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" value="<?php echo $instance['posts_per_page']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Show posts from a particular page', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'paged' ); ?>"><code>paged</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'paged' ); ?>" name="<?php echo $this->get_field_name( 'paged' ); ?>" value="<?php echo $instance['paged']; ?>" />
		</p>
		<p>
			<label title="<?php _e('ID of the post parent', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'post_parent' ); ?>"><code>post_parent</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'post_parent' ); ?>" name="<?php echo $this->get_field_name( 'post_parent' ); ?>" value="<?php echo $instance['post_parent']; ?>" />
		</p>

		</div>

		<div style="float:left;width:17%;margin-left:3%;">

		<p>
			<label title="<?php _e('Order posts in ascending or descending order', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'order' ); ?>"><code>order</code></label>
			<select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'ASC' == $instance['order'] ) echo ' selected="selected"'; ?>>ASC</option>
				<option <?php if ( 'DESC' == $instance['order'] ) echo ' selected="selected"'; ?>>DESC</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('What criteria the posts should be ordered by', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code>orderby</code></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'author' == $instance['orderby'] ) echo ' selected="selected"'; ?>>author</option>
				<option <?php if ( 'date' == $instance['orderby'] ) echo ' selected="selected"'; ?>>date</option>
				<option <?php if ( 'ID' == $instance['orderby'] ) echo ' selected="selected"'; ?>>ID</option>
				<option <?php if ( 'menu_order' == $instance['orderby'] ) echo ' selected="selected"'; ?>>menu_order</option>
				<option <?php if ( 'meta_value' == $instance['orderby'] ) echo ' selected="selected"'; ?>>meta_value</option>
				<option <?php if ( 'modified' == $instance['orderby'] ) echo ' selected="selected"'; ?>>modified</option>
				<option <?php if ( 'none' == $instance['orderby'] ) echo ' selected="selected"'; ?>>none</option>
				<option <?php if ( 'parent' == $instance['orderby'] ) echo ' selected="selected"'; ?>>parent</option>
				<option <?php if ( 'rand' == $instance['orderby'] ) echo ' selected="selected"'; ?>>rand</option>
				<option <?php if ( 'title' == $instance['orderby'] ) echo ' selected="selected"'; ?>>title</option>
			</select>
		</p>
		<p>
			<label title="<?php _e('A single post ID', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'p' ); ?>"><code>p</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'p' ); ?>" name="<?php echo $this->get_field_name( 'p' ); ?>" value="<?php echo $instance['p']; ?>" />
		</p>
		<p>
			<label title="<?php _e('A single post slug', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'name' ); ?>"><code>name</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of post IDs to include', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'post__in' ); ?>"><code>post__in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'post__in' ); ?>" name="<?php echo $this->get_field_name( 'post__in' ); ?>" value="<?php echo $instance['post__in']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of post IDs to exclude', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'post__not_in' ); ?>"><code>post__not_in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'post__not_in' ); ?>" name="<?php echo $this->get_field_name( 'post__not_in' ); ?>" value="<?php echo $instance['post__not_in']; ?>" />
		</p>
		<p>
			<label title="<?php _e('A single page ID', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'page_id' ); ?>"><code>page_id</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'page_id' ); ?>" name="<?php echo $this->get_field_name( 'page_id' ); ?>" value="<?php echo $instance['page_id']; ?>" />
		</p>
		<p>
			<label title="<?php _e('A single page path', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'pagename' ); ?>"><code>pagename</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'pagename' ); ?>" name="<?php echo $this->get_field_name( 'pagename' ); ?>" value="<?php echo $instance['pagename']; ?>" />
		</p>
		<p>
			<label title="<?php _e('User ID', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'author' ); ?>"><code>author</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" value="<?php echo $instance['author']; ?>" />
		</p>
		<p>
			<label title="<?php _e('User nicename', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'author_name' ); ?>"><code>author_name</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'author_name' ); ?>" name="<?php echo $this->get_field_name( 'author_name' ); ?>" value="<?php echo $instance['author_name']; ?>" />
		</p>

		</div>

		<div style="float:left;width:17%;margin-left:3%;">

		<p>
			<label title="<?php _e('Comma-separated list of category IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'cat' ); ?>"><code>cat</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>" value="<?php echo $instance['cat']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Category name/slug', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'category_name' ); ?>"><code>category_name</code></label>
			<select id="<?php echo $this->get_field_id( 'category_name' ); ?>" name="<?php echo $this->get_field_name( 'category_name' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( !$instance['category_name'] ) echo ' selected="selected"'; ?> value=""></option>
				<?php $cats = get_categories( array( 'type' => 'post' ) ); ?>
				<?php foreach ( $cats as $cat ) : ?>
					<option <?php if ( $cat->slug == $instance['category_name'] ) echo 'selected="selected"'; ?>><?php echo $cat->slug; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of category IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'category__and' ); ?>"><code>category__and</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'category__and' ); ?>" name="<?php echo $this->get_field_name( 'category__and' ); ?>" value="<?php echo $instance['category__and']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of category IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'category__in' ); ?>"><code>category__in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'category__in' ); ?>" name="<?php echo $this->get_field_name( 'category__in' ); ?>" value="<?php echo $instance['category__in']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of category IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'category__not_in' ); ?>"><code>category__not_in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'category__not_in' ); ?>" name="<?php echo $this->get_field_name( 'category__not_in' ); ?>" value="<?php echo $instance['category__not_in']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of tag slugs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'tag' ); ?>"><code>tag</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag' ); ?>" name="<?php echo $this->get_field_name( 'tag' ); ?>" value="<?php echo $instance['tag']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of tag IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'tag__and' ); ?>"><code>tag__and</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag__and' ); ?>" name="<?php echo $this->get_field_name( 'tag__and' ); ?>" value="<?php echo $instance['tag__and']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of tag IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'tag__in' ); ?>"><code>tag__in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag__in' ); ?>" name="<?php echo $this->get_field_name( 'tag__in' ); ?>" value="<?php echo $instance['tag__in']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of tag IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'tag_slug__and' ); ?>"><code>tag_slug__and</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag_slug__and' ); ?>" name="<?php echo $this->get_field_name( 'tag_slug__and' ); ?>" value="<?php echo $instance['tag_slug__and']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Comma-separated list of tag IDs', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'tag_slug__in' ); ?>"><code>tag_slug__in</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tag_slug__in' ); ?>" name="<?php echo $this->get_field_name( 'tag_slug__in' ); ?>" value="<?php echo $instance['tag_slug__in']; ?>" />
		</p>

		</div>

		<div style="float:left;width:17%;margin-left:3%;">

		<p>
			<label title="<?php _e('The name of the custom field key', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'meta_key' ); ?>"><code>meta_key</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'meta_key' ); ?>" name="<?php echo $this->get_field_name( 'meta_key' ); ?>" value="<?php echo $instance['meta_key']; ?>" />
		</p>
		<p>
			<label title="<?php _e('The value of the custom field', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'meta_value' ); ?>"><code>meta_value</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'meta_value' ); ?>" name="<?php echo $this->get_field_name( 'meta_value' ); ?>" value="<?php echo $instance['meta_value']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Operator to test the meta_value', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'meta_compare' ); ?>"><code>meta_compare</code></label>
			<select id="<?php echo $this->get_field_id( 'meta_compare' ); ?>" name="<?php echo $this->get_field_name( 'meta_compare' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( !$instance['meta_compare'] ) echo ' selected="selected"'; ?>></option>
				<option <?php if ( '=' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>>=</option>
				<option <?php if ( '!=' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>>!=</option>
				<option <?php if ( '>' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>>></option>
				<option <?php if ( '>=' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>>>=</option>
				<option <?php if ( '<' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>><</option>
				<option <?php if ( '<=' == $instance['meta_compare'] ) echo ' selected="selected"'; ?>><=</option>
			</select>
		</p>

		<p>
			<label title="<?php _e('Show posts from a specific year (4 digits)', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'year' ); ?>"><code>year</code></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'year' ); ?>" name="<?php echo $this->get_field_name( 'year' ); ?>" value="<?php echo $instance['year']; ?>" />
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific month', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'monthnum' ); ?>"><code>monthnum</code></label>
			<select id="<?php echo $this->get_field_id( 'monthnum' ); ?>" name="<?php echo $this->get_field_name( 'monthnum' ); ?>" class="widefat" style="width:100%;">
				<?php $months = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ); ?>
				<?php foreach ( $months as $month ) : ?>
					<option <?php if ( $month == $instance['monthnum'] ) echo 'selected="selected"'; ?>><?php echo $month; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific week', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'w' ); ?>"><code>w</code></label>
			<select id="<?php echo $this->get_field_id( 'w' ); ?>" name="<?php echo $this->get_field_name( 'w' ); ?>" class="widefat" style="width:100%;">
				<?php $weeks = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53 ); ?>
				<?php foreach ( $weeks as $week ) : ?>
					<option <?php if ( $week == $instance['w'] ) echo 'selected="selected"'; ?>><?php echo $week; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific day', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'day' ); ?>"><code>day</code></label>
			<select id="<?php echo $this->get_field_id( 'day' ); ?>" name="<?php echo $this->get_field_name( 'day' ); ?>" class="widefat" style="width:100%;">
				<?php $days = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 ); ?>
				<?php foreach ( $days as $day ) : ?>
					<option <?php if ( $day == $instance['day'] ) echo 'selected="selected"'; ?>><?php echo $day; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific hour', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'hour' ); ?>"><code>hour</code></label>
			<select id="<?php echo $this->get_field_id( 'hour' ); ?>" name="<?php echo $this->get_field_name( 'hour' ); ?>" class="widefat" style="width:100%;">
				<?php $hours = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23 ); ?>
				<?php foreach ( $hours as $hour ) : ?>
					<option <?php if ( $hour == $instance['hour'] ) echo 'selected="selected"'; ?>><?php echo $hour; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific minute', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'minute' ); ?>"><code>minute</code></label>
			<select id="<?php echo $this->get_field_id( 'minute' ); ?>" name="<?php echo $this->get_field_name( 'minute' ); ?>" class="widefat" style="width:100%;">
				<?php $minutes = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60 ); ?>
				<?php foreach ( $minutes as $minute ) : ?>
					<option <?php if ( $minute == $instance['minute'] ) echo 'selected="selected"'; ?>><?php echo $minute; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label title="<?php _e('Show posts from a specific second', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'second' ); ?>"><code>second</code></label>
			<select id="<?php echo $this->get_field_id( 'second' ); ?>" name="<?php echo $this->get_field_name( 'second' ); ?>" class="widefat" style="width:100%;">
				<?php $seconds = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60 ); ?>
				<?php foreach ( $seconds as $second ) : ?>
					<option <?php if ( $second == $instance['second'] ) echo 'selected="selected"'; ?>><?php echo $second; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		</div>

		<div style="float:left;width:17%;margin-left:3%;">

		<?php $taxonomies = get_object_taxonomies( 'post' ); ?>
		<?php if ( is_array( $taxonomies ) ) : ?>
			<?php foreach ( $taxonomies as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
				<?php if ( $tax->query_var ) : ?>

		<p>
			<label title="<?php printf( __('Select a term from the %1$s taxonomy', 'query-posts'), $tax->name ); ?>" for="<?php echo $this->get_field_id( $tax->query_var ); ?>"><code><?php echo $tax->query_var; ?></code></label>
			<select id="<?php echo $this->get_field_id( $tax->query_var ); ?>" name="<?php echo $this->get_field_name( $tax->query_var ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( !$instance[$tax->query_var] ) echo ' selected="selected"'; ?> value=""></option>
				<?php $terms = get_terms( $taxonomy ); ?>
				<?php foreach ( $terms as $term ) : ?>
					<option <?php if ( $term->slug == $instance[$tax->query_var] ) echo 'selected="selected"'; ?>><?php echo $term->slug; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<p>
			<label title="<?php _e('Reset the query back to the original after showing posts', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'wp_reset_query' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['wp_reset_query'], true ); ?> id="<?php echo $this->get_field_id( 'wp_reset_query' ); ?>" name="<?php echo $this->get_field_name( 'wp_reset_query' ); ?>" /> <code>wp_reset_query</code></label>
		</p>
		<p>
			<label title="<?php _e('Remove sticky posts from query', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'caller_get_posts' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['caller_get_posts'], true ); ?> id="<?php echo $this->get_field_id( 'caller_get_posts' ); ?>" name="<?php echo $this->get_field_name( 'caller_get_posts' ); ?>" /> <code>caller_get_posts</code></label>
		</p>
		<?php if ( function_exists( 'get_the_image' ) ) : ?>
		<p>
			<label title="<?php _e('Show a thumbnail with your posts', 'query-posts'); ?>" for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['thumbnail'], true ); ?> id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" /> <code>get_the_image</code></label>
		</p>
		<?php endif; ?>

		</div>

		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>