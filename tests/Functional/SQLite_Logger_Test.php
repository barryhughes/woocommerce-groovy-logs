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

test( 'By default, logs are ordered youngest (most recent) first.', function () {
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

test( 'It is possible to retrieve specific levels.', function () {
	$logger = get_sqlite_logger();
	$time   = time();

	$logger->handle( $time - 10, 'info',      'Business as usual.', [] );
	$logger->handle( $time - 11, 'error',     'An error occurred.', [] );
	$logger->handle( $time - 12, 'emergency', 'Paging Dr Beat, emergency.', [] );

	$info_logs  = $logger->fetch( level: 'info' );
	$error_logs = $logger->fetch( level: 'error' );
	$info_emergency_logs = $logger->fetch( level: [ 'info', 'emergency' ] );

	expect( $info_logs )->toHaveCount( 1 );
	expect( $error_logs )->toHaveCount( 1 );
	expect( $info_emergency_logs )->toHaveCount( 2 );

	expect( $info_logs[0]->message )->toEqual( 'Business as usual.' );
	expect( $info_emergency_logs[1]->message )->toEqual( 'Paging Dr Beat, emergency.' );
} );

test( 'Invalid levels are rejected.', function() {
	get_sqlite_logger()->fetch( level: 'tennis' );
} )->expectException( Exception::class );

test( 'Given a mix of valid and invalid levels, the query will be rejected.', function () {
	get_sqlite_logger()->fetch( level: [ 'info', 'debug', 'tennis' ] );
} )->expectException( Exception::class );

test( 'Log entries before, after or exactly at a specific timestamp can be fetched.', function () {
	$logger = get_sqlite_logger();
	$time   = time();

	$logger->handle( $time - 100, 'info',     'Another event.', [] );
	$logger->handle( $time - 200, 'debug',    'An interesting detail.', [] );
	$logger->handle( $time - 400, 'critical', 'Rather important.', [] );

	$oldest_two = $logger->fetch( timestamp: '<=' . $time-200 );
	expect( $oldest_two )->toHaveCount( 2 );
	expect( $oldest_two[0]->timestamp )->toEqual( $time - 200 );
	expect( $oldest_two[1]->timestamp )->toEqual( $time - 400 );
} );