<?php

namespace WooCommerce_Groovy_Logs;

use WC_Admin_Menus;

class UI {
	public function setup(): void {
		add_action( 'woocommerce_page_wc-status', [ $this, 'intercept_default_logs_screen' ], 5 );
	}

	public function intercept_default_logs_screen(): void {
		if ( ( $_GET['tab'] ?? '' ) !== 'logs' ) {
			return;
		}

		if ( get_option( 'groovy_logs_logging_engine', '' ) !== 'sqlite' ) {
			return;
		}

		Utilities::remove_anonymous_instance_callback( current_action(), WC_Admin_Menus::class, 'status_page' );
		do_action( 'groovy_logs_initialize_ui' );
	}
}