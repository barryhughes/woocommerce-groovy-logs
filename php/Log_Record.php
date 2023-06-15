<?php

namespace WooCommerce_Groovy_Logs;

/**
 * @property-read int    $timestamp
 * @property-read int    $level
 * @property-read string $message
 * @property-read mixed  $context
 */
class Log_Record {
	private $id;
	private $timestamp;
	private $level;
	private $message;
	private $context;

	/**
	 * @param int                 $id
	 * @param int                 $timestamp
	 * @param int                 $level
	 * @param string              $message
	 * @param string|array|object $context
	 */
	public function __construct( int $id, int $timestamp, int $level, string $message, $context ) {
		$this->id        = $id;
		$this->timestamp = $timestamp;
		$this->level     = $level;
		$this->message   = $message;
		$this->context   = $context;
	}


	public function __get( $key ) {
		return isset( $this->$key ) ? $this->$key : null;
	}
}