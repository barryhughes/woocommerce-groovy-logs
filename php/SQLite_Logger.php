<?php

namespace WooCommerce_Groovy_Logs;

use Exception;
use SQLite3;
use WC_Log_Handler;
use WC_Log_Handler_Interface;

class SQLite_Logger extends WC_Log_Handler implements WC_Log_Handler_Interface {
	/**
	 * Name of the main logging table.
	 */
	private const LOG_TABLE = 'log';

	/**
	 * Supported logging levels. Each is mapped to an integer. If an unknown or unsupported level is passed to the
	 * handler, it will treat it as a debug-level error.
	 */
	private const LEVELS = [
		'debug'     => 0,
		'info'      => 1,
		'notice'    => 2,
		'warning'   => 3,
		'error'     => 4,
		'critical'  => 5,
		'alert'     => 6,
		'emergency' => 8,
	];

	/**
	 * Database connection.
	 *
	 * @var SQLite3
	 */
	private $database;

	/**
	 * Handle a log entry.
	 *
	 * @param int    $timestamp Log timestamp.
	 * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
	 * @param string $message Log message.
	 * @param array  $context Additional information for log handlers.
	 *
	 * @return bool False if value was not handled and true if value was handled.
	 */
	public function handle( $timestamp, $level, $message, $context ) {
		$table = self::LOG_TABLE;

		if ( ! $this->ensure_setup() ) {
			error_log( 'SQLite handler was unable to initialize.' );
			return false;
		}

		$timestamp = absint( $timestamp );
		$level     = $this->encode_level( $level );
		$context   = json_encode( $context );

		$insert = $this->database->prepare( "
			INSERT INTO $table ( timestamp, level, message, context )
			VALUES ( :timestamp, :level, :message, :context );
		" );

		if ( ! $insert ) {
			return false;
		}

		$insert->bindValue( ':timestamp', $timestamp, SQLITE3_INTEGER );
		$insert->bindValue( ':level', $level, SQLITE3_INTEGER );
		$insert->bindValue( ':message', $message, SQLITE3_TEXT );
		$insert->bindValue( ':context', $context, SQLITE3_TEXT );

		return $insert->execute() !== false;
	}

	private function ensure_setup(): bool {
		if ( $this->database ) {
			return true;
		}

		try {
			$this->database = new SQLite3( $this->log_path() );
			$this->ensure_schema();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	private function ensure_schema(): void {
		$table = self::LOG_TABLE;

		if ( $this->database->querySingle( "SELECT name FROM sqlite_master WHERE type='table' AND name='$table'" ) ) {
			return;
		}

		$created = $this->database->exec( "
			CREATE TABLE $table (
			    id        INTEGER PRIMARY KEY AUTOINCREMENT,
			    timestamp INTEGER,
			    level     INTEGER,
			    message   TEXT,
			    context   TEXT
			)
		" );

		if ( ! $created ) {
			throw new Exception( 'SQLite logger could not establish the logging table.' );
		}
	}

	private function log_path(): string {
		if ( ! defined( 'WC_LOG_DIR' ) ) {
			throw new Exception( 'SQLite logger cannot initialize, as WC_LOG_DIR is not defined' );
		}

		return trailingslashit( WC_LOG_DIR ) . 'log.db.php';
	}

	private function encode_level( string $level ): int {
		return array_key_exists( $level, self::LEVELS ) ? self::LEVELS[ $level ] : self::LEVELS['debug'];
	}
}
