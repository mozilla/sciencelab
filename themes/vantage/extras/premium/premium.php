<?php

/**
 * Display the premium admin menu
 *
 * @action admin_menu
 */
function siteorigin_premium_admin_menu() {
	// Don't display this page if the user has already upgraded to premium
	if ( defined( 'SITEORIGIN_IS_PREMIUM' ) ) return;

	add_theme_page( __( 'Premium Upgrade', 'vantage' ), __( 'Premium Upgrade', 'vantage' ), 'switch_themes', 'premium_upgrade', 'siteorigin_premium_page_render' );
}

add_action( 'admin_menu', 'siteorigin_premium_admin_menu' );

/**
 * Render the premium page
 */
function siteorigin_premium_page_render() {
	$theme = basename( get_template_directory() );
	define('SITEORIGIN_PREMIUM_SUPPORTED_COST', 10);

	if ( isset( $_GET['action'] ) ) $action = $_GET['action'];
	else $action = 'view';

	switch ( $action ) {
		case 'view':
			global $siteorigin_premium_info;
			$siteorigin_premium_info = apply_filters( 'siteorigin_premium_content', array() );
			get_template_part('extras/premium/tpl/upgrade-page');
			break;

		case 'enter-order' :
			$option_name = 'siteorigin_order_number_' . $theme;
			if ( isset( $_POST['_upgrade_nonce'] ) && wp_verify_nonce( $_POST['_upgrade_nonce'], 'save_order_number' ) && isset( $_POST['order_number'] ) ) {
				update_option( $option_name, trim( $_POST['order_number'] ) );
			}

			// Validate the order number
			$result = wp_remote_get(
				add_query_arg( array(
					'order_number' => get_option( $option_name ),
					'action' => 'validate_order_number',
				), SITEORIGIN_THEME_ENDPOINT . '/premium/' . $theme . '/' )
			);
			$valid = null;
			if ( !is_wp_error( $result ) ) {
				$validation_result = unserialize( $result['body'] );
				$valid = isset( $validation_result['valid'] ) ? $validation_result['valid'] : null;
				if ( $valid ) {
					// Trigger a refresh of the theme update information
					set_site_transient( 'update_themes', null );
				}
			}

			?>
			<div class="wrap" id="theme-upgrade">
				<h2><?php printf(__('Your Order Number Is [%s]', 'vantage'), get_option( $option_name )) ?></h2>

				<?php if ( is_null( $valid ) ) : ?>
				<p>
					<?php _e( "There was a problem contacting our validation servers.", 'vantage' ) ?>
					<?php _e( "Please try again later, or upgrade manually using the ZIP file we sent you.", 'vantage' ) ?>
				</p>
				<?php elseif ( $valid ) : ?>
				<p>
					<?php _e( "We've validated your order number.", 'vantage' ) ?>
					<?php
					printf(
						__( 'You can update now, or later on your <a href="%s">Themes page</a>.', 'vantage' ),
						admin_url( 'themes.php' )
					);
					?>
					<?php _e( 'This update will add all the premium features.', 'vantage' ) ?>
				</p>
				<p class="submit">
					<?php
					$update_url = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $theme ) ), 'upgrade-theme_' . $theme );
					$update_onclick = 'onclick="if ( confirm(\'' . esc_js( __( "Updating this theme will lose any code customizations (CSS, PHP, Javascript, etc) you have made to the free version. You will not lose your content, images or settings. 'Cancel' to stop, 'OK' to update.", 'vantage' ) ) . '\') ) {return true;}return false;"';
					?>
					<a href="<?php echo esc_url( $update_url ) ?>" <?php echo $update_onclick ?> class="button-primary">
						<?php _e( 'Update Theme', 'vantage' ) ?>
					</a>
				</p>
				<?php else : ?>
				<p>
					<?php _e( "We couldn't validate your order number.", 'vantage' ) ?>
					<?php _e( "There might be a problem with our validation server.", 'vantage' ) ?>
					<?php _e( "Please try again later, or upgrade manually using the ZIP file we sent you.", 'vantage' ) ?>
				</p>
				<?php endif; ?>
			</div>
			<?php
			break;
	}
}

/**
 * Enqueue admin scripts
 *
 * @param $prefix
 * @return mixed
 *
 * @action admin_enqueue_scripts
 */
function siteorigin_premium_admin_enqueue( $prefix ) {
	// Ignore this for premium themes
	if(defined( 'SITEORIGIN_IS_PREMIUM' )) return;
	
	if ( $prefix == 'appearance_page_premium_upgrade' ) {
		wp_enqueue_script( 'siteorigin-premium-upgrade', get_template_directory_uri() . '/extras/premium/js/premium.min.js', array( 'jquery' ), SITEORIGIN_THEME_VERSION );
		wp_enqueue_style( 'siteorigin-premium-upgrade', get_template_directory_uri() . '/extras/premium/css/premium.css', array(), SITEORIGIN_THEME_VERSION );
	}

	$screen = get_current_screen();
	$teaser_required = false;
	
	// Check if this is a required post type
	if( ( $prefix == 'post.php' || $prefix == 'post-new.php' ) && siteorigin_premium_teaser_get_support('post-type', $screen->id) ) $teaser_required = true;
	if( siteorigin_premium_teaser_get_support('page', $prefix) || $prefix == 'appearance_page_theme_settings_page') $teaser_required = true;
	
	if($teaser_required){
		wp_enqueue_style( 'siteorigin-premium-teaser', get_template_directory_uri() . '/extras/premium/css/premium-teaser.css', array(), SITEORIGIN_THEME_VERSION );
		wp_enqueue_script( 'siteorigin-premium-teaser', get_template_directory_uri() . '/extras/premium/js/premium-teaser.min.js', array( 'jquery' ), SITEORIGIN_THEME_VERSION );
	}

	// Enqueue the page templates teaser, which works slightly differently
	if( siteorigin_premium_teaser_get_support('page-templates') ){
		wp_enqueue_script( 'siteorigin-premium-teaser-templates', get_template_directory_uri() . '/extras/premium/js/premium-teaser-templates.min.js', array( 'jquery' ), SITEORIGIN_THEME_VERSION );
		
		wp_localize_script( 'siteorigin-premium-teaser-templates', 'siteoriginTeaserTemplates' , array(
			'code' => '<p>'.siteorigin_premium_teaser(
				__('Get Additional Templates', 'vantage'),
				array(
					'description' => sprintf(__('%s Premium includes additional templates', 'vantage'), ucfirst(get_option('stylesheet')))
				),
				true
			).'</p>'
		) );
	}
}

add_action( 'admin_enqueue_scripts', 'siteorigin_premium_admin_enqueue' );

/**
 * Adds one or more post types to the list that are requesting the premium teaser scripts.
 * 
 * @param $post_types
 */
function siteorigin_premium_teaser_post_types( $post_types ) {
	$post_types = (array)$post_types;
	if ( empty( $GLOBALS[ 'siteorigin_premium_teaser_post_types' ] ) )
		$GLOBALS[ 'siteorigin_premium_teaser_post_types' ] = array();

	$GLOBALS[ 'siteorigin_premium_teaser_post_types' ] = array_merge(
		$GLOBALS[ 'siteorigin_premium_teaser_post_types' ],
		$post_types
	);
}

function siteorigin_premium_call_function($callback, $param_array, $args = array()){
	if(function_exists($callback) && defined('SITEORIGIN_IS_PREMIUM')){
		call_user_func_array($callback, $param_array);
	}
	else{
		$theme = basename( get_template_directory() );
		if(!empty($args['before'])) echo $args['before'];
		?>
		<a class="siteorigin-premium-teaser" href="<?php echo admin_url( 'themes.php?page=premium_upgrade' ) ?>" target="_blank">
			<em></em>
			<?php printf( __( 'Available in <strong>%s Premium</strong> - <strong class="upgrade">Upgrade Now</strong>', 'vantage' ), ucfirst($theme) ) ?>
			<?php if(!empty($args['teaser-image'])) : ?>
				<div class="teaser-image"><img src="<?php echo esc_url($args['teaser-image']) ?>" width="220" height="120" /><div class="pointer"></div></div>
			<?php endif; ?>
		</a>
		<?php if(!empty($args['description'])) : ?>
			<div class="siteorigin-premium-teaser-description"><?php echo $args['description'] ?></div>
		<?php
		endif;
		if(!empty($args['after'])) echo $args['after'];
	}
}

function siteorigin_premium_teaser($text, $args = null, $return = false){
	if(defined('SITEORIGIN_IS_PREMIUM')) return;
	
	if($return) ob_start();
	
	?>
	<a class="siteorigin-premium-teaser" href="<?php echo admin_url( 'themes.php?page=premium_upgrade' ) ?>" target="_blank">
		<em></em>
		<?php echo $text ?>
		<?php if(!empty($args['teaser-image'])) : ?>
			<div class="teaser-image"><img src="<?php echo esc_url($args['teaser-image']) ?>" width="220" height="120" /><div class="pointer"></div></div>
		<?php endif; ?>
	</a>
	<?php if(!empty($args['description'])) : ?>
		<div class="siteorigin-premium-teaser-description"><?php echo $args['description'] ?></div>
	<?php
	endif;
	
	if($return) return ob_get_clean();
}

/**
 * Check if we support a specific type of teaser
 * 
 * @param $type
 * @param $sub
 * @return bool
 */
function siteorigin_premium_teaser_get_support($type, $sub = false){
	if(defined('SITEORIGIN_IS_PREMIUM')) return false;

	$teaser = get_theme_support( 'siteorigin-premium-teaser' );
	if(empty($teaser)) return false;
	$teaser = $teaser[0];
	
	// If we're teasing page templates, then include the page post type
	if(empty($teaser['post-type'])) $teaser['post-type'] = array();
	if(!empty($teaser['page-templates'])) $teaser['post-type'][] = 'page';
	$teaser['post-type'] = array_unique($teaser['post-type']);

	// Return the result
	if(!empty($teaser[$type]) && is_array($teaser[$type]) && !empty($sub)){
		return in_array($sub, $teaser[$type]);
	}
	else{
		return !empty($teaser[$type]);
	}
}

/**
 * Enqueue scripts for the customizer
 */
function siteorigin_premium_teaser_customizer_enqueue(){
	if(!siteorigin_premium_teaser_get_support('customizer')) return;
	
	wp_enqueue_style( 'siteorigin-premium-teaser', get_template_directory_uri() . '/extras/premium/css/premium-teaser.css', array(), SITEORIGIN_THEME_VERSION );
	wp_enqueue_script( 'siteorigin-premium-teaser', get_template_directory_uri() . '/extras/premium/js/premium-teaser.min.js', array( 'jquery' ), SITEORIGIN_THEME_VERSION );
}
add_action('customize_controls_enqueue_scripts', 'siteorigin_premium_teaser_customizer_enqueue');

function siteorigin_premium_teaser_customizer(){
	if(!siteorigin_premium_teaser_get_support('customizer')) return;
	
	/**
	 * @var WP_Customize_Manager
	 */
	global $wp_customize;
	$teaser_customizer = new SiteOrigin_Premium_Teaser_Customizer($wp_customize, 'siteorigin-premium-teaser');
	$wp_customize->add_section($teaser_customizer);
}
add_action('customize_controls_init', 'siteorigin_premium_teaser_customizer', 100);

if(class_exists('WP_Customize_Section')) :
class SiteOrigin_Premium_Teaser_Customizer extends WP_Customize_Section{
	function render() {
		?><div class="siteorigin-premium-teaser-customizer-wrapper"><?php
		siteorigin_premium_teaser(__('Get Additional Customizations', 'vantage'));
		?></div><?php
	}
}
endif;

function siteorigin_premium_default_content($content){
	$theme = basename( get_template_directory() );

	$content['rewards'][] = array(
		'amount' => 10,
		'title' => sprintf(__('A Copy of %s Premium', 'vantage'), ucfirst($theme)),
		'text' => sprintf(__('You get a copy of %s Premium, delivered instantly to your PayPal email address. This includes the same basic support we offer users of our free themes.', 'vantage'), ucfirst($theme)),
	);

	$content['rewards'][] = array(
		'amount' => 20,
		'title' => __('Premium Support', 'vantage'),
		'text' => sprintf(__("This includes Premium email support from our support team for a single site. We'll help you as quickly as possible with all your setup and configuration questions.", 'vantage'), ucfirst($theme)),
	);

	$content['rewards'][] = array(
		'amount' => 40,
		'title' => __('Advanced Premium Support', 'vantage'),
		'text' => sprintf(__("We'll go the extra mile and help you with minor CSS customizations, plugin conflicts and anything else that falls outside standard %s Premium support.", 'vantage'), ucfirst($theme)),
	);

	$content['rewards'][] = array(
		'amount' => 60,
		'title' => __('A Special Thank You', 'vantage'),
		'text' => sprintf(__("Our highest level of support. If you need it, you'll get support directly from the developer of %s. We'll also include your name on our contributors list.", 'vantage'), ucfirst($theme)),
	);

	return $content;
}
add_filter('siteorigin_premium_content', 'siteorigin_premium_default_content', 8);