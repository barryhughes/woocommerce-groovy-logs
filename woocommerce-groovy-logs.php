<?php
/**
 * Plugin name:  Groovy Logs for WooCommerce
 * Description:  Enhances WooCommerce's logging capabilities, providing a groovier way to work with your WooCommerce logs.
 * Version:      0.1
 * License:      GPL-3.0
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires PHP: 7.3
 */

namespace WooCommerce_Groovy_Logs;

function class_loader( string $class ) {
	$namespace = __NAMESPACE__ . '\\';

	if ( 0 !== strpos( $class, $namespace ) ) {
		return;
	}

	$class = str_replace( $namespace, '', $class );
	$path = realpath( __DIR__ . '/php/' . str_replace( '\\', '/', $class ) . '.php' );

	if ( 0 !== strpos( $path, __DIR__ . '/php' ) || ! file_exists( $path ) ) {
		return;
	}

	require $path;
}

function plugin(): Plugin {
	static $plugin;
	return $plugin = empty( $plugin ) ? new Plugin : $plugin;
}


spl_autoload_register( __NAMESPACE__ . '\class_loader' );
plugin()->setup();

