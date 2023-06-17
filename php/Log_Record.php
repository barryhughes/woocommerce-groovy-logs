<?php

namespace WooCommerce_Groovy_Logs;

/**
 * @property-read int    $timestamp
 * @property-read int    $level
 * @property-read string $message
 * @property-read mixed  $context
 */
class Log_Record {
	/**
	 * @param int                 $id
	 * @param int                 $timestamp
	 * @param int                 $level
	 * @param string              $message
	 * @param string|array|object $context
	 */
	public function __construct(
		private int $id,
		private int $timestamp,
		private int $level,
		private string $message,
		private $context ) {}

	public function __get( $key ) {
		return isset( $this->$key ) ? $this->$key : null;
	}
}