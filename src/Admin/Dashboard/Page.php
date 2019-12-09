<?php

namespace TotalContest\Admin\Dashboard;

use TotalContestVendors\TotalCore\Admin\Pages\Page as TotalCoreAdminPage;
use TotalContestVendors\TotalCore\Contracts\Admin\Activation;
use TotalContestVendors\TotalCore\Contracts\Http\Request;

/**
 * Class Page
 * @package TotalContest\Admin\Dashboard
 */
class Page extends TotalCoreAdminPage {
	/**
	 * @var Activation $activation
	 */
	protected $activation;

	/**
	 * Page constructor.
	 *
	 * @param Request    $request
	 * @param array      $env
	 * @param Activation $activation
	 */
	public function __construct( Request $request, $env, Activation $activation ) {
		parent::__construct( $request, $env );
		$this->activation = $activation;
	}

	/**
	 * Page assets.
	 */
	public function assets() {
		/**
		 * @asset-script totalcontest-admin-dashboard
		 */
		wp_enqueue_script( 'totalcontest-admin-dashboard' );
		/**
		 * @asset-style totalcontest-admin-dashboard
		 */
		wp_enqueue_style( 'totalcontest-admin-dashboard' );

		// Tweets preset
		$tweets = [
			'I\'m happy with #TotalContest plugin for #WordPress!',
			'#TotalContest is a powerful plugin for #WordPress.',
			'#TotalContest is one of the best contest plugins for #WordPress out there.',
			'You\'re looking for a contest plugin for #WordPress? You should give #TotalContest a try.',
			'I recommend #TotalContest plugin for #WordPress webmasters.',
			'Check out #TotalContest, a powerful contest plugin for #WordPress.',
			'Create closed contests and public contests easily with #TotalContest for #WordPress.',
			'Run a contest easily on your #WordPress powered website using #TotalContest.',
			'Boost user engagement with your website using #TotalContest plugin for #WordPress',
		];
		// Support
		// @TODO: Get this list from server
		$support = [
			'sections' => [
				[
					'title'       => 'Basics',
					'description' => 'The basics of TotalContest',
					'url'         => 'https://totalsuite.net/documentation/totalcontest/basics-totalcontest/',
					'links'       => [
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/basics-totalcontest/create-first-contest-using-totalcontest/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Creating a new contest' ],
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/basics-totalcontest/introduction-to-contest-editor/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Introduction to contest editor' ],
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/basics-totalcontest/essential-settings-overview/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Essential settings overview' ],
					],
				],
				[
					'title'       => 'Advanced',
					'description' => 'Do more with TotalContest',
					'url'         => 'https://totalsuite.net/documentation/totalcontest/advanced-totalcontest/',
					'links'       => [
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/advanced-totalcontest/participation-limitations/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Participation limitations' ],
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/advanced-totalcontest/participation-frequency/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Participation frequency' ],
						[ 'url' => 'https://totalsuite.net/documentation/totalcontest/advanced-totalcontest/vote-limitations/?utm_source=in-app&utm_medium=support-tab&utm_campaign=totalcontest', 'title' => 'Vote limitations' ],
					],
				],
			],
		];
		wp_localize_script( 'totalcontest-admin-dashboard', 'TotalContestPresets', [ 'tweets' => $tweets ] );
		wp_localize_script( 'totalcontest-admin-dashboard', 'TotalContestActivation', $this->activation->toArray() );
		wp_localize_script( 'totalcontest-admin-dashboard', 'TotalContestSupport', $support );
	}

	/**
	 * Page content.
	 */
	public function render() {
		include __DIR__ . '/views/index.php';
	}
}
