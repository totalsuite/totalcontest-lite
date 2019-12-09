<?php

namespace TotalContest\Restrictions;

use TotalContestVendors\TotalCore\Helpers\Arrays;
use TotalContestVendors\TotalCore\Restrictions\Restriction as RestrictionBase;

/**
 * Base Restriction.
 * @package TotalContest\Restrictions
 */
abstract class Restriction extends RestrictionBase {
	use \TotalContestVendors\TotalCore\Traits\Cookies;

	/**
	 * @return bool
	 */
	public function isFullCheck() {
		return (bool) Arrays::getDotNotation( $this->args, 'fullCheck', false );
	}

	/**
	 * @return mixed
	 */
	public function getContestId() {
		return empty( $this->args['contest'] ) ? null : $this->args['contest']->getId();
	}

	/**
	 * @return mixed
	 */
	public function getSubmissionId() {
		return empty( $this->args['submission'] ) ? null : $this->args['submission']->getId();
	}

	/**
	 * @param int $default
	 *
	 * @return int
	 */
	public function getTimeout( $default = 3600 ) {
		return absint( Arrays::getDotNotation( $this->args, 'timeout', 3600 ) );
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return (string) Arrays::getDotNotation( $this->args, 'action' );
	}

	/**
	 * @param int $default
	 *
	 * @return int
	 */
	public function getPerItem( $default = 1 ) {
		return absint( Arrays::getDotNotation( $this->args, 'perItem', $default ) );
	}

	/**
	 * @param int $default
	 *
	 * @return int
	 */
	public function getCount( $default = 1 ) {
		return absint( Arrays::getDotNotation( $this->args, 'count', $default ) );
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return empty( $this->args['message'] ) ? __( 'You cannot do that again.', 'totalcontest' ) : (string) $this->args['message'];
	}

	/**
	 * @param $prefix
	 *
	 * @return string
	 */
	public function getCookieName( $prefix = null ) {
		return $this->generateCookieName( ( $prefix ?: $this->getPrefix() ) . $this->getAction() );
	}

	/**
	 * @param $prefix
	 *
	 * @return string
	 */
	public function getContestCookieName( $prefix = null ) {
		return $this->generateCookieName( ( $prefix ?: $this->getPrefix() ) . $this->getAction() . '_' . $this->getContestId() );
	}

	/**
	 * @param $prefix
	 *
	 * @return string
	 */
	public function getSubmissionCookieName( $prefix = null ) {
		return $this->generateCookieName( ( $prefix ?: $this->getPrefix() ) . $this->getAction() . '_' . $this->getContestId() . '_' . $this->getSubmissionId() );
	}

	abstract public function getPrefix();

	/**
	 * Generic
	 * @return bool|void
	 */
	public function apply() {
		$cookieTimeout = $this->getTimeout();

		if ( $this->getContestId() ):
			$cookieValue = $this->getCookie( $this->getContestCookieName(), 0 );
			$this->setCookie( $this->getContestCookieName(), (int) $cookieValue + 1, $cookieTimeout );
		endif;

		if ( $this->getSubmissionId() ):
			$cookieValue = $this->getCookie( $this->getSubmissionCookieName(), 0 );
			$this->setCookie( $this->getSubmissionCookieName(), (int) $cookieValue + 1, $cookieTimeout );
		endif;
	}
}