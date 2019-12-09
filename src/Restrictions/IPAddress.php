<?php

namespace TotalContest\Restrictions;

/**
 * Class IPAddress
 * @package TotalContest\Restrictions
 */
class IPAddress extends Restriction {
	/**
	 * Check logic.
	 *
	 * @return \WP_Error|bool
	 */
	public function check() {
		$result = true;

		if ( $this->getContestId() ):
			$cookieValue = $this->getCookie( $this->getContestCookieName() );
			$result      = ! ( $cookieValue >= $this->getCount() );
		endif;

		if ( $result && $this->getSubmissionId() ):
			$cookieValue = $this->getCookie( $this->getSubmissionCookieName() );
			$result      = ! ( $cookieValue >= $this->getPerItem() );
		endif;

		if ( $this->isFullCheck() || $result ):
			$timeout    = $this->getTimeout();
			$conditions = [
				'contest_id' => $this->getContestId(),
				'action'     => $this->getAction(),
				'status'     => 'accepted',
				'ip'         => (string) TotalContest( 'http.request' )->ip(),
				'date'       => [],
			];

			if ( $timeout !== 0 ):
				$date                 = TotalContest( 'datetime', [ "-{$timeout} minutes" ] );
				$conditions['date'][] = [ 'operator' => '>', 'value' => $date->format( 'Y/m/d H:i:s' ) ];
			endif;

			$count = TotalContest( 'log.repository' )->count( [ 'conditions' => $conditions ] );
			if ( $count >= $this->getCount() ):
				$this->setCookie( $this->getContestCookieName(), (int) $this->getCount(), $timeout );
				$result = false;
			elseif ( $this->getSubmissionId() ):
				$conditions['submission_id'] = $this->getSubmissionId();

				$count  = TotalContest( 'log.repository' )->count( [ 'conditions' => $conditions ] );
				$result = ! ( $count >= $this->getPerItem() );
				if ( ! $result ):
					$this->setCookie( $this->getSubmissionCookieName(), (int) $this->getPerItem(), $timeout );
				endif;
			endif;
		endif;

		return $result ?: new \WP_Error( 'ip', $this->getMessage() );
	}

	public function getPrefix() {
		return 'ip';
	}
}