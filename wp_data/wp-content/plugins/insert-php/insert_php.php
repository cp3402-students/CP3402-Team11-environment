<?php
/**
 * Plugin Name: Woody ad snippets (PHP snippets | Insert PHP)
 * Plugin URI: http://woody-ad-snippets.webcraftic.com/
 * Description: Executes PHP code, uses conditional logic to insert ads, text, media content and external service’s code. Ensures no content duplication.
 * Author: Will Bontrager Software, LLC <will@willmaster.com>, Webcraftic <wordpress.webraftic@gmail.com>
 * Version: 2.2.3
 * Text Domain: insert-php
 * Domain Path: /languages/
 * Author URI: http://webcraftic.com
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @formatter:off


/**
 * -----------------------------------------------------------------------------
 * CHECK REQUIREMENTS
 * Check compatibility with php and wp version of the user's site. As well as checking
 * compatibility with other plugins from Webcraftic.
 * -----------------------------------------------------------------------------
 */

require_once( dirname( __FILE__ ) . '/libs/factory/core/includes/class-factory-requirements.php' );

$plugin_info = array(
	'prefix'               => 'wbcr_inp_',
	'plugin_name'          => 'wbcr_insert_php',
	'plugin_title'         => __( 'Woody ad snippets', 'insert-php' ),
	'plugin_text_domain'   => 'insert-php',

	// PLUGIN SUPPORT
	'support_details'      => array(
		'url'       => 'http://woody-ad-snippets.webcraftic.com',
		'pages_map' => array(
			'features' => 'premium-features',  // {site}/premium-features
			'pricing'  => 'pricing',           // {site}/prices
			'support'  => 'support',           // {site}/support
			'docs'     => 'docs',              // {site}/docs
		),
	),

	// PLUGIN UPDATED SETTINGS
	/*'has_updates'          => false,
	'updates_settings'     => array(
		'repository'        => 'wordpress',
		'slug'              => 'woody-ad-snippets',
		'maybe_rollback'    => true,
		'rollback_settings' => array(
			'prev_stable_version' => '0.0.0',
		),
	),*/

	// PLUGIN PREMIUM SETTINGS
	'has_premium'          => true,
	'license_settings'     => array(
		'provider'         => 'freemius',
		'slug'             => 'woody-ad-snippets',
		'plugin_id'        => '3465',
		'public_key'       => 'pk_fc5703fe4f4fbc3e87f17fce5e0b8',
		'price'            => 19,
		'has_updates'      => true,
		'updates_settings' => array(
			'maybe_rollback'    => true,
			'rollback_settings' => array(
				'prev_stable_version' => '0.0.0',
			),
		),
	),

	// FRAMEWORK MODULES
	'load_factory_modules' => array(
		array( 'libs/factory/bootstrap', 'factory_bootstrap_413', 'admin' ),
		array( 'libs/factory/forms', 'factory_forms_413', 'admin' ),
		array( 'libs/factory/pages', 'factory_pages_413', 'admin' ),
		array( 'libs/factory/types', 'factory_types_406' ),
		array( 'libs/factory/taxonomies', 'factory_taxonomies_326' ),
		array( 'libs/factory/metaboxes', 'factory_metaboxes_405', 'admin' ),
		array( 'libs/factory/viewtables', 'factory_viewtables_406', 'admin' ),
		array( 'libs/factory/shortcodes', 'factory_shortcodes_325', 'all' ),
		array( 'libs/factory/freemius', 'factory_freemius_102', 'all' ),
	),
);

/**
 * Checks compatibility with WordPress, php and other plugins.
 */
$wbcr_compatibility = new Wbcr_Factory413_Requirements( __FILE__, array_merge( $plugin_info, array(
	'plugin_already_activate' => defined( 'WINP_PLUGIN_ACTIVE' ),
	'required_php_version'    => '5.4',
	'required_wp_version'     => '4.2.0',
) ) );

/**
 * If the plugin is compatible, it will continue its work, otherwise it will be stopped and the user will receive a warning.
 */
if ( ! $wbcr_compatibility->check() ) {
	return;
}

global $wbcr_inp_safe_mode;

$wbcr_inp_safe_mode = false;

// Set the constant that the plugin is activated
define( 'WINP_PLUGIN_ACTIVE', true );

define( 'WINP_PLUGIN_VERSION', $wbcr_compatibility->get_plugin_version() );

// Root directory of the plugin
define( 'WINP_PLUGIN_DIR', dirname( __FILE__ ) );

// Absolute url of the root directory of the plugin
define( 'WINP_PLUGIN_URL', plugins_url( null, __FILE__ ) );

// Relative url of the plugin
define( 'WINP_PLUGIN_BASE', plugin_basename( __FILE__ ) );

// The type of posts used for snippets types
define( 'WINP_SNIPPETS_POST_TYPE', 'wbcr-snippets' );

// The taxonomy used for snippets types
define( 'WINP_SNIPPETS_TAXONOMY', 'wbcr-snippet-tags' );

// The snippets types
define( 'WINP_SNIPPET_TYPE_PHP', 'php' );
define( 'WINP_SNIPPET_TYPE_TEXT', 'text' );
define( 'WINP_SNIPPET_TYPE_UNIVERSAL', 'universal' );
define( 'WINP_SNIPPET_TYPE_CSS', 'css' );
define( 'WINP_SNIPPET_TYPE_JS', 'js' );
define( 'WINP_SNIPPET_TYPE_HTML', 'html' );

// The snippet automatic insertion locations
define( 'WINP_SNIPPET_AUTO_HEADER', 'header' );
define( 'WINP_SNIPPET_AUTO_FOOTER', 'footer' );
define( 'WINP_SNIPPET_AUTO_BEFORE_POST', 'before_post' );
define( 'WINP_SNIPPET_AUTO_BEFORE_CONTENT', 'before_content' );
define( 'WINP_SNIPPET_AUTO_BEFORE_PARAGRAPH', 'before_paragraph' );
define( 'WINP_SNIPPET_AUTO_AFTER_PARAGRAPH', 'after_paragraph' );
define( 'WINP_SNIPPET_AUTO_AFTER_CONTENT', 'after_content' );
define( 'WINP_SNIPPET_AUTO_AFTER_POST', 'after_post' );
define( 'WINP_SNIPPET_AUTO_BEFORE_EXCERPT', 'before_excerpt' );
define( 'WINP_SNIPPET_AUTO_AFTER_EXCERPT', 'after_excerpt' );
define( 'WINP_SNIPPET_AUTO_BETWEEN_POSTS', 'between_posts' );
define( 'WINP_SNIPPET_AUTO_BEFORE_POSTS', 'before_posts' );
define( 'WINP_SNIPPET_AUTO_AFTER_POSTS', 'after_posts' );

require_once( WINP_PLUGIN_DIR . '/libs/factory/core/boot.php' );
require_once( WINP_PLUGIN_DIR . '/includes/compat.php' );
require_once( WINP_PLUGIN_DIR . '/includes/class.helpers.php' );
require_once( WINP_PLUGIN_DIR . '/includes/class.plugin.php' );

try {
	new WINP_Plugin( __FILE__, array_merge( $plugin_info, array(
		'plugin_version'     => WINP_PLUGIN_VERSION,
		'plugin_text_domain' => $wbcr_compatibility->get_text_domain(),
	) ) );
} catch ( Exception $exception ) {
	// Plugin wasn't initialized due to an error
	define( 'WINP_PLUGIN_THROW_ERROR', true );

	$wbcr_plugin_error_func = function () use ( $exception ) {
		$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Woody Ad Snippets', $exception->getMessage(), $exception->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $wbcr_plugin_error_func );
	add_action( 'network_admin_notices', $wbcr_plugin_error_func );
}
// @formatter:on
