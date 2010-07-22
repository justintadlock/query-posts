<?php
/**
 * A widget that allows you to easily show posts in any way.
 * There are numerous input options to choose from.
 *
 * @package QueryPosts
 */

/**
 * Advanced posts widget
 * Arguments are input through the widget control panel
 *
 * @since 0.1
 */
function widget_query_posts( $args, $widget_args = 1 ) {

	extract( $args, EXTR_SKIP );

	if ( is_numeric( $widget_args ) )
		$widget_args = array( 'number' => $widget_args );

	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

	extract( $widget_args, EXTR_SKIP );

	$options = get_option( 'widget_query_posts' );

	if ( !isset( $options[$number] ) )
		return;

	/*
	* Widget title and things not in query arguments
	*/
	$title = apply_filters( 'widget_title', $options[$number]['title'] );
	$display = $options[$number]['display'];
	$wp_reset_query = $options[$number]['wp_reset_query'];
	$thumbnail = $options[$number]['thumbnail'];

	/*
	* Ordering and such
	*/
	$showposts = $options[$number]['showposts'];
	$offset = $options[$number]['offset'];
	$posts_per_page = $options[$number]['posts_per_page'];
	$paged = $options[$number]['paged'];
	$order = $options[$number]['order'];
	$orderby = $options[$number]['orderby'];

	/*
	* Category arguments
	*/
	$cat = $options[$number]['cat'];
	$category_name = $options[$number]['category_name'];
	if ( $options[$number]['category__and'] )
		$category__and = explode( ',', str_replace( ' ', '', $options[$number]['category__and'] ) );
	if ( $options[$number]['category__in'] )
		$category__in = explode( ',', str_replace( ' ', '', $options[$number]['category__in'] ) );
	if ( $options[$number]['category__not_in'] )
		$category__not_in = explode( ',', str_replace( ' ', '', $options[$number]['category__not_in'] ) );

	/*
	* Tag arguments
	*/
	$tag = $options[$number]['tag'];
	if ( $options[$number]['tag__and'] )
		$tag__and = explode( ',', str_replace( ' ', '', $options[$number]['tag__and'] ) );
	if ( $options[$number]['tag__in'] )
		$tag__in = explode( ',', str_replace( ' ', '', $options[$number]['tag__in'] ) );
	if ( $options[$number]['tag_slug__and'] )
		$tag_slug__and = explode( ',', str_replace( ' ', '', $options[$number]['tag_slug__and'] ) );
	if ( $options[$number]['tag_slug__in'] )
		$tag_slug__in = explode( ',', str_replace( ' ', '', $options[$number]['tag_slug__in'] ) );

	/*
	* Post arguments
	*/
	$p = (int)$options[$number]['p'];
	$name = $options[$number]['name'];
	if ( $options[$number]['post__in'] )
		$post__in = explode( ',', str_replace( ' ', '', $options[$number]['post__in'] ) );
	if ( $options[$number]['post__not_in'] )
		$post__not_in = explode( ',', str_replace( ' ', '', $options[$number]['post__not_in'] ) );

	/*
	* Page arguments
	*/
	$page_id = (int)$options[$number]['page_id'];
	$pagename = $options[$number]['pagename'];

	/*
	* Author arguments
	*/
	$author_name = $options[$number]['author_name'];
	$author = (int)$options[$number]['author'];

	/*
	* Time arguments
	*/
	$hour = $options[$number]['hour'];
	$minute = $options[$number]['minute'];
	$second = $options[$number]['second'];
	$day = $options[$number]['day'];
	$monthnum = $options[$number]['monthnum'];
	$year = $options[$number]['year'];
	$w = $options[$number]['w'];

	/*
	* Meta arguments
	*/
	$meta_key = $options[$number]['meta_key'];
	$meta_value = $options[$number]['meta_value'];
	$meta_compare = $options[$number]['meta_compare'];

	/*
	* Sticky posts
	*/
	$caller_get_posts = $options[$number]['caller_get_posts'] ? '1' : '0';

	$args = array(
		'showposts' => $showposts,		// number of posts to show - int
		'offset' => $offset,
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		'order' => $order,		// ASC || DESC - string
		'orderby' => $orderby,		// author, date, category, title, modified, menu_order, parent, ID, rand - string
		'caller_get_posts' => $caller_get_posts,		// 1 (exclude stickies) - int

		'cat' => $cat,		// multiple cat IDs - string
		'category_name' => $category_name,	// single cat - string
		'category__and' => $category__and,	// cat && cat - array
		'category__in' => $category__in,	// cat || cat - array
		'category__not_in' => $category__not_in,	// exclude multiple cats - array

		'tag' => $tag, // string
		'tag__and' => $tag__and,		// tag && tag - array
		'tag__in' => $tag__in,		// tag || tag - array
		'tag_slug__and' => $tag_slug__and,	// slug && slug - array
		'tag_slug__in' => $tag_slug__in,	// slug || slug - array

		'p' => $p,				// ID - int
		'name' => $name,			// post slug -string
		'post__in' => $post__in,		// post IDs - array
		'post__not_in' => $post__not_in,	// post IDs - array

		'page_id' => $page_id,		// page ID - int
		'pagename' => $pagename,		// page's path - string

		'hour' => $hour,		// int
		'minute' => $minute,		// int
		'second' => $second,		// int
		'day' => $day,		// int
		'monthnum' => $monthnum,		// int
		'year' => $year,		// int
		'w' => $w,		//int

		'author_name' => $author_name,	// user_nicename - string
		'author' => $author,			// ID - int

		'meta_key' => $meta_key,
		'meta_value' => $meta_value,
		'meta_compare' => $meta_compare,
	);

	echo $before_widget;

	if ( $title )
		echo $before_title . $title . $after_title;

	query_posts( $args );

	if ( $display == 'list' ) : ?>

		<ul>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php the_title( '<li><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></li>' ); ?>
		<?php endwhile; endif; ?>
		</ul>

	<?php else: ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<div <?php if ( function_exists( 'post_class' ) ) post_class(); else echo 'class="post"'; ?>>

				<?php if ( function_exists( 'get_the_image' ) && $thumbnail ) : ?>
					<?php get_the_image( array( 'custom_key' => array( 'Thumbnail', 'thumbnail' ), 'default_size' => 'thumbnail' ) ); ?>
				<?php endif; ?>

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

				<p class="entry-meta">
					<span class="categories"><span class="text"><?php _e('Posted in', 'query-posts'); ?></span> <?php the_category( ', ' ); ?></span> 
					<?php the_tags( '<span class="tags"> <span class="separator">|</span> <span class="text">' . __('Tagged', 'query-posts') . '</span> ', ', ', '</span>' ); ?> 
					<?php if ( comments_open() ) : ?><span class="separator">|</span><?php endif; ?> <?php comments_popup_link( __('Leave a response', 'query-posts'), __('1 Response', 'query-posts'), __('% Responses', 'query-posts'), 'comments-link', false ); ?> 
				</p>
			</div>

		<?php endwhile; endif; ?>

	<?php endif;

	if ( $wp_reset_query )
		wp_reset_query();

	echo $after_widget;
}

/**
 * Widget controls for the posts widget
 * Options are chosen from user input from the widget panel
 *
 * @since 0.1
 */
function widget_query_posts_control( $widget_args ) {

	global $wp_registered_widgets;

	static $updated = false;

	if ( is_numeric( $widget_args ) )
		$widget_args = array( 'number' => $widget_args );

	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

	extract( $widget_args, EXTR_SKIP );

	$options = get_option( 'widget_query_posts' );

	if ( !is_array( $options ) )
		$options = array();

	if ( !$updated && !empty( $_POST['sidebar'] ) ) :

		$sidebar = (string)$_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( isset( $sidebars_widgets[$sidebar] ) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) :

			if ( 'widget_query_posts' == $wp_registered_widgets[$_widget_id]['callback'] && isset( $wp_registered_widgets[$_widget_id]['params'][0]['number'] ) ) :

				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];

				unset( $options[$widget_number] );

			endif;

		endforeach;

		foreach ( (array)$_POST['widget-query-posts'] as $widget_number => $widget_query_posts ) :

			$title = strip_tags( stripslashes( $widget_query_posts['title'] ) );
			$display = strip_tags( stripslashes( $widget_query_posts['display'] ) );
			$thumbnail = strip_tags( stripslashes( $widget_query_posts['thumbnail'] ) );
			$wp_reset_query = strip_tags( stripslashes( $widget_query_posts['wp_reset_query'] ) );

			$showposts = strip_tags( stripslashes( $widget_query_posts['showposts'] ) );
			$offset = strip_tags( stripslashes( $widget_query_posts['offset'] ) );
			$posts_per_page = strip_tags( stripslashes( $widget_query_posts['posts_per_page'] ) );
			$paged = strip_tags( stripslashes( $widget_query_posts['paged'] ) );
			$order = strip_tags( stripslashes( $widget_query_posts['order'] ) );
			$orderby = strip_tags( stripslashes( $widget_query_posts['orderby'] ) );

			$cat = strip_tags( stripslashes( $widget_query_posts['cat'] ) );
			$category_name = strip_tags( stripslashes( $widget_query_posts['category_name'] ) );
			$category_name = strip_tags( stripslashes( $widget_query_posts['category_name'] ) );
			$category__and = strip_tags( stripslashes( $widget_query_posts['category__and'] ) );
			$category__in = strip_tags( stripslashes( $widget_query_posts['category__in'] ) );
			$category__not_in = strip_tags( stripslashes( $widget_query_posts['category__not_in'] ) );

			$tag = strip_tags( stripslashes( $widget_query_posts['tag'] ) );
			$tag__and = strip_tags( stripslashes( $widget_query_posts['tag__and'] ) );
			$tag__in = strip_tags( stripslashes( $widget_query_posts['tag__in'] ) );
			$tag_slug__and = strip_tags( stripslashes( $widget_query_posts['tag_slug__and'] ) );
			$tag_slug__in = strip_tags( stripslashes( $widget_query_posts['tag_slug__in'] ) );

			$p = strip_tags( stripslashes( $widget_query_posts['p'] ) );
			$name = strip_tags( stripslashes( $widget_query_posts['name'] ) );
			$post__in = strip_tags( stripslashes( $widget_query_posts['post__in'] ) );
			$post__not_in = strip_tags( stripslashes( $widget_query_posts['post__not_in'] ) );

			$page_id = strip_tags( stripslashes( $widget_query_posts['page_id'] ) );
			$pagename = strip_tags( stripslashes( $widget_query_posts['pagename'] ) );

			$hour = strip_tags( stripslashes( $widget_query_posts['hour'] ) );
			$minute = strip_tags( stripslashes( $widget_query_posts['minute'] ) );
			$second = strip_tags( stripslashes( $widget_query_posts['second'] ) );
			$day = strip_tags( stripslashes( $widget_query_posts['day'] ) );
			$monthnum = strip_tags( stripslashes( $widget_query_posts['monthnum'] ) );
			$year = strip_tags( stripslashes( $widget_query_posts['year'] ) );
			$w = strip_tags( stripslashes( $widget_query_posts['w'] ) );

			$author_name = strip_tags( stripslashes( $widget_query_posts['author_name'] ) );
			$author = strip_tags( stripslashes( $widget_query_posts['author'] ) );

			$meta_key = strip_tags( stripslashes( $widget_query_posts['meta_key'] ) );
			$meta_value = strip_tags( stripslashes( $widget_query_posts['meta_value'] ) );
			$meta_compare = strip_tags( stripslashes( $widget_query_posts['meta_compare'] ) );

			$caller_get_posts = strip_tags( stripslashes( $widget_query_posts['caller_get_posts'] ) );

			$options[$widget_number] = compact( 'title', 'display', 'thumbnail', 'wp_reset_query', 'showposts', 'offset', 'posts_per_page', 'paged', 'order', 'orderby', 'cat', 'category__and', 'category__in', 'category__not_in', 'category_name', 'caller_get_posts', 'tag', 'tag__and', 'tag__in', 'tag_slug__and', 'tag_slug__in', 'p', 'name', 'post__in', 'post__not_in', 'page_id', 'pagename', 'hour', 'minute', 'second', 'day', 'monthnum', 'year', 'w', 'author_name', 'author', 'meta_key', 'meta_value', 'meta_compare' );

		endforeach;

		update_option( 'widget_query_posts', $options );

		$updated = true;

	endif;

	if ( $number == -1 ) :
		$title = '';
		$display = '';
		$thumbnail = '';
		$wp_reset_query = '';
		$showposts = '';
		$offset = '';
		$posts_per_page = '';
		$order = '';
		$orderby = '';
		$paged = '';
		$caller_get_posts = '';
		$cat = '';
		$category_name = '';
		$category__and = '';
		$category__in = '';
		$category__not_in = '';
		$tag = '';
		$tag__and = '';
		$tag__in = '';
		$tag_slug__and = '';
		$tag_slug__in = '';
		$p = '';
		$name = '';
		$post__in = '';
		$post__not_in = '';
		$page_id = '';
		$pagename = '';
		$hour = '';
		$minute = '';
		$second = '';
		$day = '';
		$monthnum = '';
		$year = '';
		$w = '';
		$author_name = '';
		$author = '';
		$meta_key = '';
		$meta_value = '';
		$meta_compare = '';
		$number = '%i%';
	else :
		$title = attribute_escape( $options[$number]['title'] );
		$display = attribute_escape( $options[$number]['display'] );
		$thumbnail = attribute_escape( $options[$number]['thumbnail'] );
		$wp_reset_query = attribute_escape( $options[$number]['wp_reset_query'] );
		$showposts = attribute_escape( $options[$number]['showposts'] );
		$offset = attribute_escape( $options[$number]['offset'] );
		$posts_per_page = attribute_escape( $options[$number]['posts_per_page'] );
		$paged = attribute_escape( $options[$number]['paged'] );
		$order = attribute_escape( $options[$number]['order'] );
		$orderby = attribute_escape( $options[$number]['orderby'] );
		$cat = attribute_escape( $options[$number]['cat'] );
		$category_name = attribute_escape( $options[$number]['category_name'] );
		$category__and = attribute_escape( $options[$number]['category__and'] );
		$category__in = attribute_escape( $options[$number]['category__in'] );
		$category__not_in = attribute_escape( $options[$number]['category__not_in'] );
		$caller_get_posts = attribute_escape( $options[$number]['caller_get_posts'] );
		$tag = attribute_escape( $options[$number]['tag'] );
		$tag__and = attribute_escape( $options[$number]['tag__and'] );
		$tag__in = attribute_escape( $options[$number]['tag__in'] );
		$tag_slug__and = attribute_escape( $options[$number]['tag_slug__and'] );
		$tag_slug__in = attribute_escape( $options[$number]['tag_slug__in'] );
		$p = attribute_escape( $options[$number]['p'] );
		$name = attribute_escape( $options[$number]['name'] );
		$post__in = attribute_escape( $options[$number]['post__in'] );
		$post__not_in = attribute_escape( $options[$number]['post__not_in'] );
		$page_id = attribute_escape( $options[$number]['page_id'] );
		$pagename = attribute_escape( $options[$number]['pagename'] );
		$hour = attribute_escape( $options[$number]['hour'] );
		$minute = attribute_escape( $options[$number]['minute'] );
		$second = attribute_escape( $options[$number]['second'] );
		$day = attribute_escape( $options[$number]['day'] );
		$monthnum = attribute_escape( $options[$number]['monthnum'] );
		$year = attribute_escape( $options[$number]['year'] );
		$w = attribute_escape( $options[$number]['w'] );
		$author_name = attribute_escape( $options[$number]['author_name'] );
		$author = attribute_escape( $options[$number]['author'] );
		$meta_key = attribute_escape( $options[$number]['meta_key'] );
		$meta_value = attribute_escape( $options[$number]['meta_value'] );
		$meta_compare = attribute_escape( $options[$number]['meta_compare'] );
	endif;
?>

	<div style="width:24%;float:left;margin-right:1.5%;">
	<p>
		<label for="query-posts-title-<?php echo $number; ?>">
			<?php _e('Widget Title:', 'query-posts'); ?>
		</label>
		<input id="query-posts-title-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-display-<?php echo $number; ?>">
			<?php _e('Display:', 'query-posts'); ?>
		</label>

		<select id="query-posts-display-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][display]" class="widefat" style="width:100%;">
			<option <?php if ( 'the_content' == $display ) echo 'selected="selected"'; ?>>the_content</option>
			<option <?php if ( 'the_excerpt' == $display ) echo 'selected="selected"'; ?>>the_excerpt</option>
			<option <?php if ( 'list' == $display ) echo 'selected="selected"'; ?>>list</option>
		</select>
	</p>
	<p>
		<label for="query-posts-showposts-<?php echo $number; ?>">
			<?php _e('Show Posts:', 'query-posts'); ?> <code>showposts</code>
		</label>
		<input id="query-posts-showposts-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][showposts]" type="text" value="<?php echo $showposts; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-offset-<?php echo $number; ?>">
			<?php _e('Offset:', 'query-posts'); ?> <code>offset</code>
		</label>
		<input id="query-posts-offset-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][offset]" type="text" value="<?php echo $offset ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-posts_per_page-<?php echo $number; ?>">
			<?php _e('Per Page:', 'query-posts'); ?> <code>posts_per_page</code>
		</label>
		<input id="query-posts-posts_per_page-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][posts_per_page]" type="text" value="<?php echo $posts_per_page; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-paged-<?php echo $number; ?>">
			<?php _e('Paged:', 'query-posts'); ?> <code>paged</code>
		</label>
		<input id="query-posts-paged-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][paged]" type="text" value="<?php echo $paged; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-order-<?php echo $number; ?>">
			<?php _e('Order:', 'query-posts'); ?> <code>order</code>
		</label>

		<select id="query-posts-order-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][order]" class="widefat" style="width:100%;">
			<option <?php if ( 'ASC' == $order ) echo 'selected="selected"'; ?>>ASC</option>
			<option <?php if ( 'DESC' == $order ) echo 'selected="selected"'; ?>>DESC</option>
		</select>
	</p>
	<p>
		<label for="query-posts-orderby-<?php echo $number; ?>">
			<?php _e('Order By:', 'query-posts'); ?> <code>orderby</code>
		</label>

		<select id="query-posts-orderby-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][orderby]" class="widefat" style="width:100%;">
			<option <?php if ( 'author' == $orderby ) echo 'selected="selected"'; ?>>author</option>
			<option <?php if ( 'category' == $orderby ) echo 'selected="selected"'; ?>>category</option>
			<option <?php if ( 'date' == $orderby ) echo 'selected="selected"'; ?>>date</option>
			<option <?php if ( 'ID' == $orderby ) echo 'selected="selected"'; ?>>ID</option>
			<option <?php if ( 'menu_order' == $orderby ) echo 'selected="selected"'; ?>>menu_order</option>
			<option <?php if ( 'modified' == $orderby ) echo 'selected="selected"'; ?>>modified</option>
			<option <?php if ( 'parent' == $orderby ) echo 'selected="selected"'; ?>>parent</option>
			<option <?php if ( 'rand' == $orderby ) echo 'selected="selected"'; ?>>rand</option>
			<option <?php if ( 'title' == $orderby ) echo 'selected="selected"'; ?>>title</option>
		</select>
	</p>
	<p>
		<input type="checkbox" id="query-posts-wp_reset_query-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][wp_reset_query]" <?php if ( $wp_reset_query == 'on' ) echo ' checked="checked"'; ?> />
		<label for="query-posts-wp_reset_query-<?php echo $number; ?>">
			<?php _e('Reset?', 'query-posts'); ?> <code>wp_reset_query</code>
		</label>
	</p>
	<p>
		<input type="checkbox" id="query-posts-caller_get_posts-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][caller_get_posts]" <?php if ( $caller_get_posts == 'on' ) echo ' checked="checked"'; ?> />
		<label for="query-posts-caller_get_posts-<?php echo $number; ?>">
			<del><?php _e('Stickies?', 'query-posts'); ?></del> <code>caller_get_posts</code>
		</label>
	</p>
	<?php if ( function_exists( 'get_the_image' ) ) : ?>
	<p>
		<input type="checkbox" id="query-posts-thumbnail-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][thumbnail]" <?php if ( $thumbnail == 'on' ) echo ' checked="checked"'; ?> />
		<label for="query-posts-thumbnail-<?php echo $number; ?>">
			<?php _e('Thumbnail?', 'query-posts'); ?> <code>get_the_image</code>
		</label>
	</p>
	<?php endif; ?>
	</div>
	<div style="width:24%;float:left;margin-right:1.5%;">
	<p>
		<label for="query-posts-p-<?php echo $number; ?>">
			<?php _e('Post ID:', 'query-posts'); ?> <code>p</code>
		</label>
		<input id="query-posts-p-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][p]" type="text" value="<?php echo $p; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-name-<?php echo $number; ?>">
			<?php _e('Post Slug:', 'query-posts'); ?> <code>name</code>
		</label>
		<input id="query-posts-name-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][name]" type="text" value="<?php echo $name; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-post__in-<?php echo $number; ?>">
			<?php _e('Post In:', 'query-posts'); ?> <code>post__in</code>
		</label>
		<input id="query-posts-post__in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][post__in]" type="text" value="<?php echo $post__in; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-post__not_in-<?php echo $number; ?>">
			<?php _e('Post Not In:', 'query-posts'); ?> <code>post__not_in</code>
		</label>
		<input id="query-posts-post__not_in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][post__not_in]" type="text" value="<?php echo $post__not_in; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-cat-<?php echo $number; ?>">
			<?php _e('Category:', 'query-posts'); ?> <code>cat</code>
		</label>
		<input id="query-posts-cat-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][cat]" type="text" value="<?php echo $cat; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-category_name-<?php echo $number; ?>">
			<?php _e('Category Name:', 'query-posts'); ?> <code>category_name</code>
		</label>
		<?php
			$cats = get_categories( array( 'type' => 'post' ) );
			$cats[] = false;
		?>
		<select id="query-posts-category_name-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][category_name]" class="widefat" style="width:100%;">
			<?php foreach ( $cats as $cat ) : ?>
				<option <?php if ( $cat->slug == $category_name ) echo 'selected="selected"'; ?>><?php echo $cat->slug; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-category__and-<?php echo $number; ?>">
			<?php _e('Cat And:', 'query-posts'); ?> <code>category__and</code>
		</label>
		<input id="query-posts-category__and-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][category__and]" type="text" value="<?php echo $category__and; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-category__in-<?php echo $number; ?>">
			<?php _e('Cat In:', 'query-posts'); ?> <code>category__in</code>
		</label>
		<input id="query-posts-category__in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][category__in]" type="text" value="<?php echo $category__in; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-category__not_in-<?php echo $number; ?>">
			<?php _e('Cat Not In:', 'query-posts'); ?> <code>category__not_in</code>
		</label>
		<input id="query-posts-category__not_in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][category__not_in]" type="text" value="<?php echo $category__not_in; ?>" style="width:100%;" />
	</p>
	</div>
	<div style="width:24%;float:left;margin-right:1%;">
	<p>
		<label for="query-posts-page_id-<?php echo $number; ?>">
			<?php _e('Page ID:', 'query-posts'); ?> <code>page_id</code>
		</label>
		<input id="query-posts-page_id-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][page_id]" type="text" value="<?php echo $page_id; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-pagename-<?php echo $number; ?>">
			<?php _e('Page Path:', 'query-posts'); ?> <code>pagename</code>
		</label>
		<input id="query-posts-pagename-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][pagename]" type="text" value="<?php echo $pagename; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-tag-<?php echo $number; ?>">
			<?php _e('Tag:', 'query-posts'); ?> <code>tag</code>
		</label>
		<input id="query-posts-tag-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][tag]" type="text" value="<?php echo $tag; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-tag__and-<?php echo $number; ?>">
			<?php _e('Tag And:', 'query-posts'); ?> <code>tag__and</code>
		</label>
		<input id="query-posts-tag__and-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][tag__and]" type="text" value="<?php echo $tag__and; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-tag__in-<?php echo $number; ?>">
			<?php _e('Tag In:', 'query-posts'); ?> <code>tag__in</code>
		</label>
		<input id="query-posts-tag__in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][tag__in]" type="text" value="<?php echo $tag__in; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-tag_slug__and-<?php echo $number; ?>">
			<?php _e('Tag Slug And:', 'query-posts'); ?> <code>tag_slug__and</code>
		</label>
		<input id="query-posts-tag_slug__and-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][tag_slug__and]" type="text" value="<?php echo $tag_slug__and; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-tag_slug__in-<?php echo $number; ?>">
			<?php _e('Tag Slug In:', 'query-posts'); ?> <code>tag_slug__in</code>
		</label>
		<input id="query-posts-tag_slug__in-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][tag_slug__in]" type="text" value="<?php echo $tag_slug__in; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-author-<?php echo $number; ?>">
			<?php _e('Author:', 'query-posts'); ?> <code>author</code>
		</label>
		<input id="query-posts-author-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][author]" type="text" value="<?php echo $author; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-author_name-<?php echo $number; ?>">
			<?php _e('Author Name:', 'query-posts'); ?> <code>author_name</code>
		</label>
		<input id="query-posts-author_name-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][author_name]" type="text" value="<?php echo $author_name; ?>" style="width:100%;" />
	</p>
	</div>
	<div style="width:24%;float:right;">
	<p>
		<label for="query-posts-year-<?php echo $number; ?>">
			<?php _e('Year:', 'query-posts'); ?> <code>year</code>
		</label>
		<input id="query-posts-year-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][year]" type="text" value="<?php echo $year; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-monthnum-<?php echo $number; ?>">
			<?php _e('Month:', 'query-posts'); ?> <code>monthnum</code>
		</label>
		<?php $all_months = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ); ?>

		<select id="query-posts-monthnum-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][monthnum]" class="widefat" style="width:100%;">
			<?php foreach ( $all_months as $the_month ) : ?>
				<option <?php if ( $the_month == $monthnum ) echo 'selected="selected"'; ?>><?php echo $the_month; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-w-<?php echo $number; ?>">
			<?php _e('Week:', 'query-posts'); ?> <code>w</code>
		</label>
		<?php $all_ws = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52 ); ?>

		<select id="query-posts-w-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][w]" class="widefat" style="width:100%;">
			<?php foreach ( $all_ws as $the_w ) : ?>
				<option <?php if ( $the_w == $w ) echo 'selected="selected"'; ?>><?php echo $the_w; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-day-<?php echo $number; ?>">
			<?php _e('Day:', 'query-posts'); ?> <code>day</code>
		</label>
		<?php $all_days = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31 ); ?>

		<select id="query-posts-day-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][day]" class="widefat" style="width:100%;">
			<?php foreach ( $all_days as $the_day ) : ?>
				<option <?php if ( $the_day == $day ) echo 'selected="selected"'; ?>><?php echo $the_day; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-hour-<?php echo $number; ?>">
			<?php _e('Hour:', 'query-posts'); ?> <code>hour</code>
		</label>
		<?php $all_hours = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24 ); ?>

		<select id="query-posts-hour-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][hour]" class="widefat" style="width:100%;">
			<?php foreach ( $all_hours as $the_hour ) : ?>
				<option <?php if ( $the_hour == $hour ) echo 'selected="selected"'; ?>><?php echo $the_hour; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-minute-<?php echo $number; ?>">
			<?php _e('Minute:', 'query-posts'); ?> <code>minute</code>
		</label>
		<?php $all_minutes = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60 ); ?>

		<select id="query-posts-minute-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][minute]" class="widefat" style="width:100%;">
			<?php foreach ( $all_minutes as $the_minute ) : ?>
				<option <?php if ( $the_minute == $minute ) echo 'selected="selected"'; ?>><?php echo $the_minute; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-second-<?php echo $number; ?>">
			<?php _e('Second:', 'query-posts'); ?> <code>second</code>
		</label>
		<?php $all_seconds = array( false, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60 ); ?>

		<select id="query-posts-second-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][second]" class="widefat" style="width:100%;">
			<?php foreach ( $all_seconds as $the_second ) : ?>
				<option <?php if ( $the_second == $second ) echo 'selected="selected"'; ?>><?php echo $the_second; ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="query-posts-meta_key-<?php echo $number; ?>">
			<?php _e('Meta Key:', 'query-posts'); ?> <code>meta_key</code>
		</label>
		<input id="query-posts-meta_key-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][meta_key]" type="text" value="<?php echo $meta_key; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-meta_value-<?php echo $number; ?>">
			<?php _e('Meta Value:', 'query-posts'); ?> <code>meta_value</code>
		</label>
		<input id="query-posts-meta_value-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][meta_value]" type="text" value="<?php echo $meta_value; ?>" style="width:100%;" />
	</p>
	<p>
		<label for="query-posts-meta_compare-<?php echo $number; ?>">
			<?php _e('Meta Compare:', 'query-posts'); ?> <code>meta_compare</code>
		</label>

		<select id="query-posts-meta_compare-<?php echo $number; ?>" name="widget-query-posts[<?php echo $number; ?>][meta_compare]" class="widefat" style="width:100%;">
			<option <?php if ( !$meta_compare ) echo 'selected="selected"'; ?>></option>
			<option <?php if ( '=' == $meta_compare ) echo 'selected="selected"'; ?>>=</option>
			<option <?php if ( '!=' == $meta_compare ) echo 'selected="selected"'; ?>>!=</option>
			<option <?php if ( '>' == $meta_compare ) echo 'selected="selected"'; ?>>></option>
			<option <?php if ( '>=' == $meta_compare ) echo 'selected="selected"'; ?>>>=</option>
			<option <?php if ( '<' == $meta_compare ) echo 'selected="selected"'; ?>><</option>
			<option <?php if ( '<=' == $meta_compare ) echo 'selected="selected"'; ?>><=</option>
		</select>
	</p>
	</div>

	<p style="clear:both;">
		<input type="hidden" id="query-posts-submit-<?php echo $number; ?>" name="query-posts-submit-<?php echo $number; ?>" value="1" />
	</p>
<?php
}

/**
 * Register the posts widget
 * Register the posts widget controls
 *
 * @since 0.1
 */
function widget_query_posts_register() {

	if ( !$options = get_option( 'widget_query_posts' ) )
		$options = array();

	$widget_ops = array(
		'classname' => 'posts',
		'description' => __('Display posts however you want.', 'query-posts'),
	);

	$control_ops = array(
		'width' => 820,
		'height' => 350,
		'id_base' => 'query-posts',
	);

	$name = __('Query Posts', 'query-posts');

	$id = false;

	foreach ( array_keys( $options ) as $o ) :

		if ( !isset( $options[$o]['title'] ) )
			continue;

		$id = 'query-posts-' . $o;

		wp_register_sidebar_widget( $id, $name, 'widget_query_posts', $widget_ops, array( 'number' => $o ) );

		wp_register_widget_control( $id, $name, 'widget_query_posts_control', $control_ops, array( 'number' => $o ) );

	endforeach;

	if ( !$id ) :

		wp_register_sidebar_widget( 'query-posts-1', $name, 'widget_query_posts', $widget_ops, array( 'number' => -1 ) );

		wp_register_widget_control( 'query-posts-1', $name, 'widget_query_posts_control', $control_ops, array( 'number' => -1 ) );

	endif;

}

?>