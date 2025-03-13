<?php

namespace WpAutoPosting\Core;

use WpAutoPosting\App\Services\Logger;

class View {
	public static function render( string $view, array $data = [] ): void {
		$view_path = plugin_dir_path( dirname( __FILE__, 2 ) ) . "resources/views/{$view}.php";

		if ( ! file_exists( $view_path ) ) {
			Logger::error( 'View file not found: ' . $view_path );
		}

		extract( $data );

		ob_start();
		include $view_path;
		echo ob_get_clean();
	}
}