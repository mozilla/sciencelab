<?php

if( !defined('SITEORIGIN_PANELS_VERSION') && function_exists('siteorigin_plugin_activation_is_activating') && !siteorigin_plugin_activation_is_activating('siteorigin-panels') ) {
	include get_template_directory().'/extras/panels-lite/inc/widgets.php';
}

/**
 * Add the admin menu entries
 */
function siteorigin_panels_lite_admin_menu(){
	add_theme_page(
		__('Custom Home Page Builder', 'vantage'),
		__('Home Page', 'vantage'),
		'edit_theme_options',
		'so_panels_home_page',
		'siteorigin_panels_lite_render_admin_home_page'
	);
}
add_action('admin_menu', 'siteorigin_panels_lite_admin_menu');

/**
 * Render the page used to build the custom home page.
 */
function siteorigin_panels_lite_render_admin_home_page(){
	add_meta_box( 'so-panels-panels', __( 'Page Builder', 'vantage' ), 'siteorigin_panels_metabox_render', 'appearance_page_so_panels_home_page', 'advanced', 'high' );

	if(isset($_GET['_wpnonce']) && isset($_GET['toggle']) && wp_verify_nonce($_GET['_wpnonce'], 'toggle_panels_home')){
		// Update home page enabled setting
		set_theme_mod('siteorigin_panels_home_page_enabled', (bool) $_GET['panels_new']);
	}

	get_template_part('extras/panels-lite/tpl/admin', 'home-page');
}

/**
 * Enqueue any required admin scripts.
 *
 * @param $prefix
 */
function siteorigin_panels_lite_enqueue_admin($prefix){
	if($prefix == 'appearance_page_so_panels_home_page'){
		wp_enqueue_style('siteorigin-panels-lite-teaser', get_template_directory_uri().'/extras/panels-lite/css/panels-admin.css');
	}

	if($prefix == 'post.php' || $prefix == 'post-new.php' ) {

		$install_url = siteorigin_plugin_activation_install_url(
			'siteorigin-panels',
			__('Page Builder', 'vantage')
		);

		wp_enqueue_script('siteorigin-panels-lite-teaser', get_template_directory_uri().'/extras/panels-lite/js/tab.js', array('jquery'));
		wp_localize_script('siteorigin-panels-lite-teaser', 'panelsLiteTeaser', array(
			'tab' => __('Page Builder', 'vantage'),
			'message' => __("Refresh this page after you've installed Page Builder.", 'vantage'),
			'confirm' => __("Your theme has Page Builder support. Would you like to install it? It's free."),
			'installUrl' => $install_url
		));
	}
}
add_action('admin_enqueue_scripts', 'siteorigin_panels_lite_enqueue_admin');

/**
 * Add the Edit Home Page item to the admin bar.
 *
 * @param WP_Admin_Bar $admin_bar
 * @return WP_Admin_Bar
 */
function siteorigin_panels_lite_admin_bar_menu($admin_bar){
	/**
	 * @var WP_Query $wp_query
	 */
	global $wp_query;

	if( $wp_query->is_home() && $wp_query->is_main_query() ){
		// Check that we support the home page
		if ( !current_user_can('edit_theme_options') ) return $admin_bar;

		$admin_bar->add_node(array(
			'id' => 'edit-home-page',
			'title' => __('Edit Home Page', 'vantage'),
			'href' => admin_url('themes.php?page=so_panels_home_page')
		));
	}

	return $admin_bar;
}
add_action('admin_bar_menu', 'siteorigin_panels_lite_admin_bar_menu', 100);

/**
 * Get a setting value
 *
 * @param bool $key
 * @return mixed|null|void
 */
function siteorigin_panels_lite_setting($key = false){
	$settings = array(
		'home-page' => false,                   // Is the home page supported
		'home-page-default' => false,           // What's the default layout for the home page?
		'home-template' => 'home-panels.php',   // The file used to render a home page.
		'post-types' => get_option('siteorigin_panels_post_types', array('page')),	// Post types that can be edited using panels.

		'responsive' => !isset( $display_settings['responsive'] ) ? false : $display_settings['responsive'],					// Should we use a responsive layout
		'mobile-width' => !isset( $display_settings['mobile-width'] ) ? 780 : $display_settings['mobile-width'],				// What is considered a mobile width?

		'margin-bottom' => !isset( $display_settings['margin-bottom'] ) ? 30 : $display_settings['margin-bottom'],				// Bottom margin of a cell
		'margin-sides' => !isset( $display_settings['margin-sides'] ) ? 30 : $display_settings['margin-sides'],					// Spacing between 2 cells
		'affiliate-id' => false,																								// Set your affiliate ID
		'copy-content' => !isset( $display_settings['copy-content'] ) ? true : $display_settings['copy-content'],				// Should we copy across content
		'animations' => !isset( $display_settings['animations'] ) ? true : $display_settings['animations'],						// Should we copy across content
	);

	// Filter these settings
	$settings = apply_filters('siteorigin_panels_settings', $settings);

	if( !empty( $key ) ) return isset( $settings[$key] ) ? $settings[$key] : null;
	return $settings;
}

/**
 * Modify the front page template
 *
 * @param $template
 * @return string
 */
function siteorigin_panels_lite_filter_home_template($template){
	if ( !get_theme_mod('siteorigin_panels_home_page_enabled', siteorigin_panels_lite_setting('home-page-default') ) ) return $template;
	// If the user already has their own custom home page, use that instead
	if ( get_option( 'show_on_front' ) !== 'posts' ) return $template;

	$GLOBALS['siteorigin_panels_is_panels_home'] = true;
	return locate_template(array(
		'home-panels.php',
		$template
	));
}
add_filter('home_template', 'siteorigin_panels_lite_filter_home_template');

/**
 * @return mixed|void Are we currently viewing the home page
 */
function siteorigin_panels_lite_is_home(){
	$home = (is_home() && get_theme_mod('siteorigin_panels_home_page_enabled', siteorigin_panels_lite_setting('home-page-default')) );
	return apply_filters('siteorigin_panels_is_home', $home);
}

/**
 * Enqueue the required styles
 */
function siteorigin_panels_lite_enqueue_styles(){
	if(siteorigin_panels_lite_is_home()){
		wp_enqueue_style('siteorigin-panels', get_template_directory_uri() . '/extras/panels-lite/css/front.css', array(), SITEORIGIN_THEME_VERSION );
	}
}
add_action('wp_enqueue_scripts', 'siteorigin_panels_lite_enqueue_styles');

/**
 * Set the home body class when we're displaying a panels page.
 *
 * @param $classes
 * @return array
 */
function siteorigin_panels_lite_body_class($classes){
	if(siteorigin_panels_lite_is_home()) $classes[] = 'siteorigin-panels-home';
	return $classes;
}
add_filter('body_class', 'siteorigin_panels_lite_body_class');

/**
 * Render the widget.
 *
 * @param string $widget The widget class name.
 * @param array $instance The widget instance
 * @param $grid
 * @param $cell
 * @param $panel
 * @param $is_first
 * @param $is_last
 */
function siteorigin_panels_lite_the_widget( $widget, $instance, $grid, $cell, $panel, $is_first, $is_last ) {
	if ( !class_exists( $widget ) ) return;

	$the_widget = new $widget;

	$classes = array( 'panel', 'widget' );
	if ( !empty( $the_widget->id_base ) ) $classes[] = 'widget_' . $the_widget->id_base;
	if ( $is_first ) $classes[] = 'panel-first-child';
	if ( $is_last ) $classes[] = 'panel-last-child';

	$the_widget->widget( array(
		'before_widget' => '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" id="panel-' . $grid . '-' . $cell . '-' . $panel . '">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'widget_id' => 'widget-' . $grid . '-' . $cell . '-' . $panel
	), $instance );
}

/**
 * Echo the CSS for the current panel
 *
 * @action wp_print_styles
 */
function siteorigin_panels_lite_css() {
	global $post;

	if(!siteorigin_panels_lite_is_home()) return;
	$layouts = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
	if(empty($layouts[ siteorigin_panels_lite_setting('home-page-default') ])) return;
	$panels_data = $layouts[siteorigin_panels_lite_setting('home-page-default')];
	$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, 'home' );

	// Exit if we don't have panels data
	if ( empty( $panels_data ) ) return;

	$settings = siteorigin_panels_lite_setting();

	$panels_mobile_width = $settings['mobile-width'];
	$panels_margin_bottom = $settings['margin-bottom'];

	$css = array();
	$css[1920] = array();
	$css[ $panels_mobile_width ] = array(); // This is a mobile resolution

	// Add the grid sizing
	$ci = 0;
	foreach ( $panels_data['grids'] as $gi => $grid ) {
		$cell_count = intval( $grid['cells'] );
		for ( $i = 0; $i < $cell_count; $i++ ) {
			$cell = $panels_data['grid_cells'][$ci++];

			if ( $cell_count > 1 ) {
				$css_new = 'width:' . round( $cell['weight'] * 100, 3 ) . '%';
				if ( empty( $css[1920][$css_new] ) ) $css[1920][$css_new] = array();
				$css[1920][$css_new][] = '#pgc-home-' . $gi . '-' . $i;
			}
		}

		// Add the bottom margin to any grids that aren't the last
		if($gi != count($panels_data['grids'])-1){
			$css[1920]['margin-bottom: '.$panels_margin_bottom.'px'][] = '#pg-home-' . $gi;
		}

		if ( $cell_count > 1 ) {
			if ( empty( $css[1920]['float:left'] ) ) $css[1920]['float:left'] = array();
			$css[1920]['float:left'][] = '#pg-home-' . $gi . ' .panel-grid-cell';
		}

		if ( $settings['responsive'] ) {
			// Mobile Responsive
			$mobile_css = array( 'float:none', 'width:auto' );
			foreach ( $mobile_css as $c ) {
				if ( empty( $css[ $panels_mobile_width ][ $c ] ) ) $css[ $panels_mobile_width ][ $c ] = array();
				$css[ $panels_mobile_width ][ $c ][] = '#pg-home-' . $gi . ' .panel-grid-cell';
			}

			for ( $i = 0; $i < $cell_count; $i++ ) {
				if ( $i != $cell_count - 1 ) {
					$css_new = 'margin-bottom:' . $panels_margin_bottom . 'px';
					if ( empty( $css[$panels_mobile_width][$css_new] ) ) $css[$panels_mobile_width][$css_new] = array();
					$css[$panels_mobile_width][$css_new][] = '#pgc-home-' . $gi . '-' . $i;
				}
			}
		}
	}

	if( $settings['responsive'] ) {
		// Add CSS to prevent overflow on mobile resolution.
		$panel_grid_css = 'margin-left: 0 !important; margin-right: 0 !important;';
		$panel_grid_cell_css = 'padding: 0 !important;';
		if(empty($css[ $panels_mobile_width ][ $panel_grid_css ])) $css[ $panels_mobile_width ][ $panel_grid_css ] = array();
		if(empty($css[ $panels_mobile_width ][ $panel_grid_cell_css ])) $css[ $panels_mobile_width ][ $panel_grid_cell_css ] = array();
		$css[ $panels_mobile_width ][ $panel_grid_css ][] = '.panel-grid';
		$css[ $panels_mobile_width ][ $panel_grid_cell_css ][] = '.panel-grid-cell';
	}

	// Add the bottom margin
	$bottom_margin = 'margin-bottom: '.$panels_margin_bottom.'px';
	$bottom_margin_last = 'margin-bottom: 0 !important';
	if(empty($css[ 1920 ][ $bottom_margin ])) $css[ 1920 ][ $bottom_margin ] = array();
	if(empty($css[ 1920 ][ $bottom_margin_last ])) $css[ 1920 ][ $bottom_margin_last ] = array();
	$css[ 1920 ][ $bottom_margin ][] = '.panel-grid-cell .panel';
	$css[ 1920 ][ $bottom_margin_last ][] = '.panel-grid-cell .panel:last-child';

	// This is for the side margins
	$magin_half = $settings['margin-sides']/2;
	$side_margins = "margin: 0 -{$magin_half}px 0 -{$magin_half}px";
	$side_paddings = "padding: 0 {$magin_half}px 0 {$magin_half}px";
	if(empty($css[ 1920 ][ $side_margins ])) $css[ 1920 ][ $side_margins ] = array();
	if(empty($css[ 1920 ][ $side_paddings ])) $css[ 1920 ][ $side_paddings ] = array();
	$css[ 1920 ][ $side_margins ][] = '.panel-grid';
	$css[ 1920 ][ $side_paddings ][] = '.panel-grid-cell';

	/**
	 * Filter the unprocessed CSS array
	 */
	$css = apply_filters( 'siteorigin_panels_css', $css );

	// Build the CSS
	$css_text = '';
	krsort( $css );
	foreach ( $css as $res => $def ) {
		if ( empty( $def ) ) continue;

		if ( $res < 1920 ) {
			$css_text .= '@media (max-width:' . $res . 'px)';
			$css_text .= ' { ';
		}

		foreach ( $def as $property => $selector ) {
			$selector = array_unique( $selector );
			$css_text .= implode( ' , ', $selector ) . ' { ' . $property . ' } ';
		}

		if ( $res < 1920 ) $css_text .= ' } ';
	}

	echo '<style type="text/css">';
	echo $css_text;
	echo '</style>';
}
add_action( 'wp_head', 'siteorigin_panels_lite_css', 15 );

/**
 * Renders the home page if we need it.
 */
function siteorigin_panels_lite_home_render(){
	$layouts = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
	if(empty($layouts[ siteorigin_panels_lite_setting('home-page-default') ])) return;
	$panels_data = $layouts[siteorigin_panels_lite_setting('home-page-default')];
	$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, 'home' );

	// Create the skeleton of the grids
	$grids = array();
	foreach ( $panels_data['grids'] as $gi => $grid ) {
		$gi = intval( $gi );
		$grids[$gi] = array();
		for ( $i = 0; $i < $grid['cells']; $i++ ) {
			$grids[$gi][$i] = array();
		}
	}

	foreach ( $panels_data['widgets'] as $widget ) {
		$grids[intval( $widget['info']['grid'] )][intval( $widget['info']['cell'] )][] = $widget;
	}

	ob_start();
	foreach ( $grids as $gi => $cells ) {

		$grid_classes = array('panel-grid');
		$grid_classes = apply_filters( 'siteorigin_panels_row_classes', $grid_classes );
		$grid_classes = array_map('esc_attr', $grid_classes);

		?><div class="<?php echo implode(' ', $grid_classes) ?>" id="pg-<?php echo 'home-' . $gi ?>"><?php

		if( !empty( $panels_data['grids'][$gi]['style'] ) ) {
			?><div class="panel-row-style <?php echo esc_attr('panel-row-style-' . $panels_data['grids'][$gi]['style']) ?>"><?php
		}

		foreach ( $cells as $ci => $widgets ) {
			$cell_classes = apply_filters( 'siteorigin_panels_row_cell_classes', array('panel-grid-cell') );
			$cell_classes = array_map('esc_attr', $cell_classes);

			?><div class="<?php echo implode( ' ', $cell_classes ) ?>" id="pgc-<?php echo 'home-' . $gi  . '-' . $ci ?>"><?php
			foreach ( $widgets as $pi => $widget_info ) {
				$data = $widget_info;
				unset( $data['info'] );

				siteorigin_panels_lite_the_widget( $widget_info['info']['class'], $data, $gi, $ci, $pi, $pi == 0, $pi == count( $widgets ) - 1 );
			}
			if ( empty( $widgets ) ) echo '&nbsp;';
			?></div><?php
		}
		?></div><?php

		if( !empty( $panels_data['grids'][$gi]['style'] ) ) {
			?></div><?php
		}
	}
	$html = ob_get_clean();

	return apply_filters( 'siteorigin_panels_render', $html, 'home', null );
}