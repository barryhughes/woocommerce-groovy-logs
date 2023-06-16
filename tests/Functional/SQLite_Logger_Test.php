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

	$logs = $logger->fetch();

	expect( $logs )->toHaveCount( 2, 'We should be able to fetch both log entries.' );
	expect( $logs[0] )->toBeInstanceOf( Log_Record::class, 'Fetched logs are delivered as Log_Record objects.' );
	expect( $logs[0]->level )->toEqual(
		SQLite_Logger::LEVELS['error'],
		'If a valid logging level was specified, the same level should be returned.'
	);
	expect( $logs[1]->level )->toEqual(
		SQLite_Logger::LEVELS['debug'],
		'If an invalid logging level was specified, it should show as DEBUG level on retrieval.'
	);
} );

test( 'On retrieval, logs are ordered oldest first.', function () {
	$logger       = get_sqlite_logger();
	$relative_age = [
		'3rd' => 1000,
		'4th' => 2000,
		'1st' => 500,
		'5th' => 3000,
		'2nd' => 750,
		'6th' => 4000,
	];

	foreach ( $relative_age as $message => $adjustment ) {
		$logger->handle( time() - $adjustment, 'warning', $message, [] );
	}

	$logs = $logger->fetch();
	expect( $logs[0]->message )->toEqual( '1st', 'By default, the youngest log should be the first one we retrieve.' );
	expect( $logs[5]->message )->toEqual( '6th', 'By default, the oldest log should be the last one we retrieve.' );
} );
