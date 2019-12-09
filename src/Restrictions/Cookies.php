<?php

namespace TotalContest\Restrictions;

/**
 * Class Cookies
 * @package TotalContest\Restrictions
 */
class Cookies extends Restriction {

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

		return $result ?: new \WP_Error( 'cookies', $this->getMessage() );
	}

	public function getPrefix() {
		return 'cookies';
	}
}