<?php
	/**
	 * Factory Bootstrap
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-bootstrap
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	// module provides function only for the admin area
	if( !is_admin() ) {
		return;
	}

	if( defined('FACTORY_BOOTSTRAP_413_LOADED') ) {
		return;
	}
	define('FACTORY_BOOTSTRAP_413_LOADED', true);

	define('FACTORY_BOOTSTRAP_413_DIR', dirname(__FILE__));
	define('FACTORY_BOOTSTRAP_413_URL', plugins_url(null, __FILE__));

	// sets version of admin interface
	define('FACTORY_BOOTSTRAP_413_VERSION', 'FACTORY_BOOTSTRAP_413');

	if( !defined('FACTORY_FLAT_ADMIN') ) {
		define('FACTORY_FLAT_ADMIN', true);
	}

	include_once(FACTORY_BOOTSTRAP_413_DIR . '/includes/functions.php');