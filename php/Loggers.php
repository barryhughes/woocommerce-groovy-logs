<?php

namespace WooCommerce_Groovy_Logs;

use WC_Admin_Menus;
use WC_Log_Handler_DB;
use WC_Log_Handler_Email;
use WC_Log_Handler_File;

class Loggers {
	private array $available = [];
	private $active_logger;

	public function setup(): void {
		$this->set_available_loggers();
		add_filter( 'jetpack_constant_default_value', [ $this, 'set_logger_constant' ], 10, 2 );
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

	public function set_logger_constant( $value, string $name ) {
		if ( $name !== 'WC_LOG_HANDLER' ) {
			return $value;
		}

		$active_handler = $this->get_active_logger_class();
		return class_exists( $active_handler ) ? $active_handler : $value;
	}

	public function define_handlers( array $handlers ): array {
		$active_handler = $this->get_active_logger_class();

		if ( class_exists( $active_handler ) ) {
			$this->active_logger = new $active_handler();
			return [ $this->active_logger ];
		}

		return $handlers;
	}

	private function get_active_logger_class(): string {
		$defined_handler = get_option( 'groovy_logs_logging_engine', false );

		return is_string( $defined_handler ) && isset( $this->available[ $defined_handler ] )
			? $this->available[ $defined_handler ][0]
			: '';
	}

	public function get_active_logger() {
		return $this->active_logger;
	}
}
