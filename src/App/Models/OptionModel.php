<?php

namespace WpAutoPosting\App\Models;

class OptionModel {
	public static function update( string $key, $value ): bool {
		if ( ! $key || ! $value ) {
			return false;
		}

		return update_option( $key, $value );
	}

	public static function get( string $key ): mixed {
		if ( ! $key ) {
			return false;
		}

		return get_option( $key );
	}
}