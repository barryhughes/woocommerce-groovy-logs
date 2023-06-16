<?php

use WooCommerce_Groovy_Logs\Log_Record;
use WooCommerce_Groovy_Logs\SQLite_Logger;

test( 'Basic logging functionality.', function () {
	$logger = get_sqlite_logger();

	expect(
		$logger->handle( time(), 'error', 'An error occured.', [] )
	)->toBeTrue( 'Well-formed log entries are accepted.' );

	expect(
		$logger->handle( time(), 'proclamation', 'Much ado', [] )
	)->toBeTrue( 'Log entries are accepted even if the logging level is not recognized.' );

	$results = $logger->fetch();

	expect( $results )->toHaveCount( 2, 'We should be able to fetch both log entries.' );
	expect( $results[0] )->toBeInstanceOf( Log_Record::class, 'Fetched logs are delivered as Log_Record objects.' );
	expect( $results[0]->level )->toEqual(
		SQLite_Logger::LEVELS['error'],
		'If a valid logging level was specified, the same level should be returned.'
	);
	expect( $results[1]->level )->toEqual(
		SQLite_Logger::LEVELS['debug'],
		'If an invalid logging level was specified, it should show as DEBUG level on retrieval.'
	);
} );
