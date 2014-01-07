<?php

class SiteOrigin_Panels_Widgets_Gallery extends WP_Widget {
	function __construct() {
		parent::__construct(
			'siteorigin-panels-gallery',
			__( 'Gallery (PB)', 'so-panels' ),
			array(
				'description' => __( 'Displays a gallery.', 'so-panels' ),
			)
		);
	}

	function widget( $args, $instance ) {
		echo $args['before_widget'];

		$shortcode_attr = array();
		foreach($instance as $k => $v){
			if(empty($v)) continue;
			$shortcode_attr[] = $k.'="'.esc_attr($v).'"';
		}

		echo do_shortcode('[gallery '.implode(' ', $shortcode_attr).']');

		echo $args['after_widget'];
	}

	function update( $new, $old ) {
		return $new;
	}

	function form( $instance ) {
		global $_wp_additional_image_sizes;

		$types = apply_filters('siteorigin_panels_gallery_types', array());

		$instance = wp_parse_args($instance, array(
			'ids' => '',
			'image_size' => apply_filters('siteorigin_panels_gallery_default_size', ''),
			'type' => apply_filters('siteorigin_panels_gallery_default_type', ''),
			'columns' => 3,
			'link' => '',

		));

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'ids' ) ?>"><?php _e( 'Gallery Images', 'so-panels' ) ?></label>
			<a href="#" onclick="return false;" class="so-gallery-widget-select-attachments show-in-panels hidden"><?php _e('edit gallery', 'so-panels') ?></a>
			<input type="text" class="widefat" value="<?php echo esc_attr($instance['ids']) ?>" name="<?php echo $this->get_field_name('ids') ?>" />
		</p>
		<p class="description">
			<?php _e("Comma separated attachment IDs. Defaults to all current page's attachments.") ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'size' ) ?>"><?php _e( 'Image Size', 'so-panels' ) ?></label>
			<select name="<?php echo $this->get_field_name( 'size' ) ?>" id="<?php echo $this->get_field_id( 'size' ) ?>">
				<option value="" <?php selected(empty($instance['image_size'])) ?>><?php esc_html_e('Default', 'so-panels') ?></option>
				<option value="large" <?php selected('large', $instance['image_size']) ?>><?php esc_html_e( 'Large', 'so-panels' ) ?></option>
				<option value="medium" <?php selected('medium', $instance['image_size']) ?>><?php esc_html_e( 'Medium', 'so-panels' ) ?></option>
				<option value="thumbnail" <?php selected('thumbnail', $instance['image_size']) ?>><?php esc_html_e( 'Thumbnail', 'so-panels' ) ?></option>
				<option value="full" <?php selected('full', $instance['image_size']) ?>><?php esc_html_e( 'Full', 'so-panels' ) ?></option>
				<?php if(!empty($_wp_additional_image_sizes)) : foreach ( $_wp_additional_image_sizes as $name => $info ) : ?>
					<option value="<?php echo esc_attr( $name ) ?>" <?php selected($name, $instance['image_size']) ?>><?php echo esc_html( $name ) ?></option>
				<?php endforeach; endif; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'type' ) ?>"><?php _e( 'Gallery Type', 'so-panels' ) ?></label>
			<input type="text" class="regular" value="<?php echo esc_attr($instance['type']) ?>" name="<?php echo $this->get_field_name('type') ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ) ?>"><?php _e( 'Columns', 'so-panels' ) ?></label>
			<input type="text" class="regular" value="<?php echo esc_attr($instance['columns']) ?>" name="<?php echo $this->get_field_name('columns') ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'link' ) ?>"><?php _e( 'Link To', 'so-panels' ) ?></label>
			<select name="<?php echo $this->get_field_name( 'link' ) ?>" id="<?php echo $this->get_field_id( 'link' ) ?>">
				<option value="" <?php selected('', $instance['link']) ?>><?php esc_html_e('Attachment Page', 'so-panels') ?></option>
				<option value="file" <?php selected('file', $instance['link']) ?>><?php esc_html_e('File', 'so-panels') ?></option>
				<option value="none" <?php selected('none', $instance['link']) ?>><?php esc_html_e('None', 'so-panels') ?></option>
			</select>
		</p>

		<?php
	}
}

class SiteOrigin_Panels_Widgets_PostContent extends WP_Widget {
	function __construct() {
		parent::__construct(
			'siteorigin-panels-post-content',
			__( 'Post Content (PB)', 'so-panels' ),
			array(
				'description' => __( 'Displays some form of post content form the current post.', 'so-panels' ),
			)
		);
	}

	function widget( $args, $instance ) {
		if( is_admin() ) return;

		echo $args['before_widget'];
		$content = apply_filters('siteorigin_panels_widget_post_content', $this->default_content($instance['type']));
		echo $content;
		echo $args['after_widget'];
	}

	/**
	 * The default content for post types
	 * @param $type
	 * @return string
	 */
	function default_content($type){
		global $post;
		if(empty($post)) return;

		switch($type) {
			case 'title' :
				return '<h1 class="entry-title">' . $post->post_title . '</h1>';
			case 'content' :
				return '<div class="entry-content">' . wpautop($post->post_content) . '</div>';
			case 'featured' :
				if(!has_post_thumbnail()) return '';
				return '<div class="featured-image">' . get_the_post_thumbnail($post->ID) . '</div>';
			default :
				return '';
		}
	}

	function update($new, $old){
		return $new;
	}

	function form( $instance ) {
		$instance = wp_parse_args($instance, array(
			'type' => 'content',
		));

		$types = apply_filters('siteorigin_panels_widget_post_content_types', array(
			'' => __('None', 'so-panels'),
			'title' => __('Title', 'so-panels'),
			'featured' => __('Featured Image', 'so-panels'),
		));

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ) ?>"><?php _e( 'Display Content', 'so-panels' ) ?></label>
			<select id="<?php echo $this->get_field_id( 'type' ) ?>" name="<?php echo $this->get_field_name( 'type' ) ?>">
				<?php foreach ($types as $type_id => $title) : ?>
					<option value="<?php echo esc_attr($type_id) ?>" <?php selected($type_id, $instance['type']) ?>><?php echo esc_html($title) ?></option>
				<?php endforeach ?>
			</select>
		</p>
	<?php
	}
}

class SiteOrigin_Panels_Widgets_Image extends WP_Widget {
	function __construct() {
		parent::__construct(
			'siteorigin-panels-image',
			__( 'Image (PB)', 'so-panels' ),
			array(
				'description' => __( 'Displays a simple image.', 'so-panels' ),
			)
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		echo $args['before_widget'];
		if(!empty($instance['href'])) echo '<a href="' . $instance['href'] . '">';
		echo '<img src="'.esc_url($instance['src']).'" />';
		if(!empty($instance['href'])) echo '</a>';
		echo $args['after_widget'];
	}

	function update($new, $old){
		$new = wp_parse_args($new, array(
			'src' => '',
			'href' => '',
		));
		return $new;
	}

	function form( $instance ) {
		$instance = wp_parse_args($instance, array(
			'src' => '',
			'href' => '',
		));

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'src' ) ?>"><?php _e( 'Image URL', 'so-panels' ) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'src' ) ?>" name="<?php echo $this->get_field_name( 'src' ) ?>" value="<?php echo esc_attr($instance['src']) ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href' ) ?>"><?php _e( 'Destination URL', 'so-panels' ) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href' ) ?>" name="<?php echo $this->get_field_name( 'href' ) ?>" value="<?php echo esc_attr($instance['href']) ?>" />
		</p>
	<?php
	}
}

/**
 * Display a loop of posts.
 *
 * Class SiteOrigin_Panels_Widgets_PostLoop
 */
class SiteOrigin_Panels_Widgets_PostLoop extends WP_Widget{
	function __construct() {
		parent::__construct(
			'siteorigin-panels-postloop',
			__( 'Post Loop (PB)', 'so-panels' ),
			array(
				'description' => __( 'Displays a post loop.', 'so-panels' ),
			)
		);
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		if( empty( $instance['template'] ) ) return;
		if( is_admin() ) return;

		$template = $instance['template'];
		$query_args = $instance;
		unset($query_args['template']);
		unset($query_args['additional']);
		unset($query_args['sticky']);
		unset($query_args['title']);

		$query_args = wp_parse_args($instance['additional'], $query_args);

		global $wp_rewrite;

		if( $wp_rewrite->using_permalinks() ) {

			if( get_query_var('paged') ) {
				// When the widget appears on a sub page.
				$query_args['paged'] = get_query_var('paged');
			}
			elseif( strpos( $_SERVER['REQUEST_URI'], '/page/' ) !== false ) {
				// When the widget appears on the home page.
				preg_match('/\/page\/([0-9]+)\//', $_SERVER['REQUEST_URI'], $matches);
				if(!empty($matches[1])) $query_args['paged'] = intval($matches[1]);
				else $query_args['paged'] = 1;
			}
			else $query_args['paged'] = 1;
		}
		else {
			// Get current page number when we're not using permalinks
			$query_args['paged'] = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
		}

		switch($instance['sticky']){
			case 'ignore' :
				$query_args['ignore_sticky_posts'] = 1;
				break;
			case 'only' :
				$query_args['post__in'] = get_option( 'sticky_posts' );
				break;
			case 'exclude' :
				$query_args['post__not_in'] = get_option( 'sticky_posts' );
				break;
		}

		// Exclude the current post to prevent possible infinite loop

		global $siteorigin_panels_current_post;

		if( !empty($siteorigin_panels_current_post) ){

			if(!empty($query_args['post__not_in'])){
				$query_args['post__not_in'][] = $siteorigin_panels_current_post;
			}
			else {
				$query_args['post__not_in'] = array( $siteorigin_panels_current_post );
			}

		}

		// Create the query
		query_posts($query_args);
		echo $args['before_widget'];

		// Filter the title
		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( !empty( $instance['title'] ) ) {
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		if(strpos('/'.$instance['template'], '/content') !== false) {
			while(have_posts()) {
				the_post();
				locate_template($instance['template'], true, false);
			}
		}
		else {
			locate_template($instance['template'], true, false);
		}

		echo $args['after_widget'];

		// Reset everything
		wp_reset_query();
	}

	/**
	 * Update the widget
	 *
	 * @param array $new
	 * @param array $old
	 * @return array
	 */
	function update($new, $old){
		return $new;
	}

	/**
	 * Get all the existing files
	 *
	 * @return array
	 */
	function get_loop_templates(){
		$templates = array();

		$template_files = array(
			'loop*.php',
			'*/loop*.php',
			'content*.php',
			'*/content*.php',
		);

		$template_dirs = array(get_template_directory(), get_stylesheet_directory());
		$template_dirs = array_unique($template_dirs);
		foreach($template_dirs  as $dir ){
			foreach($template_files as $template_file) {
				foreach(glob($dir.'/'.$template_file) as $file) {
					$templates[] = str_replace($dir.'/', '', $file);
				}
			}
		}

		$templates = array_unique($templates);
		sort($templates);

		return $templates;
	}

	/**
	 * Display the form for the post loop.
	 *
	 * @param array $instance
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args($instance, array(
			'title' => '',
			'template' => 'loop.php',

			// Query args
			'post_type' => 'post',
			'posts_per_page' => '',

			'order' => 'DESC',
			'orderby' => 'date',

			'sticky' => '',

			'additional' => '',
		));

		$templates = $this->get_loop_templates();
		if(empty($templates)) {
			?><p><?php _e("Unfortunately your theme doesn't have any post loops.", 'so-panels') ?></p><?php
			return;
		}

		// Get all the loop template files
		$post_types = get_post_types(array('public' => true));
		$post_types = array_values($post_types);
		$post_types = array_diff($post_types, array('attachment', 'revision', 'nav_menu_item'));

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>"><?php _e( 'Title', 'so-panels' ) ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ) ?>" id="<?php echo $this->get_field_id( 'title' ) ?>" value="<?php echo esc_attr( $instance['title'] ) ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('template') ?>"><?php _e('Template', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id( 'template' ) ?>" name="<?php echo $this->get_field_name( 'template' ) ?>">
				<?php foreach($templates as $template) : ?>
					<option value="<?php echo esc_attr($template) ?>" <?php selected($instance['template'], $template) ?>>
						<?php
						$headers = get_file_data( locate_template($template), array(
							'loop_name' => 'Loop Name',
						) );
						echo esc_html(!empty($headers['loop_name']) ? $headers['loop_name'] : $template);
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_type') ?>"><?php _e('Post Type', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id( 'post_type' ) ?>" name="<?php echo $this->get_field_name( 'post_type' ) ?>" value="<?php echo esc_attr($instance['post_type']) ?>">
				<?php foreach($post_types as $type) : ?>
					<option value="<?php echo esc_attr($type) ?>" <?php selected($instance['post_type'], $type) ?>><?php echo esc_html($type) ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('posts_per_page') ?>"><?php _e('Posts Per Page', 'so-panels') ?></label>
			<input type="text" class="small-text" id="<?php echo $this->get_field_id( 'posts_per_page' ) ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ) ?>" value="<?php echo esc_attr($instance['posts_per_page']) ?>" />
		</p>

		<p>
			<label <?php echo $this->get_field_id('orderby') ?>><?php _e('Order By', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ) ?>" name="<?php echo $this->get_field_name( 'orderby' ) ?>" value="<?php echo esc_attr($instance['orderby']) ?>">
				<option value="none" <?php selected($instance['orderby'], 'none') ?>><?php esc_html_e('None', 'so-panels') ?></option>
				<option value="ID" <?php selected($instance['orderby'], 'ID') ?>><?php esc_html_e('Post ID', 'so-panels') ?></option>
				<option value="author" <?php selected($instance['orderby'], 'author') ?>><?php esc_html_e('Author', 'so-panels') ?></option>
				<option value="name" <?php selected($instance['orderby'], 'name') ?>><?php esc_html_e('Name', 'so-panels') ?></option>
				<option value="name" <?php selected($instance['orderby'], 'name') ?>><?php esc_html_e('Name', 'so-panels') ?></option>
				<option value="date" <?php selected($instance['orderby'], 'date') ?>><?php esc_html_e('Date', 'so-panels') ?></option>
				<option value="modified" <?php selected($instance['orderby'], 'modified') ?>><?php esc_html_e('Modified', 'so-panels') ?></option>
				<option value="parent" <?php selected($instance['orderby'], 'parent') ?>><?php esc_html_e('Parent', 'so-panels') ?></option>
				<option value="rand" <?php selected($instance['orderby'], 'rand') ?>><?php esc_html_e('Random', 'so-panels') ?></option>
				<option value="comment_count" <?php selected($instance['orderby'], 'comment_count') ?>><?php esc_html_e('Comment Count', 'so-panels') ?></option>
				<option value="menu_order" <?php selected($instance['orderby'], 'menu_order') ?>><?php esc_html_e('Menu Order', 'so-panels') ?></option>
				<option value="menu_order" <?php selected($instance['orderby'], 'menu_order') ?>><?php esc_html_e('Menu Order', 'so-panels') ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('order') ?>"><?php _e('Order', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id( 'order' ) ?>" name="<?php echo $this->get_field_name( 'order' ) ?>" value="<?php echo esc_attr($instance['order']) ?>">
				<option value="DESC" <?php selected($instance['order'], 'DESC') ?>><?php esc_html_e('Descending', 'so-panels') ?></option>
				<option value="ASC" <?php selected($instance['order'], 'ASC') ?>><?php esc_html_e('Ascending', 'so-panels') ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('sticky') ?>"><?php _e('Sticky Posts', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id( 'sticky' ) ?>" name="<?php echo $this->get_field_name( 'sticky' ) ?>" value="<?php echo esc_attr($instance['sticky']) ?>">
				<option value="" <?php selected($instance['sticky'], '') ?>><?php esc_html_e('Default', 'so-panels') ?></option>
				<option value="ignore" <?php selected($instance['sticky'], 'ignore') ?>><?php esc_html_e('Ignore Sticky', 'so-panels') ?></option>
				<option value="exclude" <?php selected($instance['sticky'], 'exclude') ?>><?php esc_html_e('Exclude Sticky', 'so-panels') ?></option>
				<option value="only" <?php selected($instance['sticky'], 'only') ?>><?php esc_html_e('Only Sticky', 'so-panels') ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('additional') ?>"><?php _e('Additional ', 'so-panels') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'additional' ) ?>" name="<?php echo $this->get_field_name( 'additional' ) ?>" value="<?php echo esc_attr($instance['additional']) ?>" />
			<small><?php printf(__('Additional query arguments. See <a href="%s" target="_blank">query_posts</a>.', 'so-panels'), 'http://codex.wordpress.org/Function_Reference/query_posts') ?></small>
		</p>
	<?php
	}
}

/**
 * A panel that lets you embed video.
 */
class SiteOrigin_Panels_Widgets_EmbeddedVideo extends WP_Widget {
	function __construct() {
		parent::__construct(
			'siteorigin-panels-embedded-video',
			__( 'Embedded Video (PB)', 'so-panels' ),
			array(
				'description' => __( 'Embeds a video.', 'so-panels' ),
			)
		);
	}

	/**
	 * Display the video using
	 *
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {
		$embed = new WP_Embed();

		if(!wp_script_is('fitvids'))
			wp_enqueue_script('fitvids', plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE) . 'widgets/js/jquery.fitvids.min.js', array('jquery'), SITEORIGIN_PANELS_VERSION);

		if(!wp_script_is('siteorigin-panels-embedded-video'))
			wp_enqueue_script('siteorigin-panels-embedded-video', plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE) . 'widgets/js/embedded-video.min.js', array('jquery', 'fitvids'), SITEORIGIN_PANELS_VERSION);

		echo $args['before_widget'];
		?><div class="siteorigin-fitvids"><?php echo $embed->run_shortcode( '[embed]' . $instance['video'] . '[/embed]' ) ?></div><?php
		echo $args['after_widget'];
	}

	function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'video' => '',
		) )

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'video' ) ?>"><?php _e( 'Video', 'so-panels' ) ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'video' ) ?>" id="<?php echo $this->get_field_id( 'video' ) ?>" <?php echo esc_attr( $instance['video'] ) ?>>
		</p>
	<?php
	}

	function update( $new, $old ) {
		$new['video'] = str_replace( 'https://', 'http://', $new['video'] );
		return $new;
	}
}

class SiteOrigin_Panels_Widgets_Video extends WP_Widget {
	function __construct() {
		parent::__construct(
			'siteorigin-panels-video',
			__( 'Self Hosted Video (PB)', 'so-panels' ),
			array(
				'description' => __( 'A self hosted video player.', 'so-panels' ),
			)
		);
	}

	function widget( $args, $instance ) {
		if (empty($instance['url'])) return;
		static $video_widget_id = 1;

		$instance = wp_parse_args($instance, array(
			'url' => '',
			'poster' => '',
			'skin' => 'siteorigin',
			'ratio' => 1.777,
			'autoplay' => false,
		));

		// Enqueue jPlayer scripts and intializer
		wp_enqueue_script( 'siteorigin-panels-video-jplayer', plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE) . 'video/jplayer/jquery.jplayer.min.min.js', array('jquery'), SITEORIGIN_PANELS_VERSION, true);
		wp_enqueue_script( 'siteorigin-panels-video', plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE) . 'video/panels.video.jquery.min.js', array('jquery'), SITEORIGIN_PANELS_VERSION, true);

		// Enqueue the SiteOrigin jPlayer skin
		$skin = sanitize_file_name($instance['skin']);
		wp_enqueue_style('siteorigin-panels-video-jplayer-skin', plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE).'video/jplayer/skins/'.$skin.'/jplayer.'.$skin.'.css', array(), SITEORIGIN_PANELS_VERSION);

		$file = $instance['url'];
		$poster = !empty($instance['poster']) ? $instance['poster'] :  plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE).'video/poster.jpg';
		$instance['ratio'] = floatval($instance['ratio']);
		if(empty($instance['ratio'])) $instance['ratio'] = 1.777;

		echo $args['before_widget'];

		?>
		<div class="jp-video" id="jp_container_<?php echo $video_widget_id ?>">
			<div class="jp-type-single" id="jp_interface_<?php echo $video_widget_id ?>">
				<div id="jquery_jplayer_<?php echo $video_widget_id ?>" class="jp-jplayer"
				     data-video="<?php echo esc_url($file) ?>"
				     data-poster="<?php echo esc_url($poster) ?>"
				     data-ratio="<?php echo floatval($instance['ratio']) ?>"
				     data-autoplay="<?php echo esc_attr($instance['autoplay']) ?>"
				     data-swfpath="<?php echo plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE).'video/jplayer/' ?>"
				     data-mobile="<?php echo wp_is_mobile() ? 'true' : 'false' ?>"></div>

				<?php $this->display_gui($instance['skin']) ?>
			</div>
		</div>
		<?php

		$video_widget_id++;
		echo $args['after_widget'];
	}

	function display_gui($skin){
		$file = plugin_dir_path(SITEORIGIN_PANELS_BASE_FILE).'video/jplayer/skins/'.$skin.'/gui.php';
		if(file_exists($file)) include plugin_dir_path(SITEORIGIN_PANELS_BASE_FILE).'video/jplayer/skins/'.$skin.'/gui.php';
	}

	function update( $new, $old ) {
		$new['skin'] = sanitize_file_name($new['skin']);
		$new['ratio'] = floatval($new['ratio']);
		$new['autoplay'] = !empty($new['autoplay']) ? 1 : 0;
		return $new;
	}

	function form( $instance ) {
		$instance = wp_parse_args($instance, array(
			'url' => '',
			'poster' => '',
			'skin' => 'siteorigin',
			'ratio' => 1.777,
			'autoplay' => false,
		));

		?>
		<p>
			<label for="<?php echo $this->get_field_id('url') ?>"><?php _e('Video URL', 'so-panels') ?></label>
			<input id="<?php echo $this->get_field_id('url') ?>" name="<?php echo $this->get_field_name('url') ?>" type="text" class="widefat" value="<?php echo esc_attr($instance['url']) ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('poster') ?>"><?php _e('Poster URL', 'so-panels') ?></label>
			<input id="<?php echo $this->get_field_id('poster') ?>" name="<?php echo $this->get_field_name('poster') ?>" type="text" class="widefat" value="<?php echo esc_attr($instance['poster']) ?>" />
			<small class="description"><?php _e('An image that displays before the video starts playing.', 'so-panels') ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('skin') ?>"><?php _e('Skin', 'so-panels') ?></label>
			<select id="<?php echo $this->get_field_id('skin') ?>" name="<?php echo $this->get_field_name('skin') ?>">
				<option value="siteorigin" <?php selected($instance['skin'], 'siteorigin') ?>><?php esc_html_e('SiteOrigin', 'so-panels') ?></option>
				<option value="premium" <?php selected($instance['skin'], 'premium') ?>><?php esc_html_e('Premium Pixels', 'so-panels') ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('ratio') ?>"><?php _e('Aspect Ratio', 'so-panels') ?></label>
			<input id="<?php echo $this->get_field_id('ratio') ?>" name="<?php echo $this->get_field_name('ratio') ?>" type="text" class="widefat" value="<?php echo esc_attr($instance['ratio']) ?>" />
			<small class="description"><?php _e('1.777 is HD standard.', 'so-panels') ?></small>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('autoplay') ?>">
				<input id="<?php echo $this->get_field_id('autoplay') ?>" name="<?php echo $this->get_field_name('autoplay') ?>" type="checkbox" value="1" />
				<?php _e('Auto Play Video', 'so-panels') ?>
			</label>
		</p>
		<?php
	}
}

/**
 * A shortcode for self hosted video.
 *
 * @param array $atts
 * @return string
 */
function siteorigin_panels_video_shortcode($atts){
	/**
	 * @var string $url
	 * @var string $poster
	 * @var string $skin
	 */
	$instance = shortcode_atts( array(
		'url' => '',
		'src' => '',
		'poster' => plugin_dir_url(SITEORIGIN_PANELS_BASE_FILE).'video/poster.jpg',
		'skin' => 'siteorigin',
		'ratio' => 1.777,
		'autoplay' => 0,
	), $atts );

	if(!empty($instance['src'])) $instance['url'] = $instance['src'];
	if(empty($instance['url'])) return;

	ob_start();
	the_widget('SiteOrigin_Panels_Widgets_Video', $instance);
	return ob_get_clean();

}
add_shortcode('self_video', 'siteorigin_panels_video_shortcode');


/**
 * Register the widgets.
 */
function siteorigin_panels_widgets_init(){
	register_widget('SiteOrigin_Panels_Widgets_Gallery');
	register_widget('SiteOrigin_Panels_Widgets_PostContent');
	register_widget('SiteOrigin_Panels_Widgets_Image');
	register_widget('SiteOrigin_Panels_Widgets_PostLoop');
	register_widget('SiteOrigin_Panels_Widgets_EmbeddedVideo');
	register_widget('SiteOrigin_Panels_Widgets_Video');
}
add_action('widgets_init', 'siteorigin_panels_widgets_init');

/**
 * Enqueue widget compatibility files.
 */
function siteorigin_panels_comatibility_init(){
	if(is_plugin_active('black-studio-tinymce-widget/black-studio-tinymce-widget.php')){
		include plugin_dir_path(__FILE__).'/compat/black-studio-tinymce/black-studio-tinymce.php';
	}
}
add_action('admin_init', 'siteorigin_panels_comatibility_init', 5);