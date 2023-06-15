<?php

namespace WooCommerce_Groovy_Logs;

/**
 * @property-read Loggers $loggers
 * @property-read UI $ui
 * @property-read WooCommerce_Settings $woocommerce_settings
 */
class Plugin {
	use Object_Properties;

	private $objects = [
		'loggers'              => Loggers::class,
		'ui'                   => UI::class,
		'woocommerce_settings' => WooCommerce_Settings::class,
	];

	public function setup(): void {
		$this->loggers;
		$this->ui;
		$this->woocommerce_settings;
	}
}
