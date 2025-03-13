<?php

namespace WpAutoPosting\App\Services;

class Logger {
	const LOG_FILE = 'debug.log';

	public static function log( string $message, string $level = 'info' ): void {
		$log_dir = wp_upload_dir()['basedir'] . '/' . self::LOG_FILE;

		if ( ! file_exists( $log_dir ) ) {
			wp_mkdir_p( $log_dir );
		}

		$log_path  = $log_dir . '/' . self::LOG_FILE;
		$timestamp = date( 'Y-m-d H:i:s' );

		$formatted_message = "[{$timestamp}] [{$level}] " . sanitize_text_field( $message ) . PHP_EOL;

		file_put_contents( $log_path, $formatted_message, FILE_APPEND | LOCK_EX );
	}

	public static function info( string $message ): void {
		self::log( $message );
	}

	public static function warning( string $message ): void {
		self::log( $message, 'warning' );
	}

	public static function error( string $message ): void {
		self::log( $message, 'error' );
	}
}