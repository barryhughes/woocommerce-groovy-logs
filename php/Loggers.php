<?php

namespace WooCommerce_Groovy_Logs;

use WC_Admin_Menus;
use WC_Log_Handler_DB;
use WC_Log_Handler_Email;
use WC_Log_Handler_File;

class Loggers {
	private $available = [];

	public function setup(): void {
		$this->set_available_loggers();
		add_filter( 'woocommerce_register_log_handlers', [ $this, 'define_handlers' ], 20 );
	}

	private function set_available_loggers(): void {
		$this->available = [
			'database' => [
				WC_Log_Handler_DB::class,
				__( 'MySQL (official)', 'woocommerce-groovy-logs' ),
			],
			'file' => [
				WC_Log_Handler_File::class,
				__( 'File (official)', 'woocommerce-groovy-logs' ),
			],
			'email' => [
				WC_Log_Handler_Email::class,
				__( 'Email (official)', 'woocommerce-groovy-logs' ),
			],
		];

		if ( extension_loaded( 'sqlite3' ) ) {
			$this->available['sqlite'] = [
				SQLite_Logger::class,
				__( 'SQLite (provided by Groovy Logs for WooCommerce)', 'woocommerce-groovy-logs' ),
			];
		}
	}

	public function get_available_loggers_by_name(): array {
		return array_map(
			function ( array $logger_information ): string {
				return $logger_information[1];
			},
			$this->available
		);
	}

	public function define_handlers( array $handlers ): array {
		$defined_handler = get_option( 'groovy_logs_logging_engine', false );

		if ( is_string( $defined_handler ) && isset( $this->available[ $defined_handler ] ) ) {
			$selected_logging_class = $this->available[ $defined_handler ][0];
		}

		if ( isset( $selected_logging_class ) && class_exists( $selected_logging_class ) ) {
			return [ new ( $selected_logging_class ) ];
		}

		return $handlers;
	}
}
