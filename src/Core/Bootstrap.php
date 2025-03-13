<?php

namespace WpAutoPosting\Core;

use WpAutoPosting\App\Controllers\AdminController;
use WpAutoPosting\App\Controllers\AjaxController;

class Bootstrap {
	protected $loader;

	public function __construct() {
		$this->loader = new Loader();

		$this->define_admin_hooks();
		$this->define_ajax_hooks();
	}


	private function define_admin_hooks(): void {
		$admin = new AdminController();

		$this->loader->add_action( 'admin_menu', $admin, 'addMenuPage' );
		$this->loader->add_action( 'admin_init', $admin, 'registerSettings' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueueAdminAssets' );
	}

	private function define_ajax_hooks(): void {
		$ajax = new AjaxController();

		$this->loader->add_action( 'wp_ajax_save_open_ai_api_key', $ajax, 'saveOpenAIApiKey' );
		$this->loader->add_action( 'wp_ajax_get_open_ai_generated_post', $ajax, 'getOpenAiGeneratedPost' );
		$this->loader->add_action( 'wp_ajax_save_draft_post', $ajax, 'saveDraftPost' );
		$this->loader->add_action( 'wp_ajax_publish_post', $ajax, 'publishPost' );
	}

	public function run(): void {
		$this->loader->run();
	}
}
