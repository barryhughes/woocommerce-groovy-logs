<?php

namespace WooCommerce_Groovy_Logs;

use Exception;
use SQLite3;
use WC_Log_Handler_Interface;

class SQLite_Logger implements WC_Log_Handler_Interface {
	/**
	 * Name of the main logging table.
	 */
	private const LOG_TABLE = 'log';

	/**
	 * Supported logging levels. Each is mapped to an integer. If an unknown or unsupported level is passed to the
	 * handler, it will treat it as a debug-level error.
	 */
	public const LEVELS = [
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
	private ?SQLite3 $database = null;

	/**
	 * @var string
	 */
	private string $log_path;

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
			$log_path = $this->get_log_path();
			$log_dir  = dirname( $log_path );

			if ( ! file_exists( $log_dir ) ) {
				mkdir( dirname( $log_path ), 0777, true );
			}

			$this->database = new SQLite3( $log_path );
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

	public function set_log_path( string $path = '' ): void {
		if ( ! empty( $path ) ) {
			$this->log_path = $path;
		} elseif ( defined( 'WC_LOG_DIR' ) ) {
			$this->log_path = trailingslashit( WC_LOG_DIR ) . '/log.db.php';
		} else {
			$this->log_path = sys_get_temp_dir() . '/woocommerce-groovy-logs/log.db.php';
		}
	}

	private function get_log_path(): string {
		if ( empty( $this->log_path ) ) {
			$this->set_log_path();
		}

		return $this->log_path;
	}

	private function encode_level( string $level ): int {
		return array_key_exists( $level, self::LEVELS ) ? self::LEVELS[ $level ] : self::LEVELS['debug'];
	}

	/**
	 * @todo implement more complex searches
	 * @todo establish this method as part of a common interface
	 *
	 * @param int          $page
	 * @param int          $per_page
	 * @param string|array $level
	 * @param string|int   $time
	 * @param string       $search
	 *
	 * @return Log_Record[]
	 */
	public function fetch( int $page = 1, int $per_page = 50, $level = 'all', $time = '', string $search = '' ): array {
		$records   = [];
		$table     = self::LOG_TABLE;
		$where     = [];
		$where_sql = '';

		if ( ! $this->ensure_setup() ) {
			return [];
		}

		$where[] = $this->levels_clause( (array) $level );

		if ( ! empty( $where ) ) {
			$where_sql = join( ' AND ', $where );
		}

		$statement = $this->database->prepare( "
			SELECT   *
			FROM     $table
			$where_sql
			ORDER BY timestamp DESC
			LIMIT    :limit
			OFFSET   :offset
		" );

		if ( ! $statement ) {
			return [];
		}

		$statement->bindValue( ':limit', absint( $per_page ), SQLITE3_INTEGER );
		$statement->bindValue( ':offset', $page * $per_page - $per_page, SQLITE3_INTEGER );
		$result = $statement->execute();

		if ( ! $result ) {
			return [];
		}

		while ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) {
			$records[] = new Log_Record( ...$row );
		}

		return $records;
	}

	private function levels_clause( array $levels ): string {
		$level_codes = [];

		if ( $levels === [ 'all' ] ) {
			return '';
		}

		foreach ( $levels as $level ) {
			if ( ! isset( self::LEVELS[ $level ] ) ) {
				throw new Exception( 'Valid log levels must be specified, or else specify "all".' );
			}

			if ( ! in_array( self::LEVELS[ $level ], $level_codes ) ) {
				$level_codes[] = '"' . self::LEVELS[ $level ] . '"';
			}
		}

		return 'WHERE level IN ( ' . join( ', ', $level_codes ) . ')';
	}
}
