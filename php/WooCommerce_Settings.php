<?php

namespace WooCommerce_Groovy_Logs;

class WooCommerce_Settings {
	public const SETTINGS_SECTION = 'groovy_logs';

	public function setup() {
		add_filter( 'woocommerce_get_sections_advanced', [ $this, 'register_advanced_settings_section' ] );
		add_filter( 'woocommerce_get_settings_advanced', [ $this, 'register_logs_settings' ], 10, 2 );
	}

	public function register_advanced_settings_section( array $sections ): array {
		$sections[ self::SETTINGS_SECTION ] = __( 'Logs', 'woocommerce-groovy-logs' );
		return $sections;
	}

	public function register_logs_settings( array $settings, string $section_id ): array {
		if ( $section_id !== self::SETTINGS_SECTION ) {
			return $settings;
		}

		return [ [
				'id'    => 'groovy_logs_settings',
				'title' => __( 'Logging Settings', 'woocommerce-groovy-logs' ),
				'desc'  => __( 'Controls how and where WooCommerce stores logs.', 'woocommerce-groovy-logs' ),
				'type'  => 'title',
			], [
				'id'       => 'groovy_logs_logging_engine',
				'title'    => __( 'Logging Engine', 'woocommerce-groovy-logs' ),
				'desc'     => __( 'Which logging method should WooCommerce use?', 'woocommerce-groovy-logs' ),
				'desc_tip' => __( 'This option determines which logging engine is used to store logs.', 'woocommerce-groovy-logs' ),
				'default'  => 'file',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => plugin()->loggers->get_available_loggers_by_name(),
			], [
				'type' => 'sectionend',
				'id'   => 'groovy_logs_settings',
			],
		];
	}
}