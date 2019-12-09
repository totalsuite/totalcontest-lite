<?php

namespace TotalContest\Shortcode;

/**
 * Submissions shortcode class
 * @package TotalContest\Shortcode
 * @since   1.0.0
 */
class Submissions extends Base {

	public function handle() {
		$submission = $this->getSubmission();
		if ( $submission ):
			$submission->getContest()->setMenuItemsVisibility( [ 'submissions' => true ] );

			return $submission->render();
		endif;

		return $this->getContest()->setMenuVisibility( false )->setScreen( 'contest.submissions' )->render();
	}


}