<?php

namespace WpAutoPosting\App\Models;

class PostModel {
	public static function create( $data ) {
		if ( ! is_array( $data ) || empty( $data ) ) {
			return false;
		}

		$args = array(
			'post_title'   => sanitize_text_field( $data['title'] ),
			'post_content' => wp_kses_post( $data['content'] ),
			'post_author'  => get_current_user_id(),

			'post_status' => $data['status'],
			'post_type'   => 'post',
		);

		return wp_insert_post( $args );
	}
}