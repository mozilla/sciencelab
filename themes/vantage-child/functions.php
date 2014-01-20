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
	 * Register blog post and widgetized areas.
	 *
	 */
	function blog_sidebar_widgets_init() {
	  register_sidebar( array(
	    'name' => 'Blog Sitebar Section',
	    'id' => 'blog-sitebar-widget',
	    'before_widget' => '<div id="blog-sitebar-widget">',
	    'after_widget' => '</div>'
	  ) );
	}
	add_action( 'widgets_init', 'blog_sidebar_widgets_init' );

