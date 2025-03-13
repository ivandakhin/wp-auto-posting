<?php

namespace WpAutoPosting\App\Services;

use WpAutoPosting\App\Models\OptionModel;
use OpenAI;

class OpenAiService {
	private static ?OpenAiService $instance = null;
	private OpenAI\Client $client;
	private $api_key;

	private function __construct() {
		$this->api_key = OptionModel::get( 'wp_openai_api_key' ) ?? '';

		if ( empty( $this->api_key ) ) {
			Logger::error( "OpenAI API key is empty" );
		} else {
			$this->client = OpenAI::client( $this->api_key );
		}
	}

	public static function getInstance(): OpenAiService {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __clone() {
	}
	public function checkApiKey() {

	}

	public function availableModelsNames() {
		if ( empty( $this->api_key ) ) {
			return [ 'error' => 'API Key is not valid.' ];
		}

		try {
			$models = $this->client->models()->list();

			if (!isset($models->data) || empty($models->data)) {
				return ['error' => 'No available models found.'];
			}

			return array_map(fn($model) => $model->id, $models->data);
		} catch (\OpenAI\Exceptions\ErrorException $e) {
			return [];
		}
	}


	public function isApiKeyValid() {
		if ( empty( $this->api_key ) ) {
			return false;
		}
		$models = $this->availableModelsNames();

		return ! isset( $models['error'] ) && count( $models ) > 0;
	}

	public function generatePost( string $prompt ): array {
		if ( empty( $this->api_key ) ) {
			return [ 'error' => 'API Key is not valid.' ];
		}

		$prompts = [
			'title'   => "
            Сгенерируй заголовок статьи на тему: \"{$prompt}\".
            Требования:
            - Заголовок должен быть информативным, привлекательным и коротким (до 10 слов).
            - Без кавычек и лишних символов.
            - Не добавляй теги <h1>, просто текст заголовка.
            - Используй правильный регистр и избегай кликбейта.
        ",
			'content' => "
            Сгенерируй содержимое статьи на тему: \"{$prompt}\".
            Форматирование:
            - Статья должна быть логически структурирована.
            - Заголовки 2-го уровня должны быть в <h2>.
            - Заголовки 3-го уровня должны быть в <h3>.
            - Основной текст должен быть в <p>.
            - Используй списки <ul> и <ol>, если это уместно.
            - Не добавляй заголовок статьи, только контент.
            - Не включай <h1>, <html>, <head>, <body> или <doctype>.
        "
		];

		$response = [];

		foreach ( $prompts as $key => $prompt_text ) {
			$response[ $key ] = $this->generateText( $prompt_text, ucfirst( $key ) );
		}

		return $response;
	}

	private function generateText( string $prompt, string $type ): string {
		try {
			$response = $this->client->chat()->create( [
				'model'       => 'gpt-4o',
				'messages'    => [
					[
						'role'    => 'system',
						'content' => 'Ты - профессиональный копирайтер-шутник, все переводишь в черный юмор! '
					],
					[ 'role' => 'user', 'content' => $prompt ]
				],
				'temperature' => 0.7,
				'max_tokens'  => $type === 'Title' ? 100 : 2000,
			] );

			Logger::info( "Generated {$type} by prompt." );

			return trim( $response['choices'][0]['message']['content'] ?? 'Ошибка генерации' );
		} catch ( \Exception $e ) {
			Logger::error( "OpenAI request error ({$type}): " . $e->getMessage() );

			return "Ошибка запроса к OpenAI ({$type})";
		}
	}
}