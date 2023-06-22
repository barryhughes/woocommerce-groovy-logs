<?php

namespace WooCommerce_Groovy_Logs;

/**
 * @property-read Loggers $loggers
 * @property-read UI $ui
 * @property-read WooCommerce_Settings $woocommerce_settings
 */
class Plugin {
	use Object_Properties;

	private $plugin_url = '';

	private $objects = [
		'loggers'              => Loggers::class,
		'ui'                   => UI::class,
		'woocommerce_settings' => WooCommerce_Settings::class,
	];

	public function setup( string $main_plugin_file_path = '' ): void {
		$this->plugin_url = plugin_dir_url( $main_plugin_file_path );
		$this->loggers;
		$this->ui;
		$this->woocommerce_settings;
	}

	public function url( string $path = '' ): string {
		return ! empty( $path ) ? trailingslashit( $this->plugin_url ) . $path : $this->plugin_url;
	}
}
