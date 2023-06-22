<?php
/**
 * Plugin name:  Groovy Logs for WooCommerce
 * Description:  Enhances WooCommerce's logging capabilities, providing a groovier way to work with your WooCommerce logs.
 * Version:      0.1
 * License:      GPL-3.0
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires PHP: 8.0
 */

namespace WooCommerce_Groovy_Logs;

function plugin(): Plugin {
	static $plugin;
	return $plugin = empty( $plugin ) ? new Plugin : $plugin;
}

require __DIR__ . '/vendor/autoload.php';
plugin()->setup( __FILE__ );
