<?php

	/**
	 * Register Twitter Feed and widgetized areas.
	 *
	 */
	function twitter_feed_widgets_init() {
	  register_sidebar( array(
	    'name' => 'Twitter Feeds Section',
	    'id' => 'twitter-feed-widget',
	    'before_widget' => '<div id="twitter-feed-widget">',
	    'after_widget' => '</div>'
	  ) );
	}
	add_action( 'widgets_init', 'twitter_feed_widgets_init' );


	/**
	 * Register Extra Info (column to the right of Twitter Feed) and widgetized areas.
	 *
	 */
	function extra_info_widgets_init() {
	  register_sidebar( array(
	    'name' => 'Extra Info Section',
	    'id' => 'extra-info-widget',
	    'before_widget' => '<div class="extra-info-widget">mavis',
	    'after_widget' => '</div>'
	  ) );
	}
	add_action( 'widgets_init', 'extra_info_widgets_init' );

?>

