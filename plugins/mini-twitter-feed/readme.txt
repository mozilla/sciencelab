=== Plugin Name ===
Contributors: webdevdesigner
Donate link: http://minitwitter.webdevdesigner.com
Tags: twitter, twitter feed, mini twitter, tweets, widget, shortcode 
Requires at least: 3
Tested up to: 3.5
Stable tag: 2.0.1

Embed your twitter feed or the twitter timeline from your favorite users on your Wordpress blog. Shortcodes and widgets are used.

== Description ==

Important notice: Twitter shut down their v1 API and use now the v1.1, to update your mini twitter feed you need to go on https://twitter.com/settings/widgets/new create a new widget and get your Twitter widget ID. You can update the id on the shortcode or widget. ([minitwitter id="TWITTER_WIDGET_ID" username="YOUR_USERNAME"] or in the field "id" in the widget section).
You get this Twitter Widget ID from the code provided by Twitter when you create this widget: data-widget-id="TWITTER_WIDGET_ID" or in the URL https://twitter.com/settings/widgets/TWITTER_WIDGET_ID/edit.

The plugin "mini Twitter feed" or twitter timeline puts Twitter on your Wordpress blog, you can add your tweets or the tweets from the folks you like, lists, queries...

You can display up to 100 tweets within 7 days (limit set by Twitter's Search API).
	
It displays tweets from your feed or from the Twitter Search, from a list or from your favorite users. It is very flexible and modular.

You can read more on how to [Embed twitter on your Wordpress blog: mini Twitter](http://minitwitter.webdevdesigner.com/ "Put the twitter feed on your Wordpress blog").

== Installation ==

1. Upload `/mini-twitter_feed` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you don't have your Twitter Widget ID yet, Go on https://twitter.com/settings/widgets/new to create a new widget to get your Twitter Widget ID. Your Twitter Widget ID is in the URL (https://twitter.com/settings/widgets/TWITTER_WIDGET_ID/edit...) right after you click on the button "Create widget" or in the code provided by Twitter: data-widget-id="TWITTER_WIDGET_ID".
4. Place `[minitwitter id="TWITTER_WIDGET_ID" username="YOUR_USERNAME"]` in your templates or use the widget section.

You can limit the number of tweets displayed [minitwitter id="TWITTER_WIDGET_ID" username="webdevdesigner" limit=7]: it will show the last 7 tweets of the user @webdevdesigner.

It is possible to add a list of users to show [minitwitter id="TWITTER_WIDGET_ID" username="twitter" list="team"]: it will show 5 tweets of the list "team" of the "twitter" user.

You can show the tweets of a query [minitwitter id="TWITTER_WIDGET_ID" query="awesome"]: it will show the tweets from the query #awesome.

== Frequently Asked Questions ==

= why my feed is just showing a link ("Tweets by...")? =

You probably forgot to put your Twitter Widget ID in the shortcode or the widget. Check step 2. of the installation process. 

= What are the "query" and "list" fields in the widget section? =

The field "query" is NOT required. It is only to show themes or sentences like "#worldcup".
The field "list" is NOT required too. It is to display the tweets of one of your list.

== Screenshots ==

1. The twitter feed on your blog.

== Changelog ==

= 1.0 =

= 1.1 =

Bug fix with for color of the links, now CSS is used.
Font -1px on all plugin

= 1.2 =

Bug fix for IE
Bug fix quotes query

= 1.3 =

CSS change 
URL for author and plugin

= 1.4 =

use of jQuery for no conflict

= 2.0 =

New twitter API v1.1

== Upgrade Notice ==

= 1.1 =

Bug fix with for color of the links, now CSS is used.
Font -1px on all plugin

