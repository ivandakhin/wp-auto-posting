<?php

namespace WpAutoPosting\App\Controllers;

use WpAutoPosting\App\Models\OptionModel;
use WpAutoPosting\App\Services\OpenAiService;
use WpAutoPosting\Core\View;

class AdminController {
	private string $menu_slug = 'wp-auto-posting';
	private string $page_suffix;

	public function addMenuPage(): void {
		$this->page_suffix = add_menu_page(
			'Auto Posting',
			'Auto Posting',
			'manage_options',
			$this->menu_slug,
			[ $this, 'renderSettingsPage' ],
			'dashicons-lightbulb',
			75
		);

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminAssets' ] );
	}

	public function registerSettings(): void {
		register_setting( 'wp_auto_posting', 'wp_openai_api_key' );
	}

	public function renderSettingsPage(): void {
		$api_key = OptionModel::get( 'wp_openai_api_key' );
		$isApiKeyValid = OpenAiService::getInstance()->isApiKeyValid();
		View::render( 'admin-settings', [
			'api_key' => $api_key,
			'isApiKeyValid' => $isApiKeyValid
		] );
	}

	public function enqueueAdminAssets( $hook ): void {
		if ( $hook !== $this->page_suffix ) {
			return;
		}

		wp_enqueue_style(
			'wp-auto-posting-admin-style',
			plugin_dir_url( dirname( __FILE__, 3 ) ) . 'resources/assets/css/style.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'wp-auto-posting-admin-script',
			plugin_dir_url( dirname( __FILE__, 3 ) ) . 'resources/assets/js/index.js',
			[ 'jquery', 'semantic-script' ],
			'1.0.0',
			true
		);

		wp_enqueue_style( 'semantic-styles', plugin_dir_url( dirname( __FILE__, 3 ) ) . 'resources/assets/css/semantic.min.css', [], '1.0.0' );

		wp_enqueue_script( 'semantic-script', plugin_dir_url( dirname( __FILE__, 3 ) ) . 'resources/assets/js/semantic.min.js', ['jquery'], '1.0.0' );


//		wp_enqueue_style( 'uikit-styles', 'https://cdn.jsdelivr.net/npm/uikit@3.23.1/dist/css/uikit.min.css', [], '3.23.1' );
//
//		wp_enqueue_script( 'uikit-script', 'https://cdn.jsdelivr.net/npm/uikit@3.23.1/dist/js/uikit.min.js', [], '3.23.1' );
//		wp_enqueue_script( 'uikit-icons', 'https://cdn.jsdelivr.net/npm/uikit@3.23.1/dist/js/uikit-icons.min.js', [], '3.23.1' );
	}
}