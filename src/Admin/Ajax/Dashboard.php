<?php

namespace TotalContest\Admin\Ajax;

use TotalContest\Contest\Model as ContestModel;
use TotalContest\Contracts\Contest\Repository as ContestRepository;
use TotalContestVendors\TotalCore\Contracts\Admin\Account;
use TotalContestVendors\TotalCore\Contracts\Admin\Activation;
use TotalContestVendors\TotalCore\Contracts\Http\Request;

/**
 * Class Dashboard
 * @package TotalContest\Admin\Ajax
 * @since   1.0.0
 */
class Dashboard {
	/**
	 * @var Request $request
	 */
	protected $request;
	/**
	 * @var Activation $activation
	 */
	protected $activation;
	/**
	 * @var Account $account
	 */
	protected $account;
	/**
	 * @var ContestRepository $contestRepository
	 */
	private $contestRepository;

	/**
	 * Dashboard constructor.
	 *
	 * @param Request           $request
	 * @param Activation        $activation
	 * @param Account           $account
	 * @param ContestRepository $contestRepository
	 */
	public function __construct( Request $request, Activation $activation, Account $account, ContestRepository $contestRepository ) {
		$this->request           = $request;
		$this->activation        = $activation;
		$this->account           = $account;
		$this->contestRepository = $contestRepository;
	}

	/**
	 * Activation AJAX endpoint.
	 * @action-callback wp_ajax_totalcontest_dashboard_activate
	 */
	public function activate() {
		
	}

	/**
	 * Get contests AJAX endpoint.
	 * @action-callback wp_ajax_totalcontest_dashboard_contests_overview
	 */
	public function contests() {
		$contests = array_map(
			function ( $contest ) {
				$id = $contest->getId();

				/**
				 * Filters the contest object sent to dashboard.
				 *
				 * @param array        $contestRepresentation The representation of a contest.
				 * @param ContestModel $contest               Contest model object.
				 *
				 * @since 2.0.0
				 * @return array
				 */
				return apply_filters(
					'totalcontest/filters/admin/dashboard/contest',
					[
						'id'              => $id,
						'title'           => $contest->getTitle(),
						'status'          => get_post_status( $contest->getContestPost() ),
						'permalink'       => $contest->getPermalink(),
						'editLink'        => $contest->getAdminEditLink(),
						'logLink'         => $contest->getAdminLogLink(),
						'submissionsLink' => $contest->getAdminSubmissionsLink(),
						'statistics'      => [
							'submissions' => $contest->getSubmissionsCount(),
							'votes'       => $contest->getVotes(),
						],
					],
					$contest,
					$this
				);
			},
			$this->contestRepository->get( [ 'perPage' => 10, 'status' => 'any' ] )
		);

		/**
		 * Filters the contests list sent to dashboard.
		 *
		 * @param ContestModel[] $contests Array of contest models.
		 *
		 * @since 2.0.0
		 * @return array
		 */
		$contests = apply_filters( 'totalcontest/filters/admin/dashboard/contests', $contests );

		wp_send_json( $contests );
	}

	/**
	 * TotalSuite Account AJAX endpoint.
	 */
	public function account() {
		
	}
}