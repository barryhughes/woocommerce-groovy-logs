<?php

namespace WooCommerce_Groovy_Logs;

class Utilities {
	/**
	 * Sometimes action/filter callbacks belong to an anonymous object, and we need to resort to tricks to unhook them.
	 *
	 * @param string $action
	 * @param string $instance_of
	 * @param string $method
	 *
	 * @return void
	 */
	public static function remove_anonymous_instance_callback( $action, $instance_of, $method ): void {
		global $wp_filter;

		foreach ( $wp_filter[ current_action() ]->callbacks as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				// Once we match the instance type and method, unhook it and bail out.
				if (
					is_array( $callback['function'] )
					&& is_a( $callback['function'][0], $instance_of )
					&& $callback['function'][1] === $method
				) {
					$object = $callback['function'][0];
					remove_action( current_action(), [ $object, $method ] );
					return;
				}
			}
		}
	}
}