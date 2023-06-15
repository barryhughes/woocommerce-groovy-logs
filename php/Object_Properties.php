<?php

namespace WooCommerce_Groovy_Logs;

trait Object_Properties {
	public function __get( $key ) {
		if ( ! isset( $this->objects ) || ! isset( $this->objects[$key] ) ) {
			return null;
		}

		if ( is_object( $this->objects[ $key ] ) ) {
			return $this->objects[ $key ];
		}

		if ( ! class_exists( $this->objects[ $key ] ) ) {
			return null;
		}

		$this->objects[ $key ] = new $this->objects[ $key ]();

		if ( method_exists( $this->objects[ $key ], 'setup' ) ) {
			$this->objects[ $key ]->setup();
		}

		return $this->objects[ $key ];
	}
}