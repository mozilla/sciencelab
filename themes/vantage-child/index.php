<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package vantage
 * @since vantage 1.0
 * @license GPL 2.0
 */

get_header('home'); ?>

<div id="primary" class="content-area">

	<div id="content" class="site-content" role="main">

		<div id="three-circle-block">
			<section>
				<div class="circle-container">
					<div class="icon-wrench"></div>
				</div>
				<header class="tag">Build</header>
				Hack and innovate on tools and prototypes with the community.
			</section>
			<section>
				<div class="circle-container">
					<div class="icon-book"></div>
				</div>
				<header class="tag">Learn</header>
				Gain access to the skills and resource needed to do more efficient research.
			</section>
			<section>
				<div class="circle-container">
					<div class="icon-group"></div>
				</div>
				<header class="tag">Connect</header>
				Find out more about the community, and join the conversation.
			</section>
		</div>
		<!-- two column section -->
		<div>
			<div class="two-column">
				<?php if ( dynamic_sidebar('twitter-feed-widget') ) : else : endif; ?>
			</div>
			<div class="two-column" id="extra-info">
				<section id="supported-by">
					<h3>Supported by</h3>
					<div>
						<img src="http://ec2-107-22-90-181.compute-1.amazonaws.com/wp-content/uploads/2013/10/Sloan-Logo-DW-300x300.png" height="100" width="100"/>
						<div>
							The <a href="http://www.sloan.org/">Alfred P. Sloan Foundation</a> is a philanthropic, not-for-profit grantmaking institution. They believe that a carefully reasoned and systematic understanding of the forces of nature and society, when applied inventively and wisely, can lead to a better world for all. 
						</div>
					</div>
				</section>
				<section id="contact-us">
					<h3>Contact us</h3>
					<div>
						<a target="_blank" href="mailto:sciencelab@mozillafoundation.org?Subject=Contact%20Science%20Lab">E-mail Science Lab</a>
					</div>
				</section>
			</div>
		</div>


	</div><!-- #content .site-content -->

</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>