<?php

use WooCommerce_Groovy_Logs\SQLite_Logger;

// region: Helpers

function get_sqlite_logger(): SQLite_Logger {
	$log_path = sys_get_temp_dir() . '/groovy-logs-tests.db';

	if ( file_exists( $log_path ) ) {
		unlink( $log_path );
	}

	$logger = new SQLite_Logger;
	$logger->set_log_path( $log_path );
	return $logger;
}

// endregion

// region: WordPress Stubs

interface WC_Log_Handler_Interface {
	public function handle( $timestamp, $level, $message, $context );
}

function absint( $maybeint ) {
	return abs( (int) $maybeint );
}

function trailingslashit( $value ) {
	return untrailingslashit( $value ) . '/';
}

function untrailingslashit( $value ) {
	return rtrim( $value, '/\\' );
}

// endregion
