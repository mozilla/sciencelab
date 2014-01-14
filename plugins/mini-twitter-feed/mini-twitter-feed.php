<?php

/*

	Plugin Name: Mini twitter feed
	Plugin URI: http://minitwitter.webdevdesigner.com
	Description: This plugin displays tweets from your feed, from the Twitter Search, from a list or from your favorite users. 
	Important notice: Twitter shut down their v1 API and use now the v1.1, to update your mini twitter feed you need to go on https://twitter.com/settings/widgets/new create a new widget and get your Twitter widget ID. You can update the id on the shortcode or widget. ([minitwitter id="TWITTER_WIDGET_ID" username="YOUR_USERNAME"] or in the field "id" in the widget section).
	You get this Twitter Widget ID from the code provided by Twitter when you create this widget: data-widget-id="TWITTER_WIDGET_ID" or in the URL https://twitter.com/settings/widgets/TWITTER_WIDGET_ID/edit.
	Author: webdevdesigner
	Version: 2.0.1
	Author URI: http://www.webdevdesigner.com
	
	
    Copyright 2013  WebDevDesigner (email : olivier@webdevdesigner.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

function mtf_create_shortcode( $atts, $content=null ) {
	extract(shortcode_atts(array('id'=>'','username'=>'', 'list'=>'', 'query' => '', 'limit' => '5', 'width' => '100%', 'height' => '100%'), $atts));

	if ($query) {
		$url =  'search?q=%23' . $query;
		$type = '';
		$text = 'Tweets about "#' . $query .'"';
	} else if ($list) {
		$url =  $username . '/' . $list ;
		$type = 'data-list-owner-screen-name="' . $username . '" data-list-slug="' . $list . '"';
		$text = 'Tweets from @' . $username . '/' . $list;
	} else {
		$url = $username ;
		$type = 'data-screen-name="'.$username . '" data-show-replies="false"';
		$text = 'Tweets by ' . $username;
	}

	return 	'<div class="minitweets">
				<a class="twitter-timeline" href="https://twitter.com/' . $url . '" data-widget-id="'.$id.'" ' . $type . ' width="' . $width . '" height="'. $height .'" data-tweet-limit="' . $limit .'">' . $text . '</a>
			   	<div class="minitweets-end">
				      <a class="bird" href="http://minitwitter.webdevdesigner.com">tweets</a>
				</div>
			</div>';
}

function mtf_script() {
?>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php
}
add_action('wp_footer', 'mtf_script');

function mtf_scripts() {
	wp_enqueue_style( "mtf_css", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/minitwitter.css"));
}    
 
add_action('wp_enqueue_scripts', 'mtf_scripts');
add_shortcode('minitwitter', 'mtf_create_shortcode');

// widget

class MinitwitterWidget extends WP_Widget {
	
	function MinitwitterWidget() {
		$widget_options = array(
		'classname'		=>		'minitwitter-widget',
		'description' 	=>		'Widget which puts the twitter feed on your website.'
		);
		
		parent::WP_Widget('minitwitter-widget', 'miniTwitter', $widget_options);
	}
	
	function widget($args, $atts) {
		extract($args, EXTR_SKIP);

		extract($atts);
		echo $query;

		if ($atts['query']) {
			$url =  'search?q=%23' . $atts['query'];
			$type = '';
			$text = 'Tweets about "#' . $atts['query'] .'"';
		} else if ($atts['list']) {
			$url =  $atts['username'] . '/' . $atts['list'] ;
			$type = 'data-list-owner-screen-name="' . $atts['username'] . '" data-list-slug="' . $atts['list'] . '"';
			$text = 'Tweets from @' . $atts['username'] . '/' . $atts['list'];
		} else {
			$url =  $atts['username'] ;
			$type = 'data-screen-name="'.$atts['username'] . '" data-show-replies="false"';
			$text = 'Tweets by ' . $atts['username'];
		}

		?>
		<?php echo $before_widget; ?>
		<?php echo '<div class="minitweets">
						<a class="twitter-timeline" href="https://twitter.com/' . $url . '" data-widget-id="'.$atts['id'].'" ' . $type . ' width="' . ($atts['width']?$atts['width']:'100%') . '" height="'. ($atts['height']?$atts['height']:'100%') .'" data-tweet-limit="'.(($atts['limit'])?$atts['limit']:'5').'">' . $text . '</a>
					   	<div class="minitweets-end">
						      <a class="bird" href="http://minitwitter.webdevdesigner.com">tweets</a>
						</div>
					</div>';?>
		<?php echo $after_widget; ?>
		<?php 
	}

	function update($new, $old) {
		return $new;
	}
	
	function form($instance) {

		$i = extract($instance);

		var_dump($i);

		?>
		<p><small>If you don't have your Twitter Widget ID yet, Go on <a href="https://twitter.com/settings/widgets/new" target="_blank">create a new widget</a> to get your <strong>Twitter Widget ID</strong>. Your Twitter Widget ID is in the URL (<strong>https://twitter.com/settings/widgets/<br />TWITTER_WIDGET_ID/edit...</strong>) right after you click on the button "Create widget" or in the code provided by Twitter: <strong>data-widget-id="TWITTER_WIDGET_ID"</strong>. You can keep this Twitter Widget ID for other feeds.</small></p>

		<p><label for="<?php echo $this->get_field_id('id')?>">
		Twitter Widget ID
		<input id="<?php echo $this->get_field_id('id')?>" 
		name="<?php echo $this->get_field_name('id')?>"
		value="<?php echo $instance['id'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('username')?>">
		Username
		<input id="<?php echo $this->get_field_id('username')?>" 
		name="<?php echo $this->get_field_name('username')?>"
		value="<?php echo $instance['username'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('limit')?>">
		Limit
		<input id="<?php echo $this->get_field_id('limit')?>" 
		name="<?php echo $this->get_field_name('limit')?>"
		value="<?php echo $instance['limit'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('list')?>">
		List
		<input id="<?php echo $this->get_field_id('list')?>" 
		name="<?php echo $this->get_field_name('list')?>"
		value="<?php echo $instance['list'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('query')?>">
		Query
		<input id="<?php echo $this->get_field_id('query')?>" 
		name="<?php echo $this->get_field_name('query')?>"
		value="<?php echo $instance['query'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('width')?>">
		Width
		<input id="<?php echo $this->get_field_id('width')?>" 
		name="<?php echo $this->get_field_name('width')?>"
		value="<?php echo $instance['width'];?>" size=10 />
		</label></p>
		<p><label for="<?php echo $this->get_field_id('height')?>">
		Height
		<input id="<?php echo $this->get_field_id('height')?>" 
		name="<?php echo $this->get_field_name('height')?>"
		value="<?php echo $instance['height'];?>" size=10 />
		</label></p>
		<?php 
	}
}

function minitwitter_widget_init() {
	register_widget('MinitwitterWidget');
}
add_action('widgets_init', 'minitwitter_widget_init');
