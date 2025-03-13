<?php

namespace WpAutoPosting\App\Controllers;

use WpAutoPosting\App\Models\OptionModel;
use WpAutoPosting\App\Models\PostModel;
use WpAutoPosting\App\Services\Logger;
use WpAutoPosting\App\Services\OpenAiService;

class AjaxController {
	public function saveOpenAIApiKey() {
		$api_key = $_POST['api_key'];

		if ( empty( $api_key ) ) {
			Logger::error( 'Trying to send empty API key' );
			wp_send_json_error( [ 'message' => 'API Key is empty', 'status' => 'warning' ] );
		}

		OptionModel::update( 'wp_openai_api_key', $api_key );
		$isApiKeyValid = OpenAiService::getInstance()->isApiKeyValid();

		if ( $isApiKeyValid ) {
			Logger::info( 'API Key is Valid' );
			wp_send_json_success( [ 'success' => true ] );
		} else {
			Logger::error( 'API Key was not updated' );
			wp_send_json_success( [ 'success' => false ] );
		}

		wp_die();
	}

	public function getOpenAiGeneratedPost() {
		$prompt = $_POST['prompt'];

		if ( empty( $prompt ) ) {
			Logger::error( 'Trying to send empty prompt' );
			wp_send_json_error( [ 'message' => 'Prompt is empty', 'status' => 'warning' ] );
		}

		$openAI  = OpenAiService::getInstance();
		$newPost = $openAI->generatePost( $prompt );

		wp_send_json_success( $newPost );
		wp_die();
	}

	public function saveDraftPost() {
		if ( empty( $_POST['title'] ) || empty( $_POST['content'] ) ) {
			wp_send_json_error( [ 'message' => 'Fields cant be empty' ] );
		}

		$title   = sanitize_text_field( $_POST['title'] );
		$content = wp_kses_post( $_POST['content'] ); // Очищает HTML от опасных тегов
		$status  = 'draft';

		$post_id = PostModel::create( [
			'title'   => $title,
			'content' => $content,
			'status'  => $status
		] );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			wp_send_json_error( [ 'message' => '⚠ Ошибка при сохранении черновика.' ] );
		}

		wp_send_json_success( [
			'message' => '✅ Черновик успешно сохранён!',
			'post_id' => $post_id,
			'link'    => get_permalink( $post_id )
		] );

		wp_die();
	}

	public function publishPost() {
		if ( empty( $_POST['title'] ) || empty( $_POST['content'] ) ) {
			wp_send_json_error( [ 'message' => 'Fields cant be empty' ] );
		}

		$title   = sanitize_text_field( $_POST['title'] );
		$content = wp_kses_post( $_POST['content'] );
		$status  = 'publish';

		$post_id = PostModel::create( [
			'title'   => $title,
			'content' => $content,
			'status'  => $status
		] );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			wp_send_json_error( [ 'message' => '⚠ Ошибка при сохранении черновика.' ] );
		}

		wp_send_json_success( [
			'message' => '✅ Черновик успешно сохранён!',
			'post_id' => $post_id,
			'link'    => get_permalink( $post_id )
		] );

		wp_die();
	}
}