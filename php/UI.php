<?php

namespace WooCommerce_Groovy_Logs;

use WC_Admin_Menus;

class UI {
	public function setup(): void {
		add_action( 'load-woocommerce_page_wc-status', [ $this, 'intercept_default_logs_screen' ], 5 );

	}

	public function intercept_default_logs_screen(): void {
		if ( ( $_GET['tab'] ?? '' ) !== 'logs' ) {
			return;
		}

		if ( get_option( 'groovy_logs_logging_engine', '' ) !== 'sqlite' ) {
			return;
		}

		add_action( 'woocommerce_page_wc-status', [ $this, 'replace_log_screen' ], 5 );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
	}

	public function replace_log_screen(): void {
		Utilities::remove_anonymous_instance_callback( current_action(), WC_Admin_Menus::class, 'status_page' );
		$logger = plugin()->loggers->get_active_logger();
		$logs   = $logger->fetch();
		include __DIR__ . '/templates/log-viewer.php';
	}

	public function assets() {
		wp_enqueue_style( 'groovy-logs-log-viewer-css', plugin()->url( 'assets/log-viewer.css' ) );
	}
}