<?php

namespace TotalContest\Admin\Log;

use TotalContestVendors\TotalCore\Admin\Pages\Page as AdminPageContract;

/**
 * Class Page
 * @package TotalContest\Admin\Log
 */
class Page extends AdminPageContract {
	public function assets() {
		// TotalContest
		wp_enqueue_script( 'totalcontest-admin-log' );
		wp_enqueue_style( 'totalcontest-admin-log' );
		wp_localize_script( 'totalcontest-admin-log', 'TotalContestLog', [
			'contestId'    => $this->request->query( 'contest' ),
			'submissionId' => $this->request->query( 'submission' ),
		] );
	}

	public function render() {
		/**
		 * Filters the list of columns in log browser.
		 *
		 * @param array $columns Array of columns.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$columns = apply_filters(
			'totalcontest/filters/admin/log/columns',
			[
				'status'     => [ 'label' => __( 'Status', 'totalcontest' ), 'default' => true, ],
				'action'     => [ 'label' => __( 'Action', 'totalcontest' ), 'default' => true, ],
				'date'       => [ 'label' => __( 'Date', 'totalcontest' ), 'default' => true, ],
				'ip'         => [ 'label' => __( 'IP', 'totalcontest' ), 'default' => true, ],
				'browser'    => [ 'label' => __( 'Browser', 'totalcontest' ), 'default' => false, ],
				'contest'    => [ 'label' => __( 'Contest', 'totalcontest' ), 'default' => true, ],
				'submission' => [ 'label' => __( 'Submission', 'totalcontest' ), 'default' => true, ],
				'user_name'  => [ 'label' => __( 'Name', 'totalcontest' ), 'default' => false, ],
				'user_id'    => [ 'label' => __( 'ID', 'totalcontest' ), 'default' => false, ],
				'user_login' => [ 'label' => __( 'Username', 'totalcontest' ), 'default' => true, ],
				'user_email' => [ 'label' => __( 'Email', 'totalcontest' ), 'default' => false, ],
				'details'    => [ 'label' => __( 'Details', 'totalcontest' ), 'default' => false, 'compact' => true ],
			]
		);

		/**
		 *
		 * Filters the list of available formats that can be used for export.
		 *
		 * @param array $formats Array of formats [id => label].
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$formats = apply_filters(
			'totalcontest/filters/admin/log/formats',
			[
				'html' => __( 'HTML', 'totalcontestl' ),
				
			]
		);

		include_once __DIR__ . '/views/index.php';
	}
}