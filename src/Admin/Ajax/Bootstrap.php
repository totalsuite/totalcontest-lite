<?php

namespace TotalContest\Admin\Ajax;

/**
 * Class Bootstrap
 * @package TotalContest\Admin\Ajax
 */
class Bootstrap {
	/**
	 * Bootstrap constructor.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_options' ) ):

			

			/**
			 * @action wp_ajax_totalcontest_dashboard_contests_overview
			 * @since  2.0.0
			 */
			add_action( 'wp_ajax_totalcontest_dashboard_contests_overview', function () {
				TotalContest( 'admin.ajax.dashboard' )->contests();
			} );

			// Log
			add_action( 'wp_ajax_totalcontest_log_list', function () {
				TotalContest( 'admin.ajax.log' )->fetch();
			} );

			add_action( 'wp_ajax_totalcontest_log_download', function () {
				TotalContest( 'admin.ajax.log' )->download();
			} );

			// Modules
			add_action( 'wp_ajax_totalcontest_modules_install_from_file', function () {
				TotalContest( 'admin.ajax.modules' )->installFromFile();
			} );
			add_action( 'wp_ajax_totalcontest_modules_install_from_store', function () {
				TotalContest( 'admin.ajax.modules' )->installFromStore();
			} );
			add_action( 'wp_ajax_totalcontest_modules_list', function () {
				TotalContest( 'admin.ajax.modules' )->fetch();
			} );
			add_action( 'wp_ajax_totalcontest_modules_update', function () {
				TotalContest( 'admin.ajax.modules' )->update();
			} );
			add_action( 'wp_ajax_totalcontest_modules_uninstall', function () {
				TotalContest( 'admin.ajax.modules' )->uninstall();
			} );
			add_action( 'wp_ajax_totalcontest_modules_activate', function () {
				TotalContest( 'admin.ajax.modules' )->activate();
			} );
			add_action( 'wp_ajax_totalcontest_modules_deactivate', function () {
				TotalContest( 'admin.ajax.modules' )->deactivate();
			} );

			// Options
			add_action( 'wp_ajax_totalcontest_options_save_options', function () {
				TotalContest( 'admin.ajax.options' )->saveOptions();
			} );
			add_action( 'wp_ajax_totalcontest_options_purge', function () {
				TotalContest( 'admin.ajax.options' )->purge();
			} );
			
		endif;

		if ( current_user_can( 'edit_contests' ) ):
			// ------------------------------
			// Contests
			// ------------------------------
			/**
			 * @action wp_ajax_totalcontest_contests_add_to_sidebar
			 * @since  2.0.0
			 */
			add_action( 'wp_ajax_totalcontest_contests_add_to_sidebar', function () {
				TotalContest( 'admin.ajax.contests' )->addToSidebar();
			} );
			/**
			 * @action wp_ajax_totalcontest_contests_get_categories
			 * @since  2.0.0
			 */
			add_action( 'wp_ajax_totalcontest_contests_get_categories', function () {
				TotalContest( 'admin.ajax.contests' )->getCategories();
			} );
		endif;

		if ( current_user_can( 'publish_contest_submissions' ) ):
			/**
			 * @action wp_ajax_totalcontest_contests_approve_submission
			 * @since  2.0.0
			 */
			add_action( 'wp_ajax_totalcontest_contests_approve_submission', function () {
				TotalContest( 'admin.ajax.contests' )->approveSubmission();
			} );
		endif;

		// ------------------------------
		// Templates
		// ------------------------------
		/**
		 * @action wp_ajax_totalcontest_templates_get_defaults
		 * @since  4.0.0
		 */
		add_action( 'wp_ajax_totalcontest_templates_get_defaults', function () {
			TotalContest( 'admin.ajax.templates' )->getDefaults();
		} );
		/**
		 * @action wp_ajax_totalcontest_templates_get_preview
		 * @since  4.0.0
		 */
		add_action( 'wp_ajax_totalcontest_templates_get_preview', function () {
			TotalContest( 'admin.ajax.templates' )->getPreview();
		} );
		/**
		 * @action wp_ajax_totalcontest_templates_get_settings
		 * @since  4.0.0
		 */
		add_action( 'wp_ajax_totalcontest_templates_get_settings', function () {
			TotalContest( 'admin.ajax.templates' )->getSettings();
		} );

		/**
		 * Fires when AJAX handlers are bootstrapped.
		 *
		 * @since 2.0.0
		 * @order 7
		 */
		do_action( 'totalcontest/actions/bootstrap-ajax' );
	}

}
